<?php

/*----------------------------------------------------------------
 * sl_redirect
 *----------------------------------------------------------------*/

function sl_redirect($location, $movedPermanently)
{
  $protocol=$_SERVER["SERVER_PROTOCOL"];
  if('HTTP/1.1'!=$protocol && 'HTTP/1.0'!=$protocol)
    $protocol='HTTP/1.0';

  $status=$movedPermanently ? 301 : 302;
  $text=$movedPermanently ? 'Moved Permanently' : 'Found';

  header("$protocol $status $text", true, $status);
  //header("Status: $status $text");
  header('Location: '.$location);
}

/*----------------------------------------------------------------
 * sl_redirect_exit
 *----------------------------------------------------------------*/

function sl_redirect_exit($location, $movedPermanently)
{
  $protocol=$_SERVER["SERVER_PROTOCOL"];
  if('HTTP/1.1'!=$protocol && 'HTTP/1.0'!=$protocol)
    $protocol='HTTP/1.0';

  $status=$movedPermanently ? 301 : 302;
  $text=$movedPermanently ? 'Moved Permanently' : 'Found';

  $escaped=esc_attr($location);
  $content=
<<<EOD
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html>
<head>
  <title>$status $text</title>
</head>
<body>
  <h1>$text</h1>
  <p>The document has moved <a href="$escaped">here</a>.</p>
  </body>
</html>
EOD;

  header("$protocol $status $text", true, $status);
  //header("Status: $status $text");
  header('Location: '.$location);
  header('Content-Type: text/html');
  echo content;
  exit();
}

?>