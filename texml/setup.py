import sys
from distutils.core import setup
import os.path
import re

def test_for_sax():
    try:
        import xml.sax
    except ImportError:
        sys.stderr.write('Please install the python pyxml modules\n')
        sys.stdout.write('You must have this module before you can install texml\n')
        sys.exit(1)

def get_dtd_location():
    """
    For now, this is not used

    """
    return
    return '/home/paul/Documents/data/dtds/'


def get_version():
    # Take the version from "scripts/texml.py"
    version    = None
    version_re = re.compile('^VERSION[^"]*"([^"]+)"; # GREPVERSION')
    f = open(os.path.join('scripts', 'texml.py'))
    for line in f:
      match = version_re.search(line)
      if match:
        version = match.group(1)
        break
    f.close()
    if None == version:
      raise "Can't find version"
    return version

if 'build' in sys.argv:
    test_for_sax()

version = get_version()

setup(name="texml",
    version= version ,
    description="Convert XML to LaTeX or ConTeXt",
    long_description = """TeXML is an XML vocabulary for TeX. The processor transforms TeXML markup into the TeX markup, escaping special and out-of-encoding characters. The intended audience is developers who automatically generate TeX files.""",
    author="Oleg Paraschenko, Paul Tremblay",
    author_email="olpa@ http://uucode.com/",
    license = 'GNU GPL',
    url = 'http://getfo.sourceforge.net/texml/index.html',
    packages=['Texml'],
    scripts=['scripts/texml.py', 'scripts/texml_con'],
    )

