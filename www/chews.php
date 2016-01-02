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
$word = "";

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
and produce a periodic table style representation of the word as shown above for 'chews'.   Not all words can be done (e.g. there is no 'J' in element symbols).  Some names that do work include 'Laura', 'Nicola', 'Sam', 'Simon'</p>
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
      #echo "<p>chewsRetval=".$chewsRetval."</p>";
      # foreach ($chewsOutput as $line) {
      # 	       echo "chewsOutput = ".$line."<br/>";  
      # 	       }
       echo "</p>";
    }    
    
} else {
    echo "<p><span class=\"error\">Please enter a word above and press 'Submit'.</span></p>";
    }
?>

<h1>How it Works</h1>
<h2>Word Solver</h2>
<p>The ChEWS solver is written in python (file ChEWS.py), and uses the svgwrite library to produce the output images.</p>
<ul>
<li>We have a database of all the chemical elements and their symbols.</li>
<li>Starting with the first letter of the required word, we search through the elements to find the first one that matches the required letter, and add that element to the output list.</li>
<li>We work our way through each remaining letter in the target word until we find the solution.</li>
<li>If we have added an element that has added a second letter that does not match the word, we remove it, and look for another possibility.</li>
<li>If we get to the end of the list of elements without finding a match, we remove the previous element, and try searching again.</li>
<li>If we remove all elements and can't find a solution, we have failed and give up with the "No Solution Found" message.</li>
<li>Once we have found a solution, we draw a periodic table style representation of the elements in an output file (a Sclable Vector Graphics (SVG) file).</li>
</ul>
<h2>Web Interface</h2>
The web interface is very simple - it is written in php and displays an html form to request a word.   Once a word has been entered (and the 'submit' button pressed), it calls the ChEWS.py script to look for the solution, and request a SVG image of the solution, which are displayed on the web page.</p>
<p>The source code for the solver and this simple web interface is available on
<a href="https://github.com/jones139/ChEWS">GitHub</a></p>

<h2>Why?</h2>
<p>Why bother making this?  The simple answer is 'because I can' - we spent some time before christmas solving some words manually, and I would always prefer to put effort into teaching a computer to do the tedious work rather than doing it myself (even though it probably took 5 times longer to teach the computer than I spend doing it myself!).</p>
<p>There are other tools like this available on the internet, but they do not publish the source code, and I thought some people may like to see how these things are done, so I wrote another version.....</p>

<h2>Contact</h2>
<p>
If you have any queries, please email grahamjones139@gmail.com
</p>


</body>
</html>
