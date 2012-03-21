<?php
//
// TeX runner
// (c) pdfscript project
//
if (!defined('PATH_SEPARATOR')) {
  define('PATH_SEPARATOR', ('\\' == DIRECTORY_SEPARATOR) ? ';' : ':');
}

//
// TeX string escape
//
$texrun_replace_map = array(
  '\\'  =>  '\\textbackslash{}',
  '{'   =>  '\\{',
  '}'   =>  '\\}',
  '$'   =>  '\\textdollar{}',
  '&'   =>  '\\&',
  '#'   =>  '\\#',
  '^'   =>  '\\^{}',
  '_'   =>  '\\_',
  '~'   =>  '\\textasciitilde{}',
  '%'   =>  '\\%',
  '|'   =>  '\\textbar{}',
  '<'   =>  '\\textless{}',
  '>'   =>  '\\textgreater{}'
);

function texrun_escape($s) {
  global $texrun_replace_map;
  return strtr($s, $texrun_replace_map);
}

//
// Helper: join paths
//
function texrun_path_join($path1, $path2) {
  if ('' != $path2) {
    if (DIRECTORY_SEPARATOR == $path2[0]) { // do not care about windows
      return $path2;
    }
  }
  $a1 = explode(DIRECTORY_SEPARATOR, $path1);
  $a2 = explode(DIRECTORY_SEPARATOR, $path2);
  return implode(DIRECTORY_SEPARATOR, array_merge($a1, $a2));
}

//
// Helper: prepend path to an environment variable
//
function texrun_addenv($k, $v) {
  $s = getenv('PATH');
  if (false === $s) {
    $s = $k . '=' . $v;
  } else {
    $s = $k . '=' . $v . PATH_SEPARATOR . $s;
  }
  putenv($s);
}

//
// ========================================================
//
class tex_project {

  //
  // Initialization
  //
  function init_new() {
    $this->proj_path = tempnam(texrun_projects_dir, 'phptex');
    unlink($this->proj_path);
    return mkdir($this->proj_path);
  }

  function init_new_with_file($fpath) {
    $this->init_new(0);
    $this->base_name = basename($fpath, '.tex');
    if (! $this->init_new()) {
      return false;
    }
    $new_fpath = texrun_path_join($this->proj_path, basename($fpath));
    return copy($fpath, $new_fpath);
  }

  //
  // At which moment to create the TeX-file
  // * before generating TeX-code, or
  // * after?
  // I prefer before: if there is something wrong with file creation,
  // it is better to signal the error immediately, without
  // running potentially big TeX-code generation.
  //
  function start_output_to_file($basename) {
    $fname = texrun_path_join($this->proj_path, $basename);
    $this->base_name = basename($basename, '.tex');
    if ($this->h_tex = fopen($fname, 'w')) {
      ob_start();
      return true;
    } else {
      return false;
    }
  }

  function finish_output() {
    $s = ob_get_contents();
    ob_end_clean();
    if (strlen($s) != fwrite($this->h_tex, $s)) {
      return false;
    }
    return fclose($this->h_tex);
  }

  //
  // Information functions
  //
  function get_pdf_file_name() {
    return texrun_path_join($this->proj_path, $this->base_name.'.pdf');
  }

  function get_log_file_name() {
    return texrun_path_join($this->proj_path, $this->base_name.'.log');
  }

  function get_project_dir() {
    return $this->proj_path;
  }

  //
  // The main goal: run TeX.
  //
  function run_tex($inc_dir=null) {
    global $texrun_extraenv;
    if (defined('texrun_binpath')) {
      texrun_addenv('PATH', texrun_binpath);
    }
    if (isset($texrun_extraenv)) {
      foreach ($texrun_extraenv as $k=>$v) {
        putenv("$k=$v");
      }
    }
    if ($inc_dir != null) {
      $inc_dir = PATH_SEPARATOR . $inc_dir; // current directory
      texrun_addenv('TEXINPUTS', $inc_dir);
      texrun_addenv('OSFONTDIR', $inc_dir);
    }
    $command = $this->calculate_command_line();
    $curdir  = getcwd();
    chdir($this->proj_path);
    exec($command, $output, $return_var);
    chdir($curdir);
    return $return_var;
  }

  //
  // Helper function: calculate command line
  //
  function calculate_command_line() {
    $command = texrun_cmdline;
    if (defined('texrun_binpath')) {
      $path_with_slash =  texrun_path_join(texrun_binpath, '');
      $command = str_replace('$PATH', $path_with_slash, $command);
    } else {
      $command = str_replace('$PATH', '', $command);
    }
    if ('\\' == DIRECTORY_SEPARATOR) {
      $command = str_replace('$EXE', '.exe', $command);
    } else {
      $command = str_replace('$EXE', '', $command);
    }
    $command = str_replace('$TEXFILE', $this->base_name.'.tex', $command);
    return $command;
  }

  //
  // PDF to browser in a default way
  //
  function pdf_to_browser() {
    $pdf_file = $this->get_pdf_file_name();
    $size = filesize($pdf_file);
    if (false === $size) {
      return false;
    }
    $name = rawurldecode(basename($pdf_file));
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-Length: " . $size);
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: private');
    header('Pragma: private');
    header('Expires: Wed, 23 Nov 2011 01:00:00 GMT');
    return readfile($pdf_file);
  }

  //
  // Clean up
  //
  function delete_project() {
    $dir = $this->proj_path;
    foreach (scandir($dir) as $fname) {
      if (('.' !=  $fname) and ('..' != $fname)) {
        $fname = $dir . DIRECTORY_SEPARATOR . $fname;
        unlink($fname);
      }
    }
    return rmdir($dir);
  }
}

?>
