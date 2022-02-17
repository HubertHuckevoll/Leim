<?php

namespace view\generic\page;
class articleVP extends talVP
{
  public $viewHints = array();

  /**
   * metadata
   * ________________________________________________________________
   */
  protected function additionalHeadData()
  {
    $pv = new \cb\view\fragment\cbArticleMetadataVF($this->ep, $this->hook, $this->linker);
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $pv = new \cb\view\fragment\cbArticleClassicStyle2VF($this->ep, $this->hook, $this->linker);
    $pv->viewHints = $this->viewHints;
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }
}

namespace view\generic\page;
class searchVP extends talVP
{
  // dont't add these to the constructor.
  // all views should have the same construction api
  // so we can instantiate them programmatically
  public $articleHook = '';

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $pv = new \cb\view\fragment\cbSearchVF($this->ep, $this->hook, $this->linker);
    $pv->data = $this->data['results'];

    return $pv->render();
  }

  /**
   * render cbBox
   * ________________________________________________________________
   */
  public function drawAjax()
  {
    echo $this->mainContent();
  }

}

namespace view\generic\page;
class gaestebuchVP extends talVP
{
  // dont't add these to the constructor.
  // all views should have the same construction api
  // so we can instantiate them programmatically
  public $viewHints = array();
  public $viewOp = '';

  /**
   *
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $html = '';

    $pv = new \cb\view\fragment\cbArticleClassicStyle1VF($this->ep, $this->hook, $this->linker);
    $pv->viewHints = $this->viewHints;
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }

  /**
   *
   * ________________________________________________________________
   */
  protected function additionalContent()
  {
    $pv = new \cb\view\fragment\cbCommentsVF($this->ep, $this->hook, $this->linker);
    $pv->addDataFromArray($this->data['comments']['model']);
    $pv->addDataFromArray($this->data['comments']['meta']);

    $viewOp = $this->data['comments']['meta']['viewOp'];

    return $this->exec($pv, $viewOp);
  }

  /**
   *
   * ________________________________________________________________
   */
  public function drawAjax()
  {
    echo $this->additionalContent();
  }
}

namespace view\generic\page;
class galleryVP extends talVP
{
  // dont't add these to the constructor
  // all views should have the same construction api
  // so we can instantiate them programmatically
  public $articleHook = '';

  /**
   * render cbBox
   * ________________________________________________________________
   */
  public function mainContent()
  {
    $pv = new \cb\view\fragment\cbGalleryVF($this->ep, $this->hook, $this->articleHook, $this->linker);
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }
}

namespace view\generic\page;
class contactVP extends talVP
{
  // dont't add this to the constructor
  // all views should have the same construction api
  // so we can instantiate them programmatically
  public $viewHints = array();

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $html = '';

    $pv = new \cb\view\fragment\cbArticleClassicStyle1VF($this->ep, $this->hook, $this->linker);
    $pv->viewHints = $this->viewHints;
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function additionalContent()
  {
    $pv = new \cb\view\fragment\cbContactVF($this->ep, $this->hook, $this->linker);
    $pv->addDataFromArray($this->data['contact']['model']);
    $pv->addDataFromArray($this->data['contact']['meta']);

    return $pv->render();
  }
}

namespace view\generic\page;
class indexVP extends talVP
{
  public $articleHook = '';

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $pv = new \cb\view\fragment\cbBoxVF($this->ep, $this->hook, $this->articleHook, $this->linker);
    $pv->addDataFromArray($this->data['articles']['model']);
    $pv->addDataFromArray($this->data['articles']['meta']);

    return $pv->render();
  }
}

namespace view\generic\page;
class schlaglVP extends talVP
{
  public $viewHints = array();

  /**
   * metadata
   * ________________________________________________________________
   */
  protected function additionalHeadData()
  {
    $pv = new \cb\view\fragment\cbArticleMetadataVF($this->ep, $this->hook, $this->linker);
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }

  /**
   * render cbBox
   * ________________________________________________________________
   */
  protected function mainContent()
  {
    $pv = new \cb\view\fragment\cbArticleClassicStyle0VF($this->ep, $this->hook, $this->linker);
    $pv->viewHints = $this->viewHints;
    $pv->addDataFromArray($this->data['article']['model']);
    $pv->addDataFromArray($this->data['article']['meta']);

    return $pv->render();
  }
}

namespace view\generic\page;
class sidebarVP extends \cb\view\page\cbPageVP
{
  /**
   * render sidebarV
   * _________________________________________________________________
   */
  public function drawPage($errMsg = '')
  {
    $pv = new \cb\view\fragment\cbArticleTeasersVF($this->ep, 'sidebar', $this->linker);
    $pv->data = $this->data;

    echo $pv->render();
  }

}

error_reporting(E_ALL ^ E_NOTICE);
require_once($_SERVER["DOCUMENT_ROOT"].'/cardboard/cbLoader.php');
require_once($_SERVER["DOCUMENT_ROOT"].'/cardboard.kupc/autoload.php');

$k = new cbIterateM();

echo '<pre>';

$k->start(function($payload)
{

  if ($payload['item'] == 'article.txt')
  {
    $source = $payload['rootedPathToItem'];
    
    $parts = trim($payload['parentFolder'], '/');
    $parts = explode('/', $parts);
    $articleBox = $parts[array_key_first($parts)];
    $articleName = $parts[array_key_last($parts)];
    $targetPath = getPathFS(CB_DATA_ROOT.$articleBox.'/text/');
    @mkdir($targetPath);
    $target = $targetPath.$articleName.'.txt';

    echo $source.' '.$target.'<br>';
    @copy($source, $target);
  }

});

echo '</pre>';

require($_SERVER['DOCUMENT_ROOT'].'/cardboard/helpers.php');
  redirect('');

namespace
{
  class RSC
  {
    public static $assets = array(
      'biggrin' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAA3NCSVQICAjb4U/gAAAA1VBMVEX////XshTVrQrXqwDTqADTsBjVrQrTqADTsBjVrQrTqADTsBjUrg/VrQrTqAD//////5n/+or/+IP/9Xv/8nLr6+v/8Gz/7mT/613/6Vf/51H/5Un/40P/4T/+3jn/3TL/2yz/2Sf/1yH41zD51ir/1Rv/0hL10CXvzzD9zw/5zhPxzCH0yxfqyy/2yArzxw/uyBrlxCfpwhjrwRHuwAfkvx26urrmvRLmuwrkuAnktwTOpwuLeyGLeBqLdBCLcwxmZjNlXyllXSZlWyBlWRllVQ9lUwhSeUxkAAAAR3RSTlMAEREREVVVVWZmZnd3d3f//////////////////////////////////////////////////////////////////////////4CyhhwAAAAJcEhZcwAACvAAAArwAUKsNJgAAAAfdEVYdFNvZnR3YXJlAE1hY3JvbWVkaWEgRmlyZXdvcmtzIDi1aNJ4AAAA5ElEQVQYlT2Q6VLCQBCER4kXSEYh5NosMSCCigeKeDQK2Wzy/o/kLFh21fzor6anqodIdNCBU7dFfzoCFtPr6RtwsgcdzIZa62FevMB34BTjTKWpUjov5mjLDcyy1Jg4Tiqbj17RkoxWJgrK0CTKFhPJ4SFLoqDX64dxqq8mHyCMVRL2Ly96QeTIo5DcVuVWVJrK1j+3bscOmLfMbJgHjdtZrFfMpZCKebX5Ap0jX+8cW2YJ+XSI+2/rnEzdLOERnQmqHakdaLsaXTyPalFzs9z3IjoG3ud3T5//3Yk8f/cf33PmFze+Jo/YnJCUAAAAAElFTkSuQmCC',
    );

