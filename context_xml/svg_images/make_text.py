#!/usr/bin/env python
x_start = 620
y_start = 100
line_length = 32
first_line_length = 0
line_space = 15
font_size = 10
font_family = 'Bitstream Charter'
max_num_lines = 18
out_file = '/home/paul/Documents/context/svg_images/first_even_odd_text3.xml'
# host_file = '/home/paul/Documents/context/svg_images/simple1.svg'
#
# will force justfity text to length
text_length = 215
text_length = None


in_file = '/home/paul/Documents/context/text/thoreau.txt'
in_file = '/home/paul/Documents/context/text/temp.txt'
start_element = '<text x="150.0" y="185.0" font-size="8" font-family="Bitstream Charter"  fill="#000000" fill-opacity="1.0" drawswf:effect="0" textLength="300">'


import commands, sys, os
import xml2txt.format_txt

class Convert:

    def __init__(self):
        pass

    def format_string(self, raw_string):
        format_obj = xml2txt.format_txt.FormatText()
        if '\n' in raw_string:
            print 'WOOOPS'
            sys.exit(1)
        if first_line_length:
            first_formatted_string, rest = format_obj.break_lines(
                my_string = raw_string, 
                length = first_line_length,
                num_of_breaks = 1
                )
            rest_string, rest = format_obj.break_lines(
                my_string = rest, 
                length = line_length,
                )
            formatted_string = first_formatted_string + '\n' + rest_string
        else:
            formatted_string, rest = format_obj.break_lines(
                my_string = raw_string, 
                length = line_length,
                )
        return formatted_string.split('\n')

    def get_text_length(self):
        if not text_length:
            return 4 * line_length
        else:
            return text_length

    def convert(self, the_file):
        read_obj = open(the_file, 'r')
        all_lines = read_obj.readlines()
        read_obj.close()
        raw_string = ' '.join(all_lines)
        raw_string = raw_string.replace('\n','')
        list_of_lines = self.format_string(raw_string)
        text_length = self.get_text_length()
        write_obj = open(out_file, 'w')
        counter = 0
        for line in list_of_lines:
            changed_line = line
            changed_line = changed_line.replace('<', '&lt;')
            changed_line = changed_line.replace('>', '&gt;')
            changed_line = changed_line.replace('&', '&amp;')
            write_obj.write("""<text
x="%s" 
y="%s"
font-size="%s"
font-family="%s"
fill="#000000" fill-opacity="1.0"
textLength="%s"
>
%s
</text>
"""        % (
                str(x_start), str(y_start + counter * line_space ), str(font_size), 
                font_family, str(text_length),
                changed_line 
             )
            )
        
            counter += 1
            if counter > max_num_lines:
                break
        write_obj.close()
        return
        if host_file:
            dirname = os.path.dirname(host_file)
            new_path = os.path.join(dirname, 'expand_temp.svg')
            style_sheet = '/home/paul/Documents/data/xsl_style_sheets/examples/complete_copy.xsl'
            command = 'xsltproc %s %s > %s' % (style_sheet, host_file, new_path) 
            os.system(command)



if __name__ == '__main__':
    convert_obj = Convert()
    convert_obj.convert(in_file)
