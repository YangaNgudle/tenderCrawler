<?php
require 'vendor/autoload.php';
require 'TenderCrawler.class.php';
use TenderCrawler\TenderCrawler as TenderCrawler;
use Goutte\Client;

$crawler = new Client();

$testCrawler = new TenderCrawler($crawler); // use the default url

// does get last page index work

$lastIndex = $testCrawler->getLastPageIndex();

$data = $testCrawler->getAllTenders($lastIndex);

var_dump($data);



