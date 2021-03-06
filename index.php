<?php

namespace dummy {
class cbPageVP
{
  /**
   * Konstruktor
   * ________________________________________________________________
   */
	public function __construct($ep = '', $hook, $linker = null)
	{
		parent::__construct($ep, $hook, $linker);
	}

  /**
   * head content
   * _________________________________________________________________
   */
  protected function additionalHeadData()
  {
    return ''; // overwrite me
  }

  /**
   * main content
   * _________________________________________________________________
   */
  protected function mainContent()
  {
    return ''; // overwrite me
  }

  /**
   * additional content
   * _________________________________________________________________
   */
  protected function additionalContent()
  {
    return '';
  }

  /**
   * output the whole page - sample code
   * FIXME - put this in the not yet existing demo project
   * _________________________________________________________________
   */
  public function drawPage($errMsg = '')
  {
    $erg .= '<!DOCTYPE html>
             <html>
               <head>
                 <base href="'.PROJECT_ROOT_URL.'"></base>
                 <title>'.$this->data['pageTitle'].' - '.$this->data['siteName'].'</title>

                 <link rel="shortcut icon" href="favicon.ico" />
                 <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />

                 <meta name="robots" content="index,follow" />
                 <meta http-equiv="expires" content="0" />
                 <meta name="revisit-after" content="7 days" />
                 <meta name="keywords" content="'.$this->data['metaKeywords'].'" />
                 <meta name="description" content="'.$this->data['metaDescription'].'" />

                 <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cbBox.css" />
                 <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cbArticle.css" />
                 <link rel="stylesheet" type="text/css" href="'.CB_CSS_ROOT.'cardboardSearch.css" />

                 <link rel="stylesheet" type="text/css" href="'.PROJECT_CSS_URL.'main.css" title="CSS" media="screen" />
                 <link rel="stylesheet" media="screen and (max-width: 600px)" href="'.PROJECT_CSS_URL.'phone.css" />
                 <link rel="stylesheet" media="screen and (max-width: 480px)" href="'.PROJECT_CSS_URL.'phone.css" />'.
                 $this->additionalHeadData().
              '</head>
               <body>
                 <div id="header"></div>
                 <div id="content">'.$this->mainContent().'</div>
                 <div id="additionalContent">'.$this->additionalContent().'</div>
                 <div id="footer"></div>
               </body>
             </html>';

    echo $erg;
  }

  /**
   * output an error
   * _________________________________________________________________
   */
  public function drawErrorPage($errStr)
  {
    $erg .= '<!DOCTYPE HTML>
             <html>
               <head>
                 <title>'.$this->data['pageTitle'].'</title>
                 <link rel="shortcut icon" href="favicon.ico" />
                 <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
                 <meta name="robots" content="no-follow" />
                 <meta http-equiv="expires" content="1" />
                 <meta name="revisit-after" content="365 days" />
                 <meta name="keywords" content="error, page" />
                 <meta name="description" content="Error Page" />
                 <style type="text/css">
                   body {
                     font-family: "Verdana";
                     padding: 0 100px 0 100px;
                   }

                   h1 {
                     color: #bbb;
                     border-bottom: 1px solid #bbb;
                     margin-bottom: 30px;
                   }
                 </style>
               </head>
               <body>
                 <h1>Fehler</h1>
                 <div id="content">'.$errStr.'</div>
               </body>
             </html>';

    echo $erg;
  }

  /**
   * output fragment by Ajax - overwrite
   * _________________________________________________________________
   */
  public function drawAjax()
  {
    echo '';
  }

  /**
   * output data as json - overwrite
   * _________________________________________________________________
   */
  public function drawJson()
  {
    echo '';
  }

}

class Test
  {
    public function getText()
    {
      return "What the actual clusterfuck?";
    }
  }

} // end of namespace "dummy"


