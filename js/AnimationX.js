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