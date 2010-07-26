<?php
/**
 * TeX executor
 * @package texexec
 * @author Roman Domrachev 
 * @version 0.1, 12.07.2010
 * @since engine v.0.1
 * @copyright (c) 2010+ by getfo.org project
 */

/**
 * Remove the directory including all files inside.
 *
 * @param string $dir Directory for remove
 */
 function rmdir_force($dir) {
    $op_dir=opendir($dir);
    while ($file=readdir($op_dir)) {
        if($file != "." && $file != "..") {
            unlink ($dir."/".$file);
        }
    }
    closedir($op_dir);
    rmdir($dir);
 }

/**
 * Remove the directory of the project.
 *
 * @param string $proj_path Directory of the project
 */
 function delete_proj($proj_path) {
    if (file_exists($proj_path)) {
        rmdir_force($proj_path);
    }
 }

/**
 * Create the workspace of the project.
 *
 * This function creates project directory and copies TeX file to this directory.
 *
 * @param string $proj_path Directory of the project
 * @param string $fpath Path to TeX file
 * @param bool $force Flag for remove of existed directory
 * @return bool true = if there were'nt errors in process, false = else. 
 */
 function create_proj($proj_path, $fpath, $force) {

    if (file_exists($proj_path)) {
        if ($force) {
            rmdir_force($proj_path);
            mkdir($proj_path);
        } else {
            return false;
        }
    } else {
        mkdir($proj_path);
    }

    $fname = substr($fpath, strrpos($fpath,"/")+1);
    $nfpath = $proj_path . "/" . $fname;

    if (!copy($fpath, $nfpath)) {
        echo "File $file was'nt copied...\n";
        return false;
    }

    return true;
 }

/**
 * Detect OS Windows
 *
 * @return bool true = if it is OS Windows, false = else.
 */
 function is_winos() {
     return getenv("COMSPEC") ? true : false;
 }

/**
 * Generate the PDF file from TeX file.
 *
 * This procedure call TeX util for generation of the PDF file by the TeX file.
 *
 * @param string $tex_dir Directory of the TeX system in the OS.
 * @param string $tex_cmd Name of the TeX util (ex. pdflatex.exe)
 * @param string $proj_path Directory of the project
 * @param string $texfile Path to TeX file which you want to use as source. 
 * @return bool true = if process was successful, false = else.
 */
 function generatex($tex_dir, $tex_cmd, $proj_path, $texfile) {

    $curdir = getcwd();

    $command = $tex_cmd . " -interaction=batchmode -output-directory=" . $curdir . "/" . $proj_path . " " . $curdir . "/" . $proj_path ."/". $texfile;

    chdir($tex_dir);

    exec($command);

    chdir($curdir);

    // check that TeX process was successful.
    $logfile = str_replace(".tex",".log", $texfile);
    return file_exists($curdir . "/" . $proj_path ."/". $logfile);
 }

/**
 * Set TEXINPUTS environmental variable in the OS.
 *
 * @param string $path1, ... unlimited number of path to set TEXINPUTS variable (minimum one path).
 */
 function set_texinputs() {
    $all_args = func_get_args();
    if (!defined("PATH_SEPARATOR")) {
        define("PATH_SEPARATOR", getenv("COMSPEC") ? ";" : ":");
    }      
    array_unshift($all_args, "TEXINPUTS", PATH_SEPARATOR);
    set_envar($all_args);
 }

/**
 * Set OSFONTDIR environmental variable in the OS.
 *
 * @param string $path1, ... unlimited number of path to set TEXINPUTS variable (minimum one path).
 */
 function set_osfontdir() {
    $all_args = func_get_args();
    if (!defined("PATH_SEPARATOR")) {
        define("PATH_SEPARATOR", getenv("COMSPEC") ? ";" : ":");
    }      
    array_unshift($all_args, "OSFONTDIR", PATH_SEPARATOR);
    set_envar($all_args);
 }

/**
 * Set some environmental variable in the OS.
 *
 * @param array $args $args[0] - name of variable, $args[1] - the path separtor, $args[2], ... - unlimited number of path to set the variable.
 * (minimum one path)
 */
 function set_envar($args) {
    $env_name = $args[0];
    $path_sep = $args[1];
    unset($args[0]);
    unset($args[1]);
    $env_value = getenv($env_name);
    if (strlen(trim($env_value)) > 0) {
        $env_value = implode($path_sep, $args) . $path_sep . $env_value;
    } else {
        $env_value = implode($path_sep, $args);
    }
    putenv($env_name . "=". $env_value);
 }

/**
 * It is constant used when stream was'nt set
 */
 define("None", null);
