<?php

/*
Poets listpage. Part of Runosydan.net.
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
	require 'general_functions.php';

	// Create headers + menu
	create_site_top();
	create_top_menu();
	
	echo '<div class="poets_list">';

	// Get poets
	$q = 'SELECT id, username FROM rs_users ORDER BY username';
	try
	{
		$ret = $db->query( $q );
	}
	catch( Exception $e )
	{
		echo 'Virhe tietokantakyselyssä!';
		die();
	}

	// Here we store poets and create own index for each found alphabet
	$users = array();
	
	// Any poets?
	if( $db->numRows( $ret ) > 0 )
	{
		$ret = $db->fetchAssoc( $ret );
		$num = 0;

		// Add poets to array $users
		foreach( $ret as $cur )
		{
			// Get the first letter of poetname
			$letter = substr( $cur['username'], 0, 1 );

			// Create index for this alphabet if does ot exists
			if(! isset( $users[$letter] ) )
				$users[$letter] = array();

			// Add this poet under this array
			$users[$letter][$num]['username'] = $cur['username'];
			$users[$letter][$num]['id'] = $cur['id'];
			$num++;
		}

		// Create alphabets and list poets
		foreach( $users as $letter => $values )
		{
			echo '<p class="letter">' . $letter . '</p>';
			
			foreach( $values as $key => $value )
			{
				$id = $value['id'];
				echo '<a href="poet.php?id=' . $id . '">';
				echo $value['username'];
				echo '</a>';
				echo '<br>';
			}
		}
	}	

	echo '</div>';
?>
