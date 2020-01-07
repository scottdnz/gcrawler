<?php

namespace Lib;

class InitialiseDB {
	private $conn;

	public function __construct($conn) {
		$this->conn = $conn;
	} 

	public function createTables() {	
		$commands = [
			'CREATE TABLE IF NOT EXISTS search_batch (
		    	id INTEGER PRIMARY KEY,
	    		terms TEXT NOT NULL,
	    		dated TEXT NOT NULL
			)',

			'CREATE TABLE IF NOT EXISTS search_results (
			    id INTEGER PRIMARY KEY,
			    site_description TEXT NOT NULL,
			    url TEXT NOT NULL,
			    search_batch_id INTEGER NOT NULL,
			    FOREIGN KEY (search_batch_id) REFERENCES search_batch (id)
		  	)',
		 ];

		// execute the sql commands to create new tables
		foreach ($commands as $command) {
		    $this->conn->exec($command);
		}
	}
}