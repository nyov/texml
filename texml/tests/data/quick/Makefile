quick.dvi: quick.tex
	latex $<

quick.tex: quick.texml
	../../../bin/texml.py $< $@

quick.texml: quick.xsl quick.html
	xsltproc -o $@ $+

clean:
	rm -f *dvi *tex *texml *log *aux
