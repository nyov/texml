# Generates documentation for distribution package

# We use GetFO stylesheets
getfo=../../getfo/docs/xslt

all: index.txt spec.txt

clean:
	rm -f *~

%.html: %.xml $(getfo)/tohtml.xsl $(getfo)/tohtmlhtml.xsl
	xsltproc $(word 2,$+) $< | xsltproc -o $@ $(word 3,$+) -

index.txt: index.html
	-links -dump $< >$@
	echo >>$@
	echo 'References' >>$@
	echo >>$@
	lynx --dump index.html | grep 'http://' | sed 's/^.*http/   http/' >>$@

%.txt: %.html
	-links -dump $< >$@
