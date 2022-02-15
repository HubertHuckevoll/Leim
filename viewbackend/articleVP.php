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

?>
