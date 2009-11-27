<?php

/*
Poem editing page. Part of Runosydan.net.
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

	require 'general_functions.php';
	require 'CPoem.php';

	// This site cannot be seen if user is not logged in.
	if(! isset( $_SESSION['username'] ) )
		header( 'Location: index.php' );

	$cPoem = new CPoem( $db, $_SESSION );

	// First we must check if POST-data is given. If there is POST-data,
	// then we have shown this page already and user has pressed button
	// what will send (possible) modified data here again.
	if( isset( $_POST ) )
	{
		// Get poem ID from POST-values.
		$id = mysql_real_escape_string( $_POST['id'] );

		// Check that this poem really belongs to logged user.
		// This should be done here also, because otherwise it 
		// would be possible that someone just sends POST-data
		// and then he/she could edit any poem he wants to.
		$owner_id = $cPoem->getPoemWriterID( $id );

		// Is poem owner really the same than the user who is logged in?
		if( $owner_id == $_SESSION['id'] )
		{
			$cPoem->editPoem( $_POST );
			$_SESSION['message'] = 'Runo päivitetty!';

			// Icon to show
			$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
				. '_app_clean.png';

			header( 'Location: ownpage.php' );
		}
	}

	// Create starting HTML and menu at the top 
	create_site_top();
	create_top_menu();

	echo '<div class="edit_poem">';

	// If there is no ID given in URL, then show error.
	if(! isset( $_GET['id'] ) )
	{
		echo 'Runoa ei löytynyt annetulla ID:llä!';
	}
	else
	{
		// Just in case
		$id = mysql_real_escape_string( $_GET['id'] );

		// Check who owns this poem. So, check if user tries to
		// edit poem what is not written by himself/herself.
		$owner_id = $cPoem->getPoemWriterID( $id );

		// Is poem owner same than logged user? If so, create editing
		// form. If it is not, then show error.
		if( $owner_id == $_SESSION['id'] )
		{
			// Get the poem from database
			$poem = $cPoem->getPoemByID( $id );

			$title = $poem[0]['title'];
			$poem = $poem[0]['poem'];

			// Show form and put old values (title and poem) in the
			// form where they belong.
			echo '<form action="edit_poem.php" method="post">';
			echo '<input type="hidden" name="id" value="' . $id . '">';

			echo '<table>';

			echo '<tr>';
			echo '<td>Runon nimi:</td>';
			echo '<td><input type="text" name="title" ';

			// Remove backslashes and encode html special characters
			// to correct values.
			echo 'value="' . stripslashes( htmlspecialchars( $title ) )
				. '"></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td valign="top">Runo:</td>';
			echo '<td><textarea name="poem">';
			echo stripslashes( $poem );
			echo '</textarea></td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td colspan="2">';
			echo '<input type="submit" value="Muokkaa">';
			echo '</td>';
			echo '</tr>';
			
			echo '</table>';
			echo '</form>';
		}
		else
		{
			// If owner_id is null, then there was no poem with
			// given ID at all. If it is other than null, then that
			// poem exists, but does not belong to logged user.
			if( is_null( $owner_id ) )
				echo '<br>Runoa ei löytynyt annetulla ID:llä!<br><br>';
			else
				echo '<br>Sinulla ei ole oikeutta muokata muiden runoja!<br><br>';
		}
	}

	echo '</div>';
?>
