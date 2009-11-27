<?php

/*
Poem removing page. Part of Runosydan.net.
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

	$cPoem = new CPoem( $db, $_SESSION );

	// First we want to check if we have GET-parameter "accept".
	// If we have, then we have shown this page already and user
	// has made his decision to remove or not. 
	if( isset( $_GET['accept'] ) )
	{
		if( $_GET['accept'] == 'yes' && isset( $_GET['id'] ) )
		{
			$id = mysql_real_escape_string( $_GET['id'] );

			// User wanted to remove poem. All right. Now we
			// must check if this poem really REALLY belongs to
			// logged user. Otherwise users could remove any poem
			// they want if they just edit URL. That is not 
			// ideal solution :)
			$owner_id = $cPoem->getPoemWriterID( $id );

			if( $owner_id == $_SESSION['id'] )
			{
				$cPoem->removePoem( $id );

				// Icon to show
				$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
					. '_app_clean.png';

				$_SESSION['message'] = 'Runo poistettu.';
			}
			else
			{
				$_SESSION['message'] = 'Runo jota yritit poistaa ei '
					. 'ole sinun runosi! Runoa ei poistettu.';
			}
		}
		else
		{
			// Icon to show
			$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
				. '_app_logout.png';

			$_SESSION['message'] = 'Runoa ei poistettu.';
		}

		// Direct user to own page.
		header( 'Location: ownpage.php' );
	}

	// Create starting HTML and menu at the top 
	create_site_top();
	create_top_menu();

	echo '<div class="remove_poem">';

	if(! isset( $_GET['id'] ) )
	{
		echo 'Runoa ei löytynyt annetulla ID:llä!';
	}
	else
	{
		$id = mysql_real_escape_string( $_GET['id'] );

		// Check who has written this poem.
		$owner_id = $cPoem->getPoemWriterID( $id );

		// If logged user is same than poem owner, then we can
		// show question if user really wants to remove poem or not.
		if( $owner_id == $_SESSION['id'] )
		{
			$poem = $cPoem->getPoemByID( $id );

			$title = $poem[0]['title'];

			echo 'Haluatko varmasti poistaa runon <i>"' 
				. stripslashes( $title ) . '"</i>?<br><br>';

			echo '<a href="remove_poem.php?id=' . $id . '&accept=yes'
				. '">Kyllä</a>';
			echo ' / ';
			echo '<a href="remove_poem.php?id=' . $id . '&accept=no'
				. '">En</a>';
		}
		else
		{
			// If owner_id was null, then there was no poem with
			// given id. Otherwise user tries to remove poem what
			// does not belongs to he/she.
			if( is_null( $owner_id ) )
				echo 'Runoa ei löytynyt annetulla ID:llä!';
			else
				echo 'Sinulla ei ole oikeutta poistaa muiden runoja!';
		}
	}

	echo '</div>';

?>
