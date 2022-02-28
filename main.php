<?php

  $css = RSC::getCSS();
  $js = RSC::getJS();
  $img = RSC::$assets['avatar'];

  $m = new Test();
  $s = $m->getText();

  echo <<< "HTMCODE"
  <html>
    <head>
      <script>
  $js
      </script>
      <style>
  $css
      </style>
    </head>
    <body>
      <p>$s</p>
      <img src="$img">
    </body>
  </html>
  HTMCODE;

?>
