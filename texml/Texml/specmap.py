""" Map special TeX symbols """
# $Id: specmap.py,v 1.1 2005-03-17 04:16:24 paultremblay Exp $
import os

# text escape map and math escape map should contain the same keys

textescmap = {
  '\\': r'\textbackslash{}',
  '{':  r'\{',
  '}':  r'\}',
  '$':  r'\textdollar{}',
  '&':  r'\&',
  '#':  r'\#',
  '^':  r'\^{}',
  '_':  r'\_',
  '~':  r'\textasciitilde{}',
  '%':  r'\%',
  '|':  r'\textvert{}',
  '<':  r'\textless{}',
  '>':  r'\textgreater{}',
  # not special but typography
  u'\u00a9': r'\textcopyright{}'
}

# Paul Tremblay added this on 2005-03-08
textescmap_context = {
  '\\': r'\textbackslash{}',
  '{':  r'\{',
  '}':  r'\}',
  '$':  r'\textdollar{}',
  '&':  r'\&',
  '#':  r'\#',
  '^':  r'\^{}',
  '_':  r'\_',
  '~':  r'\textasciitilde{}',
  '%':  r'\%',
  '|':  r'\type{|}',
  '<':  r'\textless{}',
  '>':  r'\textgreater{}',
  # not special but typography
  u'\u00a9': r'\textcopyright{}'
}

mathescmap = {
  '\\': r'\backslash{}',
  '{':  r'\{',
  '}':  r'\}',
  '$':  r'\$',
  '&':  r'\&',
  '#':  r'\#',
  '^':  r'\^{}',
  '_':  r'\_',
  '~':  r'\~{}',
  '%':  r'\%',
  '|':  r'|',
  '<':  r'<',
  '>':  r'>',
  # not special but typography
  u'\u00a9': r'\copyright{}'
}

#
# Although these symbols are not special, it is better to escape them
# because in as-is form they are not so good
#
typographymap = {
  u'\u00a0':  r'~'
}

textescmap.update(typographymap)
mathescmap.update(typographymap)

#
# Mapping from spec/@cat to symbols
#
tocharmap = {
  'esc':     '\\',
  'bg':      '{',
  'eg':      '}',
  'mshift':  '$',
  'align':   '&',
  'parm':    '#',
  'sup':     '^',
  'sub':     '_',
  'tilde':   '~',
  'comment': '%',
  'vert':    '|',
  'lt':      '<',
  'gt':      '>',
  'nl':      '\n',
  'space':   ' ',
  'nil':     ''
}