    public static $css = array(
      'var' => <<< 'CSSVAR'
--root
{
  --biggrin: data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAA3NCSVQICAjb4U/gAAAA1VBMVEX////XshTVrQrXqwDTqADTsBjVrQrTqADTsBjVrQrTqADTsBjUrg/VrQrTqAD//////5n/+or/+IP/9Xv/8nLr6+v/8Gz/7mT/613/6Vf/51H/5Un/40P/4T/+3jn/3TL/2yz/2Sf/1yH41zD51ir/1Rv/0hL10CXvzzD9zw/5zhPxzCH0yxfqyy/2yArzxw/uyBrlxCfpwhjrwRHuwAfkvx26urrmvRLmuwrkuAnktwTOpwuLeyGLeBqLdBCLcwxmZjNlXyllXSZlWyBlWRllVQ9lUwhSeUxkAAAAR3RSTlMAEREREVVVVWZmZnd3d3f//////////////////////////////////////////////////////////////////////////4CyhhwAAAAJcEhZcwAACvAAAArwAUKsNJgAAAAfdEVYdFNvZnR3YXJlAE1hY3JvbWVkaWEgRmlyZXdvcmtzIDi1aNJ4AAAA5ElEQVQYlT2Q6VLCQBCER4kXSEYh5NosMSCCigeKeDQK2Wzy/o/kLFh21fzor6anqodIdNCBU7dFfzoCFtPr6RtwsgcdzIZa62FevMB34BTjTKWpUjov5mjLDcyy1Jg4Tiqbj17RkoxWJgrK0CTKFhPJ4SFLoqDX64dxqq8mHyCMVRL2Ly96QeTIo5DcVuVWVJrK1j+3bscOmLfMbJgHjdtZrFfMpZCKebX5Ap0jX+8cW2YJ+XSI+2/rnEzdLOERnQmqHakdaLsaXTyPalFzs9z3IjoG3ud3T5//3Yk8f/cf33PmFze+Jo/YnJCUAAAAAElFTkSuQmCC;
}
CSSVAR,
      'style' => <<< 'CSSFILE'
:root {
  --anchor-color: rgb(204, 51, 51);
  --anchor-color-lookalike: rgb(170, 17, 17);
  --frame-color: rgb(148, 148, 148);
  --dark-bg: rgba(153, 153, 153, .6);
  --light-bg: rgba(221, 221, 221, .8);
  --dark-headline: rgb(77, 77, 77);
  --light-headline: rgb(255, 255, 255);
  --light-text: rgb(255, 255, 255);
  --dark-text: rgb(26, 26, 26);
}

html
{
  padding: 0;
  margin: 0;
  font-size: 100%;
  line-height: 150%;
}

body
{
  margin: 0;
  width: 1140px;
  margin-right: auto;
  margin-left: auto;
  color: var(--dark-text);
  font-family: Verdana, Arial, Helvetica, sans-serif;
}

ul
{
  margin: 0;
  margin-bottom: 5px;
  margin-top: 5px;
}

li
{
  list-style-type: disc;
  padding-left: 10px;
  margin-left: 20px;
}

/* Headings */
h1, h2, h3, h4, h5, h6
{
  font-weight: normal;
}

acronym
{
  cursor: help;
}

/* Standard Link Styles ----------------------------------------------------------*/
a, a:active
{
  color: var(--anchor-color);
  text-decoration: none;
  cursor: pointer;
}

a:hover, a:active:hover, a:visited:hover
{
  text-decoration: underline;
}

/* Page Banner Styles ----------------------------------------------------------*/
#banner
{
  margin-top: 1.5%;
}

#banner h1
{
  color: var(--anchor-color);
  margin: 0px;
  font-size: 2rem;
}

#banner h2
{
  color: var(--dark-headline);
  margin-bottom: 15px;
  font-size: 1.1rem;
  font-weight: bold;
}

/* Navigation Bar ----------------------------------------------------------*/
#navigation
{
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: linear-gradient(0deg, var(--dark-bg), var(--light-bg));
}

#linkBox
{}

/* Navigation Link Styles */
#linkBox a, #linkBox a:active, #linkBox a:visited
{
  display: inline-block;
  text-decoration: none;
  padding-left: 7px;
  padding-right: 7px;
  border-left: 1px solid var(--light-bg);
  border-right: 1px solid var(--dark-bg);
  color: var(--dark-text);
  background-color: transparent;
  height: 35px;
  line-height: 35px;
  font-weight: bold;
}

#linkBox a:hover, #linkBox a:visited:hover
{
  color: var(--anchor-color-lookalike); /* miracles of perception: to appear somewhat like #CC3333, this needs to be #AA1111*/
}

/* Search Styles  ----------------------------------------------------------*/
#searchBox {
  margin-right: 5px;
}

#searchBox button {
  font-size: .7rem;
}

/* Main Content _____________________________________________________*/
main
{
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

/* Sidebar ----------------------------------------------------------*/
#sidebar
{
  margin: 0px;
  padding: 0px;
  width: 35%;
  hyphens: auto;
}

#sidebar a, #sidebar a:active, #sidebar a:visited
{
  color: var(--dark-text);
  font-weight: bold;
  text-decoration: none;
}

#sidebar a img
{
}

#sidebar a:hover, #sidebar a:hover:active
{
  color: var(--dark-text);
  text-decoration: underline;
}

.sidebarMenuBox
{
  padding: 0px;
  border: 1px solid var(--dark-bg);
  margin-bottom: 20px;
  height: 100%;
  background-color: var(--light-bg);
  background-image: url(../img/sidebarBG.gif);
  background-repeat: no-repeat;
  background-attachment: scroll;
  background-size: cover;
}

.sidebarMenuBoxContent
{
  padding: 4px;
}

#hotStuffBox .sidebarMenuBoxContent
{
  display: flex;
  flex-wrap: wrap;
  justify-content: space-evenly;
  align-items: center;
}

.sidebarMenuBox h3
{
  text-align: center;
  height: 30px;
  line-height: 30px;
  margin: 0px;
  font-size: 0.95rem;
  font-weight: bold;
  color: var(--dark-headline);
  background: linear-gradient(0deg, var(--dark-bg), var(--light-bg));
}

.sidebarMenuBox li, ul
{
  list-style-type: none;
  padding: 0px;
  margin: 0px;
  margin-bottom: 3px;
  margin-top: 3px;
  margin-left: 3px;
}

.sidebarMenuContentBox
{
  text-align: left;
  padding: 5px;
  clear: both;
}

.sidebarMenuContentBox .abcArticleOverviewAbstractMoreLink
{
  display: inline;
  margin: 0px;
  padding: 0px;
}

/**
 * cbArticleTeasersVF
 * __________________________________________________________________
 */
figure.cbArticleTeaser
{
  width: 30%;
}

figure .cbArticleTeaserImg
{
  width: 100%;
}

/* Content ----------------------------------------------------------*/
#content
{
  width: 60%;
  background-repeat: no-repeat;
  background-attachment: scroll;
  background-position: left bottom;
  hyphens: auto;
}

.abcArticleOverviewCont
{
  /*
  padding: 10px;
  box-shadow: 2px 2px 5px var(--dark-bg);
  */
}

.abcArticleOverviewDate
{
  display: none;
}

.abcArticleOverviewImg img
{
  box-shadow: 2px 2px 5px var(--dark-bg);
}

.TAL-articles .articleParagraphImg {
  margin-top: 7px;
}

.TAL-articles .articleParagraph {
  margin-bottom: 20px;
}

.TAL-pages .articleParagraphText {
  margin-bottom: 10px;
}

.TAL-pages .articleDate {
  display: none;
}

#content table
{
  margin-top: 20px;
  border: 1px solid var(--frame-color);
  margin-bottom: 25px;
  clear: both;
}

#content table th
{
  background-color: var(--light-bg);
  padding: 0px;
  padding: 4px;
  text-align: left;
  font-weight: bold;
  border-bottom: 1px solid var(--frame-color);
  vertical-align: top;
  font-size: 0.9rem;
}

#content table td
{
  background-color: var(--light-bg);
  vertical-align: top;
  width: 25%;
  text-align: left;
  padding: 5px;
  font-size: 0.8rem;
  line-height: 0.1rem;
}

#pageHeadline
{
  border-bottom: 1px solid var(--light-bg);
  font-size: 1.1rem;
  font-weight: bold;
  margin-bottom: 20px;
  padding-bottom: 10px;
  padding-top: 10px;
}

.Schlaglichter .articleParagraphImgCont {
  display: none;
}

#schlagl {
 padding: 10px;
 text-align: center;
}

#schlagl div {
  margin-bottom: 10px;
  margin-top: 10px;
  font-style: italic;
}

