""" Tranform TeXML SAX stream """
# $id$

import xml.sax.handler

class handler(xml.sax.handler.ContentHandler):

  def __init__(self, stream):
    """ Remember output stream, create maps """
    #
    # Create handler maps
    #
    self.model_nomath  = {
      'zTeXML':  self.on_texml,
      'cmd':    self.on_cmd,
      'env':    self.on_env,
      'group':  self.on_group,
      'ctrl':   self.on_ctrl,
      'spec':   self.on_spec
    }
    self.model_content          = self.model_nomath.copy();
    self.model_content['math']  = self.on_math;
    self.model_content['dmath'] = self.on_dmath;
    self.model_cmd = {
      'opt':    self.on_opt,
      'parm':   self.on_parm
    }
    self.model_opt = {
      'spec':   self.on_spec,
      'ctrl':   self.on_ctrl,
      'cmd':    self.on_cmd,
      'math':   self.on_math
    }
    self.end_handlers = {
      'TeXML':  self.on_texml,
      'cmd':    self.on_cmd,
      'env':    self.on_env,
      'group':  self.on_group,
      'ctrl':   self.on_ctrl,
      'spec':   self.on_spec,
      'opt':    self.on_opt,
      'parm':   self.on_parm,
      'math':   self.on_math,
      'dmath':  self.on_dmath
    }
  
  def startDocument(self):
    """ Initialize data structures before parsing """
    self.model       = self.model_content
    self.model_stack = []

  def startElement(self, name, attrs):
    """ Handle start of an element"""
    if name in self.model:
      self.model[name](self, attrs)
    else:
      raise ValueError("Element '%s' is not expected" % name )

  def on_texml(self):
    print "on_texml"

  def on_cmd(self):
    pass

  def on_env(self):
    pass

  def on_group(self):
    pass

  def on_ctrl(self):
    pass

  def on_spec(self):
    pass
    
  def on_opt(self):
    pass

  def on_parm(self):
    pass

  def on_math(self):
    pass

  def on_dmath(self):
    pass

#startDocument(
#endDocument(
#startElement(
#endElement(
#characters(

