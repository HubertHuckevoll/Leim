<?php
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

?>
