<?php
 require 'vendor/autoload.php';
 use Goutte\Client;

 $client = new Client();

 

 


 $crawler = $client->request('GET', 'http://localhost/web_scrap/testpage.html');

 // Scrape specifics
  $int = 0;
 $crawler->filter('tbody > tr > td')->each(function($node){
 	echo($node->text())."<br/>"; // Get indexed table value
    if($int == 0) 
    {
    	$name = $node->text();
    } else if($int == 1) {
    	$founder = $node->text();
    } else if()
    $name = "Google";
    $founder = "Larry Page";
    $started = "1998";
    $net = "300 Billion";
     // Connect to database
	 $db = new mysqli('localhost', 'root', '', 'crawler_db');
	 if($db->connect_errno > 0) {
	 	die("Error connecting");
	 }

 	$sql = "
      INSERT INTO `crawler_db`.`crawler_tbl` (`Id`, `Name`, `Founder`, `Started`, `Networth`) VALUES (NULL, '".$name."', '".$founder."', '".$started."', '".$net."')
    ";

 	if(!$result = $db->query($sql)) {
 		die("Error running query");
 	}
    
   

    $db->close();
 	//echo $node->text()."<br/>";
 });


 

 // Copy Paste

 
 
 // $client = new Client();

 // $crawler = $client->request('GET', 'http://localhost/web_scrap/testpage.html');

 // $crawler = new Crawler("http://localhost/web_scrap/testpage.html");

 //  foreach($crawler as $domElement) {
 //  	var_dump($domElement->nodeName);
 //  }

 
 // // var_dump($crawler);