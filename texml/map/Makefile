unimap.py: pymap.xsl unicode.xml
	xsltproc --novalid -o $@ $+

test: unimap.py
	python $<
