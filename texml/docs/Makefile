# Generates documentation for distribution package

# We use GetFO stylesheets
getfo=../../getfo/docs/xslt

all: index.txt spec.txt

clean:
	rm -f *~

%.html: %.xml $(getfo)/tohtml.xsl $(getfo)/tohtmlhtml.xsl
	xsltproc $(word 2,$+) $< | xsltproc -o $@ $(word 3,$+) -

%.txt: %.html
	-links -dump $< >$@
