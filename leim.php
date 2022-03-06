#!/usr/bin/env php

<?php

define('LE', "\r\n"); // default Line Ending
define('IND1', "  ");  // default indent: 2 spaces
define('IND2', "    ");  // default indent: 4 spaces
define('IND3', "      ");  // default indent: 6 spaces
define('IND4', "        ");  // default indent: 8 spaces

class Leim
{
  public $filesToEncode = ['gif', 'png', 'jpg', 'webp', 'svg'];
  public $encFilesCache = [];
  public $files = [];
  public $mainF = '';
  public $outF = '';

  /**
   * Input
   */
  public function __construct(string $mainF = 'main.php', string $outF = 'index.php')
  {
    $this->mainF = $mainF;
    $this->outF = $outF;
  }

  public function setFilesToEncode(array $exts) : void
  {
    $this->filesToEncode = $exts;
  }

  public function addFiles(string $root, array $exts) : void
  {
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
  }

  /**
   * Processing and helpers
   */

  public function file2DataURI(string $file) : string
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

  public function file2VarName(string $file) : string
  {
    return pathinfo($file, PATHINFO_FILENAME);
  }

  public function readFile(string $file) : string
  {
    $ret = '';

    if (file_exists($file))
    {
      $ret = trim((string) file_get_contents($file));
    }

    return $ret;
  }

  public function readPHPFile(string $file) : string
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

  public function log(string $str) : void
  {
    echo $str.LE;
  }

  public function walkFiles($exts, callable $workerFunc) : string
  {
    $ret = '';

    if (count($exts) > 0)
    {
      foreach($exts as $ext)
      {
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
      }
    }

    return $ret;
  }

  /**
   * Output
   */
  public function renderAssets() : string
  {
    $ret  = '';
    $ret .= 'public static $assets = array('.LE;

    $ret .= $this->walkFiles($this->filesToEncode, function($file)
    {
      $varName = $this->file2VarName($file);
      return '\''.$varName.'\' => \''.$this->file2DataURI($file).'\','.LE;
    });

    $ret .= ');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function openStyleMemberVar() : string
  {
    // open CSS array
    $ret  = '';
    $ret .= 'public static $css = array('.LE;

    return $ret;
  }

  public function renderStyleVars() : string
  {
    $ret  = '';
    $ret .= '\'var\' => <<< \'CSSVAR\''.LE;
    $ret .= '--root'.LE;
    $ret .= '{'.LE;

    $ret .= $this->walkFiles($this->filesToEncode, function($file)
    {
      $varName = $this->file2VarName($file);
      return IND1.'--'.$varName.': '.$this->file2DataURI($file).';'.LE;
    });

    $ret .= '}'.LE;
    $ret .= 'CSSVAR,'.LE;

    return $ret;
  }

  public function renderStyleFiles() : string
  {
    $ret  = '';
    $ret .= $this->walkFiles(['css'], function($file)
    {
      $str  = '';
      $str .= '\''.$this->file2VarName($file).'\' => <<< \'CSSFILE\''.LE;
      $str .= $this->readFile($file).LE;
      $str .= 'CSSFILE,'.LE;

      return $str;
    });

    return $ret;
  }

  public function closeStyleMemberVar() : string
  {
    // close CSS array
    $ret  = '';
    $ret .= '); // close CSS array'.LE;
    $ret .= LE;

    return $ret;
  }

  public function renderJsFiles() : string
  {
    $ret  = '';
    $ret .= 'public static $js = array('.LE;

    $ret .= $this->walkFiles(['js'], function($file)
    {
      $str  = '';
      $str .= '\''.$this->file2VarName($file).'\' => <<< \'JSCODE\''.LE;
      $str .= $this->readFile($file).LE;
      $str .= 'JSCODE,'.LE;

      return $str;
    });

    $ret .= '); // close JS array'.LE; // close JS array
    $ret .= LE;

    return $ret;
  }

  public function renderHelperFunctions()
  {
    $ret = '';

    $ret .= <<< 'PHPCODE'
    public static function getCSS()
    {
      $ret = '';
      foreach(self::$css as $key => $val)
      {
        $ret .= $val."\r\n";
      }
      return $ret;
    }

    public static function getJS()
    {
      $ret = '';
      foreach(self::$js as $key => $val)
      {
        $ret .= $val."\r\n";
      }
      return $ret;
    }
    PHPCODE.LE;
    $ret .= LE;

    return $ret;
  }

  public function renderPHP($rscClassAsString) : string
  {
    $ret = '';
    $str = '';
    $match = array();
    $namespaces = array();
    $no_namespace = 'no_namespace';

    $ret .= $this->openPHP();

    $this->walkFiles(['php'], function($file) use (&$namespaces, $no_namespace)
    {
      $str = $this->readPHPFile($file);
      $namespaceName = (preg_match("/namespace\s(.*);.*(class.*)/mis", $str, $match) == 1) ? $match[1] : $no_namespace;
      $str = ($namespaceName !== $no_namespace) ? $match[2] : $str;
      $str = trim($str);
      $namespaces[$namespaceName][] = $str;
    });

    if (count($namespaces) > 0)
    {
      foreach($namespaces as $namespace => $classes)
      {
        if ($namespace !== $no_namespace)
        {
          $ret .= 'namespace '.$namespace.' {'.LE;
          foreach($classes as $class)
          {
            $ret .= $class.LE.LE;
          }
          $ret .= '} // end of namespace "'.$namespace.'"'.LE.LE.LE;
        }
      }
    }

    if (isset($namespaces[$no_namespace]))
    {
      $ret .= 'namespace {'.LE;

      foreach($namespaces[$no_namespace] as $class)
      {
        $ret .= $class.LE;
      }

      $ret .= $rscClassAsString;

      $ret .= $this->readPHPFile($this->mainF);
      $ret .= LE;

      $ret .= '} // end of root namespace'.LE.LE;
    }

    $this->closePHP();

    return $ret;
  }

  public function openPHP() : string
  {
    $ret  = '';
    $ret .= '<?php'.LE;
    $ret .= LE;

    return $ret;
  }

  public function closePHP() : string
  {
    $ret  = '';
    $ret .= LE;
    $ret .= '?>';

    return $ret;
  }

  public function openRscClass() : string
  {
    $ret  = '';
    $ret .= 'class RSC'.LE;
    $ret .= '{'.LE;

    return $ret;
  }

  public function closeRscClass() : string
  {
    $ret  = '';
    $ret .= '} // end of RSC class'.LE; // class
    $ret .= LE;

    return $ret;
  }

  /**
   *  Controller
   */
  public function run() : void
  {
    $str  = '';

    $str .= $this->openRscClass();
    $str .= $this->renderAssets();
    $str .= $this->openStyleMemberVar();
    $str .= $this->renderStyleVars();
    $str .= $this->renderStyleFiles();
    $str .= $this->closeStyleMemberVar();
    $str .= $this->renderJsFiles();
    $str .= $this->renderHelperFunctions();
    $str .= $this->closeRscClass();

    $str = $this->renderPHP($str);

    file_put_contents($this->outF, $str);
  }

}

$l = new Leim();
$l->addFiles('.', ['php']);
$l->addFiles('./js', ['js']);
$l->addFiles('./css', ['css']);
$l->addFiles('./assets', ['gif', 'png']);
$l->run();

?>