#!/usr/bin/env python
import sys, os, commands

class Shift:

    def __init__(self):
        pass


    def convert(self):
        in_files = sys.argv[1:]
        for the_file in in_files:
            x = raw_input('How much do you want to shift x?\n')
            y = raw_input('How much do you want to shift y?\n')
            filename, ext = os.path.splitext(the_file)
            out_file = '%s_shift.svg' % filename
            xslt_sheet = '/home/paul/Documents/context/xslt/shift.xsl'
            command = 'xsltproc --stringparam shiftx %s --stringparam shifty %s %s %s >  %s' % (x, y, xslt_sheet, the_file, out_file)
            print command
            os.system(command)


if __name__ == '__main__':
    convert_obj = Shift()
    convert_obj.convert()


