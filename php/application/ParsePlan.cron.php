<?php
include_once 'classes/autoloader.php';


$pp = new PlanParser();
$pp->setSource([new HeutePlanSource(), new MorgenPlanSource()]);

$pp->parse();

$current = $pp->dataAsJson();

// echo $current;

$file = "plan.json";
file_put_contents($file, $current);
?>