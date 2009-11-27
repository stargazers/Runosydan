<?php

/*
Loginpage. Part of Runosydan.net.
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

	// *********************************************
	//	check_post_values
	//
	//	@brief Check if there is any POST-values. If there is,
	//		then user has tried to login.
	//
	//	@param $db Database class instance
	//
	//	@param $data POST-data 
	//
	// *********************************************
	function check_post_values( $db, $data )
	{
		// Is there username and password given in POST-data?
		if( isset( $data['username'] ) 
			&& isset( $data['password'] ) )
		{
			// No SQL-injections!
			$username = mysql_real_escape_string( $data['username'] );
			$password = mysql_real_escape_string( $data['password'] );

			// Get user information by username
			$q = 'SELECT id, username, password FROM rs_users WHERE '
				. 'username="' . $username . '"';

			$ret = $db->query( $q );

			// If we found in database user, then check password
			if( $db->numRows( $ret ) > 0 )
			{
				$row = $db->fetchAssoc( $ret );

				// Is password correct? If it is, then set
				// session variables.
				if( $row[0]['password'] == sha1( $password ) )
				{
					$_SESSION['id'] = $row[0]['id'];
					$_SESSION['username'] = $row[0]['username'];

					// Icon to show
					$_SESSION['message_icon'] = 
						'graphics/32px-Crystal_Clear_app_clean.png';

					$_SESSION['message'] = 'Olet kirjautunut sisään '
						. 'käyttäjätunnuksella "' . $username . '"';
				}
				else
				{
					// Icon to show
					$_SESSION['message_icon'] = 
						'graphics/32px-Crystal_Clear_app_logout.png';

					// Someting went wrong! Show error.
					$_SESSION['message'] = 'Virheellinen salasana!';
				}
			}	
			else
			{
				// Icon to show
				$_SESSION['message_icon'] = 
					'graphics/32px-Crystal_Clear_app_logout.png';

				// Someting went wrong! Show error.
				$_SESSION['message'] = 'Käyttäjätunnusta ei löytynyt!';
			}
		}
	}

	// *********************************************
	//	create_login_form
	//
	//	@brief Create login form
	//
	// *********************************************
	function create_login_form()
	{
		echo '<form action="login.php" method="post">';
		echo '<table>';
		echo '<tr><td>Käyttäjätunnus</td>';
		echo '<td><input type="text" name="username"></td></tr>';
		echo '<tr><td>Salasana</td>';
		echo '<td><input type="password" name="password"></td></tr>';
		echo '';
		echo '<tr><td colspan="2"><input type="submit" '
			. 'value="Kirjaudu"></td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		echo '<a href="register.php">Luo uusi käyttäjätunnus</a>';
	}

	session_start();
	require 'general_functions.php';

	// Check POST-values if there is any.
	check_post_values( $db, $_POST );

	// Create html start tags and top menu
	create_site_top();
	create_top_menu();

	echo '<div class="login">';

	// Show possible messages (eg. if login is succesfully made or
	// if something has failed in login or something like that).
	show_message();

	// If we are not logged in, show login form.
	if(! isset( $_SESSION['username'] ) )
		create_login_form();

	echo '</div>';

	// Create HTML end tags.
	create_site_bottom();
?>

