#!/usr/bin/python

# Import the needed libraries
import sys
import Texml.processor

# Set up the input file and the output file object
infile = 'document.xml'
out = file('out.tex', 'w')
# Older versions of python need the following code:
# out = open('out.tex', 'w')

# Convert
Texml.processor.process(infile, out)

# Clean up resources
out.close()
