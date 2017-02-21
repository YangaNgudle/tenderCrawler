<?php
// Get all government tenders 
// 17-02-2017 
// Let's go
require 'vendor/autoload.php';
 use Goutte\Client;

function totalTenderNumber($client) {
	$crawler = $client->request('GET', "http://www.etenders.gov.za/content/advertised-tenders");
	$data = $crawler->filter(".pager-last")->each(function($node){
	  return $node->html();
	});
    
	$link = new SimpleXMLElement($data[0]);
	$linkParts = explode("=", $link['href']);
	$lastCount = count($linkParts) - 1;

	echo $linkParts[$lastCount];
}

function gettenders($client, $count) { 
 $crawler = $client->request('GET', "http://www.etenders.gov.za/content/advertised-tenders?field_tender_category_tid=All&amp;field_region_tid=All&amp;field_sector_tid=All&amp;field_tender_type_tid=All&amp;field_department_tid=All&amp;page=$count");

$category[] = $crawler->filter('.views-field-field-tender-category')->each(function($node){
  return $node->text();
 });

$description[] = $crawler->filter('.views-field-title')->each(function($node){
  // Get the link too return an array as a result
  
 
  $link = strpos($node->html(), '<')? new SimpleXMLElement($node->html()) : '';
  if($link != '') {
  	 $data['link'] = "http://www.etenders.gov.za/".$link['href'];
  }
 
  $data['title'] = $node->text();
  return $data;
 });

$tenderNumber[] = $crawler->filter('.views-field-field-code')->each(function($node){
  return $node->text();
 });
$datePublished[] = $crawler->filter('.views-field-field-date-published')->each(function($node){
  return $node->text();
 });
$closingDate = $crawler->filter('.views-field-field-closing-date')->each(function($node){
  return $node->text();
 });

$compulsory_brief = $crawler->filter('.views-field-field-compulsory-briefing-sessio')->each(function($node){
  return $node->text();
 });

unset($category[0][0]);
unset($description[0][0]);
unset($tenderNumber[0][0]);
unset($datePublished[0][0]);
unset($closingDate[0]);
unset($compulsory_brief[0]);
$category[0]= array_values($category[0]);
$description[0] = array_values($description[0]);
$tenderNumber[0] = array_values($tenderNumber[0]);
$closingDate = array_values($closingDate);
$datePublished[0] = array_values($datePublished[0]);
$compulsory_brief = array_values($compulsory_brief);

/*
 INSERT INTO `crawler_db`.`tenders` (`id`, `category`, `description`, `tendernumber`, `datepublished`, `closingdate`, `brief`) VALUES (NULL, 'asdfa', 'asdfa', 'asdfa', 'asdfa', 'asdfa', 'asdfa');
**/

$db = new mysqli('localhost', 'root', '', 'crawler_db');
  if($db->connect_errno > 0) {
	die("Error connecting");
}

for ($i=0; $i < count($category[0]) ; $i++) { 
   $sql = "
   INSERT INTO `crawler_db`.`tenders` (`id`, `category`, `description`, `tendernumber`, `datepublished`, `closingdate`, `brief`,`link`) VALUES (NULL, '".trim($category[0][$i])."', '".trim($description[0][$i]['title'])."', '".trim($tenderNumber[0][$i])."', '".trim($datePublished[0][$i])."', '".trim($closingDate[$i])."', '".trim($compulsory_brief[$i])."', '".trim($description[0][$i]['link'])."');

  ";
   echo "Done with row: $i <br/>";
 if(!$result = $db->query($sql)) {
 	die("Error running query");
 }
}
 $db->close();

}

 $client = new Client();

 totalTenderNumber($client);
 exit();

for ($i=0; $i < 20 ; $i++) { 
	gettenders($client, $i);
	echo "Done with iternation: $i";
}

   
