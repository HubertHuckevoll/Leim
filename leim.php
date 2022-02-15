#!/usr/bin/env php

<?php

define('LE', "\r\n"); // default Line Ending
define('IND1', "  ");  // default indent: 2 spaces
define('IND2', "    ");  // default indent: 4 spaces
define('IND3', "      ");  // default indent: 6 spaces
define('IND4', "        ");  // default indent: 6 spaces

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
        $path = substr($file->getPath(), 2);
        $this->files[$ext][$path][] = $filename;
      }
    }

    return $ret;
  }

  public function openRSC()
  {
    $ret  = '';
    $ret .= 'namespace'.LE;
    $ret .= '{'.LE;
    $ret .= IND1.'class RSC'.LE;
    $ret .= IND1.'{'.LE;

    return $ret;
  }

  public function closeRSC()
  {
    $ret  = '';
    $ret .= IND1.'}'.LE; // class
    $ret .= '}'.LE; // namespace

    return $ret;
  }

  public function writeAssets()
  {
    $ret  = '';
    $ret .= IND2.'public static $assets = array('.LE;
    foreach ($this->encImgs as $name => $asset)
    {
      $ret .= IND3.'\''.$name.'\' => \'data:'.$asset['mime'].';base64,'.$asset['content'].'\','.LE;
    }
    $ret .= IND2.')'.LE;
    $ret .= LE;

    return $ret;
  }

  public function writeStyleVars()
  {
    $ret  = IND2.'public static $css = array('.LE;
    $ret .= IND3.'\'var\' => <<< CSSVAR'.LE;
    $ret .= IND3.'--root'.LE;
    $ret .= IND3.'{'.LE;

    foreach ($this->encImgs as $name => $asset)
    {
      $ret .= IND4.'--'.$name.': data:'.$asset['mime'].';base64,'.$asset['content'].';'.LE;
    }

    $ret .= IND3.'}'.LE;
    $ret .= IND3.'CSSVAR,'.LE;
    // add CSS files before closing array...

    return $ret;
  }

  public function writeStyleFiles()
  {
    $ret = '';
    foreach($this->files['css'] as $path => $files)
    {
      foreach($files as $file)
      {
        $cont = file_get_contents($file);
        $cont = str_replace("\r", "", $cont);
        $cont = str_replace("\n", "", $cont);

        $ret .= IND3.'\''.pathinfo($file, PATHINFO_FILENAME).'\' => \''.$cont.'\','.LE;
      }
    }

    $ret .= IND2.');'.LE; // close CSS array
    $ret .= LE;

    return $ret;
  }

  public function writeJsFiles()
  {
    $ret  = '';
    $ret .= IND2.'public static $js = array('.LE;
    foreach($this->files['js'] as $path => $files)
    {
      foreach ($files as $file)
      {
        $cont = file_get_contents($file);
        $cont = str_replace("\r", "", $cont);
        $cont = str_replace("\n", "", $cont);

        $ret .= IND3.'\''.pathinfo($file, PATHINFO_FILENAME).'\' => <<< JSCODE'.LE;
        $ret .= IND3.$cont.LE;
        $ret .= IND3.'JSCODE,'.LE;
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
      foreach ($files as $file)
      {
        $cont = file_get_contents($file);
        /*
        preg_match_all("/\<\?php\s(.*)\s\?>/m", $cont, $match, PREG_SET_ORDER);
        var_dump($match);
        $cont = $match[0];
        */

        $cont = str_replace('<?php', '', $cont);
        $cont = str_replace('?>', '', $cont);
        $cont = trim($cont);

        $ret .= $cont;
        $ret .= LE.LE;
      }
    }

    return $ret;
  }

  public function run()
  {
    $this->addAssets();
    $this->add('php');
    $this->add('css');
    $this->add('js');
    $this->dump();
  }

  public function dump()
  {
    $ret = '<?php'.LE.LE;

    $ret .= $this->writePHPFiles('php');
    $ret .= $this->openRSC();
    $ret .= $this->writeAssets();
    $ret .= $this->writeStyleVars();
    $ret .= $this->writeStyleFiles();
    $ret .= $this->writeJsFiles();
    $ret .= $this->closeRSC();

    $ret .= 'var_dump(RSC::$css);';

    $ret .= LE.'?>'.LE;
    file_put_contents($this->outF, $ret);
  }

}

$l = new Leim();
$l->run();

?>