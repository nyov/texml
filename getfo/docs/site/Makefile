xml_files = index.xml
html_files := $(xml_files:.xml=.html)

all: $(html_files)

html_deps = inc/menu.xml inc/footer.xml xslt/site.xsl

%.html: %.xml $(html_deps)
	xsltproc -o $@ xslt/site.xsl $<

clean:
	rm -f *~ */*~
