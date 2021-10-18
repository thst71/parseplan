<?php
class PageValues {
	public $current;
	public $maximum;
	
	function __construct($current, $maximum) {
		$this->current = $current;
		$this->maximum = $maximum;
	}
}