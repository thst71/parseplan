<?php

function autoloader($classname) {
	include_once "classes/$classname" . ".class.php";
}


spl_autoload_register("autoloader");
?>