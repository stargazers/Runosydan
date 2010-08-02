<?php

/*
User own page. Part of Runosydan.net.
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
	//	create_poem_adding_form
	//
	//	@brief Create a form where we can add poem.
	//
	// *********************************************
	function create_poem_adding_form()
	{
		// Make sure that user has not just removed her/his
		// account and then try to add poems or something
		// like that...
		if(! isset( $_SESSION['username'] ) )
			return;

		echo '<div class="textblock">';
		echo '<h3>Lisää uusi runo</h3>';
		echo '</div>';
		echo '<form action="addpoem.php" method="post" class="adding_form">';
		echo '<table>';
		echo '<tr><td>Runon nimi:</td>';
		echo '<td><input type="text" name="title">';
		echo '</td></tr>';

		echo '<tr><td valign="top">Runo:</td>';
		echo '<td><textarea name="poem"></textarea></td></tr>';

		echo '<tr><td colspan="2">';
		echo '<input type="checkbox" name="hidden_poem">Piilotettu runo</td></tr>';

		echo '<td colspan="2"><input type="submit" value="Lisää runo">'
			. '</td>';
		echo '</table>';
		echo '</form>';
	}

	// *********************************************
	//	create_own_page
	//
	//	@brief Create user own page.
	//
	//	@param $db Database class instance
	//
	// *********************************************
	function create_own_page( $db )
	{
		echo '<div class="ownpage">';

		// If there is some message in session variable, then we
		// should show it to user and after that unset that variable,
		// so same message will not be shown many times.
		show_message();

		// Show form where we can add poem.
		create_poem_adding_form();
		echo '<hr>';

		// Show own info here, eg. Real name, number of poems etc.
		show_poet_info( $db, $_SESSION['id'] );
		echo '<hr>';

		// Get poems.
		echo '<div class="poem">';
		show_poems( $_SESSION['id'] );
		echo '</div>';

		echo '</div>';
	}


	session_start();

	// Only logged user can see this page.
	if(! isset( $_SESSION['username'] ) )
		header( 'Location: index.php' );

	require 'general_functions.php';

	create_site_top();
	create_top_menu();

	// Create the whole page.
	create_own_page( $db );

?>
