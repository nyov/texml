#!/usr/local/bin/python
# $Id: texml.py,v 1.1 2005-03-17 04:16:24 paultremblay Exp $

VERSION = "1.24.devel"; # GREPVERSION # Format of this string is important
usage = """Convert TeXML markup to [La]TeX markup. v.%s. Usage:
python texml.py [-e encoding] [-w auto_width] [-c|--context] 
                [--ns ]
in_file out_file""" % VERSION



# added this Paul Tremblay
from xml.sax.handler import feature_namespaces

#
# Check command line, print help
#
import sys
if len(sys.argv) < 3:
  print >>sys.stderr, usage
  sys.exit(1)

#
# Parse command line options
#
encoding    = 'ascii'
width       = 50
use_context = 0
use_namespace = 0
import getopt
try:
  opts, args = getopt.getopt(sys.argv[1:], 'hcw:e:', ['help', 'context', 'width=', 'encoding='])
except getopt.GetoptError, e:
  print >>sys.stderr, 'texml: Can\'t parse command line: %s' % e
  print >>sys.stderr, usage
  sys.exit(2)
for o, a in opts:
  if o in ('-h', '--help'):
    print >>sys.stderr, usage;
    sys.exit(1)
  if o in ('-c', '--context'):
    use_context = 1
  if o in ('--ns'):
    use_namespace = 1
  if o in ('-w', '--width'):
    try:
      width = int(a)
    except:
      print >>sys.stderr, "texml: Width is not an integer: '%s'" % a
      sys.exit(1)
    if width < 3: # just a random value
      print >>sys.stderr, "texml: Width should be greater 3 but get '%s'" % a
      sys.exit(1)
  if o in ('-e', '--encoding'):
    encoding = a

#
# Get input and output file
#
if len(args) != 2:
  print >>sys.stderr, 'texml: Expected two command line arguments, but got %d' % len(args)
  sys.exit(3)
(infile, outfile) = args

#
# Prepare transformation-1: input file, XML parser
#
import xml.sax 

p = xml.sax.make_parser()
# p = xml.sax.make_parser(['drv_libxml2']) # for libxml2
if '-' == infile:
  infile = sys.stdin

#
# Prepare transformation-2: output
#
if '-' == outfile:
  f = sys.stdout
else:
  f = file(outfile, 'wb')
import Texml.texmlwr
try:
  f = Texml.texmlwr.stream_encoder(f, encoding)
except Exception, e:
  print >>sys.stderr, "texml: Can't create encoder: '%s'" % e
  sys.exit(5)

#
# Create transformer and run process
#
import Texml.handler

h = Texml.handler.glue_handler(f, width, use_context)

parser = xml.sax.make_parser()
parser.setFeature(feature_namespaces, use_namespace)
parser.setContentHandler(h)
parser.setFeature("http://xml.org/sax/features/external-general-entities", False)
parser.parse(infile)             



#
# Ok
#
f.close()
sys.exit(0)

