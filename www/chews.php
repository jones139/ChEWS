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
define('PATH_SVG_WWW','/chews/tmp/');

// define variables and set to empty values
$wordErr = "";
$word = $solution = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["word"])) {
     $nameErr = "word is required";
   } else {
     $word = test_input($_POST["word"]);
   }   
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<img src="chews.svg">
<h1>CHemical Element Word Solver (ChEWS)</h1>
<p>This tool will attempt to spell a word using chemical element symbols
and produce a periodic table style representation of the word as shown above for 'chews'.</p>
<p>The source code for the solver and this simple web interface is available on
<a href="https://github.com/jones139/ChEWS">GitHub</a></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   Enter Word: <input type="text" name="word" value="<?php echo $word;?>">   
   <input type="submit" name="submit" value="Submit"> 
</form>

<?php
if ($word!="") {
    # If we have been passed a word, try to solve it
    $tmpdir = dirname(__FILE__)."/tmp";
    $svgName = tempnam($tmpdir,"ChEWS_").".svg";
    #echo "<p>svgName = ".$svgName."</p>";

    $chewsExec = dirname(__FILE__)."/../ChEWS.py";
    # echo "<p>chewsExec = ".$chewsExec."</p>";
    $chewsCmd = $chewsExec." ".$word." --svg=".$svgName;
    # echo "<p>chewsCmd = ".$chewsCmd."</p>";
    exec($chewsCmd,$chewsOutput,$chewsRetval);

    echo "<h3>ChEWS Output</h3><p>";
    #echo "<p>chewsRetval=".$chewsRetval."</p>";
    if ($chewsRetval==0) {
       echo $chewsOutput[2];
       #foreach ($chewsOutput as $line) {
       #	       echo "chewsOutput = ".$line."<br/>";  
       #	       }
       echo "</p>";
    

       echo "<p>";
       echo "<h3>Image</h3>";
       echo "<img src=".PATH_SVG_WWW.basename($svgName).">";
    } else {
      echo "<p>No Solution Found, sorry!<br/>";
      echo "<p>chewsRetval=".$chewsRetval."</p>";
       foreach ($chewsOutput as $line) {
       	       echo "chewsOutput = ".$line."<br/>";  
       	       }
       echo "</p>";
    }    
    
} else {
    echo "<p><span class=\"error\">not trying to solve for an empty string!</span></p>";
    }
?>

</body>
</html>