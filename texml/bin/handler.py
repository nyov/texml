""" Tranform TeXML SAX stream """
# $Id: handler.py,v 1.10 2004-03-26 14:26:14 olpa Exp $

import xml.sax.handler
import texmlwr
import specmap
import StringIO

class handler(xml.sax.handler.ContentHandler):

  # Object variables
  # writer
  # no_text_content
  # 
  # For <cmd /> and <env /> support:
  # parm_content
  # opt_content
  # parm_stack
  # opt_stack
  # cmdname
  # cmdname_stack
  # endenv
  # endenv_stack

  def __init__(self, stream):
    """ Create writer, create maps """
    self.writer        = texmlwr.texmlwr(stream)
    self.parm_stack    = []
    self.opt_stack     = []
    self.cmdname_stack = []
    self.endenv_stack  = []
    self.parm_content  = ''
    self.opt_content   = ''
    self.cmdname       = ''
    self.endenv        = ''
    self.no_text_content = 0
    #
    # Create handler maps
    #
    self.model_nomath = {
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
    self.model_cmd    = {
      'opt':    self.on_opt,
      'parm':   self.on_parm
    }
    self.model_opt    = {
      'spec':   self.on_spec,
      'ctrl':   self.on_ctrl,
      'cmd':    self.on_cmd,
      'math':   self.on_math
    }
    self.model_parm   = self.model_opt
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
    self.model       = {'TeXML': self.on_texml}
    self.model_stack = []

  def startElement(self, name, attrs):
    """ Handle start of an element"""
    if name in self.model:
      self.model[name](attrs)
    else:
      raise ValueError("Element '%s' is not expected" % name)

  def characters(self, content):
    """ Handle text data """
    if self.no_text_content:
      raise ValueError('Text content is not expected')
    self.writer.write(content)

  def endElement(self, name):
    """ Handle end of en element """
    self.end_handlers[name]()
    self.unstack_model()

  def stack_model(self, model):
    """ Remember content model of parent and set model for current node """
    self.model_stack.append(self.model)
    self.model = model

  def unstack_model(self):
    """ Restore content model of parent """
    self.model = self.model_stack.pop()

  # -----------------------------------------------------------------

  def get_boolean(self, attrs, aname):
    """ Returns true if value of attribute "aname" is "1", false if "0" and None if attribute not exists. Raises error in other cases."""
    aval = attrs.get(aname, None)
    if None == aval:
      return None
    elif '1' == aval:
      return 1
    elif '0' == aval:
      return 0
    raise ValueError("Value of boolean attribute '%s' is not '0' or '1', but '%s'" % (aname, aval))

  def on_texml(self, attrs):
    """ Handle TeXML element """
    self.stack_model(self.model_content)
    #
    # Set new mode ("text" or "math")
    #
    str = attrs.get('mode', None)
    if None == str:
      mode = texmlwr.DEFAULT
    elif 'text' == str:
      mode = texmlwr.TEXT
    elif 'math' == str:
      mode = texmlwr.MATH
    else:
      raise ValueError("Unknown value of TeXML/@mode attribute: '%s'" % str)
    emptylines = self.get_boolean(attrs, 'emptylines')
    escape     = self.get_boolean(attrs, 'escape')
    self.writer.stack_mode(mode)
    self.writer.stack_emptylines(emptylines)
    self.writer.stack_escape(escape)

  def on_texml_end(self):
    """ Handle TeXML element. Restore old mode. """
    self.writer.unstack_escape()
    self.writer.unstack_emptylines()
    self.writer.unstack_mode()

  # -----------------------------------------------------------------

  def on_cmd(self, attrs):
    """ Handle 'cmd' element """
    self.stack_model(self.model_cmd)
    #
    # Get name of the command
    #
    name = attrs.get('name', '')
    if 0 == len(name):
      raise ValueError('Attribute cmd/@name is empty')
    self.cmdname_stack.append(self.cmdname)
    self.cmdname = name
    #
    # Setup output streams and stacks
    #
    self.writer.stack_stream(StringIO.StringIO())
    self.parm_stack.append(self.parm_content)
    self.opt_stack.append(self.opt_content)
    self.parm_content = '';
    self.opt_content  = '';

  def on_cmd_end(self):
    #
    # Get name, text content, "parm" and "opt" strings
    # Restore old values.
    #
    chars = self.writer.unstack_stream()
    name  = self.cmdname
    parm  = self.parm_content
    opt   = self.opt_content
    self.cmdname      = self.cmdname_stack.pop()
    self.parm_content = self.parm_stack.pop()
    self.opt_content  = self.opt_stack.pop()
    #
    # Check that chars inside are only spaces (comment char also ok)
    #
    if 0 != len(chars.strip(" \t\n\r\f\v%")):
      raise ValueError("Element 'cmd' has text content: '%s'" % chars.encode('latin-1', 'replace'))
    #
    # And now write command
    #
    self.writer.writech('\\', 0)
    if (0 == len(parm)) and (0 == len(opt)):
      self.writer.write(name, 0)
      self.writer.writech(' ', 0)
    else:
      self.writer.write(name, 0)
      if 0 != len(opt):
        self.writer.writech('[', 0)
        self.writer.write(opt, 0)
        self.writer.writech(']', 0)
      if 0 != len(parm):
        self.writer.writech('{', 0)
        self.writer.write(parm, 0)
        self.writer.writech('}', 0)

  def on_opt(self, attrs):
    """ Handle 'opt' element """
    self.on_opt_and_parm(self.opt_content)

  def on_parm(self, attrs):
    """ Handle 'parm' element """
    self.on_opt_and_parm(self.parm_content)

  def on_opt_and_parm(self, str):
    self.stack_model(self.model_opt)
    stream = StringIO.StringIO()
    if 0 != len(str):
      stream.write(str)
      stream.write(',')
    self.writer.stack_stream(stream)
 
  def on_opt_end(self):
    self.opt_content = self.writer.unstack_stream()

  def on_parm_end(self):
    self.parm_content = self.writer.unstack_stream()

  # -----------------------------------------------------------------

  def on_env(self, attrs):
    """ Handle 'cmd' element """
    self.stack_model(self.model_content)
    #
    # Get name of the environment, and begin and end commands
    #
    name = attrs.get('name', '')
    if 0 == len(name):
      raise ValueError('Attribute env/@name is empty')
    begenv = attrs.get('begin', 'begin')
    self.cmdname_stack.append(self.cmdname)
    self.endenv_stack.append(self.endenv)
    self.cmdname = name
    self.endenv  = attrs.get('end',   'end')
    self.writer.write('\%s{%s}' % (begenv, name), 0)

  def on_env_end(self):
    self.writer.write('\%s{%s}' % (self.endenv, self.cmdname), 0)
    self.cmdname = self.cmdname_stack.pop()
    self.endenv  = self.endenv_stack.pop()

  def on_group(self, attrs):
    """ Handle 'group' element """
    self.stack_model(self.model_content)
    self.writer.writech('{', 0)

  def on_group_end(self):
    self.writer.writech('}', 0)

  # -----------------------------------------------------------------

  def on_ctrl(self, attrs):
    #
    # Get character, check and print tex command
    #
    ch = attrs.get('ch', '')
    if 1 != len(ch):
      raise ValueError("Attribute ctrl/@ch is not a char: '%s'" % ch)
    self.writer.writech('\\', 0)
    self.writer.writech(ch,   0)
    #
    # Content of this element is empty
    #
    self.stack_model({})
    self.no_text_content = 1

  def on_ctrl_end(self):
    self.no_text_content = 0

  def on_spec(self, attrs):
    #
    # Get category, get corresponding character
    #
    cat = attrs.get('cat', '')
    if not (cat in specmap.tocharmap):
      raise ValueError("Attribute spec/@cat unknown: '%s'" % cat)
    self.writer.writech(specmap.tocharmap[cat], 0)
    #
    # Content of this element is empty
    #
    self.stack_model({})
    self.no_text_content = 1

  def on_spec_end(self):
    self.no_text_content = 0

  # -----------------------------------------------------------------

  def on_math(self, attrs):
    self.stack_model(self.model_nomath)
    self.writer.writech('$', 0)
    self.writer.stack_mode(texmlwr.MATH)

  def on_math_end(self):
    self.writer.unstack_mode()
    self.writer.writech('$', 0)

  def on_dmath(self, attrs):
    self.writer.writech('$', 0)
    self.on_math(attrs)

  def on_dmath_end(self):
    self.on_math_end()
    self.writer.writech('$', 0)
