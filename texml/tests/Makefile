# $Id: Makefile,v 1.1 2004-03-15 12:52:24 olpa Exp $

all: tests

xml_tests:=$(notdir $(wildcard data/*xml))
test_stamps=$(addprefix tmp/data/,$(xml_tests:.xml=.out.stamp))

tests: $(test_stamps)

.SECONDARY:

init=tmp/data/.stamp tmp/faildata/.stamp

tmp/data/.stamp tmp/faildata/.stamp:
	mkdir -p $(dir $@)
	touch $@

tmp/data/%.out: data/%.xml ../bin/texml.py ../bin/*py $(init)
	python $(word 2,$+) $< $@

%.stamp: % FORCE
	@diff -qs $* data/$(notdir $*)
	@touch $@

FORCE:


