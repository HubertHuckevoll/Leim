#!/usr/bin/env php

<?php

define('LE', "\r\n"); // default Line Ending
define('IND1', "  ");  // default indent: 2 spaces
define('IND2', "    ");  // default indent: 4 spaces
define('IND3', "      ");  // default indent: 6 spaces
define('IND4', "        ");  // default indent: 8 spaces

class Leim
{
  public $filesToEncode = array('gif', 'png', 'jpg', 'webp');
  public $encFilesCache = array();
  public $files = array();
  public $outF = '';

  public function __construct($outF = 'index.php')
  {
    $this->outF = $outF;
  }

  public function setFilesToEncode($exts)
  {
    $this->filesToEncode = $exts;
  }

  public function add($root, $exts)
  {
    $ret = '';
    $di = new RecursiveDirectoryIterator($root);

    foreach (new RecursiveIteratorIterator($di) as $filename => $file)
    {
      $ext = strtolower($file->getExtension());
      if (
            (in_array($ext, $exts)) &&
            (!$file->isDir()) &&
            ($filename != '.') &&
            ($filename != '..') &&
            (basename($filename) != $this->outF) &&
            (basename($filename) != basename(__FILE__))
         )
      {
        $path = substr($file->getPath(), 2);
        $this->files[$ext][$path][] = $filename;
      }
    }

    return $ret;
  }

  public function openPHP()
  {
    $ret  = '';
    $ret .= '<?php'.LE;
    $ret .= LE;

    return $ret;
  }

  public function closePHP()
  {
    $ret  = '';
    $ret .= LE;
    $ret .= '?>';

    return $ret;
  }

  public function openRscClass()
  {
    $ret  = '';
    $ret .= 'namespace'.LE;
    $ret .= '{'.LE;
    $ret .= IND1.'class RSC'.LE;
    $ret .= IND1.'{'.LE;

    return $ret;
  }

  public function closeRscClass()
  {
    $ret  = '';
    $ret .= IND1.'}'.LE; // class
    $ret .= '}'.LE; // namespace

    return $ret;
  }

  public function openStyleMemberVar()
  {
    // open CSS array
    $ret  = '';
    $ret .= IND2.'public static $css = array('.LE;

    return $ret;
  }

  public function closeStyleMemberVar()
  {
    // close CSS array
    $ret  = '';
    $ret .= IND2.');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function file2DataURI($file)
  {
    if (isset($this->encFilesCache[$file]))
    {
      $ret = $this->encFilesCache[$file];
    }
    else
    {
      $mime = strtolower(mime_content_type($file)); // return mime type ala mimetype extension
      $content = base64_encode(file_get_contents($file));
      $ret = 'data:'.$mime.';base64,'.$content;
      $this->encFilesCache[$file] = $ret;
    }

    return $ret;
  }

  public function file2VarName($file)
  {
    return pathinfo($file, PATHINFO_FILENAME);
  }

  public function writeAssets()
  {
    $ret  = '';
    $ret .= IND2.'public static $assets = array('.LE;
    foreach($this->filesToEncode as $ext)
    {
      if (isset($this->files[$ext]))
      {
        foreach ($this->files[$ext] as $path => $files)
        {
          foreach($files as $file)
          {
            $varName = $this->file2VarName($file);
            $ret .= IND3.'\''.$varName.'\' => \''.$this->file2DataURI($file).'\','.LE;
          }
        }
      }
    }
    $ret .= IND2.');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function writeStyleVars()
  {
    $ret  = '';
    $ret .= IND3.'\'var\' => <<< \'CSSVAR\''.LE;
    $ret .= '--root'.LE;
    $ret .= '{'.LE;

    foreach($this->filesToEncode as $ext)
    {
      if (isset($this->files[$ext]))
      {
        foreach ($this->files[$ext] as $path => $files)
        {
          foreach($files as $file)
          {
            $varName = $this->file2VarName($file);
            $ret .= IND1.'--'.$varName.': '.$this->file2DataURI($file).';'.LE;
          }
        }
      }
    }

    $ret .= '}'.LE;
    $ret .= 'CSSVAR,'.LE;

    return $ret;
  }

  public function writeStyleFiles()
  {
    $ret = '';

    foreach($this->files['css'] as $path => $files)
    {
      if (count($this->files['css']) > 0)
      {
        foreach($files as $file)
        {
          $cont = trim(file_get_contents($file));
          $ret .= IND3.'\''.$this->file2VarName($file).'\' => <<< \'CSSFILE\''.LE;
          $ret .= $cont.LE;
          $ret .= 'CSSFILE,'.LE;
        }
      }
    }

    return $ret;
  }

  public function writeJsFiles()
  {
    $ret  = '';
    $ret .= IND2.'public static $js = array('.LE;
    foreach($this->files['js'] as $path => $files)
    {
      if (count($this->files['js']) > 0)
      {
        foreach ($files as $file)
        {
          $cont = trim(file_get_contents($file));
          $ret .= IND3.'\''.$this->file2VarName($file).'\' => <<< \'JSCODE\''.LE;
          $ret .= $cont.LE;
          $ret .= 'JSCODE,'.LE;
        }
      }
    }
    $ret .= IND2.');'.LE; // close CSS array

    return $ret;
  }

  public function writePHPFiles()
  {
    $ret = '';

    foreach($this->files['php'] as $path => $files)
    {
      if (count($this->files['php']) > 0)
      {
        foreach ($files as $file)
        {
          $cont = file_get_contents($file);
          preg_match_all("/<\?php(.*)\?>/ms", $cont, $match, PREG_SET_ORDER);
          $cont = $match[0][1];
          $cont = trim($cont);

          $ret .= $cont;
          $ret .= LE.LE;
        }
      }
    }

    return $ret;
  }

  public function run()
  {
    $ret  = '';

    $ret .= $this->openPHP();
    $ret .= $this->writePHPFiles();

    $ret .= $this->openRscClass();
    $ret .= $this->writeAssets();
    $ret .= $this->openStyleMemberVar();
    $ret .= $this->writeStyleVars();
    $ret .= $this->writeStyleFiles();
    $ret .= $this->closeStyleMemberVar();
    $ret .= $this->writeJsFiles();
    $ret .= $this->closeRscClass();

    $ret .= LE;
    $ret .= 'var_dump(RSC::$css);'.LE;
    $ret .= LE;

    $ret .= $this->closePHP();

    file_put_contents($this->outF, $ret);
  }

  public function log($str)
  {
    echo $str.LE;
  }

}

$l = new Leim();
$l->add('.', array('php', 'css', 'js'));
$l->add('./assets', array('gif', 'png'));
//$l->setFilesToEncode(array('png'));
$l->run();

?>