#schlagl img {
  box-shadow: 2px 2px 5px var(--dark-bg);
  max-width: 100%;
}

#schlagl img:hover {
  cursor: pointer;
}

#schlagl #slideStatus {
  text-align: center;
  font-size: .8rem;
}

.TAL-downloads .abcArticleOverviewCont
{
  clear: both;
  display: list-item;
  margin-left: 25px;
}

/* footer styles ----------------------------------------------------------*/
#footer
{
  display: flex;
  margin-top: 10px;
  margin-bottom: 10px;
  padding-top: 10px;
  border-top: 1px solid var(--light-bg);
  font-size: 0.8rem;
  line-height: 1.35rem;
  color: var(--dark-bg);
  justify-content: space-between;
  hyphens: auto;
}

#footerLeft
{
  text-align: left;
  padding-bottom: 20px;
}

#footerRight
{
  text-align: right;
}
CSSFILE,
    );

    public static $js = array(
      'FormoBase' => <<< 'JSCODE'
"use strict";

class FormoBase extends HTMLElement
{
  constructor()
  {
    super();

    this.showHideCSS = {
      showClass: 'element--show',
      hideClass: 'element--hide'
    };
  }

  showElement()
  {
    this.show(this, this.showHideCSS);
  }

  hideElement()
  {
    this.hide(this, this.showHideCSS);
  }
}

Object.assign(FormoBase.prototype, Events);
Object.assign(FormoBase.prototype, AnimationX);
JSCODE,
      'FormoSlider' => <<< 'JSCODE'
"use strict";

class FormoSlider extends FormoBase
{
  constructor()
  {
    super();

    this.innerHTML = '<span class="formoSliderLabel"></span>'+
                     '<div class="formoSliderWrapper">'+
                       '<button class="formoSliderDec">-</button>'+
                       '<input class="formoSliderRange" type="range" max="" min="" step="" value="">'+
                       '<input class="formoSliderMirror" type="number" max="" min="" step="" value="">'+
                       '<button class="formoSliderInc">+</button>'+
                     '</div>';
  }

  connectedCallback()
  {
    this.querySelector('.formoSliderRange').addEventListener('change', this.valueChanged.bind(this));
    this.querySelector('.formoSliderMirror').addEventListener('change', this.valueChanged.bind(this));

    this.querySelector('.formoSliderDec').addEventListener('click', this.decButtonPressed.bind(this));
    this.querySelector('.formoSliderInc').addEventListener('click', this.incButtonPressed.bind(this));

    this.updateUI();
  }

  updateUI()
  {
    this.querySelector('.formoSliderLabel').innerHTML = this.label;

    this.querySelector('.formoSliderRange').setAttribute('max', this.max);
    this.querySelector('.formoSliderRange').setAttribute('min', this.min);
    this.querySelector('.formoSliderRange').setAttribute('step', this.step);
    this.querySelector('.formoSliderRange').setAttribute('value', this.value);
    this.querySelector('.formoSliderRange').value = this.value;

    this.querySelector('.formoSliderMirror').setAttribute('max', this.max);
    this.querySelector('.formoSliderMirror').setAttribute('min', this.min);
    this.querySelector('.formoSliderMirror').setAttribute('step', this.step);
    this.querySelector('.formoSliderMirror').setAttribute('value', this.value);
    this.querySelector('.formoSliderMirror').value = this.value;
  }

  boundsCheck(val)
  {
    val = (val < this.min) ? this.min : val;
    val = (val > this.max) ? this.max : val;

    return val;
  }

  /*
    calling "this.value = val" invokes the setter,
    which calls setAttribute,
    which calls attributeChangedCallback,
    which calls updateUI,
    which distributes our changes to all UI elements,
    which is a mess...
  */
  valueChanged(ev)
  {
    let val = parseInt(ev.target.value);
    val = this.boundsCheck(val);
    this.value = val;
    this.emit('formoSliderChange', {'value': val});

    ev.preventDefault();
    ev.stopPropagation();
    return false;
  }

  decButtonPressed(ev)
  {
    let val = this.value - this.step;
    val = this.boundsCheck(val);
    this.value = val;
    this.emit('formoSliderChange', {'value': val});

    ev.preventDefault();
    return false;
  }

  incButtonPressed(ev)
  {
    var val = this.value + this.step;
    val = this.boundsCheck(val);
    this.value = val;
    this.emit('formoSliderChange', {'value': val});

    ev.preventDefault();
    return false;
  }

  static get observedAttributes()
  {
    return ['label', 'value', 'step', 'max', 'min'];
  }

  // Reflect attribute changes to UI
  attributeChangedCallback(name, oldValue, newValue)
  {
    this.updateUI();
  }

  // Reflect property changes to attributes
  get label()
  {
    return this.getAttribute('label');
  }

  get value()
  {
    return parseInt(this.getAttribute('value'));
  }

  get step()
  {
    return parseInt(this.getAttribute('step'));
  }

  get min()
  {
    return parseInt(this.getAttribute('min'));
  }

  get max()
  {
    return parseInt(this.getAttribute('max'));
  }

  set label(newValue)
  {
    this.setAttribute('label', newValue);
  }

  set value(newValue)
  {
    this.setAttribute('value', newValue);
  }

  set step(newValue)
  {
    this.setAttribute('step', newValue);
  }

  set min(newValue)
  {
    this.setAttribute('min', newValue);
  }

  set max(newValue)
  {
    this.setAttribute('max', newValue);
  }

}

Object.assign(FormoSlider.prototype, Events);
JSCODE,
      'FormoDualButton' => <<< 'JSCODE'
"use strict";

class FormoDualButton extends FormoBase
{
  constructor()
  {
    super();
    this.innerHTML = '<button value=""></button>';
  }

  connectedCallback()
  {
    this.querySelector('button').addEventListener('click', this.buttonPressed.bind(this));
    this.updateUI();
  }

  updateUI()
  {
    this.querySelector('button').innerHTML = this.caption;
    this.querySelector('button').value = this.value;
  }

  buttonPressed(ev)
  {
    this.value = (this.value == "1") ? "2" : "1";
    this.emit('formoDualButtonClick', {'caption': this.caption, 'value': this.value});

    ev.preventDefault();
    return false;
  }

  static get observedAttributes()
  {
    return ['value'];
  }

  // Reflect attribute changes to properties and UI
  attributeChangedCallback(name, oldValue, newValue)
  {
    this.updateUI();
  }

  // Reflect property changes to attributes
  get caption()
  {
    if (this.value == "1")
    {
      return this.getAttribute('caption1');
    }
    else
    {
      return this.getAttribute('caption2');
    }
  }

  get value()
  {
    return this.getAttribute('value');
  }

  set caption(newValue)
  {
    if (this.value == "1")
    {
      this.setAttribute('caption1', newValue);
    }
    else
    {
      this.setAttribute('caption2', newValue);
    }
  }

  set value(newValue)
  {
    this.setAttribute('value', newValue);
  }

}

Object.assign(FormoDualButton.prototype, Events);
JSCODE,
      'BaseC' => <<< 'JSCODE'
"use strict";

/**
 * at the moment this is just a stub
 */

class BaseC
{
  constructor()
  {}
}

Object.assign(BaseC.prototype, Events);
JSCODE,
      'BaseM' => <<< 'JSCODE'
"use strict";

class BaseM
{
  constructor()
  {
  }

  request(urlStr, succFunc)
  {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = () =>
    {
      if (xhttp.readyState == 4 && xhttp.status == 200) {
        succFunc(JSON.parse(xhttp.responseText));
      }
    };
    xhttp.open("GET", urlStr, true);
    xhttp.send();
  }
}

Object.assign(BaseM.prototype, Events);
JSCODE,
      'FormoTabbox' => <<< 'JSCODE'
"use strict";

class FormoTabbox extends FormoBase
{

  constructor()
  {
    super();
  }

  connectedCallback()
  {
    this.addEventListener('click', this.tabstripeClicked.bind(this));
  }

  static get observedAttributes()
  {
    return ['active'];
  }

  // switch the tab when the "active" attribut changes
  attributeChangedCallback(name, oldValue, newValue)
  {
    this.setActiveTab();
  }

  // we don't keep a list of internal properties, we just use the attributes
  get active()
  {
    return this.getAttribute('active');
  }

  set active(newVal)
  {
    this.setAttribute('active', newVal);
  }

  get tabbox()
  {
    return this.getAttribute('tabbox');
  }

  set tabbox(newVal)
  {
    this.setAttribute('tabbox', newVal);
  }

  tabstripeClicked(ev)
  {
    const tab = ev.target.closest('li');
    const tabId = tab.getAttribute('tab');
    this.active = tabId; // calls the setter, calls attributeChangedCallback, calls setActiveTab...
  }

  setActiveTab()
  {
    let tabbox = document.getElementById(this.tabbox);
    let nodes = null;
    let oldTab = null;
    let newTab = null;

    // Tabstripe: Unset old "active tab"
    nodes = this.querySelectorAll("li");
    nodes.forEach((node) =>
    {
      if (node.classList.contains('formoTabStripeActive') === true)
      {
        oldTab = node;
        oldTab.classList.remove("formoTabStripeActive");
      }
    });

    // Tabbox: temporarily hide all tab cards
    nodes = tabbox.querySelectorAll(".formoTab");
    nodes.forEach((node) =>
    {
      node.style.display = 'none';
    });

    // Tabstripe: set new active tab; Tabbox: set tab card
    newTab = this.querySelector('[tab="'+this.active+'"]');
    newTab.classList.add('formoTabStripeActive');
    document.getElementById(this.active).style.display = 'block';

    this.emit('formoTabChange', {'oldTab': oldTab, 'newTab': newTab});
  }

}

Object.assign(FormoTabbox.prototype, Events);
JSCODE,
      'AnimationX' => <<< 'JSCODE'
/**
 * Animation Mixin
 * a class for all your dynamic CSS needs.
 */

let AnimationX =
{

  hide: function(elem, css)
  {
    let nodes = AnimationX.normalizeTarget(elem);
    var promises = [];

    nodes.forEach((node) =>
    {
      let elemP = new Promise(function(resolve, reject)
      {
        //console.log('Hiding:' + node.tagName);
        node.ontransitionend = () =>
        {
          //console.log('Resolved Hide:' + node.tagName);
          resolve();
        }
        node.classList.remove(css.showClass);
        node.classList.add(css.hideClass);
      });

      promises.push(elemP);
    });

    return Promise.all(promises);
  },

  show: function(elem, css)
  {
    let nodes = AnimationX.normalizeTarget(elem);
    var promises = [];

    nodes.forEach((node) =>
    {
      let elemP = new Promise(function(resolve, reject)
      {
        //console.log('Showing:' + node.tagName);
        node.ontransitionend = () =>
        {
          //console.log('Resolved Show:' + node.tagName);
          resolve();
        }
        node.classList.remove(css.hideClass);
        node.classList.add(css.showClass);
      });

      promises.push(elemP);
    });

    return Promise.all(promises);
  },

  normalizeTarget: function(target)
  {
    // target should be a NodeList/Array
    // if target != string and != object we assume a NodeList/Array
    // and don't need to do anything
    let nodes = null;

    if (typeof(target) == 'string')
    {
      nodes = document.querySelectorAll(target);
    }
    else if (typeof(target) == 'object')
    {
      nodes = [target];
    }

    return nodes;
  },

  wait: async function(ms)
  {
    return new Promise((resolve, reject) =>
    {
      let timerID = setTimeout(resolve, ms);
    });
  }

}
JSCODE,
      'BaseV' => <<< 'JSCODE'
"use strict";

class BaseV
{
  constructor()
  {
  }
}

Object.assign(BaseV.prototype, Events);
Object.assign(BaseV.prototype, AnimationX);
JSCODE,
      'Events' => <<< 'JSCODE'
"use strict";

/**
 * Mixin
 * contains our event handling magic, lel
 */

let Events =
{
  on(eventTypeStr, ...restArgs)
  {
    let queryStr = '';
    let callback = null;

    // add global variables if not defined yet
    if (window.frontschweine === undefined)
    {
      window.frontschweine = {
        eventTypeIsHandled: {},
        actions: []
      }
    }

    switch (restArgs.length)
    {
      case 1:
        queryStr = null;
        callback = restArgs[0];
      break;

      case 2:
        queryStr = restArgs[0];
        callback = restArgs[1];
      break;
    }

    if ((window.frontschweine.eventTypeIsHandled[eventTypeStr] !== true))
    {
      window.addEventListener(eventTypeStr, this.doHandle.bind(this), false);
      window.frontschweine.eventTypeIsHandled[eventTypeStr] = true;
    }

    window.frontschweine.actions.push(
    {
      'eventTypeStr': eventTypeStr,
      'queryStr': queryStr,
      'callback': callback
    });
  },

  doHandle(ev)
  {
    for (var z = 0; z < window.frontschweine.actions.length; z++)
    {
      if (window.frontschweine.actions[z].eventTypeStr == ev.type)
      {
        if (window.frontschweine.actions[z].queryStr !== null)
        {
          let nodes = document.querySelectorAll(window.frontschweine.actions[z].queryStr);

          nodes.forEach((node) =>
          {
            if (node === ev.target)
            {
              window.frontschweine.actions[z].callback(ev);
            }
          });
        }
        else
        {
          window.frontschweine.actions[z].callback(ev);
        }
      }
    }
  },

  emit(eventType, payload = null)
  {
    // set up event
    let evDetails =
    {
      detail: {
        payload: payload,
        eventOrigin: null
      },
      bubbles: true,
      cancelable: true
    }

    // setup event origin
    // if this is a custom element it has dispatchEvent and should be source
    // if this is a different, non visual, object we use the window object as source
    let evOriginObj = (this.dispatchEvent) ? this : window;
    evDetails.detail.eventOrigin = evOriginObj;

    // dispatch Event
    let ev = new CustomEvent(eventType, evDetails);
    evOriginObj.dispatchEvent(ev);
  }

}
JSCODE,
    );
  }
}

var_dump(RSC::$css);


?>