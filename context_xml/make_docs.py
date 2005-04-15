#!/usr/bin/env python
"""
Creates all the docs

"""
import os, sys, glob
import txt2xml.Text2xml
import xml2txt.xml_to_txt
import tempfile
import shutil
import xml2txt.xml_to_txt
import commands

class Validate:

    def __init__(self, the_doc):
        self.__doc = the_doc 
        pass

    def validate(self):
        not_valid = 0
        command = 'validate_tei_jing %s ' % self.__doc
        (exit_status, out_text) = commands.getstatusoutput(command)
        if out_text:
            not_valid = 1
            print 'Doc not a valid TEI doc'
            return 1, out_text
        else:
            print 'Document valid'
        command = 'xsltproc %s %s' % ('/home/paul/Documents/context/xslt/check_ana_element.xsl',
            self.__doc)
        (exit_status, out_text) = commands.getstatusoutput(command)
        if len(out_text):
            return 2, out_text

        return 0, 0

class MakeDocs:

    def __init__(self):
        self.__home_dir = os.path.dirname(sys.argv[0])

    def convert(self):
        # make xml files from examples
        self.change_ex_to_xml()
        # convert str documents
        str_files = self.get_str_files()
        xml_files = self.convert_str(str_files)
        for xml_file in xml_files:
            basename = os.path.basename(xml_file)
            new_path = os.path.join(self.__home_dir, 'xml', basename)
            os.rename(xml_file, new_path)

        # convert fragements to TEI
        result = self.convert_to_tei()
        tei_doc = result[0]
        self.validate_doc(tei_doc)
        self.make_html(tei_doc)
        # self.make_wiki(tei_doc)

    def convert_str(self, list_of_files):
        sys.stdout.write('Convering str files to XML...\n')
        convert_obj = txt2xml.Text2xml.TextToXml(processor='xsltproc-command')
        result = convert_obj.txt2xml(
            files_to_convert = list_of_files,
            ext = 'xml',
            command_line = 0,
            return_files = 1,
            namespace = 1,
            )
        sys.stdout.write('Done\n')
        return result

    def convert_to_tei(self):
        sys.stdout.write('Converting XML to TEI...\n')
        convert_obj = txt2xml.Text2xml.TextToXml(processor='xsltproc-command')
        list_of_files = [os.path.join(self.__home_dir,'xml', 'fragments.xml'),]
        outfile = os.path.join(self.__home_dir, 'xml', 'context_xml.xml')
        style_sheet = os.path.join(self.__home_dir, 'xslt', 'to_tei.xsl')
        list_of_stylesheets = [style_sheet ]

        result = convert_obj.xsl_convert(
            list_of_xml_files = list_of_files,
            list_of_xsl_stylesheets = list_of_stylesheets,
            out_file = outfile,
        )
        command = 'xmlformat.pl -i -f /usr/share/xmlformat/tei.txt %s ' % (result[0]) 
        os.system(command)
        
        sys.stdout.write('Done\n')
        return result

    def convert_str_(self, list_of_files, list_of_stylesheets):
        convert_obj = txt2xml.Text2xml.TextToXml(processor='xsltproc-command')
        result = convert_obj.convert_and_change(
            files_to_convert = list_of_files,
            list_of_xsl_stylesheets = list_of_stylesheets,
            ext = 'xml',
            xmlformat = 1,
        )
        return result


    def validate_doc(self, tei_doc):
        print '=========VALIDATING DOC==============='
        valid_obj = Validate(tei_doc)
        exit_status, out_text = valid_obj.validate()
        if exit_status:
            print 'Some problems with doc'
            print out_text
        print '======================================'

    def get_str_files(self):
        str_files = glob.glob(os.path.join(self.__home_dir, 'str', '*str'))
        return str_files

    def change_ex_to_xml(self):
        the_files = glob.glob(os.path.join(self.__home_dir,'examples', '*tex')) 
        for the_file in the_files:
            read_obj = open(the_file, 'r')
            file_name, ext = os.path.splitext(the_file)
            out_file = file_name + '.xml'
            write_obj = open(out_file, 'w')
            write_obj.write('<doc>\n')
            line_to_read = 1
            while line_to_read:
                line_to_read = read_obj.readline()
                line = line_to_read
                line = line.replace('<', '&lt;')
                line = line.replace('>', '&gt;')
                line = line.replace('&', '&amp;')
                write_obj.write(line)
            read_obj.close()
            write_obj.write('</doc>\n')
            write_obj.close()

    def make_wiki(self, main_file):
        out = tempfile.mktemp()

        convert_obj = txt2xml.Text2xml.TextToXml(processor='xsltproc-command')
        list_of_xsl_stylesheets = ['/home/paul/Documents/context/xslt/tei2wiki.xsl',]
        convert_obj.xsl_convert(
            list_of_xsl_stylesheets = list_of_xsl_stylesheets,
            list_of_xml_files = [main_file],
            out_file = out,
            )

        # I'm spliting the file into chunks, so out will just be junk
        try:
            os.remove(out)
        except OSError:
            pass

        wiki_dir = os.path.join(self.__home_dir, 'wiki' , '*xml')
        the_files = glob.glob(wiki_dir )

        # convert to actual text
        for in_file in the_files:
            filename, ext = os.path.splitext(in_file)
            out_file = filename + '.txt'
            txt2xml_obj = xml2txt.xml_to_txt.XmlToText(
                the_file = in_file, 
                output = out_file,
            )
            txt2xml_obj.translate()
            # get rid of smart quotes and so on
            convert_obj.utf2txt(list_of_files = [out_file])
            try:
                #os.remove(in_file)
                pass
            except OSError:
                pass




    def make_html(self, tei_doc):
        print 'Making HTML...\n'
        os.chdir(self.__home_dir)
        stylesheet = os.path.join(self.__home_dir, 'xslt', 'to_html.xsl')
        command = 'xsltproc %s %s > /dev/null' % (stylesheet, tei_doc)
        os.system(command)
        print 'Done'

if __name__ == '__main__':
    docs_obj = MakeDocs()
    docs_obj.convert()
