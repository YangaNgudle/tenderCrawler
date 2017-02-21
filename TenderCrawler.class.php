<?php
 namespace TenderCrawler; 

 class TenderCrawler {

  private $crawler = null;
  private $url = null;
  private $lastIndex = null;

  public function __construct($crawler, $url = 'http://www.etenders.gov.za/content/advertised-tenders') {
    $this->crawler = $crawler;
    $this->url = $url;
    $this->lastIndex = $this->getLastPageIndex();
  }
  
  public function getLastPageIndex() {
  	$crawler = $this->crawler->request('GET', $this->url);
  	// .page index is a class name used on the etender.gov.za site to set the last index
  	$data = $lastHTML = $crawler->filter(".pager-last")->each(function($node){
	  return $node->html();
	});

	//  Extract the last page index from the html 
	$link = new \SimpleXMLElement($data[0]);
	$linkParts = explode("=", $link['href']);
	$lastCount = count($linkParts) - 1;
    return $linkParts[$lastCount];
  }

  public function getAllTenders($endIndex = '') {
  
    $endIndex = $endIndex === '' ? $this->lastIndex : $endIndex;

  	$crawler = $this->crawler->request('GET', "http://www.etenders.gov.za/content/advertised-tenders?field_tender_category_tid=All&amp;field_region_tid=All&amp;field_sector_tid=All&amp;field_tender_type_tid=All&amp;field_department_tid=All&amp;page=$endIndex");

  	$category[] = $crawler->filter('.views-field-field-tender-category')->each(function($node){
      return $node->text();
    });

    $description[] = $crawler->filter('.views-field-title')->each(function($node){
	  // Get the link too return an array as a result
	  
	  $link = strpos($node->html(), '<')? new \SimpleXMLElement($node->html()) : '';
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

    // Unset the first value because that's the table head
    // the structure is a bit weird where some values are the array and others are nested inside but whatever I'll fix it
	unset($category[0][0]);
	unset($description[0][0]);
	unset($tenderNumber[0][0]);
	unset($datePublished[0][0]);
	unset($closingDate[0]);
	unset($compulsory_brief[0]);

	// Re-arrange the index structure inside the array
	$category[0]= array_values($category[0]);
	$description[0] = array_values($description[0]);
	$tenderNumber[0] = array_values($tenderNumber[0]);
	$closingDate = array_values($closingDate);
	$datePublished[0] = array_values($datePublished[0]);
	$compulsory_brief = array_values($compulsory_brief);

     $data = array();

	// Construct array
	for ($i=0; $i < count($category[0]) ; $i++) {
       $data['categories'][] = $category[0][$i];
       $data['tenderNumber'][] = $tenderNumber[0][$i];
       $data['title'][] = $description[0][$i]['title'];
       $data['link'][] = $description[0][$i]['link'];
       $data['closingDate'][] = $closingDate[$i];
       $data['datePublished'][] = $datePublished[0][$i];
       $data['brief'][] = $compulsory_brief[$i];
	}

    return $data;
  	
    // return an array of all the tenders bro
  }

  // public function makeTable($data) {

  // 	for ($i=0; $i < count($data['categories']); $i++) { 
  // 		$result = "<tr/><td>".$data['categories'][$i]."</td><td>".$data['tenderNumber'][$i]."</td><td>".$data['title'][$i]."</td><td>".$data['datePublished'][$i]."</td><td>".$data['brief'][$i]."</td><td>".$data['link'][$i]."</td></tr>";
  // 	}

  // 	return "
  //     <table>
		// <tr>
		//    <th>Categories</th>
		//    <th>Tender Number</th> 
		//    <th>Title</th>
		//    <th>Date Published</th>
		//    <th>Closing Dated</th>
		//    <th>Brief</th>
		//    <th>Link</th>
		// </tr>
		//     ".$result."
		
  //   </table>

  // 	";
  // }

 }

?>