namespace {
define('NUMERIC', 0);
define('ALPHA', 1);
define('ALPHANUMERIC', 2);

/**
 * getReqVar
 * _________________________________________________________________
 */
/*
function getReqVar($varName, $stripTags = true)
{
  $req = $_GET + $_POST;

  if (isset($req[$varName]))
  {
    $var = $req[$varName];
    $var = trim($var);
    $var = preg_replace("/^(content-type:|bcc:|cc:|to:|from:)/im", "", $var);

    if ($stripTags == true)
    {
      $var = strip_tags($var);
    }

    return $var;
  }

  return false;
}
*/

/**
 * Check a variable for a certain pattern
 * _________________________________________________________________
 */
function checkVar($var, $pregMode)
{
  switch($pregMode)
  {
    case 'nick':
      $pattern = '/^[\w\d\.\-\_]{2,255}$/';
    break;

    case 'password':
      $pattern = '/^[\w\d\.\-\_]{5,255}$/';
    break;

    case 'plz':
      $pattern = '/^[\d]{5}$/';
    break;

    case 'digits':
      $pattern = '/^[\d]+$/';
    break;

    case 'email':
      $pattern = '/^[\w.+-]{2,}\@[\w.-]{2,}\.{0,1}[a-z]{2,6}$/';
    break;

    case 'url':
      $pattern = '#(^|[^\"=]{1})(http://|ftp://|mailto:|news:)([^\s<>]+)([\s\n<>]|$)#sm';
    break;
  }

  if (preg_match($pattern, $var)) {
    return true;
  }

  return false;
}

/**
 * Debug a variable by putting its contents
 * on top of an html file
 * ________________________________________________________________
 */
function dv($var, $callers = false)
{
  $fname = 'debug.html';

  if (!defined('DEBUG_FLAG')) {
    @unlink($fname);
    define('DEBUG_FLAG', true);
  }

  $caller_info = debug_backtrace();
  $file = $caller_info[0]['file'];
  $line = $caller_info[0]['line'];
  $func = $caller_info[1]['function'];

  $srcFile = file($file);
  $debugLine = $srcFile[$line - 1];
  preg_match('/.*'.__FUNCTION__.'\((.*)\).*/', $debugLine, $treffer);
  $varMeta = '<strong>'.$treffer[1].'</strong> (in <strong>'.basename($file).'</strong> on line <strong>'.$line.'</strong> in function <strong>'.$func.'</strong>) is';

  $varInfo = print_r($var, true);

  if ($callers == true)
  {
    $varInfo .= getCallingStack();
  }

  $fc = @file_get_contents($fname);
  if ($fc != '') {
    $matches = array();
    preg_match('/<body>(.*)<\/body>/s', $fc, $matches);
    $body = $matches[1];
  }

  $body = '<html><head><title>Debug: '.basename($file).'</title></head><body>'
         .'<div>
            <p>'.$varMeta.'</p>
            <div style="border: 1px solid #DDD; padding: 5px;"><pre>'.$varInfo.'</pre></div>'
         .'</div>'
         .$body
         .'</body></html>';

  file_put_contents($fname, $body);
}

/**
 * Debug a request
 * ________________________________________________________________
 */
function debugRequest($tag = '')
{
  $sep = "---------------------------------------------------------------------\r\n\r\n";

  $lastErr = "LAST ERROR: \r\n".$php_errormsg.$sep;
  $get = "GET VARIABLES: \r\n".print_r($_GET, true).$sep;
  $post = "POST VARIABLES: \r\n".print_r($_POST, true).$sep;
  $files = "FILES: \r\n".print_r($_FILES, true).$sep;
  $server = "SERVER: \r\n".print_r($_SERVER, true).$sep;
  $env = "ENVIRONMENT: \r\n".print_r($_ENV, true).$sep;
  $session = "SESSION: \r\n".print_r($_SESSION, true).$sep;
  $cookie = "COOKIE: \r\n".print_r($_COOKIE, true);

  $data = $lastErr.$get.$post.$files.$server.$env.$session.$cookie;

  $fname = 'request_'.$tag.'_['.$_SERVER['REMOTE_ADDR'].']_'.time().'.txt';

  file_put_contents($fname, $data);
}

/**
 * Get calling stack
 * _________________________________________________________________
 */
function getCallingStack()
{
  $callerInfo = debug_backtrace();
  array_splice($callerInfo, 0, 1);
  $i = 0;
  $last = count($callerInfo) - 1;
  $varInfo = '<ul><li>';
  foreach ($callerInfo as $step) {
    $varInfo .= $step['class'].':<strong>'.$step['function'].'()</strong>';
    if ($i != $last) {
      $varInfo .= ' &laquo; ';
    }
    $i++;
  }
  $varInfo .= '</li></ul>';

  return $varInfo;
}

/**
 * Benchmarking
 * __________________________________________________________________
 */
function benchmark()
{
	static $start = NULL;
	if (is_null($start)) {
		$start = getMicrotime();
	} else {
		$benchmark = getMicrotime() - $start;
		$start = getMicrotime();
		return $benchmark;
	}
}

/**
 * get Microtime
 * ________________________________________________________________
 */
function getMicrotime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

/**
 * Aus String UNIX-gerechten Datei/Verzeichnisnamen erzeugen
 * Achtung: auch als CSS Selektor gedacht, daher sind
 * nur Binde- und Unterstriche, Zahlen und Buchstaben erlaubt
 * __________________________________________________________
 */
function makeFileName($str)
{
  $str = trim($str);
  $str = str_replace(array('??','??','??','??','??','??','??'), array('ae','oe','ue','ss','Ae','Oe','Ue'), $str);
  $str = preg_replace('/[^a-zA-Z0-9\-\_]/', '-', $str);
  return $str;
}


/**
 * Name einer Datei ohne Endung ermitteln
 * __________________________________________________________________
 */
function getFileBasename($fname)
{
  return pathinfo($fname, PATHINFO_FILENAME);
}

/**
 * Endung einer Datei ermitteln
 * __________________________________________________________________
 */
function getFileExt($fname)
{
  return pathinfo($fname, PATHINFO_EXTENSION);
}

/**
 * slightly improved version of scandir
 * pass names of files / folders to exclude from
 * scan results as second parameter
 * ____________________________________________________________________
 */
function scanpath($dir, $excludeArr = array())
{
  $items = array();
  $excludeArr = array_merge($excludeArr, array('..', '.'));

  if (file_exists($dir))
  {
    $items = scandir($dir);
    $items = array_diff($items, $excludeArr);
    return $items;
  }

  return $items;
}

/**
 * Versionsnummer auslesen
 * ____________________________________________________________________
 */
function getVer()
{
  $fnames = glob("*.ver");
  return basename($fnames[0], '.ver');
}

/**
 * Versuche, Mobilbrowser zu erkennen - discouraged, use
 * CSS instead
 * ____________________________________________________________________
 */
function isMobile()
{
  if (defined('FORCE_MOBILE') && (FORCE_MOBILE == true)) {
    return true;
  }

  if (defined('FORCE_DESKTOP') && (FORCE_DESKTOP == true)) {
    return false;
  }

  $agents = array('android','webos','iphone','ipad','ipod','blackberry','iemobile','opera mini');

  // Pr??fen der Browserkennung
  foreach ($agents as $agent)
  {
    if (
        (isset($_SERVER["HTTP_USER_AGENT"])) &&
        (strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), strtolower($agent)) !== false)
       )
    {
      return true;
    }
  }
  return false;
}

