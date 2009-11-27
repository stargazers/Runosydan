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

	// create_site_top and create_site_menu is defined in
	// file general_functions.php
	require 'general_functions.php';

	create_site_top();
	create_top_menu();

	// If user has not pressed Account Remove button, then show the form
	if(! isset( $_GET['action'] ) )
	{
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

	}
	echo '<br>';
	echo '</div>';

?>
