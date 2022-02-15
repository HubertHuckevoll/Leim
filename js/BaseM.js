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

