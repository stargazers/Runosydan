<?php

/*
General functions. Part of Runosydan.net.
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

create_site_top();
create_top_menu();

echo '<div class="random_poem">';

// Get random poem
$cPoem = new CPoem( $db, $_SESSION );
$random = $cPoem->getRandomPoem();

// If array of random poem is empty, just show information
// that there is no data.
if( empty( $random ) )
{
	echo 'Tietokannasta ei löydy vielä yhtään runoa.';
}
else
{
	echo '<p class="poem_header">';
	echo stripslashes( $random[0]['title'] );
	echo '</p>';

	echo '<p class="poem">';
	$random[0]['poem'] = str_replace( '<br />', '<br>', 
		$random[0]['poem'] );
	echo nl2br( stripslashes( $random[0]['poem'] ) );

	echo '<p class="poem_added">';
	echo $random[0]['added'];
	echo '<br><a href="poet.php?id=' . $random[0]['user_id'] . '">';
	echo $random[0]['username'];
	echo '</a>';
	echo '</p>';
}

echo '</div>';

create_site_bottom();

?>
