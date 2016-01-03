<?php
#include 'config.php';

$debug = False;

function connectToDb() {
	 # Returns a connection to the database, or False if connection fails.
	 include 'config.php';
	 global $debug;
	 if ($debug) echo "<br/>";
	 if ($debug) echo "servername=".$servername."<br/>";
	 if ($debug) echo "dbuser=".$dbuser."<br/>";
	 if ($debug) echo "dbpasswd=".$dbpasswd."<br/>";
	 if ($debug) echo "database=".$database."<br/>";
	 $conn = new mysqli($servername,
			$dbuser, $dbpasswd, $database);
	if ($conn->connect_error) {
	   echo "error connecting to database<br/>";
	   die("database connection failed: ".$conn->connect_error);
	   return(False);
	   } else {
	     if ($debug) echo "Connected to Database OK<br/>";
	     return($conn);
           }
}

function isWordInDb($word) {
	 # return the number of times the word appears in
	 # the db (or 0 = false if it does not).
	 global $debug;
	 $word = strtolower($word);
	 if ($debug) echo "<br/>isWordInDb() - word=".$word."<br/>";
	 $conn = connectToDb();
	 $sql = "select count(word) from words where word=?";
	 $smt = $conn->prepare($sql);
	 $smt->bind_param("s",$word);	 
	 if ($smt->execute() === TRUE) {
 	     $smt-> bind_result($count);
 	     $smt-> fetch();
	     $smt->close();
	     $conn->close();
             if ($debug) echo "sql execute ok - count = ".$count."<br/>";
	     return $count;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }
}

function getStatistics() {
	 # returns an array of the count of words in the database
	 # [total, success, failure]
	 global $debug;
	 if ($debug) echo "<br/>getStatistics().<br/>";
	 $conn = connectToDb();
	 $sql = "select count(word) from words where success=1";
	 $smt = $conn->prepare($sql);
	 if ($smt->execute() === TRUE) {
 	     $smt-> bind_result($count);
 	     $smt-> fetch();
	     $smt->close();
             if ($debug) echo "sql execute ok - success count = ".$count."<br/>";
	     $successCount = $count;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }	 
	 $sql = "select count(word) from words where success=0";
	 $smt = $conn->prepare($sql);
	 if ($smt->execute() === TRUE) {
 	     $smt-> bind_result($count);
 	     $smt-> fetch();
	     $smt->close();
	     $conn->close();
             if ($debug) echo "sql execute ok - fail count = ".$count."<br/>";
	     $failCount = $count;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }	 
	 return ["success" => $successCount,
	 	 "fail" => $failCount,
		 "total" => $successCount+$failCount
		 ];
}

function getWords($success) {
	 # return an array of words in the database that are flagged
	 # as success being equal to $success.
	 global $debug;
	 if ($debug) echo "<br/>getWords() - success=".$success."<br/>";
	 $conn = connectToDb();
	 $sql = "select word,wordcount from words where success=? order by word";
	 $smt = $conn->prepare($sql);
	 $smt->bind_param("i",$success);	 
	 if ($smt->execute() === TRUE) {
	     $resultArr = [];
 	     $smt-> bind_result($word,$wordCount);
 	     while ($smt-> fetch()) {
	     	$resultArr[] = [$word,$wordCount];   
	     }
	     $smt->close();
	     $conn->close();
             if ($debug) echo "sql execute ok - resultArr = <br/>";
	     if ($debug) var_dump($resultArr);
	     return $resultArr;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }
}



function getWordCount($word) {
	 # Returns the wordcount parameter for word $word,
	 # or zero if $word is not in the database.
	 global $debug;
	 $word = strtolower($word);
	 if ($debug) echo "<br/>getWordCount() - word=".$word."<br/>";
	 $conn = connectToDb();
	 $sql = "select wordcount from words where word=?";
	 $smt = $conn->prepare($sql);
	 $smt->bind_param("s",$word);	 
	 if ($smt->execute() === TRUE) {
 	     $smt-> bind_result($wordcount);
 	     $smt-> fetch();
	     $smt->close();
	     $conn->close();
             if ($debug) echo "sql execute ok - wordcount = ".$wordcount."<br/>";
	     return $wordcount;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }
}


function incrementWordCount($word) {
	 # Increments the wordCount for word $word
	 global $debug;
	 $word = strtolower($word);
	 if ($debug) echo "<br/>incrementWordCount() - word=".$word."<br/>";
	 $conn = connectToDb();
	 $wordCount = getWordCount($word);
	 $wordCount = $wordCount + 1;

	 $sql = "update words set wordcount=? where word=?";
	 $smt = $conn->prepare($sql);
	 $smt->bind_param("is",$wordCount,$word);	 
	 if ($smt->execute() === TRUE) {
 	     #$smt-> bind_result($result);
 	     #$smt-> fetch();
	     $smt->close();
	     $conn->close();
             if ($debug) echo "sql execute ok <br/>";
	     return $wordcount;
         } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
             $conn->close();
	     return -1;
         }

}


function writeToDb($word,$success) {
	 # write $word to the db.  If it already exists, the word count in 
	 # the database is incremented instead.
	 global $debug;
	 $word = strtolower($word);
	 $count = isWordInDb($word);
	 $wordcount = getWordcount($word);
	 if ($debug) echo "writeToDb - word=".$word.", success=".$success.
	      ", count=".$count.", wordcount=".$wordcount."<br/>";
	 if ($wordcount > 0) {
	    if ($debug) echo "incrementing word count<br/>";
	    incrementWordCount($word);
 	 } else {
	   if ($debug) echo "Inserting record into database<br/>";
	   $conn = connectToDb();
	   $sql = "insert into words(word,success,wordcount) ".
	      "values(?, ?, 1)";
	   if ($debug) echo "sql=".$sql."<br/>";
	   $smt = $conn->prepare($sql);
	   $smt->bind_param("si",$word,$success);
	   if ($smt->execute() === TRUE) {
             if ($debug) echo "New record created successfully";
           } else {
             echo "Error: " . $sql . "<br>" . $conn->error;
           }
	   $smt->close();
           $conn->close();
        }
}

?>