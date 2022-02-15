<?php
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

?>
