<?php
/*global $wpdb;
echo 'func = ' . $_GET["func"];
echo 'id = ' . htmlspecialchars($_GET["id"]);*/



function jakoblist_remove() {
	global $wpdb;
	
	$id=$_GET['id'];	

	$SQL = "DELETE FROM `".$wpdb->prefix."jakoblist` WHERE `id` = 16";
	
	$wpdb->query($SQL);
	
	echo 'dsfsdfsfsdf';

}


if ($_GET["func"] = "jakoblist_remove") bbb();

?>