""" Tranform TeXML SAX stream """
# $Id: handler.py,v 1.2 2004-03-15 12:18:33 olpa Exp $

import xml.sax.handler
import texmlwr

class handler(xml.sax.handler.ContentHandler):

  def __init__(self, stream):
    """ Create writer, create maps """
    self.writer = texmlwr.texmlwr(stream)
    #
    # Create handler maps
    #
    self.model_nomath  = {
      'TeXML':  self.on_texml,
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
      'TeXML':  self.on_texml_end,
      'cmd':    self.on_cmd_end,
      'env':    self.on_env_end,
      'group':  self.on_group_end,
      'ctrl':   self.on_ctrl_end,
      'spec':   self.on_spec_end,
      'opt':    self.on_opt_end,
      'parm':   self.on_parm_end,
      'math':   self.on_math_end,
      'dmath':  self.on_dmath_end
    }

  # -------------------------------------------------------------------
  
  def startDocument(self):
    """ Initialize data structures before parsing """
    self.model       = self.model_content
    self.model_stack = []

  def startElement(self, name, attrs):
    """ Handle start of an element"""
    if name in self.model:
      self.model[name](attrs)
    else:
      raise ValueError("Element '%s' is not expected" % name)
  
  def characters(self, content):
    """ Handle text data """
    self.writer.write(content)

  # -------------------------------------------------------------------

  def endElement(self, name):
    """ Handle end of en element """
    self.end_handlers[name]()

  def on_texml(self, attrs):
    print "on_texml", attrs

  def on_texml_end(self):
    print "on_texml_end"

  def on_cmd(self):
    pass

  def on_cmd_end(self):
    pass

  def on_env(self):
    pass

  def on_env_end(self):
    pass

  def on_group(self):
    pass

  def on_group_end(self):
    pass

  def on_ctrl(self):
    pass

  def on_ctrl_end(self):
    pass

  def on_spec(self):
    pass
   
  def on_spec_end(self):
    pass
 
  def on_opt(self):
    pass
 
  def on_opt_end(self):
    pass

  def on_parm(self):
    pass

  def on_parm_end(self):
    pass

  def on_math(self):
    pass

  def on_math_end(self):
    pass

  def on_dmath(self):
    pass

  def on_dmath_end(self):
    pass