/**
 * Re-usable class to parse LaTeX log
 * 
 * @package texexec
 */
 class texloginfo {
    var $need_return;
    var $warn_stream;
    var $err_stream;
    var $miss_stream;
    var $missed_seen;
     /**
      * Expected output streams are implementation of stream class or None
      * 
      * @param $warn_stream stream stream of warnings
      * @param $err_stream stream stream of errors
      * @param $miss_stream stream stream of misses
      */
    function __construct($warn_stream = None, $err_stream = None, $miss_stream = None) {
        $need_return = 0;
        if (None == $warn_stream) {    
            $this->warn_stream = new stream();
        } else {
            $this->warn_stream = $warn_stream;        
        }
        if (None == $err_stream) {    
            $this->err_stream = new stream();
        } else {
            $this->err_stream = $err_stream;        
        }
        if (None == $miss_stream) {    
            $this->miss_stream = new stream();        
        } else {
            $this->miss_stream = $miss_stream;        
        }
        $this->missed_seen = array();
    }   
    /**
     * Process TeX log data in the input stream
     * 
     * @param $in_stream stream input stream (ex. based on the file)
     */
    function parse_log_stream($in_stream) {
        $re_warning = new re("/(at lines \\d+\\-\\-\\d+|at line \\d+)$/");         
        $re_missed = new re("/^! LaTeX (Error|warning): File `([^']+)' not found.$/");
        //
        // Loop over the file strings
        //
        while ($l = $in_stream->nexto()) {
            //
            // Check for missed image
            //
            if (None != $this->miss_stream) {
                $matched = $re_missed->match($l);
                if ($matched) {
                    $file = $re_missed->group(2);
                    if (!array_search($file, $this->missed_seen)) {
                        $this->missed_seen[] = $file;    
                        $this->miss_stream->printo($file);
                    }
                }
            }
            //
            // Check if the line is a warning message
            //  
            if (None != $this->warn_stream) {
                if ($re_warning->search($l)) {
                    $this->warn_stream->printo($l);
                }
            }
            //
            // Check if re-run is expected
            //   
            if (strpos($l,"Rerun to")!==false && strpos($l, "LaTeX Warning")!==false) {
                $this->need_return = 1;
                if (None != $this->warn_stream) {
                    $this->warn_stream->printo($l);
                }
            }
            //
            // Check if the line is an error message
            //
            if (strlen($l) > 1 && '!' == $l{0} && ' ' == $l{1}) {
                // Actually, it might be a warning:
                // ! pdfTeX warning (ext4): destination with the same identifier...
                if (strpos($l, "warning")!==false) {
                    if (None != $this->warn_stream) {
                        $this->warn_stream->printo($l);
                    }    
                } else { // Print the error
                    if (None != $this->err_stream) {
                        $this->err_stream->printo($l);
                    }    
                }           
            }
        }
    }
    /**
     * Process TeX log file. If file doesn't exist, we consider that
     * there are no errors and warnings, and no re-run required.
     * 
     * @param $filename string Name of file
     */
    function parse_log_file($filename) {
        try {
            $in_stream = new stream($filename, "rt");    
        } catch (Exception $e) { // IOError
            echo "Exception: " . $e->getMessage();
            return;
        }
        $this->parse_log_stream($in_stream);
        $in_stream->closet();
    }
    /**
     * Return collected data on warnings. Close the stream.
     * 
     */
    function get_warnings() {
        $ws = &$this->warn_stream; 
        $s = $ws->getvalue();
        $this->warn_stream->closet();
        $this->warn_stream = None;
        return $s;    
    }
    /**
     * Return collected data on errors. Close the stream.
     * 
     */
    function get_errors() {
        $es = &$this->err_stream; 
        $s = $es->getvalue();
        $this->err_stream->closet();
        $this->err_stream = None;
        return $s;    
    }
    /**
     * Return collected data on misses. Close the stream.
     * 
     */
    function get_missed() {
        $ms = &$this->miss_stream;
        $s = $ms->getvalue();
        $this->miss_stream->closet();
        $this->miss_stream = None;
        return $s;    
    }
    /**
     * Return flag of necessity of rerun.
     * 
     */
    function get_rerun() {
        return $this->need_return;
    }

 }

/**
 * Class of regular expressions 
 * 
 * @package texexec
 */
 class re {
    var $regexp;
    var $matches;
    function __construct($regexp) {
        $this->regexp = $regexp; 
    }
    function match($str) {
        return preg_match($this->regexp, trim($str), $this->matches);
    }        
    function group($n) {
        return $this->matches[$n];
    }
    function search($str) {
        return $this->match($str);
    }

 }
/**
 * Class of stream
 * 
 * @package texexec
 */
 class stream {
     var $file;
     var $is_file;
     function __construct($filename = false, $mode = false) {
          if (!($filename==false && $mode==false)) {
              $this->file = fopen($filename, $mode);
              $this->is_file = true;
          } else {
              $this->is_file = false;
          }
     }
     function nexto() {
        if ($this->is_file) {
            if (!feof($this->file)) {
                return fgets($this->file);
            } else {
                return false;
            }
        } else {
            return false;
        }
     }
     function printo($str) {
        if ($this->is_file) {
            fwrite($this->file, trim($str));
        } else {
            $this->file .= trim($str) . "\n";
        }
     }
     function getvalue() {
        if ($this->is_file) {
            return file_get_contents($this->file);
        } else {
            return $this->file;
        }
     }
     function closet() {
        if ($this->is_file) {
            fclose($this->file);
        } else {
            unset($this->file);
            $this->file = null;
        }
     }
 }
?>
