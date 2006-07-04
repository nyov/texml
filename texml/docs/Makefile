# Generates documentation for distribution package

all: html

html: index.html spec.html thesis.html python.html texml.html install.html tour_simple.html tour_more.html tour_better.html tour_pdf.html tour_xslt.html tour_encoding.html tour_ascii.html

# ---------------------------------------------------------
# generation

distclean:
	rm -f *~ *.txt *.html

clean:
	rm -f *~

%.html: %.xml support/tohtml.xsl support/tohtmlhtml.xsl
	xsltproc --xinclude $(word 2,$+) $< | xsltproc -o $@ $(word 3,$+) -

tour_%.html: tour.xml support/tohtml.xsl support/tohtmlhtml.xsl
	xsltproc --xinclude --stringparam doc.id texml.tour.$* $(word 2,$+) $< | xsltproc -o $@ $(word 3,$+) -

texml.tmp: texml.1
	-man2html $< | tidy -asxml -numeric >$@

texml.xml: texml.tmp support/man.xsl
	-xsltproc -o $@ --nonet $(word 2,$+) $<

quick.html: ../tests/data/quick/quick.texml ../tests/data/quick/quick.tex ../tests/data/quick/quick.html ../tests/data/quick/quick.xsl

