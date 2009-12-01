<?php
	
/*
Poems mainpage. Part of Runosydan.net.
Copyright (C) 2009 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

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

echo '<div class="poems">';
echo '<h2>Runot</h2>';
echo '<table class="title_table">';
echo '<tr><td><h2><a href="newest_poems.php">10 uusinta runoa</a></h2></td>';
echo '<td>';
echo '<a href="rss.php?special=newest"><img src="graphics/rss.gif" class="rss" alt="rss">';
echo '</a></td></tr></table>';

// Get 10 newest poems
$cPoem = new CPoem( $db, $_SESSION );
$cUsers = new CUsers( $db, $_SESSION );
$poems = $cPoem->getNewestPoems( 10 );

echo '<table>';
echo '<tr>';
echo '<td><b>Runoilija</b></td>';
echo '<td><b>Runon nimi</b></td>';
echo '<td><b>Lisätty</b></td>';
echo '</tr>';

$poems_per_page = $cPoem->getPoemsPerPage();

foreach( $poems as $poem )
{
	echo '<tr>';
	echo '<td>';
	echo '<a href="poet.php?id=' . $poem['user_id'] . '">';
	echo $cUsers->getUsername( $poem['user_id'] );
	echo '</a>';
	echo '</td>';
	echo '<td><a href="poet.php?id=' . $poem['user_id'];

	// Count how many poems this writer has written
	// since this poem. This is necessary so we can
	// create link to correct poem even if that poem
	// is on another page than the first one.
	$q = 'SELECT COUNT(*) FROM rs_poem WHERE added > "' 
		. $poem['added'] . '" AND user_id=' 
		. $poem['user_id'];
	
	try
	{
		$ret = $db->query( $q );
		if( $db->numRows( $ret ) > 0 )
		{
			$ret = $db->fetchAssoc( $ret );
			$num_after = $ret[0]['COUNT(*)'];

			// In which page poem will be
			$correct_page = ceil( ( $num_after+1 ) / $poems_per_page );
		}
	}
	catch( Exception $e )
	{
		echo '"></a>Virhe tietokantakyselyssä!';
	}

	echo '&amp;page=' . $correct_page . '#poem_' . $poem['id'] . '">';

	// If there is no name given to poem, then we
	// must give at least information that this poem
	// is nameless, so users can click and see that poem.
	if( $poem['title'] == '' )
		echo '<i>(Nimetön runo)</i>';
	else
		echo $poem['title'];

	echo '</a>';
	echo '</td>';
	echo '<td>';
	echo date( 'd.m.Y H:i', strtotime( $poem['added'] ) );
	echo '</td>';
	echo '</tr>';
}
echo '</table>';

echo '<h3><a href="random.php">Satunnainen runo</a></h3>';

// Get random poem
$random = $cPoem->getRandomPoem();

echo '<div id="random_poem_embedded">';
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
echo '</div>';
create_site_bottom();
?>
