# Builds a TeXML distribution
# $Id: Makefile,v 1.15 2005-03-23 20:17:50 olpa Exp $
version:=$(shell grep GREPVERSION ../scripts/texml.py | sed 's/";.*$$//' | sed 's/^.*"//')

txtext = .txt
outdir=../dist
texmlname=texml.$(version)
distdir=$(outdir)/$(texmlname)

all: init copy pack

bin_files = $(addprefix scripts/,texml.py texml_local) $(addprefix Texml/,handler.py unimap.py specmap.py texmlwr.py __init__.py)
docs_files = $(addprefix docs/,index.html spec.html quick.html thesis.html index$(txtext) spec$(txtext) quick$(txtext) thesis$(txtext)) readme$(txtext) license$(txtext) ChangeLog$(txtext)
quick_test_files := $(addprefix ../tests/data/quick/quick, .texml .tex .html .xsl .pdf .bat -pdf.bat)
good_tests_files := $(wildcard ../tests/data/*xml) $(wildcard ../tests/data/*out) $(quick_test_files)
fail_tests_files := $(wildcard ../tests/faildata/*xml) $(wildcard ../tests/faildata/*grep)
tests_files = $(patsubst ../%,%,$(good_tests_files) $(fail_tests_files)) tests/Makefile tests/readme$(txtext)
dtd_files = dtd/texml.dtd
more_root_files=MANIFEST.in setup.py

out_files = $(addprefix $(distdir)/,$(bin_files) $(docs_files) $(tests_files) $(dtd_files) $(more_root_files))

init:
	mkdir -p $(distdir)/Texml $(distdir)/scripts $(distdir)/docs $(distdir)/tests/data/quick $(distdir)/tests/faildata $(distdir)/dtd

clean:
	cd $(outdir) && rm -rf texml.$(version)*  # Not $(texmlname)*: imagine mistype

pack: copy
	cd $(outdir) && tar zcf $(texmlname)-unix.tar.gz $(texmlname)
	@# First pack text files with CRLF conversion, then binary files
	cd $(outdir) && rm -f $(texmlname)-windows.zip && zip -q -r -l $(texmlname)-windows.zip $(texmlname) -x '*pdf' && zip -q -r $(texmlname)-windows.zip $(texmlname) -i '*pdf'

copy: init $(out_files)

$(distdir)/%: ../%
	cp $< $@

$(distdir)/docs/%: ../docs/%
	cp  $< $@

$(distdir)/docs/%: ../docs/%.txt
	cp  $< $@

$(distdir)/readme$(txtext): ../docs/distfiles/readme
	cat $< | sed 's/__VERSION__/$(version)/g' >$@

$(distdir)/license$(txtext): ../docs/distfiles/license
	cp $< $@

$(distdir)/ChangeLog$(txtext): ../docs/distfiles/ChangeLog
	cp $< $@

$(distdir)/tests/readme$(txtext): ../tests/readme
	cp $< $@

