#!/usr/bin/env python

import os, sys, commands



class Update:

    def __init__(self):
        pass


    def update_files(self):
        os.chdir('/home/paul/cvs/context_xml')
        command = 'rsync --update -e ssh -v -R  -r --delete *css *html png_images/* texml_examples/*xml context_examples/*tex paultremblay@shell.sourceforge.net:/home/groups/g/ge/getfo/htdocs/context_xml'

        print command
        exit_status = os.system(command)




if __name__ == '__main__':
    update_obj = Update()
    update_obj.update_files()

