# Generates documentation for distribution package

all: $(html) $(text)

html: index.html spec.html thesis.html python.html texml.html install.html tour_simple.html tour_more.html tour_better.html tour_pdf.html tour_xslt.html tour_encoding.html tour_ascii.html

text: index.txt  spec.txt thesis.txt

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
	-xsltproc -o $@ $(word 2,$+) --nonet $<

quick.html: ../tests/data/quick/quick.texml ../tests/data/quick/quick.tex ../tests/data/quick/quick.html ../tests/data/quick/quick.xsl

index.txt: index.html
	links -dump $< >$@
	echo >>$@
	echo 'References' >>$@
	echo >>$@
	lynx --dump index.html | grep 'http://' | sed 's/^.*http/   http/' >>$@

%.txt: %.html
	links -dump $< >$@