/**
 * is ajax?
 * ____________________________________________________________________
 */
function isAjax()
{
  $ret = false;
  $ret = (isset($_GET['ajax'])) ? $_GET['ajax'] : false;
  if ($ret == false)
  {
    $ret = (isset($_POST['ajax'])) ? $_POST['ajax'] : false;
  }

  $ret = ($ret == 'ajax') ? true : false;

  return $ret;
}

/**
 * create random password
 * ____________________________________________________________________
 */
function randomStr($length = 5, $type = ALPHANUMERIC)
{
  switch ($type)
  {
    case ALPHA:        $chars = "abcdefghijkmnpqrstuvwxyz"; break;
    case NUMERIC:      $chars = "123456789"; break;
    case ALPHANUMERIC: $chars = "abcdefghijkmnpqrstuvwxyz123456789"; break;
  }

  list($usec, $sec) = explode(' ', microtime());
  $seed = (float) $sec + ((float) $usec * 100000);
  mt_srand($seed);
  $i = 0;
  $pass = '';

  while ($i < $length)
  {
    $num = mt_rand() % 33;
    $tmp = substr($chars, $num, 1);
    $pass = $pass.$tmp;
    $i++;
  }

  return $pass;
}

/**
 * create htpasswd compatible password
 * ____________________________________________________________________
 */
