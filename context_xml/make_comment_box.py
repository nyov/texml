#!/usr/bin/env python
import os, sys, commands
out_file = 'out_comments.txt'
comment_in = 'comment_block.xml'
command  = 'xml2txt -o %s %s' % (out_file, comment_in)
os.system(command)
