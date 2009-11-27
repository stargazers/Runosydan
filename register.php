<?php

/*
Registering page. Part of Runosydan.net.
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
	//	@brief Check if there is POST-values
	//
	//	@param $db Database class instance
	//
	//	@param $data POST-data.
	//
	// *********************************************
	function check_post_values( $db, $data )
	{
		// If user has pressed "Rekisteröidy" -button,
		// then username key index is set. Let's try to
		// register new user.
		if( isset( $data['username'] ) )
		{
			require 'CRegister.php';

			// Try to register new user.
			try
			{
				$reg = new CRegister( $db, $data );
				
				// Message to show after registeration.
				$_SESSION['message'] = 'Käyttäjätunnus "' 
					. $data['username'] . '" on nyt rekisteröity.';

				// Icon to show.
				$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
					. '_app_clean.png';
			}
			catch( Exception $e )
			{
				// Icon to show
				$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
					. '_app_logout.png';

				$_SESSION['message'] = $e->getMessage();
			}

			// If there were no errors, good.
			if(! isset( $_SESSION['message'] ) )
			{
				create_site_top();
				create_top_menu();

				echo '<div class="register">';
				show_message();
				echo '</div>';

				create_site_bottom();
				die();
			}
			else
			{
				create_site_top();
				create_top_menu();
				echo '<div class="register">';

				// If message icon is app_logout then there
				// was problem while registering. We must give a link
				// where user can go back to registering page.
				if( $_SESSION['message_icon'] == 'graphics/32px-Crystal_Clear_app_logout.png' )
					$back_to_register = true;
				else
					$back_to_register = false;

				show_message();

				// Show correct link, depending on how succesfully user
				// registering was.
				if( $back_to_register )
					echo '<a href="register.php">Takaisin rekisteröitymissivulle</a><br><br>';
				else
					echo '<a href="login.php">Kirjautumissivulle</a><br><br>';

				echo '</div>';
				create_site_bottom();
				die();
			}
		}
	}

	// *********************************************
	//	show_register_form
	//
	//	@brief Create registeration form.
	//
	// *********************************************
	function show_register_form()
	{
		create_site_top();
		create_top_menu();

		// Fields to add in form
		$fields = array(
			'username' => 'text',
			'password' => 'password',
			'password_again' => 'password',
			'firstname' => 'text',
			'lastname' => 'text',
			'city' => 'text',
			'homepage' => 'text',
			'birthdate' => 'text',
			'email' => 'text' );

		// Field names 
		$field_names = array(
			'username' => 'Käyttäjätunnus (*)',
			'firstname' => 'Etunimi',
			'lastname' => 'Sukunimi',
			'password' => 'Salasana (*)',
			'password_again' => 'Salasana uudelleen (*)',
			'city' => 'Kaupunki',
			'homepage' => 'Kotisivu',
			'birthdate' => 'Syntymäpäivä (pv.kk.vvvvv)',
			'email' => 'Sähköposti' );

		echo '<div class="register">';

		// Show message if there is any.
		// For example message will be shown if user has changed
		// his/her profile.
		show_message();

		echo '<form action="register.php" method="post">';
		echo '<table>';

		// Add every field in the form.
		foreach( $fields as $key => $value )
		{
			echo '<tr>';
			echo '<td>';
			echo $field_names[$key];
			echo '</td>';

			echo '<td>';
			echo '<input type="' . $value . '" name="' . $key . '">';
			echo '</td>';
			echo '</tr>';
		}

		// "Rekisteröidy" -button.
		echo '<tr>';
		echo '<td colspan="2">';
		echo '<input type="submit" value="Rekisteröidy">';
		echo '</td>';
		echo '</tr>';

		echo '</table>';
		echo '</form>';
		echo '<p>Vain tähdellä merkityt kohdat ovat pakollisia.</p>';
		echo '</div>';
	}


	session_start();
	require 'general_functions.php';

	// Check if there is POST-values set and try to register new user
	// if there is any values given.
	check_post_values( $db, $_POST );

	// Normally we show registeration form. This will never be called
	// if user has sent POST-data and new user is registered.
	show_register_form();

	$showForm = true;
	$errorMessage = '';

?>

