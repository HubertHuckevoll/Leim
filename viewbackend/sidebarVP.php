<?php
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

?>
