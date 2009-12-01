<?php

/*
Newest poems. Part of Runosydan.net.
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

// If there is no defined how many poems we will show,
// then by default we show 50
if(! isset( $_GET['num_poems'] ) )
	$_GET['num_poems'] = 50;

// Maximum is 100 poems per page. We do not want
// to make whole server lag because of this.
if( $_GET['num_poems'] > 100 )
	$_GET['num_poems'] = 100;

echo '<div class="poems">';
echo '<table width="100%">';
echo '<tr><td><h3>' . $_GET['num_poems'] . ' uusinta runoa</h3></td>';
echo '<td>';
echo '<a href="rss.php?special=newest"><img src="graphics/rss.gif" class="rss">';
echo '</a></td></tr></table>';

// Get 50 newest poems
$cPoem = new CPoem( $db, $_SESSION );
$cUsers = new CUsers( $db, $_SESSION );
$poems = $cPoem->getNewestPoems( $_GET['num_poems'] );

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

echo '<a href="poems.php">Takaisin runolistaukseen</a><br><br>';

?>
