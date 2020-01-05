<?php

// namespace main;

require "./vendor/autoload.php";

// use Goutte\Client;
// use Symfony\Component\DomCrawler\Crawler;

use \Lib\InitialiseDB;
use \Lib\SearchStorer;
use \Lib\StoredResultsParser;

/*
Doing things in try_local.php file (save getting google blacklist)
file:///home/scott/ws/php/tests/t_crawler/v1/data/results2.html
aim is put this on pi to do scraping / crawling
ws/php/tests/t_crawler/v1

-had to activate sqlite & sqlite_pdo extensions in /etc/php/7.2/cli/php.ini
*/

function mainExec() {
	// These should be set in a config
	$numPerPage = 10;
	$numIterations = 1;
	$dataDir = "./data";
	$conn = new \PDO("sqlite:" . "/opt/sqlite_dbs/gcrawler.db");

	$terms = [
		"nz books", 
		"nz gems"
	];

	$searchStorer = new SearchStorer($dataDir);

	// Don't turn on unless crawling live
	// foreach ($terms as $currentTerms) {
	// 	$dateSfx = SearchStorer::getDateSuffix();
	// 	$searchStorer->getPagesContentForTerms($currentTerms, $numIterations, $numPerPage, $dateSfx);
	// }

	$storedResultsParser = new StoredResultsParser($dataDir);
	$storedResultsParser->parseContentFiles($terms, $conn);

	
	
	// $initialise = new InitialiseDB($conn);
	// $initialise->createTables();

}

mainExec();
