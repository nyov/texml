#!/usr/bin/python

# Import the needed libraries
import sys
import Texml.processor

# Use the standard input and output
in_stream  = sys.stdin
out_stream = sys.stdout

# Convert
Texml.processor.process(in_stream, out_stream)
