<?php
/*
Plugin Name: Jakobus-Buecherliste
Plugin URI: http://www.ainotenshi.org
Description: Erzeugt einen einheitliches Design für die Bücherliste.
Version: 0.1
Author: Julian Saraceni
Author URI: http://www.ainotenshi.org
License: GPL2
*/
global $wpdb;
include_once(ABSPATH.'wp-content/plugins/jakoblist/jakoblist-setup.php');
include_once(ABSPATH.'wp-content/plugins/jakoblist/jakoblist-functions.php');
require(ABSPATH . WPINC . '/pluggable.php');
register_activation_hook(__FILE__,'jakoblist_install');
register_activation_hook(__FILE__,'jakoblist_install_data');



function search_url()
{

	echo 'meep';

}

function sort_class($atts, $sort, $order)
{
	/*<?php if($sort == 'title') {echo 'sorted';} else { echo 'sortable';} ?> <?php if($sort == 'title' and $order == 'DESC') { echo 'desc';} else { echo 'asc';} ?>*/	
	echo $sort;
	if($sort == $atts)
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
			if($order == 'desc')
				{
					echo ' desc';
				}
			else
				{
					echo ' asc';
				}
			
			echo ' sortable';
		}

	

}


function buecherliste($atts, $content = null)
{	
	global $wpdb;
	

	$books = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` order by `title`" );
	$result ='';
	if(count($books) == 0)
		{
			/* wenn nix da is */
		}
	else
		{
			/* wenn was da is */
			foreach($books as $book)
				{	
					$thetitle		=	strclean($book->title);
					$theauthor		=	strclean($book->author);
					$thepublisher		=	strclean($book->publisher);
					$theinfo		=	strclean($book->info);
					$thecurrency		= 	'€';
					
					if($book->price !== '0.00')
						{
							$theprice		=	'; '.$thecurrency.strclean(str_replace('.', ',', $book->price));
						}
					else
						{
							$theprice		=	'';
						}
					
					$result =  $result.'<p><span style="color: #D9A404;"><em><strong>'.$thetitle.'</strong></em></span><br /><strong>'.$theauthor.'</strong><br />'.$thepublisher.'<br />'.$theinfo.$theprice.'</p>';
				}
		}
		
	return $result;

}

function jakoblist_add()
{
	global $wpdb;
	global $current_user;
	get_currentuserinfo();
	
	$id = $_GET["id"];
	
	$thetitle 			= 	$_POST["title"];
	$theauthor 			= 	$_POST["author"];
	$thepublisher 			= 	$_POST["publisher"];
	$theinfo 			= 	$_POST["info"];
	$theprice			=	str_replace(',', '.', $_POST["price"]);
	$thecreation			= 	current_time('mysql');
	$thecreator			=	$current_user->user_login;
	$themodification		= 	current_time('mysql');
	$themodificator			=	$current_user->user_login;
	$table_name 			= 	$wpdb->prefix . "jakoblist";


	if(!$id == '1')
		{
			$wpdb->insert($table_name, array( 'created' => $thecreation, 'createdby' => $thecreator, 'title' => $thetitle, 'author' => $theauthor, 'publisher' => $thepublisher, 'info' => $jakoblist_info, 'price' => $theprice ) );
		}
	else
		{
			$wpdb->update($wpdb->prefix.'jakoblist', array('title' => $thetitle,'modifiedby' => $themodificator, 'modified' => $themodification, 'author' => $theauthor, 'publisher' => $thepublisher, 'info' => $theinfo, 'price' => $theprice), array('id' => $id));	
		}

}

function jakoblist_edit()
{
	/*
	This functions serves as both the way to add new books and the way to edit exisiting books.

		*	the 'add' functionality is accessible through the dashboard sidebar menu
		* 	the 'edit' functionality is accessible by clicking the edit-button in the jakoblist management view
	*/

	global $wpdb;
	$id = $_GET["id"]; /*	Extracts the id from the URL	*/
	$books = $wpdb->get_results("SELECT * FROM `".$wpdb->prefix."jakoblist` WHERE `id` = ".$id); /*	Selects the Database entry matching the id	*/
	
	?>

	<div class="wrap">
		<?php
			if(isset($id)) /*	If an id is provided in the URL the 'edit' functionality is loaded	*/
				{
					$pagetitle   		= 	__("Bearbeiten eines Buches");
					$edit 			= 	'&edit=1';
					$formaction 		= 	'admin.php?page=jakoblist&func=jakoblist_add'.$edit.'&id='.$id;
					$savebtn  		=	__("Änderung übernehmen");
					
					foreach($books as $book)
						{	
							/*	Gets the values of the book matching the earlier select query	*/
							$thetitle		=	strclean($book->title);
							$theauthor		=	strclean($book->author);
							$thepublisher		=	strclean($book->publisher);
							$theinfo		=	strclean($book->info);
							$theprice		=	strclean($book->price);
						}
				}
			else	/*	If no id is provided in the URL the 'add' functionality is loaded */
				{
					$pagetitle		= 	__("Hinzufügen eines Buches");
					$formaction 		= 	'admin.php?page=jakoblist&func=jakoblist_add'.$edit;
					$savebtn  		=	__("Hinzufügen");
				}
				
			echo
				'<h2>'.$pagetitle.'</h2>
				<form action="'.$formaction.'" method="post">
					<table class="widefat">
						<thead>
							<tr>
								<th colspan="2">Details Editieren</th>
							</tr>
						</thead>
						<tr>
							<td width="10%"><strong>'.__("Titel").': </strong></td>
							<td><input type="text" size="120" maxlength="200" name="title" value="'.$thetitle.'" /><br /><em>Max. 200 Zeichen</em></td>
						</tr>
						<tr>
							<td><strong>'.__("Autor").': </strong></td>
							<td><input type="text" size="120" maxlength="200" name="author" value="'.$theauthor.'" /><br /><em>Max. 200 Zeichen</em></td>
						</tr>
						<tr>
							<td><strong>'.__("Verlag").': <strong></td>
							<td><input type="text" size="120" maxlength="200" name="publisher" value="'.$thepublisher.'" /><br /><em>Max. 200 Zeichen</em></td>
						</tr>
						<tr>
							<td><strong>'.__("Information").': </strong></td>
							<td><input type="text" size="120" maxlength="200" name="info" value="'.$theinfo.'" /><br /><em>Max. 200 Zeichen</em></td>
						</tr>
						<tr>
							<td><strong>'.__("Preis").': </strong></td>
							<td><input type="text" size="10" maxlength="10" name="price" value="'.$theprice.'" /><br /><em>Max. 200 Zeichen</em></td>
						</tr>
					</table>
					<br /><input type="submit" class="button-primary" value=" '.$savebtn.' "/>
				</form>';
			
			
				?>
	
	</div>
	<?php
	
	
	
	
}


function jakoblist_remove()
{
	global $wpdb;
	
	$id=$_GET['id'];
	$SQL = "DELETE FROM `".$wpdb->prefix."jakoblist` WHERE `id` = $id";
	$wpdb->query($wpdb->prepare($SQL));
}

function jakoblist_manage()
{


	/*
	Scan the URL for sort/order parameters and keep them.
	*/

		$searchterm = $_GET['search'];

		switch ($_GET['sortby'])
			{
				case 'title':
					$sort = "title";
					break;
				case 'author':
					$sort = "author";
					break;
				case 'publisher':
					$sort = "publisher";
					break;
				case 'info':
					$sort = "info";
					break;
				case 'price':
					$sort = "price";
					break;
				default:
					$sort = "title";
			}


		switch ($_GET['order'])
			{
				case 'desc':
					$order = "DESC";
					echo 'desc';
					break;
				default:
					$order = "";
			}


		

	?>
	<div class="wrap">
	<h2>Bücherliste verwalten</h2>
	<form action="admin.php?page=jakoblist&sortby=<?php echo $sort; ?>" method="post">
		<table style="margin-bottom:0.2em;">
		<tr>
		<td>
		<select name="jakoborder" size="1">
			<option><?php echo __("Sortieren nach...") ?></option>
			<option value="titleasc"><?php echo __("Titel (auftsteigend)") ?></option>
			<option value="titledesc"><?php echo __("Titel (absteigend)") ?></option>
			<option value="authorasc"><?php echo __("Autor (aufsteigend)") ?></option>
			<option value="authordesc"><?php echo __("Autor (absteigend)") ?></option>
			<option value="publisherasc"><?php echo __("Verlag (aufsteigend)") ?></option>
			<option value="publisherdesc"><?php echo __("Verlag (absteigend)") ?></option>
			<option value="infoasc"><?php echo __("Information (aufsteigend)") ?></option>
			<option value="infoesc"><?php echo __("Information (absteigend)") ?></option>
		</select>
		</td>
		<td>
		<input type="submit" value=" <?php echo __("sortieren") ?> " class="button-secondary"/>
		</form>
		<form action="admin.php?" method="get">
		</td>
		<td width="100%">
		</td>
		<td>
			<input type="text" value="" name="search" />
		</td>
		<td>
			<input type="hidden" name="page" value="jakoblist">
			<input type="hidden" name="sortby" value="<?php echo $sort; ?>">
			<input type="hidden" name="order" value="<?php echo $order; ?>">
			<input type="submit" value=" <?php echo _("suchen") ?> " class="button-secondary" />
		</td>
		</form>
		</tr>
		</table>
		

	<table id="mytable" class="widefat" width="50%">
	<thead>
		<tr>
			<th width="20%" class="manage-column column-date <?php sort_class('title', $sort, $order) ?>"><a href="<?php echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&order=title'.$thsortlink; ?>">Titel<?php if($sort == 'title') echo '<span class=sorting-indicator>&nbsp;</span>'; ?></a></th>
			<th width="20%" class="manage-column column-date <?php sort_class('author', $sort, $order) ?>"><a href="<?php echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&order=author'.$thsortlink; ?>">Autor<?php if($sort == 'author') echo '<span class="sorting-indicator"></span>'; ?></a></th>
			<th width="20%" class="manage-column column-date <?php sort_class('publisher', $sort, $order) ?>"><a href="<?php echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&order=publisher'.$thsortlink; ?>">Verlag<?php if($sort == 'publisher') echo '<span class="sorting-indicator"></span>'; ?></a></th>
			<th class="manage-column column-date <?php sort_class('info', $sort, $order) ?>"><a href="<?php echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&order=info'.$thsortlink; ?>">Information<?php if($sort == 'info') echo '<span class="sorting-indicator"></span>'; ?></th>
			<th colspan="3" width="10"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<form action="admin.php?page=jakoblist&func=jakoblist_add" method="post">
				<td><input name="title" type="text" size="30%" maxlength="200"><br /><em><?php var_dump($search); ?>Max. 200 Zeichen</em></td>
				<td><input name="author" type="text" size="30%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="publisher" type="text" size="30%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="info" type="text" size="50%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="price" type="text" size="10" maxlength="10"><br /><em>Max. 200 Zeichen</em></td>
				<td align="left" colspan="2"><input type="submit" name="add" value=" ✚ " class="button-primary" onclick=''></td>
			</form>
		</tr>
	<?php

		global $wpdb;
		
		
		if($_GET['search'] == '' ) 
			{ /* Alles außer Suche */ 
				$books = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` order by `".$sort."`".$order."" );
			}
		else 
			{ /* Suche */	
				$books = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` WHERE `title` LIKE '%".$searchterm."%' OR `author` LIKE '%".$searchterm."%' OR `publisher` LIKE '%".$searchterm."%' OR `info` LIKE '%".$searchterm."%' order by `".$sort."` ".$order."" );	
			}
		
		if(count($books) > 0)
			{ /* keine Suchergebnisse */			
				foreach($books as $book)
					{
						$class = ('alternate' != $class) ? 'alternate' : '';
						echo '<form action="" method="post"><tr class="'.$class.'">';
						echo '<td>'.strclean($book->title).'</td>';
						echo '<td>'.strclean($book->author).'</td>';
						echo '<td>'.strclean($book->publisher).'</td>';
						echo '<td>'.strclean($book->info).'</td>';
						echo '<td>'.strclean($book->price).'</td>';
						echo '<td align="left"><input type="button" name="edit" value=" ✎ " class="button-secondary" onclick=location.href="';
						echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist_edit&id='.$book->id.'"';
						echo '></td>';
						echo '<td align="center"><input type="button" name="-" value=" - " class="button-secondary" onclick="if(confirm(\'Sind Sie sicher?\')) {location.href=\'';
						echo bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist&func=jakoblist_remove&id='.$book->id.'\'} else {return false;}"';
						echo '></td>';
						echo '</tr></form>';
					}
			}	

	?>


	</tbody>
	</table>
	</div>
	<?php

}

function jakoblist_dashboard()
{

	add_object_page('Bücherliste', 'Bücherliste', 10, 'jakoblist', 'jakoblist_manage');
		add_submenu_page('jakoblist', 'Verwalten', 'Bücherliste verwalten', 10, 'jakoblist', 'jakoblist_manage');
		add_submenu_page('jakoblist', 'Buch hinzufügen', 'Hinzufügen', 10, 'jakoblist_edit', 'jakoblist_edit');	
		/*add_submenu_page('jakoblist', 'remove', 'remove', 10, 'jakoblist_remove', 'jakoblist_remove');*/

}



add_shortcode('buecherliste', 'buecherliste');
add_action('admin_menu', 'jakoblist_dashboard');

if ($_GET["func"] == "jakoblist_remove") jakoblist_remove();
if ($_GET["func"] == "jakoblist_edit") jakoblist_edit();
if ($_GET["func"] == "jakoblist_add") jakoblist_add();
/*if ($_GET["func"] == 'search') jakoblist_search();*/
if (isset($_POST["search"])) search_url(); 

/*if ($_GET["page"] == "jakoblist_edit") jakoblist_editt();*/
?>
