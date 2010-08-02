<?php

/*
Comment adding page. Part of Runosydan.net.
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

	// This site cannot be seen if user is not logged in.
	if(! isset( $_SESSION['username'] ) )
		header( 'Location: index.php' );

	require 'general_functions.php';

	if(! isset( $_GET['action'] ) )
	{
		create_site_top();
		create_top_menu();
		echo '<div class="leave_comment">';

		if(! isset( $_GET['id'] ) )
		{
			echo 'Olet tullut tälle sivulle väärää kautta.';
			echo '<br><br>';
			echo '</div>';
			die();
		}

		// Get a poem and its title
		$q = 'SELECT user_id, title, poem FROM rs_poem WHERE id="' 
			. $_GET['id'] . '"';

		try 
		{
			$ret = $db->query( $q );
		}
		catch( Exception $e )
		{
			echo 'Error in database query!<br><br>';
			die();
		}

		// If poem is found, give commenting box
		// and show the poem.
		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );
			$ret[0]['poem'] = nl2br( $ret[0]['poem'] );
	
			echo '<div class="textblock">';
			echo '<h3>Kommentoi runoa</h3>';
			echo '<div class="inner_textblock">';
			echo '<p class="poem_header">';
			echo $ret[0]['title'];
			echo '</p>';
			echo '<p class="poem">';
			$ret[0]['poem'] = str_replace( '<br />', '<br>', 
					$ret[0]['poem'] );
			echo stripslashes( $ret[0]['poem'] );
			echo '</p>';

			echo '<form action="add_comment.php?action=add" method="post">';
			echo '<textarea name="comment"></textarea><br>';
			echo '<input type="hidden" name="id" value="' . $_GET['id'] . '">';
			echo '<input type="hidden" name="page" value="' . $_GET['page'] . '">';
			echo '<input type="hidden" name="poet_id" value="' . $ret[0]['user_id']. '">';
			echo '<input type="submit" value="Kommentoi">';
			echo '</form>';
			echo '<br>';
			echo '<a href="poet.php?id=' . $ret[0]['user_id'] 
				. '&amp;page="' . $ret[0]['page'] . '">Takaisin runoihin</a>';
			echo '<br><br>';
			echo '</div>';
			echo '</div>';
			echo '</div>';
		}
	}
	// Add comment to poem and forward back to poems list.
	else
	{
		$id = mysql_real_escape_string( $_POST['id'] );
		$comment = mysql_real_escape_string( $_POST['comment'] );

		$q = 'INSERT INTO rs_comments VALUES( "", "' . $id . '", "'
			. $_SESSION['id'] . '", "' . $comment . '", "'
			. date( 'Y-m-d H:i:s' ) . '", "0", ' . $_POST['poet_id'] . ' )';

		// Add comment and forward user back to correct page.
		try
		{
			$db->query( $q );
			header( 'Location: poet.php?id=' . $_POST['poet_id'] 
				. '&page=' . $_POST['page'] );
		}
		catch( Exception $e )
		{	
			echo 'Virhe tietokantakyselyssä!';
		}
	}
?>
