<?php

namespace Lib;

use Goutte\Client;


class SearchStorer {

	private $client;
	private $dataDir;

	public function __construct($dataDir) {
		$this->client = new Client();
		$this->dataDir = $dataDir;
	}

	public static function getDateSuffix() {
		return (new \DateTime())->format("YmdHis");
	}

	public static function getTidiedTerms($terms) {
		$tidiedTerms = str_replace(" ", "_", $terms);
		return $tidiedTerms;
	}

	/**
	 * Makes an HTTP request to fetch a page of google search results content.
	 */
	private function getPageContent($terms, $dateSfx, $pageNum, $start=null) {
		// $dataDir = "./data";
		$queryParams = ['q' => $terms];
		// for pagination
		if (! is_null($start)) {
			$queryParams['start'] = $start;
		}
		$url = 'https://www.google.co.nz/search?' . http_build_query($queryParams);
		// echo $url . PHP_EOL;
		
		// Should be an ID for terms, then subfolder for ID for datetime record
		$tidiedTerms = self::getTidiedTerms($terms);

		$outputDir = $this->dataDir . DIRECTORY_SEPARATOR . $tidiedTerms . DIRECTORY_SEPARATOR . $dateSfx . DIRECTORY_SEPARATOR;
		
		if (! file_exists($outputDir)) {
			mkdir($outputDir, 0777, true);
		}
		$fName = $outputDir . "page" . ($pageNum + 1) . ".html";

		$crawler = $this->client->request('GET', $url);
		$response = $this->client->getResponse()->getContent();
		file_put_contents($fName, $response);

		return $fName;
	}

	/**
	 * Makes a number of HTTP requests to retreive X google search results pages.
	 */
	public function getPagesContentForTerms($terms, $numIterations, $numPerPage, $dateSfx) {
		for ($i = 0; $i < $numIterations; $i++) {
			if ($i === 0) {
				$contentFile = $this->getPageContent($terms, $dateSfx, $i);
			}
			else {
				$contentFile = $this->getPageContent($terms, $dateSfx, $i, $i * $numPerPage);	
			}
			echo $contentFile . PHP_EOL;
		}
	}

}
