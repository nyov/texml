xml_files = index.xml
html_files := $(xml_files:.xml=.html)

all: $(html_files)

%.html: %.xml
	xsltproc -o $@ xslt/site.xsl $<

clean:
	rm -f *~ */*~
