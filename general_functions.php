<?php

/*
General functions. Part of Runosydan.net.
Copyright (C) 2009	Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	session_start();
	require 'CMySQL.php';
	require 'CUsers.php';

	// If we do not have database class instance, then we do it now.
	if(! isset( $db ) )
		$db = create_db_instance();

	// *********************************************
	//	create_db_instance
	//
	//	@brief Create new database class instance.
	//
	// *********************************************
	function create_db_instance()
	{
		require 'db_settings.php';
		try
		{
			$db = new CMySQL();
			$db->connect( $db_server, $db_username, $db_password );
			$db->selectDatabase( $db_database );
			return $db;
		}
		catch( Exception $e )
		{
			echo 'Virhe tietokantayhteydessä!';
		}

		return null;
	}

	// *********************************************
	//	get_poet_username
	//
	//	@brief Get poet username by user ID
	//
	//	@param $id Poet ID.
	//
	// *********************************************
	function get_poet_username( $id )
	{

		$id = mysql_real_escape_string( $id );
		global $db;

		$cUsers = new CUsers( $db );

		// Try to get poet username.
		$q = 'SELECT username FROM rs_users WHERE id="' . $id . '"';
		$ret = $db->query( $q );

		// Poet usrename found, as it should.
		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );
			return $ret[0]['username'];
		}
			
		// Username is not found, uhm. Something strange is
		// happening here... :O
		return null;
	}


	// *********************************************
	//	show_poems
	//
	//	@brief Show poems in div.
	//
	//	@param $id Poet ID.
	//
	// *********************************************
	function show_poems( $id )
	{
		global $db;

		require_once 'CPoem.php';
		$cPoem = new CPoem( $db, $_SESSION );

		// Poems are splitted in multiple pages if there is too
		// many poems. If we do not have page number in URL, then
		// we want to show first page.
		if( isset( $_GET['page'] ) )
			$page = $_GET['page'];
		else
			$page = 1;

		// Get poems. Note! This does NOT return all the poems
		// at the time. This automatically split results if there
		// is too many poems in one page.
		$poems = $cPoem->getPoems( $id, $page );

		$poemsPerPage = $cPoem->getPoemsPerPage();
		$last = $poemsPerPage * $page;

		// Count number of poems.
		$numPoems = $cPoem->numPoems( $id );

		// Was there any poems in database?
		if(! is_null( $poems ) )
		{
			foreach( $poems as $cur )
			{
				$cur['added'] = date( 'd.m.Y H:i', 
					strtotime( $cur['added'] ) );
				$cur['poem'] = nl2br( $cur['poem'] );

				echo '<p class="poem_header">';
				echo stripslashes( $cur['title'] );
				echo '</p>';

				echo '<p class="poem">';
				$cur['poem'] = str_replace( '<br />', '<br>', 
					$cur['poem'] );
				echo stripslashes( $cur['poem'] );

				echo '<p class="poem_added">';
				echo $cur['added'];
				echo '</p>';

				// Get comments for this poem.
				$comments = $cPoem->getComments( $cur['id'] );
				$num = count( $comments );

				// If we are browsing our own poems, then we should
				// add also links where we can remove and 
				// modify those poems.
				if( isset( $_SESSION['id'] )
					&& $_SESSION['id'] == $id )
				{
					echo '<p class="poem_actions">';
					echo '<a href="edit_poem.php?id='
						. $cur['id'] . '">Muokkaa</a>';
					echo ' / ';
					echo '<a href="remove_poem.php?id='
						. $cur['id'] . '">Poista</a> / ';
					echo '<a href="#" id="comment' . $cur['id'] 
						. '" class="showComments" onClick="return false;">'
						. 'Näytä kommentit (' . $num . ')</a>';
					echo '</p>';
				}
				else
				{
					echo '<p class="poem_actions">';
					if( isset( $_SESSION['username'] ) )
					{
						echo '<a href="add_comment.php?id=' . $cur['id'] 
							. '&amp;page=' . $page . '">Jätä kommentti</a> / ';
					}
					echo '<a href="#" id="comment' . $cur['id'] 
						. '" class="showComments" onClick="return false;">'
						. 'Näytä kommentit (' . $num . ')</a>';
					echo '</p>';
				}

				// Generate unique ID so we can toggle comments
				// to visible and hidden by this unique ID.
				echo '<div class="poem_comment" id="poem_comment' . $cur['id'] . '">';
				for( $i=0; $i < $num; $i++ )
				{
					echo '<p>';
					echo $comments[$i]['comment'];
					echo '<br>';
					echo 'Kommentoija: <a href="poet.php?id=' . $comments[$i]['commenter_id'] 
						. '">' . $comments[$i]['username'] . '</a><br>';
					echo $comments[$i]['date_added'];
				}
				echo '</div>';
			}

			echo '<hr>';

			echo '<div class="change_page">';

			// Count number of poem pages.
			$numPages = ceil( $numPoems / $poemsPerPage );
			
			echo 'Sivu ' . $page . ' / ' . $numPages . '<br>';

			// Get the correct URL where user should be forwareded
			// if he/she press "Seuraava" or "Edellinen" link.
			if( isset( $_SESSION['id'] ) && $_SESSION['id'] == $id )
				$url = 'ownpage.php';
			else
				$url = 'poet.php';

			// Is "Seuraava sivu" shown?
			$prevShown = false;

			// Show link to previous poem page?
			if( $page > 1 )
			{
				$prevPage = $page -1;

				// Link to prev poem page
				echo '<a href="' . $url . '?page=' . $prevPage
					. '&amp;id=' . $id . '">' . 'Edellinen sivu</a>';

				$prevShown = true;
			}

			// Show link to next poem page?
			if( $numPoems > $last )
			{
				if( $prevShown )
					echo ' / ';
					
				$nextPage = $page + 1;

				// Link to next poem page
				echo '<a href="' . $url . '?page=' . $nextPage
					. '&amp;id=' . $id . '">' . 'Seuraava sivu</a>';
			}
		}
		else
		{
			echo '<p class="poem">';
			echo 'Yhtään runoa ei löytynyt.';
			echo '</p>';
		}

		echo '<br><br>';
		echo '</div>';
	}

	// *********************************************
	//	create_top_menu
	//
	//	@brief Create main menu at the top of the page.
	//
	// *********************************************
	function create_top_menu()
	{
		echo '<div class="menu">';

		// If there is unseen comments, then show link
		// where we can open a page where all those poems
		// are listed in one page so comments are easy
		// to check all at the same time.
		if( isset( $_SESSION['unseen_comments'] ) 
			&& $_SESSION['unseen_comments'] > 0 )
		{
			echo '<a href="show_new_comments.php">(';
			echo $_SESSION['unseen_comments'];
			echo ')</a>';
		
		}
		echo '<a href="index.php">Etusivu</a>';

		if( isset( $_SESSION['username'] ) )
			echo '<a href="ownpage.php">Oma sivu</a>';

		if( isset( $_SESSION['username'] ) )
			echo '<a href="logout.php">Kirjaudu ulos</a>';
		else
			echo '<a href="login.php">Kirjaudu</a>';

		echo '<a href="poets.php">Runoilijat</a>';
		echo '<a href="random.php">Satunnainen</a>';
		echo '</div>';
	}

	function create_site_top()
	{
		echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
		echo '<html>';
		echo '<head>';
		echo '<script type="text/javascript" src="jquery-1.3.2.min.js"></script>';

		echo '<script type="text/javascript">';
		echo '$(document).ready( function() {';

		// This function will be called when user clicks "Näytä kommentit"
		// and this will display/hide comments.
		echo '  $("a.showComments").click( function() {';
		echo '    var commentbox_id = $(this).attr("id");';
		echo '    $("#poem_" + commentbox_id).toggle();';
		echo ' } );';
		echo '} );';
		echo '</script>';
		echo '<title>Runosydän</title>';
		echo '<meta http-equiv="Content-Type" content="text/xhtml;charset=utf-8">';
		echo '<link rel="stylesheet" type="text/css" href="runosydan.css">';
		echo '</head>';
		echo '<body>';
	}

	function create_site_bottom()
	{
		echo '</body>';
		echo '</html>';
	}

	// *********************************************
	//	show_poet_info
	//
	//	@brief Show poet information. This information
	//		is usally shown in "Oma sivu" and also
	//		in poet info page.
	//
	//	@param $id Poet user ID.
	//
	// *********************************************
	function show_poet_info( $db, $id )
	{
		require_once 'CPoem.php';
		$cPoem = new CPoem( $db, $_SESSION );

		echo '<div>';

		$id = mysql_real_escape_string( $id );
		$numPoems = $cPoem->numPoems( $id );

		$q = 'SELECT firstname, lastname, city, homepage FROM rs_users '
			. 'WHERE id="' . $id . '"';
		
		$ret = $db->query( $q );

		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );

			echo '<p class="own_info">';

			if( isset( $ret[0]['firstname'] ) )
				echo $ret[0]['firstname'];

			if( isset( $ret[0]['lastname'] ) )
				echo ' ' . $ret[0]['lastname'];

			if( isset( $ret[0]['city'] ) )
				echo '<br>' . $ret[0]['city'];

			if( isset( $ret[0]['homepage'] ) 
				&& $ret[0]['homepage'] != '' )
			{
				echo '<br><a href="' . $ret[0]['homepage'];
				echo '">' . $ret[0]['homepage'] . '</a>';
			}

			echo '<br>Runoja: ' . $numPoems . '<br><br>';

			// If we are on own page, then we show some useful
			// links too, eg. possibility to change our informations.
			if( $_SESSION['id'] == $id )
			{
				echo '<a href="edit_profile.php">Muuta tietoja</a>';
				echo ' / ';
				echo '<a href="download.php">Lataa runot</a>';
				echo ' / ';
				echo '<a href="remove_profile.php">Poista käyttäjätunnus</a>';
			}
		}
		echo '</div>';
	}

	// *********************************************
	//	show_message
	//
	//	@brief Show message and message icon if
	//		there is session variable set.
	//
	// *********************************************
	function show_message()
	{
		if( isset( $_SESSION['message'] ) )
		{
			echo '<div class="message">';

			echo '<table><tr><td>';

			if( isset( $_SESSION['message_icon'] ) )
				echo '<img src="' . $_SESSION['message_icon'] . '">';

			echo '</td><td>';
			echo $_SESSION['message'];
			echo '</td></tr></table>';
			echo '<br>';
			echo '</div>';

			unset( $_SESSION['message'] );
			unset( $_SESSION['message_icon'] );
		}
	}

	// *********************************************
	//	set_message
	//
	//	@brief Set message to session variable.
	//
	//	@param $msg Message
	//
	//	@param $type 0 = Ok-icon, 1 = Error-icon
	//
	// *********************************************
	function set_message( $msg, $type )
	{
		// Icon to show
		if( $type == 0 )
			$icon = 'graphics/32px-Crystal_Clear_app_clean.png';
		else
			$icon = 'graphics/32px-Crystal_Clear_app_logout.png';

		$_SESSION['message'] = $msg;
		$_SESSION['message_icon'] = $icon;
	}
?>
