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
