# Builds a TeXML distribution
# $Id: Makefile,v 1.12 2004-06-28 13:12:04 olpa Exp $
#version=1.00
version:=$(shell grep GREPVERSION ../bin/texml.py | sed 's/";.*$$//' | sed 's/^.*"//')

txtext = .txt
outdir=../dist
texmlname=texml.$(version)
distdir=$(outdir)/$(texmlname)

all: init copy pack

bin_files = $(addprefix bin/,handler.py texml.py unimap.py specmap.py  texmlwr.py)
docs_files = $(addprefix docs/,index.html spec.html quick.html index$(txtext) spec$(txtext) quick$(txtext)) readme$(txtext) license$(txtext) ChangeLog$(txtext)
quick_test_files := $(addprefix ../tests/data/quick/quick, .texml .tex .html .xsl .pdf .bat -pdf.bat)
good_tests_files := $(wildcard ../tests/data/*xml) $(wildcard ../tests/data/*out) $(quick_test_files)
fail_tests_files := $(wildcard ../tests/faildata/*xml) $(wildcard ../tests/faildata/*grep)
tests_files = $(patsubst ../%,%,$(good_tests_files) $(fail_tests_files)) tests/Makefile tests/readme$(txtext)
dtd_files = dtd/texml.dtd

out_files = $(addprefix $(distdir)/,$(bin_files) $(docs_files) $(tests_files) $(dtd_files))

init:
	mkdir -p $(distdir)/bin $(distdir)/docs $(distdir)/tests/data/quick $(distdir)/tests/faildata $(distdir)/dtd

clean:
	cd $(outdir) && rm -rf texml.$(version)*  # Not $(texmlname)*: imagine mistype

pack: copy
	cd $(outdir) && tar zcf $(texmlname).tar.gz $(texmlname)
	cd $(outdir) && rm -f $(texmlname).zip && zip -q -r -l $(texmlname).zip $(texmlname)

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

