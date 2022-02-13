#!/usr/bin/env php

<?php

define('LE', "\r\n");

class Leim
{
  // Input
  public $pathAssets = './assets/';

  // Output
  public $encImgs = array();
  public $files = array();
  public $outF = 'index.php';

  // PutPut
  public function addAssets()
  {
    $imgs = scandir($this->pathAssets);
    $encImgs = array();

    foreach ($imgs as $img)
    {
      if (($img != '.') && ($img != '..'))
      {
        $file = $this->pathAssets.$img;
        $varName = pathinfo($img, PATHINFO_FILENAME);
        $mime = mime_content_type($file); // return mime type ala mimetype extension
        $this->encImgs[$varName]['mime'] = strtolower($mime);
        $this->encImgs[$varName]['content'] = base64_encode(file_get_contents($file));
      }
    }
  }

  public function add($ext)
  {
    $ret = '';
    $di = new RecursiveDirectoryIterator('.');

    foreach (new RecursiveIteratorIterator($di) as $filename => $file)
    {
      if (
            (strtolower($file->getExtension()) == strtolower($ext)) &&
            (!$file->isDir()) &&
            ($filename != '.') &&
            ($filename != '..') &&
            (basename($filename) != 'index.php') &&
            (basename($filename) != 'leim.php')
         )
      {
        $this->files[$ext][] = $filename;
      }
    }

    return $ret;
  }

  public function openRSC()
  {
    $ret = 'Class RSC'.LE.'{'.LE;
    return $ret;
  }

  public function closeRSC()
  {
    $ret = '}'.LE.LE;
    return $ret;
  }

  public function writeAssets()
  {
    $ret = '';
    foreach ($this->encImgs as $name => $asset)
    {
      $ret .= '  public static $'.$name.' = \'data:'.$asset['mime'].';base64,'.$asset['content'].'\';'.LE;
    }

    return $ret;
  }

  public function writeStyleVars()
  {
    $ret  = '  public static $css[\'var\'] = <<< CSSVAR'.LE;
    $ret .= '  --root'.LE;
    $ret .= '  {'.LE;

    foreach ($this->encImgs as $name => $asset)
    {
      $ret .= '    --'.$name.': self::$'.$name.';'.LE;
    }

    $ret .= '  }'.LE;
    $ret .= '  CSSVAR;';
    $ret .= LE.LE;

    return $ret;
  }

  public function writeStyleFiles()
  {
    $ret = '';
    foreach($this->files['css'] as $file)
    {
      $cont = file_get_contents($file);
      $cont = str_replace("\r", "", $cont);
      $cont = str_replace("\n", "", $cont);

      $ret .= '  public static $css[\''.basename($file).'\'] = \''.$cont.'\';'.LE;
    }

    return $ret;
  }

  public function writeFiles($ext)
  {
    $ret = '';

    foreach($this->files[$ext] as $file)
    {
      $ret .= file_get_contents($file);
      $ret .= LE.LE;
    }

    return $ret;
  }

  public function run()
  {
    $this->addAssets();
    $this->add('css');
    //$this->add('php');
    $this->dump();
  }

  public function dump()
  {
    $ret = '<?php'.LE.LE;

    $ret .= $this->openRSC();
    $ret .= $this->writeAssets();
    $ret .= $this->writeStyleVars();
    $ret .= $this->writeStyleFiles();
    $ret .= $this->closeRSC();
    //$ret .= $this->writeFiles('php');

    $ret .= LE.'?>'.LE;
    file_put_contents($this->outF, $ret);
  }

}

$l = new Leim();
$l->run();

?>