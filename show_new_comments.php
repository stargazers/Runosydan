<?php
/*
New comments page. Part of Runosydan.net.
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

// This site cannot be seen if user is not logged in
// or there is no any unseen comments.
if(! isset( $_SESSION['username'] )
	|| $_SESSION['unseen_comments'] == 0 ) 
	header( 'Location: index.php' );

// create_site_top and create_site_menu is defined in
// file general_functions.php
require 'general_functions.php';
require 'CPoem.php';
$cPoem = new CPoem( $db, $_SESSION );

create_site_top();
create_top_menu();

// Get poem ID's where we have unseen comments.
$q = 'select p.id as poem_id, p.poem, p.title FROM rs_comments c LEFT JOIN '
	. 'rs_poem p ON c.poem_id = p.id WHERE p.user_id=' . $_SESSION['id']
	. ' AND c.is_seen IS NULL OR c.is_seen != 1';

try 
{
	$ret = $db->query( $q );

	// Found unseen comments
	if( $db->numRows( $ret ) > 0 )
		$ret = $db->fetchAssoc( $ret );

}
catch( Exception $e )
{
	echo 'Virhe tietokantakyselyssä!';
	die();
}

echo '<div class="new_comments">';
echo '<h2>Runosi joissa on lukemattomia kommentteja</h2>';

// Here we list poems what we have listed already
// so we do not show them as many times as there
// is comments.
$already_shown = array();

// Here we store poem comment ids.
$comments_ids = array();

// List all poems, poem titles and comments.
foreach( $ret as $key => $value )
{
	// If this poem is not already shown, show it.
	// Poem will occur in array as many times as there
	// is comments, so this is why there is this if-part.
	if(! in_array( $value['poem_id'], $already_shown ) )
	{
		$already_shown[] = $value['poem_id'];

		// Show poem title
		echo '<p class="poem_header">';
		echo $value['title'] . "\n";
		echo '</p>';

		// Show a poem. Stripslashes is required so we
		// do not add \ before " char etc.
		echo '<p class="poem">';
		echo stripslashes( nl2br( $value['poem'] ) ) . "\n";
		echo '</p>';

		// Get all comments to this poem
		$comments = $cPoem->getComments( $value['poem_id'] );
		$num = count( $comments );

		// List all comments, even those what have already seen.
		// Otherwise user might think that "where is my comments what
		// I have seen already? Is those comments removed???" or something...
		echo '<div class="poem_comment_visible">';
		for( $i=0; $i < $num; $i++ )
		{
			echo '<p>';
			echo $comments[$i]['comment'];
			echo '<br>';
			echo 'Kommentoija: <a href="poet.php?id=' . $comments[$i]['commenter_id'] 
				. '">' . $comments[$i]['username'] . '</a><br>';
			echo $comments[$i]['date_added'];

			// Save comment ID to array so we can later
			// mark this poem comment as shown on UPDATE-query.
			$comments_ids[] = $comments[$i]['id'];
		}
		echo '</div>';
	}
}

echo '</div>';

// Now all unseen comments are shown. Update database and tell
// that there is no unseen comments anymore.
// NOTE: We do not want to update all this user poems and
// mark comments as shown. This can cause unwanted resuts.
// For example, when we have came here, somebody might have
// commented a poem what is not listed here. 
// Surely, that possibility is very small but you can never be sure... 
$q = 'UPDATE rs_comments SET is_seen=1 WHERE id IN(';
$q .= implode( ', ', $comments_ids ) . ')';

// Update comments & update session variable.
try
{
	$db->query( $q );
	$_SESSION['unseen_comments'] = 0;
}
catch( Exception $e )
{
	echo 'Virhe tietokantakyselyssä!';
}

create_site_bottom();

?>
