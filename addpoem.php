<?php

/*
Add a poem to database. Part of Runosydan.net.
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
	require 'CPoem.php';

	// Create new Poem-class instance and add poem.
	$cPoem = new CPoem( $db, $_SESSION );
	$cPoem->addPoem( $_POST );

	// Icon to show.
	$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
		. '_app_clean.png';

	// Message to show on own page.
	$_SESSION['message'] = 'Runo lisätty!';

	// Forward user to own page.
	header( 'Location: ownpage.php' );

?>
