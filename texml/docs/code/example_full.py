#!/usr/bin/python

# Import the needed libraries
import sys
import Texml.processor

# Input can be given by a filename, output should be a file object
infile = 'document.xml'
out    = file('out.tex', 'w')
# older versions of python need the following code:
# out = open('out.tex', 'w')

# More parameters
width        = 62
encoding     = 'UTF-8'
always_ascii = 0
use_context  = 0

# Convert TeXML inside a try-except block
try:
  Texml.processor.process(
      in_stream    = infile,
      out_stream   = out,
      autonl_width = width,
      encoding     = encoding,
      always_ascii = always_ascii,
      use_context  = use_context)
except Exception, msg:
  print sys.stderr, 'texml: %s' % str(msg)

# Clean up resources
out.close()
