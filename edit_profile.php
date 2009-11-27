<?php

/*
Profile editing page. Part of Runosydan.net.
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
	//	show_profile_edit_form
	//
	//	@brief Create a form where we can change
	//		profile information.
	//
	// *********************************************
	function show_profile_edit_form( $db )
	{
		//require 'CUsers.php';

		// Get information of logged user.
		$cUsers = new CUsers( $db );
		$ret = $cUsers->getUserInfo( $_SESSION['id'] );

		// Create birthdate in correct format.
		$tmp = explode( '-', $ret[0]['birthdate'] );
		$ret[0]['birthdate'] = $tmp[2] . '.' . $tmp[1] . '.' . $tmp[0];

		// Fields to add in form
		$fields = array(
			'id' => 'hidden',
			'firstname' => 'text',
			'lastname' => 'text',
			'city' => 'text',
			'homepage' => 'text',
			'birthdate' => 'text',
			'email' => 'text',
			'password' => 'password',
			'password_again' => 'password' );

		// Field names 
		$field_names = array(
			'firstname' => 'Etunimi',
			'lastname' => 'Sukunimi',
			'password' => 'Salasana',
			'password_again' => 'Salasana uudelleen',
			'city' => 'Kaupunki',
			'homepage' => 'Kotisivu',
			'birthdate' => 'Syntymäpäivä (pv.kk.vvvvv)',
			'email' => 'Sähköposti' );

		echo '<div class="edit_profile">';

		// Show message if there is any.
		// For example message will be shown if user has changed
		// his/her profile.
		show_message();

		echo '<form action="edit_profile.php" method="post">';
		echo '<table>';

		// Add every field in the form.
		foreach( $fields as $key => $value )
		{
			echo '<tr>';

			// We do not want add empty input boxes for hidden values!
			if( $value != 'hidden' )
			{
				echo '<td>';
				echo $field_names[$key];
				echo '</td>';

				echo '<td>';

				// If text field is other than password, then we
				// should get user information as an default value.
				if( ( $key != 'password' && $key != 'password_again' ) )
				{
					// If we are going to show birthdate, let's check
					// if it is empty value, eg. 00.00.0000. If it is,
					// then we want to show just empty string instead
					// of that 00.00.0000.
					if( $key == 'birthdate' 
						&& $ret[0][$key] == '00.00.0000' ) 
					{
						$ret[0][$key] = '';
					}

					echo '<input type="' . $value . '" value="'
						. $ret[0][$key] . '" name="' . $key . '">';
				}
				else
				{
					echo '<input type="' . $value . '" name="' 
						. $key . '">';
				}

				echo '</td>';
			}

			echo '</tr>';
		}

		// "Päivitä tiedot" -button.
		echo '<tr>';
		echo '<td colspan="2">';
		echo '<input type="submit" value="Päivitä">';
		echo '</td>';
		echo '</tr>';

		echo '</table>';

		echo '<div class="bottom_note">';
		echo 'Huom! Jos jätät salasanakentät tyhjäksi, salasana '
			. 'pysyy muuttamattomana!';

		echo '</div>';

		echo '</form>';
		echo '</div>';
	}

	// *********************************************
	//	check_post_values
	//
	//	@brief Check POST-data and modify user
	//		information.
	//
	//	@param $db Database class instance
	//
	//	@param $data POST-data.
	//
	// *********************************************
	function check_post_values( $db, $data )
	{
		$changePassword = false;

		$firstname = mysql_real_escape_string( $data['firstname'] );
		$lastname = mysql_real_escape_string( $data['lastname'] );
		$birthdate = mysql_real_escape_string( $data['birthdate'] );
		$city = mysql_real_escape_string( $data['city'] );
		$homepage = mysql_real_escape_string( $data['homepage'] );
		$email = mysql_real_escape_string( $data['email'] );
		$password = mysql_real_escape_string( $data['password'] );
		$password_again = mysql_real_escape_string( 
			$data['password_again'] );

		// Did user changed her/his password too?
		if( $password != '' && $password_again != '' )
		{
			// Password and password again match? Create SHA1.
			if( $password == $password_again )
			{
				$password = mysql_real_escape_string( $password );
				$password = sha1( $password );
				$changePassword = true;
			}
		}	

		// Generate birthdate
		$tmp = explode( '.', $birthdate );

		// If there is enough indexes, then create birthday in
		// correct format for database.
		if( count( $tmp ) == 3 )
			$birthdate = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
		
		// Update basic informations
		$q = 'UPDATE rs_users SET firstname="' . $firstname . '", '
			. 'lastname="' . $lastname . '", '
			. 'city="' . $city . '", '
			. 'homepage="' . $homepage . '", '
			. 'birthdate="' . $birthdate . '", '
			. 'email="' . $email . '"';

		// If user has changed password, then update password too.
		if( $changePassword )
			$q .= ', password="' . $password . '"';

		$q .= ' WHERE id="' . $_SESSION['id'] . '"';

		try
		{
			$db->query( $q );

			// Icon to show
			$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
				. '_app_clean.png';

			$_SESSION['message'] = 'Käyttäjätiedot päivitetty.';

		}
		catch( Exception $e )
		{
			// Icon to show
			$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
				. '_app_logout.png';

			// Someting went wrong! Show error.
			$_SESSION['message'] = 'Tietokantavirhe! '
				. 'Tietoja ei päivitetty.';
		}
	}

	require 'general_functions.php';

	// Check if user has already sent POST-data.
	if( isset( $_POST ) && ! empty( $_POST ) )
		check_post_values( $db,  $_POST );

	// Create form where we can change our informations.
	create_site_top();
	create_top_menu();
	show_profile_edit_form( $db );
	create_site_bottom();

?>
