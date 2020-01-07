<?php

// namespace main;

require "./vendor/autoload.php";

// use Goutte\Client;
// use Symfony\Component\DomCrawler\Crawler;

use \Lib\InitialiseDB;
use \Lib\SearchStorer;
use \Lib\StoredResultsParser;


function mainExec() {
	// These should be set in a config
	$numPerPage = 10;
	$numIterations = 1;
	$dataDir = "./data";
	$dataDir = "./data/samples";
	$conn = new \PDO("sqlite:" . "/opt/sqlite_dbs/gcrawler.db");

	$initialise = new InitialiseDB($conn);
	$initialise->createTables();

	$terms = [
		"nz books", 
		"nz gems"
	];

	$searchStorer = new SearchStorer($dataDir);

	
	foreach ($terms as $currentTerms) {
		$dateSfx = SearchStorer::getDateSuffix();
		// Don't turn on unless crawling live	
		// $searchStorer->getPagesContentForTerms($currentTerms, $numIterations, $numPerPage, $dateSfx);

		$storedResultsParser = new StoredResultsParser($dataDir, $conn);
		$allStoredLinks = $storedResultsParser->parseContentFiles($currentTerms);

		$dated = (new \DateTime())->format("Y-m-d H:i:s");
		$searchBatchId = $storedResultsParser->insertSearchBatch($currentTerms, $dated);
		// echo "searchBatchId: " . $searchBatchId . PHP_EOL;

		$storedResultsParser->insertSearchResultsForBatch($searchBatchId, $allStoredLinks);

	}

}

mainExec();
