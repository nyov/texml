""" TeXML Writer and string services """
# $Id: texmlwr.py,v 1.1 2004-03-15 12:16:53 olpa Exp $

#
# Modes of processing of special characters
#
DEFAULT = 0;
TEXT    = 1;
MATH    = 2;
ASIS    = 3;

import unimap
import specmap

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
    self.after_ch09   = 0;
    self.after_ch0a   = 0;
    self.mode         = TEXT;
    self.mode_stack   = [];
    self.stream = stream
    self.stream_stack = [];
  
  def writech(self, ch, esc_specials):
    """ Write a char, (maybe) escaping specials """
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
