<?php global $wpdb;
function jakoblist_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . "jakoblist";
      
	$sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  createdby text NOT NULL,
	  modified datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	  modifiedby text NOT NULL,
	  title text NOT NULL,
	  author text NOT NULL,
	  publisher text NOT NULL,
	  info text NOT NULL,
	  price decimal(5,2) NOT NULL,
	  active tinyint(1) NOT NULL,
	  UNIQUE KEY id (id)
	);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);

}

function jakoblist_install_data() {
global $wpdb;
$thetitle 	= 	"Beispielbuch";
$theauthor 	= 	"Max Mustermann";
$thepublisher 	= 	"Exempel-Verlag";
$theinfo 	= 	"Sehr ausführliche Beschreibung von Beispielen";
$theprice 	= 	9.99;
$active 	= 	1;
$table_name = $wpdb->prefix . "jakoblist";
$wpdb->insert($table_name, array( 'time' => current_time('mysql'), 'title' => $thetitle, 'author' => $theauthor, 'publisher' => $thepublisher, 'info' => $theinfo, 'price' => $theprice ) );

}
?>
