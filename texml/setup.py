import sys, os, commands
from distutils.core import setup
import shutil




def test_for_sax():
    try:
        import xml.sax
    except ImportError:
        sys.stderr.write('Please install the python pyxml modules\n')
        sys.stdout.write('You must have this module before you can install paxbac\n')
        sys.exit(1)

def get_dtd_location():
    """
    For now, this is not used

    """
    return
    return '/home/paul/Documents/data/dtds/'


def get_version():
    """
    I like to have one place where my version is stored. 
    For now, hard-code this.

    """
    return '1.25.devel'
    temp_module =  os.getcwd()
    sys.path.insert(0, temp_module)
    import texml.version
    vers_obj = texml.version.Version()
    version = vers_obj.get_version()
    return version

if 'build' in sys.argv:
    test_for_sax()

version = get_version()

# where you want to put the dtd
# dtd_location = get_dtd_location()

# where the dtd resides

# schemas_dir = os.path.join(os.getenv('paxbac_write_dir'), 'important_info', 'schemas')

setup(name="texml",
    version= version ,
    description="Convert XML to LaTeX or ConTeXt",
    long_description = """TeXML is an XML vocabulary for TeX. The processor transforms TeXML markup into the TeX markup, escaping special and out-of-encoding characters. The intended audience is developers who automatically generate TeX files.""",
    author="Oleg Paraschenko, Paul Tremblay",
    author_email="olpa@ http://uucode.com/",
    license = 'GNU GPL',
    url = 'http://getfo.sourceforge.net/texml/index.html',
    packages=['Texml'],
    scripts=['scripts/texml.py', 'scripts/texml_con],
    )