function shaPwd($pass = '')
{
  return ($pass != '') ? "{SHA}".base64_encode(pack("H*", sha1($pass))) : '';
}

/**
 * ellipsis
 * ____________________________________________________________________
 */
function ellipsis($str, $length)
{
  if (strlen($str) > $length)
  {
    $separator = '...';
    $separatorlength = strlen($separator) ;
    $maxlength = $length - $separatorlength;
    $start = $maxlength / 2;
    $trunc = strlen($str) - $maxlength;
    $str = substr_replace($str, $separator, $start, $trunc);
  }

  return $str;
}

/**
 * wrapper for get_object_vars to use INSIDE of classes
 * ______________________________________________________________________
 */
function getPublicObjectVars($obj)
{
  return get_object_vars($obj);
}

/**
 * get Object fields as array
 * _________________________________________________________________
 */
function getObjProps($obj, $details = '')
{
  $props = get_object_vars($obj);

  if ($details != '')
  {
    $details = explode(';', $details);

    if (is_array($details))
    {
      foreach ($details as $detail)
      {
        foreach ($props as $propKey => $prop)
        {
          if ($propKey == $detail)
          {
            $nProps[$propKey] = $prop;
            break;
          }
        }
      }
    }
    else
    {
      $nProps = $props;
    }
  }
  else
  {
    $nProps = $props;
  }

  return $nProps;
}

/**
 * returns full path for fragment as URL
 * _________________________________________________________________
 */
