""" TeXML Writer and string services """
# $Id: texmlwr.py,v 1.24 2004-07-07 10:25:39 olpa Exp $

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
  # then writer converts weak whitespaces into newlines.
  # And flag if it is possible
  # > autonewline_after_len
  # > allow_weak_ws_to_nl
  # > is_after_weak_ws
  
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
    self.allow_weak_ws_to_nl     = 1
    self.is_after_weak_ws        = 0

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

  def set_allow_weak_ws_to_nl(self, flag):
    """ Set flag if weak spaces can be converted to new lines """
    self.allow_weak_ws_to_nl = flag

  def conditionalNewline(self):
    """ Write a new line unless already at the start of a line """
    if self.approx_current_line_len != 0:
      self.writech('\n', 0)

  def writeWeakWS(self):
    """ Write a whitespace instead of whitespaces deleted from source XML """
    self.is_after_weak_ws = 1
    #self.last_ch          = ' ' # no, setting so is an error: new lines are not corrected after it. Anyway, check for weak ws is the first action in writech, so it should not lead to errors
    #
    # Break line if it is too long
    # We should not break lines if we regard spaces
    #
    if (self.approx_current_line_len > self.autonewline_after_len) and self.allow_weak_ws_to_nl:
      self.conditionalNewline()
      return                                               # return

  def writech(self, ch, esc_specials):
    """ Write a char, (maybe) escaping specials """
    #
    # Write a suspended whitespace
    #
    if self.is_after_weak_ws:
      self.is_after_weak_ws = 0
      if (self.approx_current_line_len != 0) and not(ch in string.whitespace):
        self.writech(' ', 0)
    #
    # Update counter
    #
    self.approx_current_line_len = self.approx_current_line_len + 1
    #
    # Handle well-known standard TeX ligatures
    #
    if not(self.ligatures):
      if '-' == ch:
        if '-' == self.last_ch:
          self.writech('{', 0)
          self.writech('}', 0)
      elif "'" == ch:
        if "'" == self.last_ch:
          self.writech('{', 0)
          self.writech('}', 0)
      elif '`' == ch:
        if ('`' == self.last_ch) or ('!' == self.last_ch) or ('?' == self.last_ch):
          self.writech('{', 0)
          self.writech('}', 0)
    #
    # Handle end-of-line symbols.
    # XML spec says: 2.11 End-of-Line Handling:
    # ... contains either the literal two-character sequence "#xD#xA" or
    # a standalone literal #xD, an XML processor must pass to the
    # application the single character #xA.
    #
    if ('\n' == ch) or ('\r' == ch):
      #
      # We should never get '\r', but only '\n'.
      # Anyway, someone will copy and paste this code, and code will
      # get '\r'. In this case rewrite '\r' as '\n'.
      #
      if '\r' == ch:
        ch = '\n'
      #
      # TeX interprets empty line as \par, fix this problem
      #
      if (self.last_ch == '\n') and (not self.emptylines):
        self.writech('%', 0)
      #
      # Now create newline, update counters and return
      #
      self.stream.write(os.linesep)
      self.approx_current_line_len = 0
      self.last_ch                 = ch
      return                                               # return
    self.last_ch = ch
    #
    # Handle specials
    #
    if esc_specials:
      try:
        if self.mode == TEXT:
          self.write(specmap.textescmap[ch], 0)
        else:
          self.write(specmap.mathescmap[ch], 0)
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
        self.write(unimap.textmap[chord], 0)
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
        self.write(unimap.mathmap[chord], 0)
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
        self.write('\\ensuremath{', 0)
      else:
        self.write('\\ensuretext{', 0)
      self.write(tostr, 0)
      self.writech('}', 0)
      return                                               # return
    #
    # Finally, write symbol in &#xNNN; form
    #
    self.write('&#x%X;' % chord, 0)

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

