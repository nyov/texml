all: quick.pdf

quick.pdf: quick.tex
	pdflatex $<

pyfiles := $(wildcard ../../../bin/*py)

quick.tex: quick.texml $(pyfiles)
	../../../bin/texml.py --width 60 $< $@

quick.texml: quick.xsl quick.html
	xsltproc -o $@ $+

clean:
	rm -f *log *aux *~
