""" TeXML interface entry """
# $Id: processor.py,v 1.1 2006-04-20 03:59:50 olpa Exp $

import Texml.texmlwr
import Texml.handler

def process(in_stream, out_stream, encoding='ascii', autonl_width=62, always_ascii=0, use_context=0):
  transform_obj = Texml.handler.ParseFile()
  texml_writer =  Texml.texmlwr.texmlwr(
      stream       = out_stream,
      encoding     = encoding,
      autonl_width = autonl_width,
      use_context  = use_context,
      always_ascii = always_ascii,
      )
  transform_obj.parse_file(
      read_obj     = in_stream,
      texml_writer = texml_writer,
      use_context  = use_context,
      )

