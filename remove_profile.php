<?php

/*
Profile remove page. Part of Runosydan.net.
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

	// create_site_top and create_site_menu is defined in
	// file general_functions.php
	require 'general_functions.php';

	// If user has not pressed Account Remove button, then show the form
	if(! isset( $_GET['action'] ) )
	{
		create_site_top();
		create_top_menu();

		echo '<div class="remove_profile">';
		echo '<h3>Käyttäjätunnuksen poisto</h3>';
		echo '<p>Voit poistaa käyttäjätunnuksesti painamalla alempana olevaa '
			. 'painiketta sekä antamalla salasanasi alempana olevaan laatikkoon. '
			. '<br><br><b>Huomaa että käyttäjätunnuksen palauttaminen '
			. 'ei ole mahdollista</b>, vaan kaikki tiedot poistetaan '
			. 'tietokannasta. Voit tietenkin myöhemmin tehdä käyttäjätunnuksen '
			. 'uudelleen mikäli tahdot.</p>';
		echo '<form method="post" action="remove_profile.php?action=remove">';
		echo 'Salasanasi: <input type="password" name="password">&nbsp;&nbsp;';
		echo '<input type="submit" value="Poista tunnus">';
		echo '</form>';
	}
	// User pressed account remove button, try to remove account
	else
	{
		// Search user by ID.
		$q = 'SELECT password FROM rs_users WHERE id="' . $_SESSION['id'] . '"';
		$ret = $db->query( $q );

		// User found (If it is not found, then something is wrong...)?
		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );

			// Make sure that given password match with the password
			// that is stored to database.
			if( $ret[0]['password'] == sha1( $_POST['password'] ) )
			{
				// Remove user account
				$q = 'DELETE FROM rs_users WHERE id="' . $_SESSION['id'] . '"';
				try 
				{
					$db->query( $q );
				} 
				catch( Exception $e ) 
				{
					echo 'Virhe tietokantayhteydessä!';
				}

				// Remove all poems by this user
				$q = 'DELETE FROM rs_poem WHERE user_id="' . $_SESSION['id'] . '"';
				try 
				{
					$db->query( $q );
				} 
				catch( Exception $e ) 
				{
					echo 'Virhe tietokantayhteydessä!';
				}

				// Remove also login information from sessions.
				unset( $_SESSION['username'] );
				unset( $_SESSION['id'] );

				create_site_top();
				create_top_menu();

				echo '<div class="remove_profile">';
				echo '<br>';
				echo 'Käyttäjätunnuksesi on nyt poistettu!<br><br>';
				echo '<a href="index.php">Etusivulle</a><br><br>';
				echo '</div>';
			}
			else
			{
				create_site_top();
				create_top_menu();
				echo '<div class="remove_profile">';
				echo '<br>';
				echo 'Antamasi salasana oli väärä!<br><br>';
				echo '<a href="remove_profile.php">Palaa takaisin</a><br><br>';
				echo '</div>';
			}
		}

	}
	echo '<br>';
	echo '</div>';

?>
