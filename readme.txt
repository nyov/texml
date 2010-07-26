TeX Exector
-----------

TeX Exector is a library designed to generate the PDF file from the input TeX file. It is PHP implementation.

------------------------

release date: 14-07-2010

------------------------

Installing
----------

File ./php/texexec_lib.php contains all code of the library. You need include it file in the PHP code 
(ex. "require 'texexec_lib.php';") that to use it. 

Tutorial
--------

You can call functions and instance classes defined in the library. All functions and classes of the library are documented 
in the directory ./doc. A usage example of the library is given in the script ./php/runner.php which also tests 
that the library is working correctly. 

  This distribution contains the following directories and files:

  doc\               - API documentaion.
  examples\tex\*.tex - TeX files for examples.
  include\*.*        - Files for complex.tex example that include it from TeX file.   
  php\               - PHP for the library.
    config.php       - Configure file for the runner.
    runner.php       - The runner which shows as the library to work.
    texexec_lib.php  - The code of the library.
  projects           - Directory for projects (defined in .\php\config.php).
  readme.txt         - This file.
  build.xml          - Build file for ANT tool.

---------------
End of document