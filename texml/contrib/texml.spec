%define version 2.0.0
%define release alt2

%setup_python_module texml

Summary: TeXML: an XML syntax for TeX (LaTeX, ConTeXt)
Name: texml
Version: %version
Release: %release
Source: %modulename-%version.tar.gz
Packager: Oleg Parashchenko <olpa@altlinux.ru>
License: MIT
Group: Publishing
Url: http://getfo.org/texml/
BuildArch: noarch

# Automatically added by buildreq on Mon Jul 10 2006
BuildRequires: python-base python-dev python-modules-compiler python-modules-encodings python-modules-xml

%description
TeXML is an XML syntax for TeX. The processor transforms the TeXML
markup into the TeX markup, escaping special and out-of-encoding
characters. The intended audience is developers who automatically
generate [La]TeX or ConTeXt files.

%prep
%setup -q

%build
%__python setup.py build
               
%install
%__python setup.py install --root=%buildroot \
                          --optimize=2 \
                          --record=INSTALLED_FILES
# texml.1 somehow becomes texml.1.gz, INSTALLED_FILES becomes incorrect
cp INSTALLED_FILES INSTALLED_FILES.0 && /bin/sed 's/texml.1/texml.1.gz/' INSTALLED_FILES.0 >INSTALLED_FILES

%files -f INSTALLED_FILES

%changelog
* Tue Jul 11 2006 Oleg Parashchenko <olpa@altlinux.ru> 2.0.0-alt2
- Field "Packager" is added to spec-file.

* Mon Jul 10 2006 Oleg Parashchenko <olpa@altlinux.ru> 2.0.0-alt1
- Initial build.
