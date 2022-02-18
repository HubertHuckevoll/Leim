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
  public $mainF = '';
  public $outF = '';

  /**
   * Input
   */
  public function __construct($mainF = 'main.php', $outF = 'index.php')
  {
    $this->mainF = $mainF;
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
            (basename($filename) != $this->mainF) &&
            (basename($filename) != basename(__FILE__))
         )
      {
        $path = substr($file->getPath(), 2);
        $this->files[$ext][$path][] = $filename;
      }
    }

    return $ret;
  }

  /**
   * Processing and helpers
   */
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

  public function openRootNamespace()
  {
    $ret  = '';
    $ret .= 'namespace'.LE;
    $ret .= '{'.LE;

    return $ret;
  }

  public function openRscClass()
  {
    $ret  = '';
    $ret .= IND1.'class RSC'.LE;
    $ret .= IND1.'{'.LE;

    return $ret;
  }

  public function closeRscClass()
  {
    $ret  = '';
    $ret .= IND1.'}'.LE; // class
    $ret .= LE;

    return $ret;
  }

  public function closeRootNamespace()
  {
    $ret  = '';
    $ret .= '}'.LE; // namespace
    $ret .= LE;

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
      $content = base64_encode($this->readFile($file));
      $ret = 'data:'.$mime.';base64,'.$content;
      $this->encFilesCache[$file] = $ret;
    }

    return $ret;
  }

  public function file2VarName($file)
  {
    return pathinfo($file, PATHINFO_FILENAME);
  }

  public function readFile($file)
  {
    $ret = '';

    if (file_exists($file))
    {
      $ret = trim(file_get_contents($file));
    }

    return $ret;
  }

  public function readPHPFile($file)
  {
    $ret = '';

    $cont = $this->readFile($file);
    if ($cont != '');
    {
      preg_match_all("/<\?php(.*)\?>/ms", $cont, $match, PREG_SET_ORDER);
      $cont = $match[0][1];
      //$cont = trim($cont);

      $ret .= $cont.LE;
      $ret .= LE;
    }

    return $ret;
  }

  public function log($str)
  {
    echo $str.LE;
  }

  public function walkFiles($ext, $workerFunc)
  {
    $ret = '';

    if (isset($this->files[$ext]))
    {
      foreach ($this->files[$ext] as $path => $files)
      {
        if (count($files) > 0)
        {
          foreach($files as $file)
          {
            $ret .= $workerFunc($file);
          }
        }
      }
    }

    return $ret;
  }

  /**
   * Output
   */
  public function renderAssets()
  {
    $ret  = '';
    $ret .= IND2.'public static $assets = array('.LE;

    if (count($this->filesToEncode) > 0)
    {
      foreach($this->filesToEncode as $ext)
      {
        $ret .= $this->walkFiles($ext, function($file)
        {
          $varName = $this->file2VarName($file);
          return IND3.'\''.$varName.'\' => \''.$this->file2DataURI($file).'\','.LE;
        });
      }
    }

    $ret .= IND2.');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function renderStyleVars()
  {
    $ret  = '';
    $ret .= IND3.'\'var\' => <<< \'CSSVAR\''.LE;
    $ret .= '--root'.LE;
    $ret .= '{'.LE;

    if (isset($this->filesToEncode))
    {
      foreach($this->filesToEncode as $ext)
      {
        $ret .= $this->walkFiles($ext, function($file)
        {
          $varName = $this->file2VarName($file);
          return IND1.'--'.$varName.': '.$this->file2DataURI($file).';'.LE;
        });
      }
    }

    $ret .= '}'.LE;
    $ret .= 'CSSVAR,'.LE;

    return $ret;
  }

  public function renderStyleFiles()
  {
    $ret  = '';

    $ret .= $this->walkFiles('css', function($file)
    {
      $cont = $this->readFile($file);
      $ret .= IND3.'\''.$this->file2VarName($file).'\' => <<< \'CSSFILE\''.LE;
      $ret .= $cont.LE;
      $ret .= 'CSSFILE,'.LE;
      return $ret;
    });

    return $ret;
  }

  public function renderJsFiles()
  {
    $ret  = '';
    $ret .= IND2.'public static $js = array('.LE;

    $ret .= $this->walkFiles('js', function($file)
    {
      $str  = '';
      $cont = $this->readFile($file);
      $str .= IND3.'\''.$this->file2VarName($file).'\' => <<< \'JSCODE\''.LE;
      $str .= $cont.LE;
      $str .= 'JSCODE,'.LE;
      return $str;
    });

    $ret .= IND2.');'.LE; // close CSS array

    return $ret;
  }

  public function renderPHPFiles()
  {
    $ret = '';

    $ret .= $this->walkFiles('php', function($file)
    {
      return $this->readPHPFile($file);
    });

    return $ret;
  }

  public function renderMainPHP()
  {
    $ret = '';
    $ret = $this->readPHPFile($this->mainF);

    return $ret;
  }

  /**
   *  Controller
   */
  public function run()
  {
    $ret  = '';

    $ret .= $this->openPHP();
    $ret .= $this->renderPHPFiles();

    $ret .= $this->openRootNamespace();

    $ret .= $this->openRscClass();
    $ret .= $this->renderAssets();
    $ret .= $this->openStyleMemberVar();
    $ret .= $this->renderStyleVars();
    $ret .= $this->renderStyleFiles();
    $ret .= $this->closeStyleMemberVar();
    $ret .= $this->renderJsFiles();
    $ret .= $this->closeRscClass();

    $ret .= $this->renderMainPHP();
    $ret .= $this->closeRootNamespace();

    $ret .= LE;
    $ret .= 'var_dump(RSC::$css);'.LE;
    $ret .= LE;

    $ret .= $this->closePHP();

    file_put_contents($this->outF, $ret);
  }

}

$l = new Leim();
$l->add('.', array('php'));
$l->add('../frontschweine/js', array('js'));
$l->add('./assets', array('gif', 'png'));
//$l->setFilesToEncode(array('png'));
$l->run();

?>