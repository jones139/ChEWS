<!DOCTYPE HTML>
<!-- boilerplate form example from http://www.w3schools.com/Php/showphp.asp?filename=demo_form_validation_complete -->
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 



<img src="chews.png" alt="CHeWS Sample Image">
<h1>CHemical Element Word Solver (ChEWS)</h1>

<?php
// Configuration
define('PATH_SVG_FS','/home/graham/ChEWS/www/tmp');
define('PATH_SVG_WWW','tmp/');
    $tmpdir = dirname(__FILE__)."/tmp";
  if ($dh = opendir($tmpdir)){
    while (($file = readdir($dh)) !== false){
      $ext = pathinfo($file,PATHINFO_EXTENSION);
      #echo "filename:" . $file . " - ext = ".$ext."<br>";
      if ($ext == "svg") {
         echo "<img src='".PATH_SVG_WWW.$file."'>";
      }
    }
    closedir($dh);
  }

</body>
</html>
