""" Tranform TeXML SAX stream """
# $Id: handler.py,v 1.18 2004-06-21 08:10:10 olpa Exp $

import xml.sax.handler
import texmlwr
import specmap
import StringIO

# WhiteSpace (WS) elimination
# In most cases, WS around tags (both opening and closing) are removed.
# But these tags save ws: <ctrl/> and <spec/>.
# WS processing is allowed or disallowed by "process_ws".

class handler(xml.sax.handler.ContentHandler):

  # Object variables
  # writer
  # no_text_content
  # text_is_only_spaces
  #
  # Whitespace handling:
  # process_ws
  # process_ws_stack
  # nl_spec
  # nl_spec_stack
  # 
  # For <env/> support:
  # cmdname
  # cmdname_stack
  # endenv
  # endenv_stack
  #
  # For <cmd/> support:
  # has_parm # Stacking is not required: if <cmd/> is in <cmd/>,
  #          # then is it wrapped by <parm/> or <opt/>

  def __init__(self, stream):
    """ Create writer, create maps """
    self.writer        = texmlwr.texmlwr(stream)
    self.cmdname_stack = []
    self.endenv_stack  = []
    self.cmdname       = ''
    self.endenv        = ''
    self.has_parm      = 0
    self.no_text_content     = 0
    self.text_is_only_spaces = 0
    self.process_ws          = 1
    self.process_ws_stack    = []
    self.nl_spec             = None
    self.nl_spec_stack       = []
    self.last_content        = ''
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
    self.model_content          = self.model_nomath.copy()
    self.model_content['math']  = self.on_math
    self.model_content['dmath'] = self.on_dmath
    self.model_cmd    = {
      'opt':    self.on_opt,
      'parm':   self.on_parm
    }
    self.model_env = self.model_nomath.copy()
    self.model_env.update(self.model_cmd)
    self.model_opt    = {
      'spec':   self.on_spec,
      'ctrl':   self.on_ctrl,
      'cmd':    self.on_cmd,
      'group':  self.on_group,
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

  def endDocument(self):
    """ Finalize document """
    self.writer.conditionalNewline()

  def startElement(self, name, attrs):
    """ Handle start of an element"""
    if name in self.model:
      # 6 May 2004, olpa: (see also 'characters' handler)
      # Now we eat leading spaces in <env/>. Any new element
      # should stop such behaviour. Ugly hack, but should work.
      self.text_is_only_spaces = 0
      self.model[name](attrs)
    else:
      raise ValueError("Element '%s' is not expected" % name)

  def characters(self, content):
    """ Handle text data """
    #print '[chars: [[[[[[[[[[[%s]]]]]]]]]]]' % content
    #
    # First, check if content allowed at all
    #
    # Elements like <spec/> should be empty
    if self.no_text_content:
      raise ValueError("Text content is not expected: '%s'" % content.encode('latin-1', 'replace'))
    # Element <cmd/> should not have text content,
    # but it also may contain spaces due to indenting
    if self.text_is_only_spaces:
      stripped = content.lstrip(" \t\n\r\f\v")
      if 0 != len(stripped):
        # 6 May 2004, olpa: now <env/> also have <parm/> and <opt/>
        # (for environments like 'tabular'). In LaTeX document, parameters
        # should go immediately after start of environment. The program
        # should delete possible (due to XML indenting) spaces. Here
        # we implement a "hack", in future we should introduce
        # smart handling of spaces.
        if self.model == self.model_env:
          self.text_is_only_spaces = 0
          self.writer.write(stripped)
          return                                           # return
        raise ValueError("Only whitespaces are expected, not text content: '%s'" % content.encode('latin-1', 'replace'))
      return                                               # return
    #
    # Eliminate whitespaces
    #
    post_content_ws = 0
    if self.process_ws:
      content2 = content.lstrip()
      if len(content2) != len(content):
	self.writer.writeWeakWS()
      content  = content2.rstrip()
      if len(content2) != len(content):
	post_content_ws = 1
    #
    # Finally, write content
    #
    self.writer.write(content)
    if post_content_ws:
      self.writer.writeWeakWS()

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

  def get_boolean(self, attrs, aname, default=None):
    """ Returns true if value of attribute "aname" is "1", false if "0" and None if attribute not exists. Raises error in other cases."""
    aval = attrs.get(aname, None)
    if None == aval:
      return default
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
    ligatures  = self.get_boolean(attrs, 'ligatures')
    self.writer.stack_mode(mode)
    self.writer.stack_emptylines(emptylines)
    self.writer.stack_escape(escape)
    self.writer.stack_ligatures(ligatures)
    ws = self.get_boolean(attrs, 'ws')
    self.process_ws_stack.append(self.process_ws)
    if ws != None:
      self.process_ws = 0 == ws

  def on_texml_end(self):
    """ Handle TeXML element. Restore old mode. """
    self.writer.unstack_ligatures()
    self.writer.unstack_escape()
    self.writer.unstack_emptylines()
    self.writer.unstack_mode()
    self.process_ws = self.process_ws_stack.pop()

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
    self.writer.writech('\\', 0)
    self.writer.write(name, 0)
    #
    # Setup in-cmd processing
    #
    self.has_parm            = 0
    self.text_is_only_spaces = 1
    self.nl_spec_stack.append(self.nl_spec)
    self.nl_spec = self.get_boolean(attrs, 'nl')

  def on_cmd_end(self):
    self.text_is_only_spaces = 0
    #
    # Write additional space if command has no parameters
    #
    if self.nl_spec:
      self.writer.writech('\n', 0)
    elif not(self.has_parm):
      self.writer.writech(' ', 0)
    self.nl_spec = self.nl_spec_stack.pop()

  def on_opt(self, attrs):
    """ Handle 'opt' element """
    self.stack_model(self.model_opt)
    self.writer.writech('[', 0)
    self.text_is_only_spaces = 0

  def on_parm(self, attrs):
    """ Handle 'parm' element """
    self.stack_model(self.model_opt)
    self.writer.writech('{', 0)
    self.text_is_only_spaces = 0
 
  def on_opt_end(self):
    self.writer.writech(']', 0)
    self.text_is_only_spaces = 1 # Because returns to <cmd/>
    self.has_parm            = 1 # At the end to avoid collision of nesting

  def on_parm_end(self):
    self.writer.writech('}', 0)
    self.text_is_only_spaces = 1
    self.has_parm            = 1

  # -----------------------------------------------------------------

  def on_env(self, attrs):
    """ Handle 'cmd' element """
    self.stack_model(self.model_env)
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
    #
    # Write <env/> and setup newline processing
    #
    if self.get_boolean(attrs, 'nl1', 1):
      self.writer.conditionalNewline()
    self.writer.write('\%s{%s}' % (begenv, name), 0)
    if self.get_boolean(attrs, 'nl2', 1):
      self.writer.conditionalNewline()
    self.nl_spec_stack.append(self.nl_spec)
    self.nl_spec = (self.get_boolean(attrs, 'nl3', 1), self.get_boolean(attrs, 'nl4', 1))
    # See '6 May 2004' comment in 'characters' handler
    self.text_is_only_spaces = 1

  def on_env_end(self):
    nl3, nl4 = self.nl_spec
    self.nl_spec = self.nl_spec_stack.pop()
    if nl3:
      self.writer.conditionalNewline()
    self.writer.write('\%s{%s}' % (self.endenv, self.cmdname), 0)
    if nl4:
      self.writer.conditionalNewline()
    self.cmdname = self.cmdname_stack.pop()
    self.endenv  = self.endenv_stack.pop()
    # See '6 May 2004' comment in 'characters' handler
    self.text_is_only_spaces = 0

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
