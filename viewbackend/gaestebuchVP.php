<?php
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

?>
