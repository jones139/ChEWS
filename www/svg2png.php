<!DOCTYPE HTML>
<!-- boilerplate form example from http://www.w3schools.com/Php/showphp.asp?filename=demo_form_validation_complete -->
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 

<?php
// Configuration
define('PATH_SVG_FS','/home/graham/ChEWS/www/tmp');
define('PATH_SVG_WWW','tmp/');

// define variables and set to empty values
$wordErr = "";
$svgName = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
   if (empty($_GET["svgname"])) {
     $nameErr = "svgname is required";
   } else {
     $svgName = test_input($_GET["svgname"]);
   }   
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<img src="chews.png" alt="CHeWS Sample Image">
<h1>CHemical Element Word Solver (ChEWS)</h1>

<?php
if ($svgName!="") {
    # If we have been passed a file, try to render it
    $tmpdir = dirname(__FILE__)."/tmp";
    $svgPath = $tmpdir."/".$svgName;
    echo "<p>svgPath = ".$svgPath."</p>";
    $pngName = $svgName.".png";
    $pngPath = $tmpdir."/".$pngName;
    echo "<p>pngPath = ".$pngPath."</p>";

   
   $im = new Imagick();
   $svg = file_get_contents($svgPath);
   $im->readImageBlob($svg);
   $im->setImageFormat("png24");

   $im->writeImage($pngPath);
   $im->clear();
   $im->destroy();

    echo "<h3>svg2png Output</h3><p>";
       echo "<p>";
       echo "<h3>SVG Image</h3>";
       echo "<img src=".PATH_SVG_WWW.basename($svgName).">";
       echo "<h3>PNG Image</h3>";
       echo "<img src=".PATH_SVG_WWW.basename($pngName).">";
} else {
    echo "<p><span class=\"error\">Please enter a word above and press 'Submit'.</span></p>";
    }
?>

</body>
</html>
