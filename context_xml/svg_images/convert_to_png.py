#!/usr/bin/env python
import os, sys, commands, shutil

class Convert:

    def __init__(self):
        pass
        self.__png_dir = '/home/paul/cvs/context_xml/png_images/'


    def convert(self):
        in_files = sys.argv[1:]
        for in_file in in_files:
            command = 'xsltproc /home/paul/cvs/context_xml/xslt/get_svg_info.xsl %s ' % (in_file)
        (exit_status, out_text) = commands.getstatusoutput(command)
        height, width = out_text.split()
        scale = raw_input('What is the scale?\n')
        new_height = str(float(scale) * float(height))
        new_width = str(float(scale) * float(width))
        filename, ext = os.path.splitext(in_file)
        command = 'svg-convert -bg 255.255.255.255 -h %s -w %s %s' % (new_height, new_width, in_file)
        print command
        os.system(command)
        new_path = filename + '.png'
        move_path = os.path.join(self.__png_dir,new_path )
        if os.path.isfile(new_path):
            # shutil.copy(new_path, self.__png_dir)
            os.rename(new_path, move_path)


if __name__ == '__main__':
    convert_obj = Convert()
    convert_obj.convert()
