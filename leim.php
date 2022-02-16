#!/usr/bin/env php

<?php

define('LE', "\r\n"); // default Line Ending
define('IND1', "  ");  // default indent: 2 spaces
define('IND2', "    ");  // default indent: 4 spaces
define('IND3', "      ");  // default indent: 6 spaces
define('IND4', "        ");  // default indent: 8 spaces

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

  public function openRscCSS()
  {
    // open CSS array
    $ret  = '';
    $ret .= IND2.'public static $css = array('.LE;

    return $ret;
  }

  public function closeRscCSS()
  {
    // close CSS array
    $ret  = '';
    $ret .= IND2.');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function file2DataURI($file)
  {
    $mime = strtolower(mime_content_type($file)); // return mime type ala mimetype extension
    $content = base64_encode(file_get_contents($file));
    $ret = 'data:'.$mime.';base64,'.$content;

    return $ret;
  }

  public function writeAssets($exts)
  {
    $ret  = '';
    $ret .= IND2.'public static $assets = array('.LE;
    foreach($exts as $ext)
    {
      foreach ($this->files[$ext] as $path => $files)
      {
        foreach($files as $file)
        {
          $varName = pathinfo($file, PATHINFO_FILENAME);
          $ret .= IND3.'\''.$varName.'\' => \''.$this->file2DataURI($file).'\','.LE;
        }
      }
    }
    $ret .= IND2.');'.LE;
    $ret .= LE;

    return $ret;
  }

  public function writeStyleVars($exts)
  {
    $ret  = '';
    $ret .= IND3.'\'var\' => <<< CSSVAR'.LE;
    $ret .= IND3.'--root'.LE;
    $ret .= IND3.'{'.LE;

    foreach($exts as $ext)
    {
      foreach ($this->files[$ext] as $path => $files)
      {
        foreach($files as $file)
        {
          $varName = pathinfo($file, PATHINFO_FILENAME);
          $ret .= IND4.'--'.$varName.': '.$this->file2DataURI($file).';'.LE;
        }
      }
    }

    $ret .= IND3.'}'.LE;
    $ret .= IND3.'CSSVAR,'.LE;

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
        preg_match_all("/<\?php(.*)\?>/ms", $cont, $match, PREG_SET_ORDER);
        $cont = $match[0][1];
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
    $this->add('.', array('php', 'css', 'js'));
    $this->add('./assets', array('gif', 'png'));
    $this->dump();
  }

  public function dump()
  {
    $ret = '<?php'.LE.LE;

    $ret .= $this->writePHPFiles();

    $ret .= $this->openRscClass();
    $ret .= $this->writeAssets(array('gif', 'png'));
    $ret .= $this->openRscCSS();
    $ret .= $this->writeStyleVars(array('gif', 'png'));
    $ret .= $this->writeStyleFiles();
    $ret .= $this->closeRscCSS();
    $ret .= $this->writeJsFiles();
    $ret .= $this->closeRscClass();

    $ret .= 'var_dump(RSC::$css);';

    $ret .= LE.'?>'.LE;
    file_put_contents($this->outF, $ret);
  }

  public function log($str)
  {
    echo $str.LE;
  }

}

$l = new Leim();
$l->run();

?>