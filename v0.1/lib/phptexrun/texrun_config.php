<?php
/**
 * TeX executor
 * @package texexec
 * @author Roman Domrachev 
 * @version 0.1, 12.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by by getfo.org project
 */

/**
 * TeXrun needs a temporary directory to compile a TeX file.
 * These directories are created inside texrun_projects_dir.
 * Default:
 * Web-server mode: the directory "project" near the web documents
 * root directory (usually named "public_html").
 * Command-line mode: the directory "projects"
 *
 */
if ('cli' != PHP_SAPI) {
  define('texrun_projects_dir', dirname($_SERVER['DOCUMENT_ROOT']).'/texrun_projects');
} else {
  define('texrun_projects_dir', 'projects');
}

/**
 * Command line to run tex.
 * $EXE is changed to '.exe' under Windows and ignored otherwise.
 * $PATH is the path to a non-standard tex location, if set.
 *   More precisely, it is '$texrun_tex_home/bin'.
 * $TEXFILE is the name of the TeX file.
 */
define('texrun_cmdline', '"$PATHxelatex$EXE" -no-shell-escape -interaction=batchmode $TEXFILE');

/**
 * If TeX is installed into a non-standard location,
 * "texrun_texhome" points to the root directory of the installation.
 * No slash at the end.
 * The TeX executable is expected to be
 * "texrun_texhome/bin/xelatex" or "texrun_texhome/bin/xelatex.exe"
 */
define("texrun_binpath", 'I:/nonsort/programs/MiKTeX/miktex/bin');
//define("texrun_binpath", 'D:/programs/MiKTeX 2.8/miktex');
//define('texrun_binpath', '/opt/texlive/dvd/bin/i386-linux');

/**
 * If TeX is installed to a non-standard location, it might need
 * help to find its runtime files. Depending on how it is installed,
 * the main possibilities are:
 * - no need to point to the runtime files
 * - the environment variable TEXMFCNF is enough
 * - one needs a set of variables: TEXMFMAIN TEXMFDIST TEXMFLOCAL
 *   TEXMFSYSVAR TEXMFSYSCONFIG TEXMFCNF
 * To get the value of the variables: make sure your TeX installation
 * does work in command line. Then get the values using:
 * kpsewhich -expand-var variable
 */
$texrun_extraenv = null;
//$texrun_extraenv = array('TEXMFCNF' => '/opt/texlive/texmf-var/web2c');
?>