function getPathURL($relPath)
{
  if (!((strpos($relPath, 'http') === 0) || (strpos($relPath, 'https') === 0)))
  {
    $relPath = DIRECTORY_SEPARATOR.ltrim($relPath, DIRECTORY_SEPARATOR);

    $protocol = ($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $relPath = $protocol.$_SERVER['HTTP_HOST'].$relPath;
  }

  return $relPath;
}

/**
 * returns full path for fragment as FS path
 * _________________________________________________________________
 */
function getPathFS($relPath)
{
  $relPath = DIRECTORY_SEPARATOR.ltrim($relPath, DIRECTORY_SEPARATOR);
  $root = ($_SERVER['DOCUMENT_ROOT'] != '') ? $_SERVER['DOCUMENT_ROOT'] : CLI_DOCUMENT_ROOT;

  return $root.$relPath;
}

/**
 * project root as relative path
 * _________________________________________________________________
 */
function getProjectRoot()
{
  return rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
}

/**
 * project root as URL
 * _________________________________________________________________
 */
function getProjectRootURL()
{
  $protocol = ($_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
  return $protocol.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
}

/**
 * Send mail
 * _________________________________________________________________
 */
function sendEmail($from, $to, $subject, $msg)
{
  if (substr(PHP_OS, 0, 3) == 'WIN') { $crlf = "\r\n"; } else { $crlf = "\n"; }

  $mailheaders  = 'From: '.$from.$crlf;
  $mailheaders .= 'Content-type: text/plain; charset=iso-8859-1'.$crlf;
  $mailheaders .= 'MIME-Version: 1.0'.$crlf;
  $mailheaders .= 'Content-Transfer-Encoding: 8bit'.$crlf;
  $mailheaders .= 'Reply-To: '.$from.$crlf;
  $mailheaders .= 'Return-Path: '.$from.$crlf.$crlf;

  if (@mail($to, $subject, $msg, $mailheaders))
  {
    return true;
  }
  return false;
}

/**
 * returns an obfuscated email adress
 * _________________________________________________________________
 */
function obfuscateStr($originalString)
{
  $encodedString = "";
  $nowCodeString = "";
  $randomNumber = -1;
  $isUTF8 = false;

	if (mb_check_encoding($originalString, 'UTF-8'))
  {
		$originalString = utf8_decode($originalString);
	}

  $originalLength = strlen($originalString);

  for ($i = 0; $i < $originalLength; $i++)
  {
    $encodeMode = rand(1, 2);
    switch ($encodeMode)
    {
      case 1: //Decimal code
      {
        $nowCodeString = "&#".ord($originalString[$i]).";";
        break;
      }
      case 2: //Hexadecimal code
      {
        $nowCodeString = "&#x".dechex(ord($originalString[$i])).";";
        break;
      }
      default:
      {
        return 'Error';
      }
    }
    $encodedString .= $nowCodeString;
  }

	if ($isUTF8)
  {
		$encodedString = utf8_encode($encodedString);
	}

  return $encodedString;
}

/**
 * returns an obfuscated email link
 * ____________________________________________________________________
 */
function encodeEmail($originalString)
{
  return obfuscateStr($originalString);
}

/**
 * returns an obfuscated email link
 * ____________________________________________________________________
 */
function emailLink($originalString)
{
  $encodedString = encodeEmail($originalString);
  return '<a href="mailto:'.$encodedString.'">'.$encodedString.'</a>';
}

/**
 * Umleitung
 * ____________________________________________________________________
 */
function redirect($fname = '')
{
  header('Location: '.getProjectRootURL().'/'.$fname);
}

/**
 * Request via curl, good for proxies
 * ____________________________________________________________________
 */
function request($url)
{
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'mkProxy/0.99');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}

/**
 * get URL parameters and EP from an URL string
 * ____________________________________________________________________
 */
function getLocationParams($url)
{
  // Get target file
  $urlArr = parse_url($url);
  $pInfo = pathinfo($urlArr['path']);
  $file = $pInfo['filename'];

  // Get future GET params = query string
  $query = str_replace('&amp;', '&', $urlArr['query']);
  $q = array();
  parse_str($query, $q);

	$q['ep'] = $file;
	return $q;
}

/**
 * getRandomElement
 * ______________________________________________________________________
 */
function getRandomElementIdx($arr)
{
  $x = false;
  $max = count($arr)-1;
  if ($max > 0) {
    $x = mt_rand(0, $max);
  }
	return $x;
}
class RSC
{
public static $assets = array(
'avatar' => 'data:image/gif;base64,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',
'biggrin' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAA3NCSVQICAjb4U/gAAAA1VBMVEX////XshTVrQrXqwDTqADTsBjVrQrTqADTsBjVrQrTqADTsBjUrg/VrQrTqAD//////5n/+or/+IP/9Xv/8nLr6+v/8Gz/7mT/613/6Vf/51H/5Un/40P/4T/+3jn/3TL/2yz/2Sf/1yH41zD51ir/1Rv/0hL10CXvzzD9zw/5zhPxzCH0yxfqyy/2yArzxw/uyBrlxCfpwhjrwRHuwAfkvx26urrmvRLmuwrkuAnktwTOpwuLeyGLeBqLdBCLcwxmZjNlXyllXSZlWyBlWRllVQ9lUwhSeUxkAAAAR3RSTlMAEREREVVVVWZmZnd3d3f//////////////////////////////////////////////////////////////////////////4CyhhwAAAAJcEhZcwAACvAAAArwAUKsNJgAAAAfdEVYdFNvZnR3YXJlAE1hY3JvbWVkaWEgRmlyZXdvcmtzIDi1aNJ4AAAA5ElEQVQYlT2Q6VLCQBCER4kXSEYh5NosMSCCigeKeDQK2Wzy/o/kLFh21fzor6anqodIdNCBU7dFfzoCFtPr6RtwsgcdzIZa62FevMB34BTjTKWpUjov5mjLDcyy1Jg4Tiqbj17RkoxWJgrK0CTKFhPJ4SFLoqDX64dxqq8mHyCMVRL2Ly96QeTIo5DcVuVWVJrK1j+3bscOmLfMbJgHjdtZrFfMpZCKebX5Ap0jX+8cW2YJ+XSI+2/rnEzdLOERnQmqHakdaLsaXTyPalFzs9z3IjoG3ud3T5//3Yk8f/cf33PmFze+Jo/YnJCUAAAAAElFTkSuQmCC',
);

public static $css = array(
'var' => <<< 'CSSVAR'
--root
{
  --avatar: data:image/gif;base64,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;
  --biggrin: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAA3NCSVQICAjb4U/gAAAA1VBMVEX////XshTVrQrXqwDTqADTsBjVrQrTqADTsBjVrQrTqADTsBjUrg/VrQrTqAD//////5n/+or/+IP/9Xv/8nLr6+v/8Gz/7mT/613/6Vf/51H/5Un/40P/4T/+3jn/3TL/2yz/2Sf/1yH41zD51ir/1Rv/0hL10CXvzzD9zw/5zhPxzCH0yxfqyy/2yArzxw/uyBrlxCfpwhjrwRHuwAfkvx26urrmvRLmuwrkuAnktwTOpwuLeyGLeBqLdBCLcwxmZjNlXyllXSZlWyBlWRllVQ9lUwhSeUxkAAAAR3RSTlMAEREREVVVVWZmZnd3d3f//////////////////////////////////////////////////////////////////////////4CyhhwAAAAJcEhZcwAACvAAAArwAUKsNJgAAAAfdEVYdFNvZnR3YXJlAE1hY3JvbWVkaWEgRmlyZXdvcmtzIDi1aNJ4AAAA5ElEQVQYlT2Q6VLCQBCER4kXSEYh5NosMSCCigeKeDQK2Wzy/o/kLFh21fzor6anqodIdNCBU7dFfzoCFtPr6RtwsgcdzIZa62FevMB34BTjTKWpUjov5mjLDcyy1Jg4Tiqbj17RkoxWJgrK0CTKFhPJ4SFLoqDX64dxqq8mHyCMVRL2Ly96QeTIo5DcVuVWVJrK1j+3bscOmLfMbJgHjdtZrFfMpZCKebX5Ap0jX+8cW2YJ+XSI+2/rnEzdLOERnQmqHakdaLsaXTyPalFzs9z3IjoG3ud3T5//3Yk8f/cf33PmFze+Jo/YnJCUAAAAAElFTkSuQmCC;
}
CSSVAR,
'skin' => <<< 'CSSFILE'
img
{
  width: 500px;
}
CSSFILE,
); // close CSS array

public static $js = array(
'code' => <<< 'JSCODE'
class Greeter
{
  greet()
  {
    alert('Hello');
  }
}

let g = new Greeter();
document.addEventListener('DOMContentLoaded', () => {
  g.greet();
});
JSCODE,
); // close JS array

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

} // end of RSC class


  $css = RSC::getCSS();
  $js = RSC::getJS();
  $img = RSC::$assets['avatar'];

  $m = new \dummy\Test();
  $s = $m->getText();

  echo <<< "HTMCODE"
  <html>
    <head>
      <script>
  $js
      </script>
      <style>
  $css
      </style>
    </head>
    <body>
      <p>$s</p>
      <img src="$img">
    </body>
  </html>
  HTMCODE;



} // end of root namespace

