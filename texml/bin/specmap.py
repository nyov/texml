""" Map special TeX symbols """
# $Id: specmap.py,v 1.1 2004-03-15 12:11:09 olpa Exp $

# text escape map and map escape map should contain the same keys

textescmap = {
  '\\': r'$\backslash$',
  '{':  r'\{',
  '}':  r'\}',
  '$':  r'\$',
  '&':  r'\&',
  '#':  r'\#',
  '^':  r'\^{}',
  '_':  r'\_',
  '~':  r'\~{',
  '%':  r'\%',
  '|':  r'$|$',
  '<':  r'$<$',
  '>':  r'$>$'
}

mathescmap = {
  '\\': r'\backslash',
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
  '>':  r'>'
}

