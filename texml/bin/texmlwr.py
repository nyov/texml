""" TeXML Writer and string services """
# $Id: texmlwr.py,v 1.4 2004-03-16 08:43:24 olpa Exp $

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
  #
  # Stream and mode. They can be temporary substituted,
  # so stacks of streams and modes are used.
  # stream
  # mode
  # stream_stack
  # mode_stack
  
  def __init__(self, stream):
    """ Remember output stream, initialize data structures """
    self.stream = stream
    self.after_char0d = 1;
    self.after_char0a = 1;
    self.mode         = TEXT;
    self.mode_stack   = [];
    self.stream       = stream
    self.stream_stack = [];

  def stack_mode(self, mode):
    """ put new mode into the stack of modes """
    self.mode_stack.append(self.mode)
    if mode != DEFAULT:
      self.mode = mode

  def unstack_mode(self):
    self.mode = self.mode_stack.pop()
  
  def writech(self, ch, esc_specials):
    """ Write a char, (maybe) escaping specials """
    #
    # Handle end-of-line symbols in special way
    # We automagically process "\n", "\r", "\n\r" and "\r\n" line ends
    #
    if ('\n' == ch) or ('\r' == ch):
      #
      # line end symbol of the same type starts new line
      #
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
      # Write line end char and return
      #
      self.stream.write(ch)
      return                                         # return
    #
    # Not end-of-line symbol
    #
    self.after_char0d = 0
    self.after_char0a = 0
    if not esc_specials:
      self.stream.write(ch)
    else:
      if self.mode == TEXT:
        self.stream.write(specmap.textescmap.get(ch, ch))
      else:
        self.stream.write(specmap.mathescmap.get(ch, ch))

  def write(self, str):
    """ Write symbols char-by-char in current mode of escaping """
    escape = self.mode != ASIS
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
