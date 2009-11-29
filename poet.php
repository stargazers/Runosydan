<?php

/*
Poets poempage. Part of Runosydan.net.
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
	//	get_id
	//
	//	@brief Check if ID is set.
	//
	//	@param $db Database class instance
	//
	//	@param $data GET-data
	//
	// *********************************************
	function get_id( $db, $data )
	{
		$id = null;
		$username = null;

		if(! isset( $data['id'] ) )
			set_message( 'Virheellinen käyttäjä-ID', 1 );
		else
			$id = mysql_real_escape_string( $data['id'] );

		if(! is_null( $id ) )
		{
			$username = get_poet_username( $id );

			if( is_null( $username ) )
				set_message( 'Annetulla ID:llä ei löydy käyttäjää!', 1 );
		}

		if( is_null( $username ) )
		{
			echo '<div class="poems">';
			show_message();
			echo '</div>';
			create_site_bottom();
			die();
		}

		return $id;
	}

	// *********************************************
	//	show_page
	//
	//	@brief Show poet page.
	//
	//	@param $id Poet user ID.
	//
	// *********************************************
	function show_page( $id )
	{
		echo '<div class="poems">';
		echo '<p class="poet_name">';

		// Show poet username as a link.
		echo '<a href="rss.php?poet_id=' . $id . '">';
		echo '<img src="graphics/rss.gif" class="rss"></a>&nbsp;&nbsp;&nbsp;';
		echo '<a href="show_poet_info.php?id=' . $id . '">';
		echo get_poet_username( $id );
		echo '</a>';
		echo '</p>';

		// Show poet poems.
		show_poems( $id );

		echo '</div>';
	}

	session_start();
	require 'general_functions.php';

	create_site_top();
	create_top_menu();

	// Get user ID.
	$id = get_id( $db, $_GET );

	show_page( $id );

	create_site_bottom();

?>
