#!/usr/bin/env python
import os, sys, glob,shutil

class AddNs:

    def __init__(self):
        self.__main_dir = '/home/paul/cvs/texml/tests/data'
        self.__name_space = 'http://getfo.sourceforge.net/texml/ns1'

    def add_ns(self):
        pattern = os.path.join(self.__main_dir, '*xml')
        pattern = os.path.join(self.__main_dir, '*out')
        all_files = glob.glob(pattern)
        for the_file in all_files:
            filename, ext = os.path.splitext(the_file)
            new_filename = filename + '_ns.out'
            shutil.copy(the_file, new_filename)

    def add_file_(self, path):
        dirname = os.path.dirname(path)
        filename, ext = os.path.splitext(path)
        new_filename = filename + '_ns.xml'
        read_obj = open(path, 'r')
        write_obj = open(new_filename, 'w')
        line_to_read = 1
        counter = 0
        while line_to_read:
            line_to_read = read_obj.readline()
            counter += 1
            if line_to_read.strip() == '<TeXML>':
                write_obj.write('<TeXML xmlns="%s">\n' % self.__name_space)
            else:
                write_obj.write(line_to_read)
        write_obj.close()
        read_obj.close()

if __name__ == '__main__':
    add_obj = AddNs()
    add_obj.add_ns()
