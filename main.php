<?php

  $css = RSC::$css['skin'];
  $js = RSC::$js['code'];
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
