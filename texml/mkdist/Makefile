# Builds a TeXML distribution
# $Id: Makefile,v 1.1 2004-03-19 12:48:25 olpa Exp $
txtext = .txt
version=0.9.beta

outdir=../dist
distdir=$(outdir)/texml.$(version)

all: init copy pack

bin_files = $(addprefix bin/,handler.py texml.py unimap.py specmap.py  texmlwr.py)
docs_files = $(addprefix docs/,readme.html spec.html readme$(txtext) spec$(txtext)) readme$(txtext) license$(txtext)
good_tests_files := $(wildcard ../tests/data/*xml) $(wildcard ../tests/data/*out)
fail_tests_files := $(wildcard ../tests/faildata/*xml) $(wildcard ../tests/faildata/*grep)
tests_files = $(patsubst ../%,%,$(good_tests_files) $(fail_tests_files))

out_files = $(addprefix $(distdir)/,$(bin_files) $(docs_files) $(tests_files))

init:
	mkdir -p $(distdir)/bin $(distdir)/docs $(distdir)/tests/data $(distdir)/tests/faildata

copy: init $(out_files)

$(distdir)/%: ../%
	cp $< $@

$(distdir)/docs/readme.%: ../docs/index.%
	cp  $< $@

$(distdir)/docs/%: ../docs/%
	cp  $< $@

x:
	echo $(out_files)
