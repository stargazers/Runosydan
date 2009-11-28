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

	// This site cannot be seen if user is not logged in.
	if(! isset( $_SESSION['username'] ) )
		header( 'Location: index.php' );

	require 'general_functions.php';

	// If user has selected to show poems with or without comments
	if( isset( $_GET['action'] ) )
	{
		// By default do not show comments
		$com = false;

		// This is required when we want to get comments
		require 'CPoem.php';
		$cPoem = new CPoem( $db, $_SESSION );

		if( $_GET['action'] == 'with_comments' )
			$com = true;

		// Select all poems by this writer.
		$q = 'SELECT id, title, poem FROM rs_poem WHERE user_id="'
			. $_SESSION['id'] . '"';

		try
		{
			$ret = $db->query( $q );
		}
		catch( Exception $e )
		{
			echo 'Tietokantavirhe!';
			die();
		}

		$num = $db->numRows( $ret );
		if( $num > 0 )
		{
			header( 'Content-Type: text/plain; charset=UTF-8' );
			$ret = $db->fetchAssoc( $ret );

			for( $i=0; $i < $num; $i++ )
			{
				echo $ret[$i]['title'] . "\n";
				echo str_pad( '-', strlen( $ret[$i]['title'] ), '-', STR_PAD_LEFT );
				echo "\n\n";
				echo $ret[$i]['poem'];
				echo "\n\n";

				// If user wanted comments too
				if( $com )
				{
					// Get all comments to this poem
					$comments = $cPoem->getComments( $ret[$i]['id'] );
					$num_comments = count( $comments );

					// If comments found, show separator
					if( $num_comments > 0 )
					{
						echo "==========================================\n";
						echo "   KOMMENTIT \n";
						echo "==========================================\n";
					}

					// Show all found comments
					for( $i2=0; $i2 < $num_comments; $i2++ )
					{
						echo $comments[$i2]['comment'] . "\n";
						echo 'Kommentoija: ' . $comments[$i2]['username'] 
							. "\n";
						echo $comments[$i2]['date_added'] . "\n\n";
					}
					echo "\n\n";
				}
			}
		}
	}
	else
	{
		create_site_top();
		create_top_menu();
		echo '<div class="download">';
		echo '<h2>Lataa runot</h2>';
		echo 'Voit ladata runosi joko kommenttien kanssa tai ilman niitä.<br><br>';
		echo '<a href="download.php?action=with_comments" target="_new">Kommenttien kanssa</a><br>';
		echo '<a href="download.php?action=without_comments" target="_new">Ilman kommentteja</a><br>';
		echo '<br>';
		echo '</div>';
	}
?>
