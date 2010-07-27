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
 * It is set property of PHP for compatibility with PHP 4
*/
 ini_set('zend.ze1_compatibility_mode', 'On');

 require "texexec_lib.php";
 require "config.php";

 function main() {

    $fname = "hello.tex";

    $fpath = "examples" . DIRECTORY_SEPARATOR . "tex" . DIRECTORY_SEPARATOR . $fname; 

    // First project
    echo "First project\n";

    $proj_path = projects_dir . DIRECTORY_SEPARATOR . "project1"; 

    create_proj($proj_path, $fpath, true);
    
    $tex_cmd = is_winos() ? "pdflatex.exe" : "pdflatex";
 
    if (generatex(tex_dir, $tex_cmd, $proj_path, $fname)) {
        // Here you can use texloginfo for analysis log-file
        // require_once("texexec_lib.php");
        echo "generation was successful\n";
    } else {
        echo "unfortunately generation was not done\n";
    }

    // Second project
    echo "Second project\n";

    $proj_path = projects_dir . DIRECTORY_SEPARATOR . "project2"; 

    create_proj($proj_path, $fpath, true);

    delete_proj($proj_path);

    // Third project
    echo "Third project\n";

    $fname = "test.tex";

    $lname = str_replace(".tex", ".log", $fname);

    $fpath = "tests" . DIRECTORY_SEPARATOR . "samples". DIRECTORY_SEPARATOR ."tex" . DIRECTORY_SEPARATOR . $fname; 

    $proj_path = projects_dir . DIRECTORY_SEPARATOR . "project3"; 

    create_proj($proj_path, $fpath, true);

    $tex_cmd = is_winos() ? "pdflatex.exe" : "pdflatex";
 
    if (generatex(tex_dir, $tex_cmd, $proj_path, $fname)) {
        // Here you can use texloginfo for analysis log-file
        // it need - require_once("texexec_lib.php");
        $tli = new texloginfo();
        $tli->parse_log_file($proj_path . DIRECTORY_SEPARATOR . $lname);
        echo $tli->get_warnings() . "\n";
        //echo $tli->get_errors() . "\n";
        //echo $tli->get_missed() . "\n";
        //echo $tli->get_rerun() . "\n";
        // see API for texloginfo class
        echo "generation was successful\n";
    } else {
        echo "unfortunately generation was not done\n";
    }

    // Fourth project
    echo "Fourth project\n";

    $fname = "complex.tex";

    $fpath = "examples" . DIRECTORY_SEPARATOR . "tex" . DIRECTORY_SEPARATOR . $fname; 

    $proj_path = projects_dir . DIRECTORY_SEPARATOR . "project4"; 

    create_proj($proj_path, $fpath, true);

    $tex_cmd = is_winos() ? "pdflatex.exe" : "pdflatex";

    $include_dir = is_winos() ? "" : ":";
    $include_dir .= getcwd() . DIRECTORY_SEPARATOR . "include";

    set_texinputs($include_dir);

    if (generatex(tex_dir, $tex_cmd, $proj_path, $fname)) {
        // Here you can use texloginfo for analysis log-file
        // require_once("texexec_lib.php");
        echo "generation was successful\n";
    } else {
        echo "unfortunately generation was not done\n";
    }

    // Other exaples
    echo "Other examples\n";
    set_texinputs("c:\\", "d:\\somepath\\somesub");
    echo "TEXINPUTS=". getenv("TEXINPUTS") . "\n";
    set_texinputs("e:\\");
    echo "TEXINPUTS=". getenv("TEXINPUTS") . "\n";
    set_osfontdir("f:\\fonts");
    echo "OSFONTDIR=". getenv("OSFONTDIR") . "\n";
  
 }

 main();

?>