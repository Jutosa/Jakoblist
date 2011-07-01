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
global $wpdb; //Important! DBQueries don't work without this!
include_once(ABSPATH.'wp-content/plugins/jakoblist/jakoblist-setup.php');
include_once(ABSPATH.'wp-content/plugins/jakoblist/jakoblist-functions.php');
require(ABSPATH . WPINC . '/pluggable.php');
register_activation_hook(__FILE__,'jakoblist_install');
register_activation_hook(__FILE__,'jakoblist_install_data');



function search_url()
{

	echo 'meep';

}




function jakoblist_output($atts, $content = null)
{
	
	/*####################################################################################
	# This function generates the output for the front-end.                              #
	# The shortcode [jakoblist_output] is used to place the list within pages or posts.  #
	#####################################################################################*/
	
	global $wpdb; //Important! DBQueries don't work without this!
	

	$books = $wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` WHERE `active` = 1 order by `title`" );
	$result ='';
	if(count($books) == 0)
		{
			/* If there are no books */
		}
	else
		{
			/* If there are books */
			foreach($books as $book)
				{	
					$thetitle		=	strclean($book->title);
					$theauthor		=	strclean($book->author);
					$thepublisher		=	strclean($book->publisher);
					$theinfo		=	strclean($book->info);
					$thecurrency		= 	'€';
					
					if($book->price !== '0.00')
						{
							$theprice 	=	'; '.$thecurrency.strclean(str_replace('.', ',', $book->price));
						}
					else
						{
							$theprice	=	'';
						}
					
					$result =  $result.'<p><span style="color: #D9A404;"><em><strong>'.$thetitle.'</strong></em></span><br /><strong>'.$theauthor.'</strong><br />'.$thepublisher.'<br />'.$theinfo.$theprice.'</p>';
				}
		}
		
	return $result;

}

function jakoblist_add()
{
	/*####################################################################################
	# This function provides add end edit functionality. If there's an ID in the URL the #
	# corrensponding database entry will be edited; if there's no ID a new entry is      #
	# created.                                                                           #
	#####################################################################################*/

	global $wpdb; //Important! DBQueries don't work without this!
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
	
	($_POST['active']) ? $active = 1 :  $active = 0;


	if(!$id == '1')
		{
			$wpdb->insert(
				$table_name,
				array(
					'created' 	=> 	$thecreation,
					'createdby' 	=> 	$thecreator,
					'title' 	=> 	$thetitle,
					'author' 	=> 	$theauthor,
					'publisher' 	=> 	$thepublisher,
					'info' 		=> 	$theinfo,
					'price' 	=> 	$theprice,
					'active' 	=> 	$active	 
				     ) 
				     );
		}
	else
		{
			$wpdb->update(
				$wpdb->prefix.'jakoblist',
				array(
					'title' 	=> 	$thetitle,
					'modifiedby' 	=> 	$themodificator,
					'modified' 	=> 	$themodification,
					'author' 	=> 	$theauthor,
					'publisher' 	=> 	$thepublisher,
					'info' 		=> 	$theinfo,
					'price' 	=> 	$theprice,
					'active' 	=> 	$active
				     ),
				array(
					'id' 	=> 	$id
				     )
				     );	
		}

}

function jakoblist_edit()
{
	/*####################################################################################
	# This functions serves as both the way to add new books and the way to edit         #
	# exisiting books.                                                                   #
	#	*	the 'add' functionality is accessible through the dashboard sidebar  # 
	#       	menu.                                                                #
	#	* 	the 'edit' functionality is accessible by clicking the edit-button   #
	#               in the jakoblist management view                                     #
	#####################################################################################*/

	global $wpdb; //Important! DBQueries don't work without this!
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
							
							($book->active) ? $checked = 'checked="yes"' : $checked = '';
							$cancelbtn 		= '<a href="admin.php?page=jakoblist" class="button">Abbrechen</a>';
						}
				}
			else	/*	If no id is provided in the URL the 'add' functionality is loaded */
				{
					$pagetitle		= 	__("Hinzufügen eines Buches");
					$formaction 		= 	'admin.php?page=jakoblist&func=jakoblist_add'.$edit;
					$savebtn  		=	__("Hinzufügen");
					$checked 		= 	'checked="yes"';
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
							<td><input type="text" size="10" maxlength="10" name="price" value="'.$theprice.'" /><br /><em>Max. 200 Zeichen</em></td><td></td>
						</tr>
						<tr>
							<td><strong>Aktiv?</strong></td>
							<td><input type="checkbox"'.$checked.' name="active" value="active"></td>
						</tr>
					</table>
					<br /><input type="submit" class="button-primary" value=" '.$savebtn.' "/>&nbsp;'.$cancelbtn.' 
				</form>';
			
			
				?>
	
	</div>
	<?php
	
	
	
	
}


function jakoblist_remove()
{
	/*####################################################################################
	# This function deletes the entry that matches the ID.                               #
	#####################################################################################*/

	global $wpdb; //Important! DBQueries don't work without this!
	
	$id 	= 	$_GET['id'];  //Extracts the ID from the URL.
	$SQL 	= 	"DELETE FROM `".$wpdb->prefix."jakoblist` WHERE `id` = $id"; //The SQL query needed to delete the entry matching the ID.
	$wpdb->query($wpdb->prepare($SQL)); //Execution of the SQL query
}

function jakoblist_manage()
{

	global $wpdb; //Important! DBQueries don't work without this!

	/*
	Scan the URL for sort/order parameters and keep them.
	*/

		$searchterm 	= 	$_GET['search'];

		switch ($_GET['orderby'])
			{
				case 'title':
					$orderby 	= 	"title";
					break;
				case 'author':
					$orderby 	= 	"author";
					break;
				case 'publisher':
					$orderby 	= 	"publisher";
					break;
				case 'info':
					$orderby 	= 	"info";
					break;
				case 'price':
					$orderby 	= 	"price";
					break;
				default:
					$orderby 	= 	"title";
			}


		switch ($_GET['order'])
			{
				case 'desc':
					$order 	= 	"DESC";
					break;
				default:
					$order 	= 	"";
			}
		
		if($_GET['search'] == '' ) 
			{ /* Alles außer Suche */ 
				$books 	= 	$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` order by `".$orderby."`".$order."" );
			}
		else 
			{ /* Suche */	
				$books 	= 	$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` WHERE `title` LIKE '%".$searchterm."%' OR `author` LIKE '%".$searchterm."%' OR `publisher` LIKE '%".$searchterm."%' OR `info` LIKE '%".$searchterm."%' order by `".$orderby."` ".$order."" );	
			}

		

	?>
	<div class="wrap">
		<h2>Bücherliste verwalten <a href="admin.php?page=jakoblist_edit" class="button add-new-h2" >Neues Buch</a><?php if($_GET['search']) echo '<span class=\'subtitle\'> 	Suchergebnisse für "'.$_GET['search'].'"</span>'; ?></h2>
		<table style="margin-bottom:0.2em;">
			<tr>
				<td class="tablenav">
					<span class="displaying-num"><?php echo count($books); ?>&nbsp;<?php echo 'Bücher'; ?></span>
				</td>
				<td>
					<form action="admin.php?" method="get">
				</td>
				<td width="100%">
				</td>
				<td>
					<input type="text" value="<?php echo $_GET["search"] ?>" name="search" />
				</td>
				<td>
					<input type="hidden" name="page" value="jakoblist">
					<input type="hidden" name="orderby" value="<?php echo $orderby; ?>">
					<input type="hidden" name="order" value="<?php echo $order; ?>">
					<input type="submit" value=" <?php echo _("suchen") ?> " class="button-secondary" />			
				</td>
					</form>
			</tr>
		</table>
		

	<table id="mytable" class="widefat" width="50%">
	<thead>
		<tr>
			<th width="20%" class="manage-column column-date <?php sort_class('title', $orderby, $order) ?>"><a href="<?php echo sort_link('title', $orderby, $order, $searchterm); ?>">Titel<?php if($orderby == 'title') echo '<span class=sorting-indicator>&nbsp;</span>'; ?></a></th>
			<th width="20%" class="manage-column column-date <?php sort_class('author', $orderby, $order) ?>"><a href="<?php echo sort_link('author', $orderby, $order, $searchterm); ?>">Autor<?php if($orderby == 'author') echo '<span class="sorting-indicator"></span>'; ?></a></th>
			<th width="20%" class="manage-column column-date <?php sort_class('publisher', $orderby, $order) ?>"><a href="<?php echo sort_link('publisher', $orderby, $order, $searchterm); ?>">Verlag<?php if($orderby == 'publisher') echo '<span class="sorting-indicator"></span>'; ?></a></th>
			<th class="manage-column column-date <?php sort_class('info', $orderby, $order) ?>"><a href="<?php echo sort_link('info', $orderby, $order, $searchterm); ?>">Information<?php if($orderby == 'info') echo '<span class="sorting-indicator"></span>'; ?></th>
			<th colspan="3" width="10"></th>
		</tr>
	</thead>
	<tbody>
		<?php /*<tr> 
			<form action="admin.php?page=jakoblist&func=jakoblist_add" method="post">
				<td><input name="title" type="text" size="30%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="author" type="text" size="30%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="publisher" type="text" size="30%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="info" type="text" size="50%" maxlength="200"><br /><em>Max. 200 Zeichen</em></td>
				<td><input name="price" type="text" size="10" maxlength="10"><br /><em>Max. 200 Zeichen</em></td>
				<td align="left" colspan="2"><input type="submit" name="add" value=" ✚ " class="button-primary" onclick=''></td>
			</form>
		</tr> */?>
	<?php

		global $wpdb; //Important! DBQueries don't work without this!
		
		
		/*if($_GET['search'] == '' ) 
			{
 				$books 	= 	$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` order by `".$orderby."`".$order."" );
			}
		else 
			{ 
				$books 	= 	$wpdb->get_results( "SELECT * FROM `".$wpdb->prefix."jakoblist` WHERE `title` LIKE '%".$searchterm."%' OR `author` LIKE '%".$searchterm."%' OR `publisher` LIKE '%".$searchterm."%' OR `info` LIKE '%".$searchterm."%' order by `".$orderby."` ".$order."" );	
			}*/
		
		if(count($books) > 0)
			{ /* keine Suchergebnisse */			
				foreach($books as $book)
					{
						$thecurrency 	= 	'&nbsp;€'; //temporary, check back soon
						$status		= 	(!$book->active) ? '<br /><span class="post-state">Entwurf</span>' : '';	
						$class 		= 	('alternate' != $class) ? 'alternate' : '';
						$editlink 	= 	get_bloginfo('wpurl').'/wp-admin/admin.php?page=jakoblist_edit&id='.$book->id;
						echo '<form action="" method="post"><tr class="'.$class.'">';
						echo '<td><strong><a class="row-title" href="'.$editlink.'">'.strclean($book->title).'</a>'.$status.'</strong></td>';
						echo '<td>'.strclean($book->author).'</td>';
						echo '<td>'.strclean($book->publisher).'</td>';
						echo '<td>'.strclean($book->info).'</td>';
						echo '<td align="right">'.strclean($book->price).$thecurrency.'</td>';
						/*echo '<td align="left"><input type="button" name="edit" value=" ✎ " class="button-secondary" onclick=location.href="';
						echo $editlink.'"';
						echo '></td>';*/
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



add_shortcode('jakoblist_output', 'jakoblist_output');
add_action('admin_menu', 'jakoblist_dashboard');

if ($_GET["func"] == "jakoblist_remove") jakoblist_remove();
if ($_GET["func"] == "jakoblist_edit") jakoblist_edit();
if ($_GET["func"] == "jakoblist_add") jakoblist_add();
/*if ($_GET["func"] == 'search') jakoblist_search();*/
if (isset($_POST["search"])) search_url(); 

/*if ($_GET["page"] == "jakoblist_edit") jakoblist_editt();*/
?>
