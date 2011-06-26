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
	  UNIQUE KEY id (id)
	);";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);

}

function jakoblist_install_data() {
global $wpdb;
$jakoblist_title = "Beispielbuch";
$jakoblist_author = "Max Mustermann";
$jakoblist_publisher = "Exempel-Verlag";
$jakoblist_info = "Sehr ausführliche Beschreibung von Beispielen; €0.99";
$table_name = $wpdb->prefix . "jakoblist";
$wpdb->insert($table_name, array( 'time' => current_time('mysql'), 'title' => $jakoblist_title, 'author' => $jakoblist_author, 'publisher' => $jakoblist_publisher, 'info' => $jakoblist_info ) );

}
?>