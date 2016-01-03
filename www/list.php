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
#######################
# Database Statistics
#######################
include 'database.php';
$stats = getStatistics();
$successWords = getWords(True);
$failWords = getWords(False);

echo "<h3> Database Contains ".$stats['success']." words with solutions</h3>";
$lastWord = "";
foreach ($successWords as $wordData) {
   if ($lastWord!="")
       if (substr($lastWord,0,1) != substr($wordData[0],0,1)) 
           echo "<br/>";
       else
           echo ", ";
   echo $wordData[0]." (".$wordData[1].")";
   $lastWord = $wordData[0];
}

echo "<h3> Database Contains ".$stats['fail']." words without solutions</h3>";
$lastWord = "";
foreach ($failWords as $wordData) {
   if ($lastWord!="")
       if (substr($lastWord,0,1) != substr($wordData[0],0,1)) 
           echo "<br/>";
       else
           echo ", ";
   echo $wordData[0]." (".$wordData[1].")";
   $lastWord = $wordData[0];
}


echo "<h3>Images Currently Stored on Server</h3>";
// Configuration
define('PATH_SVG_FS','/home/graham/ChEWS/www/tmp');
define('PATH_SVG_WWW','tmp/');
    $tmpdir = dirname(__FILE__)."/tmp";
  if ($dh = opendir($tmpdir)){
    while (($file = readdir($dh)) !== false){
      $ext = pathinfo($file,PATHINFO_EXTENSION);
      #echo "filename:" . $file . " - ext = ".$ext."<br>";
      if ($ext == "svg") {
         echo "<img src='".PATH_SVG_WWW.$file."'><br/>";
      }
    }
    closedir($dh);
  }
?>
</body>
</html>
