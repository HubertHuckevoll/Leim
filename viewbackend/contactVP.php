<?php
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

?>
