<?php
abstract class PlanSource {
	abstract function planUrl($page);

	function titlePath() {
		return '/html/body/*/div[@class="mon_title"]';
	}
	function dataTableRowsPath() {
		return '/html/body/*/table[@class="mon_list"]/tr[contains(@class,"list") and count(td) > 0 ]';
	}
	function dataTableHeaders() {
		return array (
				"Klasse",
				"Stunde",
				"NeuVertreter",
				"NeuFach",
				"PlanRaum",
				"PlanLehrer",
				"Fach",
				"Art",
				"Bemerkung" 
		);
	}
	
	function motdPath() {
		return '/html/body/*/table[@class="info"]';
	}
	function motdRowsPath() {
		return '/html/body/*/table[@class="info"]/tr/th | /html/body/*/table[@class="info"]/tr/td';
	}
	function getDate($title) {
		// 19.12.2014 Freitag (Seite 1 / 2)
		// match.1    match.2 match.3+.4
		$success = preg_match ( '/([0-9\\.]+) (.*)(\\(Seite ([0-9]+) [\\/] ([0-9]+)\\))?/', $title, $matches );
		if(!$success) {
			return trim(substr($title, 0, 10));
		}
		
		$success = preg_match( '/([0-9]+)[\\.]([0-9]+)[\\.]([0-9]+)/', $matches[1], $dateparts);
		if($success) {
			return sprintf("%02d.%02d.%04d", $dateparts[1], $dateparts[2], $dateparts[3]);
		}
		
		return trim($matches[1]);
	}
	
	/* return PageValues mit der aktuellen und der maximalen Seite */
	function getPageValues($title) {
		preg_match ( '/\\(Seite ([0-9]+) [\\/] ([0-9]+)\\)/', $title, $matches );

		if(count($matches) == 3 ) {
			return new PageValues ( $matches [1], $matches [2] );
		}
		else {
			return new PageValues ( 1, 1 );
		}
	}
}