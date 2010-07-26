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
 * It is your directory for projects  
 */
 define("projects_dir", "projects");
/**
 * It is path to TeX system in your OS (There where to place exe files)
 */
 define("tex_dir", is_winos() ? 'D:/programs/MiKTeX 2.8/miktex/bin' : '/usr/bin');
?>
