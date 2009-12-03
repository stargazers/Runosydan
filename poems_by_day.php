<?php
	
/*
Poems by day. Part of Runosydan.net.
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

$cPoem = new CPoem( $db, $_SESSION );
$cUsers = new CUsers( $db, $_SESSION );

if( isset( $_GET['date'] ) )
	$date = $_GET['date'];
else
	$date = date( 'Y-m-d' );

// Get poems by day needs to get date in YYYY-MM-DD format
// so get poems before change date format.
$poems = $cPoem->getPoemsByDay( $date );

// Convert to finnish time format, day.month.year
$date = date( 'd.m.Y', strtotime( $date ) );

echo '<div class="poems_by_day">';
echo '<div class="header_div">';
echo '<a href="poems_by_day.php?date=' . date( 'Y-m-d', strtotime( "-1 day", strtotime( $date ) ) )
	. '">&lt;&lt;</a>';
echo '<span class="header">Poems written ' .  $date . '</span>';
echo '<a href="poems_by_day.php?date=' . date( 'Y-m-d', strtotime( "+1 day", strtotime( $date ) ) )
	. '">&gt;&gt;</a>';
echo '</div>';

if( count( $poems ) > 0 )
{
    foreach( $poems as $poem )
    {
	    $poet = $cUsers->getUsername( $poem['user_id'] );
	    $poem['added'] = date( 'd.m.Y H:i', 
		    strtotime( $poem['added'] ) );

	    $poem['poem'] = nl2br( $poem['poem'] );

	    echo '<p class="poem_header">';
	    echo stripslashes( $poem['title'] );
	    echo '</p>';

	    echo '<p class="poem">';
	    $poem['poem'] = str_replace( '<br />', '<br>', 
		    $poem['poem'] );
	    echo stripslashes( $poem['poem'] );

	    echo '<p class="poem_added">';
	    echo $poem['added'];
	    echo '<br><a href="poet.php?id=' . $poem['user_id'] . '">';
	    echo $poet;
	    echo '</a>';
	    echo '</p>';
    }
}
else
{
	echo '<p>Ei runoja tällä päivällä.</p>';
}
echo '</div>';
create_site_bottom();
?>
