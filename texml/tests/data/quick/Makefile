all: quick.pdf

quick.pdf: quick.tex
	pdflatex $<

quick.tex: quick.texml
	../../../bin/texml.py --width 60 $< $@

quick.texml: quick.xsl quick.html
	xsltproc -o $@ $+

clean:
	rm -f *log *aux
