<?php

    define("phpunit", "PHPUnit");

    chdir(dirname( __FILE__));
    chdir("../..");
    $workdir = getcwd();

    define("phpcode", $workdir . "/" . "php");
    define("work_dir", $workdir);
?>
