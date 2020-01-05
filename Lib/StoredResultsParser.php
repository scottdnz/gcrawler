<?php

namespace Lib;

use Symfony\Component\DomCrawler\Crawler;

class StoredResultsParser {
	private $dataDir;
	private $conn;

	public function __construct($dataDir, $conn) {
		$this->dataDir = $dataDir;
		$this->conn = $conn;
	}

	private function parseStoredContentFile($fName) {
		$html = file_get_contents($fName);
		// Just use DOMCrawler so we can parse a local file
		$crawler = new Crawler($html);

		$storedLinks = [];
		$crawler->filter('div.kCrYT > a')->each(function ($node) use (&$storedLinks) {
		    $linkText = $node->children()->first();

			$url = $node->attr('href');
			$queryString = parse_url($url)["query"];
			parse_str($queryString, $params); 
			
			$storedLinks[] = [
				"label" => $linkText->html(), 
				"site" => $params["q"]
			];
		});

		return $storedLinks;
	}

	// Up to here - sqlite commands need to go in
	private function insertSearchBatch() {

	}

	private function insertSearchResultsForBatch() {

	}

	//Parse directories containing saved html content files
	public function parseContentFiles($terms) {
		foreach ($terms as $currentTerms) {
			$tidiedTerms = SearchStorer::getTidiedTerms($currentTerms);

			$termsDir = $this->dataDir . DIRECTORY_SEPARATOR . $tidiedTerms . DIRECTORY_SEPARATOR;
			// Find all subdirectories
			$directories = glob($termsDir . "*" , GLOB_ONLYDIR);
			// print_r($directories);

			sort($directories);
			$latestSubDir = end($directories); 
			$files = scandir($latestSubDir);
			foreach ($files as $f) {
				if (is_dir($f)) {
					continue;
				}

				// echo $f . PHP_EOL;
				$fName = $latestSubDir . DIRECTORY_SEPARATOR . $f;
				echo $fName . PHP_EOL;
				$storedLinks = $this->parseStoredContentFile($fName);
				var_dump($storedLinks);

				// insertSearchBatch
			}
		}


	}

}