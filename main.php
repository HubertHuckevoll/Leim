<?php

  $css = RSC::$css['skin'];
  $js = RSC::$js['code'];
  $img = RSC::$assets['avatar'];

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
      <p>Hä?</p>
      <img src="$img">
    </body>
  </html>
  HTMCODE;
?>
