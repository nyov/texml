""" TeXML Writer and string services """
# $Id: texmlwr.py,v 1.19 2004-06-23 07:17:16 olpa Exp $

#
# Modes of processing of special characters
#
DEFAULT = 0;
TEXT    = 1;
MATH    = 2;
ASIS    = 3;

import unimap
import specmap
import codecs
import os
import string

#
# Writer&Co class
#
class texmlwr:
  
  #
  # Object variables
  #
  # Empty line detection by end-of-line symbols
  # after_ch09
  # after_ch0a
  # Handling of '--', '---' and other ligatures
  # last_char
  #
  # Modes of transformation can be tuned and nested
  # mode
  # mode_stack
  # escape
  # escape_stack
  # ligatures
  # ligatures_stack
  # emptylines
  # emptylines_stack
  #
  # Current length of a line that is being written. Value usually
  # incorrect, but always correct to detect the start of a line (0)
  # > approx_current_line_len
  # If length of a current line is greater the value
  # then writer converts weak whitespaces into newlines
  # >autonewline_after_len
  # Total number of printed characters (incorrect, but can be
  # used to detect one weak whitespace after another)
  # > approx_total_len
  # Position of last weak whitespace
  # > last_weak_ws_pos (autonl_width)
  # A ws suspend flag to avoid weak whitespaces at end of line
  # > weak_ws_is_suspended
  
  def __init__(self, stream, autonl_width):
    """ Remember output stream, initialize data structures """
    self.stream = stream
    self.after_char0d     = 1
    self.after_char0a     = 1
    self.last_ch          = None
    self.mode             = TEXT
    self.mode_stack       = []
    self.escape           = 1
    self.escape_stack     = []
    self.ligatures        = 0
    self.ligatures_stack  = []
    self.emptylines       = 0
    self.emptylines_stack = []
    self.approx_current_line_len = 0
    self.autonewline_after_len   = autonl_width
    self.approx_total_len        = 0
    self.last_weak_ws_pos        = -2 # anything less than 1
    self.weak_ws_is_suspended    = 0

  def stack_mode(self, mode):
    """ Put new mode into the stack of modes """
    self.mode_stack.append(self.mode)
    if mode != DEFAULT:
      self.mode = mode

  def unstack_mode(self):
    """ Restore mode """
    self.mode = self.mode_stack.pop()

  def stack_escape(self, ifdo):
    """ Set if escaping is required. Remember old value. """
    self.escape_stack.append(self.escape)
    if ifdo != None:
      self.escape = ifdo

  def unstack_escape(self):
    """ Restore old policy of escaping """
    self.escape = self.escape_stack.pop()

  def stack_ligatures(self, ifdo):
    """ Set if breaking of ligatures is required. Remember old value. """
    self.ligatures_stack.append(self.ligatures)
    if ifdo != None:
      self.ligatures = ifdo

  def unstack_ligatures(self):
    """ Restore old policy of breaking ligatures """
    self.ligatures = self.ligatures_stack.pop()

  def stack_emptylines(self, ifdo):
    """ Set if empty lines are required. Remember old value. """
    self.emptylines_stack.append(self.emptylines)
    if ifdo != None:
      self.emptylines = ifdo

  def unstack_emptylines(self):
    """ Restore old policy of handling of empty lines """
    self.emptylines = self.emptylines_stack.pop()

  def conditionalNewline(self):
    """ Write a new line unless already at the start of a line """
    if self.approx_current_line_len != 0:
      self.write(os.linesep, 0)

  def writeWeakWS(self):
    """ Write a whitespace instead of whitespaces deleted from source XML """
    #
    # Break line if it is too long
    #
    if self.approx_current_line_len > self.autonewline_after_len:
      self.conditionalNewline()
      return                                               # return
    #
    # Ignore weak whitespace at the beginning of a line
    #
    if self.approx_current_line_len == 0:
      return                                               # return
    #
    # Ignore weak whitespace after another another weak whitespace
    #
    if self.last_weak_ws_pos + 1 == self.approx_total_len:
      return                                               # return
    #
    # Finally, "write" a space
    #
    self.last_weak_ws_pos = self.approx_total_len
    self.last_ch = ' '
    self.weak_ws_is_suspended = 1

  def writech(self, ch, esc_specials):
    """ Write a char, (maybe) escaping specials """
    #
    # Write a suspended whitespace
    #
    if self.weak_ws_is_suspended:
      self.weak_ws_is_suspended = 0
      if not(ch in string.whitespace):
        self.writech(' ', 0)
    #
    # Update counters
    #
    self.approx_current_line_len = self.approx_current_line_len + 1
    self.approx_total_len        = self.approx_total_len        + 1
    #
    # Handle well-known standard TeX ligatures
    #
    if not(self.ligatures):
      if '-' == ch:
        if '-' == self.last_ch:
          self.stream.write('{}')
      elif "'" == ch:
        if "'" == self.last_ch:
          self.stream.write('{}')
      elif '`' == ch:
        if ('`' == self.last_ch) or ('!' == self.last_ch) or ('?' == self.last_ch):
          self.stream.write('{}')
    self.last_ch = ch
    #
    # Handle end-of-line symbols in special way
    # We automagically process "\n", "\r", "\n\r" and "\r\n" line ends
    #
    if ('\n' == ch) or ('\r' == ch):
      #
      # line end symbol of the same type starts new line
      #
      if not self.emptylines:
        if (('\n' == ch) and self.after_char0a) or (('\r' == ch) and self.after_char0d):
          self.stream.write('%')
      #
      # Two different line end symbols clean each other
      #
      if self.after_char0a and self.after_char0d:
        self.after_char0a = 0
        self.after_char0d = 0
      #
      # Remember that end of line has happended
      #
      if '\n' == ch:
        self.after_char0a = 1
      else:
        self.after_char0d = 1
      #
      # Write line end char (only once for \r\n) and return
      #
      if (0 == self.after_char0a) or (0 == self.after_char0d):
        self.stream.write(os.linesep)
        self.approx_current_line_len = 0
      return                                               # return
    #
    # Not end-of-line symbol. If it has to be escaped, we write
    # plain ASCII substitution and return
    #
    self.after_char0d = 0
    self.after_char0a = 0
    #
    # Handle specials
    #
    if esc_specials:
      try:
        if self.mode == TEXT:
          self.stream.write(specmap.textescmap[ch])
        else:
          self.stream.write(specmap.mathescmap[ch])
        return                                             # return
      except:
        pass
    #
    # First attempt to write symbol as-is
    #
    try:
      self.stream.write(ch)
      return                                               # return
    except:
      pass
    #
    # Symbol have to be rewritten. Let start with math mode.
    #
    chord = ord(ch)
    if self.mode == TEXT:
      #
      # Text mode, lookup text map
      #
      try:
        self.stream.write(unimap.textmap[chord])
        return                                             # return
      except:
        #
        # Text mode, lookup math map
        #
        tostr = unimap.mathmap.get(chord, None)
    else: # self.mode == MATH:
      #
      # Math mode, lookup math map
      #
      try:
        self.stream.write(unimap.mathmap[chord])
        return                                             # return
      except:
        #
        # Math mode, lookup text map
        #
        tostr = unimap.textmap.get(chord, None)
    #
    # If mapping in another mode table is found, use a wrapper
    #
    if tostr != None:
      if self.mode == TEXT:
        self.stream.write('\\ensuremath{')
      else:
        self.stream.write('\\ensuretext{')
      self.stream.write(tostr)
      self.writech('}', 0)
      return                                               # return
    #
    # Finally, write symbol in &#xNNN; form
    #
    self.stream.write('&#x%X;' % chord)

  def write(self, str, escape = None):
    """ Write symbols char-by-char in current mode of escaping """
    if None == escape:
      escape = self.escape
    for ch in str:
      self.writech(ch, escape)
#
# Wrapper over output stream to write is desired encoding
#
class stream_encoder:

  def __init__(self, stream, encoding):
    """ Construct a wrapper by stream and encoding """
    self.stream = stream
    self.encode = codecs.getencoder(encoding)

  def write(self, str):
    """ Write string encoded """
    self.stream.write(self.encode(str)[0])

  def close(self):
    """ Close underlying stream """
    self.stream.close()

