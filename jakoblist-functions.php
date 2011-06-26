<?php


/*###############################################
#	Cleans a string from "'" and escapes HTML	#
#################################################*/
function strclean($input) {
	
	$input 	= 	str_replace('\\\'', '\'', $input);
	$input 	= 	htmlspecialchars($input);
	return $input;

}

function sort_class($atts, $orderby, $order)
{
	/*<?php if($orderby == 'title') {echo 'sorted';} else { echo 'sortable';} ?> <?php if($orderby == 'title' and $order == 'DESC') { echo 'desc';} else { echo 'asc';} ?>*/	
	if($orderby == $atts)
		{
			echo ' sorted';
			if($order == 'DESC')
				{
					echo ' desc';
				}
			else
				{
					echo ' asc';
				}
		}
	else
		{
			echo ' desc';			
			echo ' sortable';
		}

	

}

function sort_link($atts, $orderby, $order, $searchterm)
{
	/*<?php echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&order=info'.$thsortlink; ?>*/
	
	if($atts == $orderby)
		{
			if($order == 'DESC')
				{
					$theorder = 'asc';
				}
			else
				{
					$theorder = 'desc';
				}
		}
	else
		{
			
		}

	$link 	= 	bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&search='.$searchterm.'&orderby='.$atts.'&order='.$theorder;

	return $link; 
}

function jakoblist_search() {
	global $wpdb;
	
	$books 	= 	$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` order by `".$order."`".$direction."" );
	$result = 	'';

	if(count($books) == 0) {
		/* wenn nix da is */
	} else {
		/* wenn was da is */
		foreach($books as $book) {
			$class = ('alternate' != $class) ? 'alternate' : '';
			echo '<form action="" method="post"><tr class="'.$class.'">';
			echo '<td>'.strclean($book->title).'</td>';
			echo '<td>'.strclean($book->author).'</td>';
			echo '<td>'.strclean($book->publisher).'</td>';
			echo '<td>'.strclean($book->info).'</td>';
			echo '<td align="left"><input type="button" name="edit" value=" ✎ " class="button-secondary" onclick=location.href="';
			echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist_edit&id='.$book->id.'"';
			echo '></td>';
			echo '<td align="center"><input type="button" name="-" value=" - " class="button-secondary" onclick=location.href="';
			echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&func=jakoblist_remove&id='.$book->id.'"';
			echo '></td>';
			echo '</tr></form>';
		}
	}
}

?>
