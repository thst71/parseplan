<?php
class HeutePlanSource extends PlanSource {
	function planUrl($page) {
		if (! $page) // undefined or 0
			$page = 1;

		return 'http://www.liebigschule-frankfurt.de/images/vertretungsplan/schuelerheute/subst_00' . $page . '.htm';
	}
}
?>