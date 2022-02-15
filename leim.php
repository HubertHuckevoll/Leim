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
    $ret = 'class RSC'.LE.'{'.LE;
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
    $ret  = '  public static $css = array(\'var\' => <<< CSSVAR'.LE;
    $ret .= '  --root'.LE;
    $ret .= '  {'.LE;

    foreach ($this->encImgs as $name => $asset)
    {
      $ret .= '    --'.$name.': data:'.$asset['mime'].';base64,'.$asset['content'].';'.LE;
    }

    $ret .= '  }'.LE;
    $ret .= '  CSSVAR,'.LE;
    // add CSS files before closing array...

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

      $ret .= '  \''.pathinfo($file, PATHINFO_FILENAME).'\' => \''.$cont.'\','.LE;
    }
    $ret .= '  );'.LE; // close CSS array

    return $ret;
  }

  public function writeJsFiles()
  {
    $ret  = '';
    $ret .= '  public static $js = array('.LE;
    foreach($this->files['js'] as $file)
    {
      $cont = file_get_contents($file);
      $cont = str_replace("\r", "", $cont);
      $cont = str_replace("\n", "", $cont);

      $ret .= '  \''.pathinfo($file, PATHINFO_FILENAME).'\' => <<< JSCODE'.LE.'  '.$cont.LE.'  JSCODE,'.LE;
    }
    $ret .= '  );'.LE; // close CSS array

    return $ret;
  }

  public function writePHPFiles($ext)
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
    $this->add('js');
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
    $ret .= $this->writeJsFiles();
    $ret .= $this->closeRSC();
    //$ret .= $this->writePHPFiles('php');

    $ret .= 'var_dump(RSC::$css);';

    $ret .= LE.'?>'.LE;
    file_put_contents($this->outF, $ret);
  }

}

$l = new Leim();
$l->run();

?>