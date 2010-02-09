<?php

/*
RSS feedpage. Part of Runosydan.net.
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

function create_rss( $poems, $title, $url )
{
    	// Output data must be RSS+XML
    	header( 'Content-Type: application/rss+xml' );

	$now = date( DATE_RFC822 );

	// Basic output what does not change.
	echo '<?xml version="1.0"?>' . "\n";
	echo '<rss version="2.0">' . "\n";
	echo '<channel>' . "\n";
	echo '<description>' . $title . '</description>' . "\n";
	echo '<title>' . $title . '</title>' . "\n";
	echo '<link>' . $url . '</link>' . "\n";
	echo '<language>fi</language>' . "\n";
	echo '<pubDate>' . $now . '</pubDate>' . "\n";
	echo '<lastBuildDate>' . $now . '</lastBuildDate>' . "\n";

	foreach( $poems as $poem )
	{
	    // Replace & char to &amp;
	    $poem['poem'] = str_replace( "&", '&amp;', $poem['poem'] );

	    // Replace \n to &lt;br&gt; so it will add new line 
	    // break on RSS-feedreaders.
	    $poem['poem'] = str_replace( "\n", '&lt;br&gt;', $poem['poem'] );

	    echo '<item>' . "\n";
	    echo '<title>' . $poem['title'] . '</title>' . "\n";
	    echo '<description>' . $poem['poem'] . "\n" .'</description>' . "\n";
	    echo '</item>' . "\n";
	}

	echo '</channel>';
	echo '</rss>';
}

// This function is called when we want to create
// RSS-feed of poems but do not want to create
// it for any special user, instead we want to
// create it for newest poems etc.
function create_special_feed( $db, $type)
{
	require 'CPoem.php';
	$cPoem = new CPoem( $db, $_SESSION );

	if( $type == 'newest' )
	{
		$poems = $cPoem->getNewestPoems( 10 );
		create_rss( $poems, 'Uusimmat runot', 
			'http://www.runosydan.net/rss.php?special=newest' );
	}
}


require 'general_functions.php';

// We must get poet ID OR special-action, so we have something to show.
if(! isset( $_GET['poet_id'] ) && ! isset( $_GET['special'] ) )
	header( 'Location: index.php' );

// In special feeds (newest poems) call function which will create
// correct RSS-feed for this.
if( isset( $_GET['special'] ) )
{
	create_special_feed( $db, $_GET['special'] );
}
// When we want to create RSS feed for poet
else
{
	// Get poet username
	$cUsers = new CUsers( $db );
	$poet_username = $cUsers->getUsername( $_GET['poet_id'] );

	if( $poet_username == '' )
		header( 'Location: index.php' );

	// Get poet poems on first page
	require 'CPoem.php';
	$cPoems = new CPoem( $db, $_SESSION );
	$poems = $cPoems->getPoems( $_GET['poet_id'], 1, false );

	// Feed title
	$RSS_feed_title = 'Runoilijan ' . $poet_username . ' uusimmat runot';
	$RSS_feed_url = 'http://www.runosydan.net/rss.php?poet_id=' 
		. $_GET['poet_id'];
	
	create_rss( $poems, $RSS_feed_title, $url );
}
?>
