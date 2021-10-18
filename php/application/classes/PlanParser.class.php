<?php
class PlanParser {
	
	/**
	 * an array of PlanSource objects.
	 */
	public $domSource;
	public $dataStruct;
	
	/* give a PlanSource class */
	public function setSource($sources) {
		$this->domSource = $sources;
	}
	public function parse() {
		if (! $this->domSource)
			return;
		$this->dataStruct = array ();
		
		foreach ( $this->domSource as $source ) {
			$rows = array ();
			$pageUri = $source->planUrl ( 0 );
			$pagecount = $this->parseUrl ( $source, $pageUri, $rows );
			
			if ($pagecount > 1) {
				for($i = 2; $i <= $pagecount; $i ++) {
					$this->parseUrl ( $source, $source->planUrl ( $i ), $rows );
				}
			}
			
			$this->dataStruct [] = $rows;
		}
	}
	
	/**
	 * returns the amount of additional pages as seen in the document loaded from url.
	 *
	 * @param unknown $url        	
	 */
	public function parseUrl($source, $url, &$rows) {
		$page = file_get_contents ( $url );
		if (! $page)
			return;
		
		$doc = new DOMDocument ();
		if (! $doc->loadHTML ( $page ))
			return;
		
		return $this->parseDoc ( $source, $doc, $rows );
	}
	
	/**
	 * returns the amount of additional pages as seen in the document.
	 *
	 * @param unknown $doc        	
	 */
	public function parseDoc($source, $doc, &$rows) {
		$xp = new DOMXPath ( $doc );
		$titleNode = $xp->query ( $source->titlePath () );
		$tStr = $titleNode->item(0)->nodeValue;
		$rows ["plandate"] = PlanSource::getDate ( $tStr );
		
		if (! array_key_exists ( "pages", $rows )) {
			$planPages = PlanSource::getPageValues ( $tStr );
			$rows ["pages"] = intval($planPages->maximum);
		}
		
		$motd = $this->parseMotd ( $source, $xp );
		if (array_key_exists ( "motd", $rows )) {
			$rows ["motd"] = array_merge ( $rows ["motd"], $motd );
		} else {
			$rows ["motd"] = $motd;
		}
		
		if (array_key_exists ( "data", $rows )) {
			$data = $rows ["data"];
		} else {
			$data = array ();
		}
		$data = $this->parseDataRows ( $source, $xp, $data );
		$rows ["data"] = $data;
		
		return $rows ["pages"];
	}
	function parseMotd($source, $xp) {
		$motd = array ();
		$list = $xp->query ( $source->motdPath () );
		if ($list && $list->item(0)) {
			$rows = $xp->query ( $source->motdRowsPath (), $list->item(0) );
			if ($rows) {
				$idx = 0;
				foreach ( $rows as $row ) {
					$motd [$idx ++] = $row->nodeValue;
				}
			}
		}
		
		return $motd;
	}
	function parseDataRows($source, $xp, &$dataRows) {
		$list = $xp->query ( $source->dataTableRowsPath () );
		if (! $list)
			return;
		
		if ($dataRows == NULL)
			$dataRows = array ();
		
		foreach ( $list as $rowNd ) {
			$curr = array ();
			$idx = 0;
			foreach ( $rowNd->childNodes as $child ) {
				$arr = $source->dataTableHeaders ();
				$key = $arr[$idx];
				$curr [$key] = $child->nodeValue;
				$idx ++;
			}
			
			$dataRows = array_merge($dataRows, $this->expandDataSet($curr)); // push to datarows
		}
		var_dump($dataRows);
		return $dataRows;
	}
	
	public function expandDataSet(&$curr) {
		$expanded = array();
		
		$key = $curr["Klasse"];
		
		foreach($this->expandKlasse($key) as $part) {
			$copy = $curr;
			$copy["Klasse"] = $part;
			$expanded[] = $copy;
		}
		
		return $expanded;
	}
	
	public function expandKlasse($klasse) {
// int substr_compare ( string $main_str , string $str , int $offset [, int $length [, bool $case_insensitivity = false ]] )
		if("0" == substr($klasse, 0, 1) ) {
			$result = array();
			$k = (string)substr($klasse, 0, 2);
			for($idx = 2; $idx < strlen($klasse); $idx++) {
				$result[] = "$k$klasse[$idx]";
			}
			return $result;
		}
		
		if("Q" == substr($klasse, 0, 1)) {
			$result = array();
			$parts = preg_split("/[ \/]+/", substr($klasse,1));
			$k = "$klasse[0]";
			for($idx = 0; $idx < count($parts); $idx++) {
				$result[] = "$k$parts[$idx]";
			}
			return $result;
		}

		if("E" == substr($klasse, 0, 1)) {
			$result = array();
			$parts = preg_split("/[ \/]+/", substr($klasse,1));
			$k = "$klasse[0]";
			for($idx = 0; $idx < count($parts); $idx++) {
				$result[] = "$k$parts[$idx]";
			}
			return $result;
		}
		
		return array($klasse);
	}
	
	public function dataAsJson() {
		return json_encode ( $this->dataStruct );
	}
}

?>