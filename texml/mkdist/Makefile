# Builds a TeXML distribution
# $Id: Makefile,v 1.2 2004-03-19 13:52:30 olpa Exp $
txtext = .txt
version=0.9.beta

outdir=../dist
distdir=$(outdir)/texml.$(version)

all: init copy pack

bin_files = $(addprefix bin/,handler.py texml.py unimap.py specmap.py  texmlwr.py)
docs_files = $(addprefix docs/,index.html spec.html index$(txtext) spec$(txtext)) readme$(txtext) license$(txtext)
good_tests_files := $(wildcard ../tests/data/*xml) $(wildcard ../tests/data/*out)
fail_tests_files := $(wildcard ../tests/faildata/*xml) $(wildcard ../tests/faildata/*grep)
tests_files = $(patsubst ../%,%,$(good_tests_files) $(fail_tests_files)) tests/Makefile tests/readme$(txtext)

out_files = $(addprefix $(distdir)/,$(bin_files) $(docs_files) $(tests_files))

init:
	mkdir -p $(distdir)/bin $(distdir)/docs $(distdir)/tests/data $(distdir)/tests/faildata

copy: init $(out_files)

$(distdir)/%: ../%
	cp $< $@

$(distdir)/docs/%: ../docs/%
	cp  $< $@

$(distdir)/readme$(txtext): ../docs/distfiles/readme
	cat $< | sed 's/__VERSION__/$(version)/g' >$@

$(distdir)/license$(txtext): ../docs/distfiles/license
	cp $< $@

$(distdir)/tests/readme$(txtext): ../tests/readme
	cp $< $@

x:
	echo $(distdir)/readme$(txt)
