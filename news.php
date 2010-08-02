<?php

/*
Main page. Part of Runosydan.net.
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

create_site_top();
create_top_menu();

echo '<div class="news">';

if( file_exists( 'news.txt' ) )
{
	echo '<h3>Uutisia</h3>';
	echo '<div class="textblock">';

	$data = file( 'news.txt' );
	$rows = count( $data );
	$nextIsHeader = true;
	$headers = 0;

	for( $i=0; $i < $rows; $i++ )
	{ 
		// If next line is header, then it must bold font
		if( $nextIsHeader )
		{
			echo '<div class="news_item_header">';

			// Increase number of shown news
			$headers++;

			echo '<b>' . $data[$i] . '</b>';
			$nextIsHeader = false;

			echo '</div>';
		}
		else
		{
			echo '<div class="news_item">';

			if( strstr( $data[$i], '-----' ) )
				$nextIsHeader = true;
			else
				echo $data[$i];

			echo '</div>';
		}

	}
}
echo '<br><a href="index.php">Takaisin etusivulle</a>';
echo '</div>';
echo '</div>';

create_site_bottom();

?>
