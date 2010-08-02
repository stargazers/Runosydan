<?php

/*
User profilepage. Part of Runosydan.net.
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
	//	show_poet_information
	//
	//	@brief Show poet information
	//
	//	@param $db Database class instance
	//
	//	@param $data GET-data
	//
	// *********************************************
	function show_poet_information( $db, $data )
	{
		$cUsers = new CUsers( $db );

		echo '<div class="poet_info">';
		echo '<div class="textblock">';
		echo '<h3>Runoilijan tiedot</h3>';
		echo '</div>';


		// Get user ID.
		if( isset( $data['id'] ) )
			$id = mysql_real_escape_string( $data['id'] );
		else
			echo 'Käyttäjän ID:tä ei olla annettu!';

		$ret = $cUsers->getUserInfo( $id );

		// Fields to add in form
		$fields = array(
			'username' => 'Käyttäjätunnus',
			'firstname' => 'Etunimi',
			'lastname' => 'Sukunimi',
			'city' => 'Kaupunki',
			'homepage' => 'Kotisivu',
			'birthdate' => 'Syntymäpäivä',
			'email' => 'Sähköposti',
			'num_poems' => 'Runoja' );

		// Count number of poems
		$cPoem = new CPoem( $db, $_SESSION );

		if( $_SESSION['id'] == $id )
			$ret[0]['num_poems'] = $cPoem->numPoems( $ret[0]['id'], true );
		else
			$ret[0]['num_poems'] = $cPoem->numPoems( $ret[0]['id'], false );

		echo '<table>';

		foreach( $ret[0] as $key => $value )
		{
			// Convert birthdate to correct format
			if( $key == 'birthdate' && $value != '' )
			{
				$tmp = explode( '-', $value );
				$value = $tmp[2] . '.' . $tmp[1] . '.' . $tmp[0];
			}

			// Show only fields what are listed in array $fiels
			if( isset( $fields[$key] ) )
			{
				echo '<tr>';
				echo '<td>';
				echo $fields[$key];
				echo '</td>';

				echo '<td>';
				echo $value;
				echo '</td>';
				echo '</tr>';
			}
		}

		echo '</table>';

		// Link back to poems
		echo '<div class="back_to_poems">';
		echo '<a href="poet.php?id=' . $ret[0]['id'] . '">'	
			. 'Takaisin runoilijan runoihin</a>';
		echo '</div>';
		echo '</div>';
	}

	require 'general_functions.php';
	require 'CPoem.php';

	create_site_top();
	create_top_menu();

	show_poet_information( $db, $_GET );

	create_site_bottom();
?>
