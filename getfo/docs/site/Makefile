xml_files = index.xml objectives.xml workplan.xml texml/index.xml texml/spec.xml texml/thesis.xml texml/quick.xml
html_files := $(xml_files:.xml=.html)
texml_docs = ../../../texml/docs

all: $(html_files)

html_deps = inc/menu.xml inc/footer.xml xslt/site.xsl

%.html: %.xml $(html_deps)
	xsltproc -o $@ xslt/site.xsl $<

texml/spec.xml: $(texml_docs)/spec.xml ../xslt/tohtml.xsl
	xsltproc -o $@ $(word 2,$+) $<

texml/index.xml: $(texml_docs)/index.xml ../xslt/tohtml.xsl
	xsltproc -o $@ $(word 2,$+) $<

texml/thesis.xml: $(texml_docs)/thesis.xml ../xslt/tohtml.xsl
	xsltproc -o $@ $(word 2,$+) $<

texml/quick.xml: $(texml_docs)/quick.xml ../xslt/tohtml.xsl
	xsltproc -o $@ --xinclude $(word 2,$+) $<

clean:
	rm -f *~ */*~
