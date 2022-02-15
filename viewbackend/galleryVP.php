<?php
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

?>
