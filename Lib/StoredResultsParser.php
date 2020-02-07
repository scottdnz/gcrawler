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

    /**
     * Parses directories containing saved html content files
     */
    public function parseContentFiles($currentTerms) {
        $allStoredLinks = [];
        $tidiedTerms = SearchStorer::getTidiedTerms($currentTerms);

        $termsDir = $this->dataDir . DIRECTORY_SEPARATOR . $tidiedTerms . DIRECTORY_SEPARATOR;
        // Find all subdirectories
        $directories = glob($termsDir . "*", GLOB_ONLYDIR);
        if (count($directories) === 0) {
            return $allStoredLinks;
        }

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
            // var_dump($storedLinks);
            // insertSearchBatch
            $allStoredLinks = array_merge($allStoredLinks, $storedLinks);
        }
        return $allStoredLinks;
    }

    /**
     * Does a single insert for search_batch.
     */
    public function insertSearchBatch($terms, $dated) {
        $sql = "insert into search_batch 
(
	terms, 
	dated
) values
(
	:terms,
	:dated
);";

        $params = [
            ":terms" => $terms,
            ":dated" => $dated
        ];

        $query = $this->conn->prepare($sql);
        $query->execute($params);

        return $this->conn->lastInsertId();
    }

    /**
     * Does a batch insert for search_results.
     */
    public function insertSearchResultsForBatch($searchBatchId, $allStoredLinks) {
        $valuesBlock = "(
	?,
	?,
	?		
)";

        $valuesBlocks = array_fill(0, count($allStoredLinks), $valuesBlock);

        $params = [];
        foreach ($allStoredLinks as $stored) {
            $params[] = $stored["label"];
            $params[] = $stored["site"];
            $params[] = $searchBatchId;
        }

        $sql = "insert into search_results
(
	site_description,
	url,
	search_batch_id
) values " . implode(", ", $valuesBlocks);

        $query = $this->conn->prepare($sql);
        $query->execute($params);
        
    }

}
