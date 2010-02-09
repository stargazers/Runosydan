<?php

/*
Poem hiding page. Part of Runosydan.net.
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
require 'CPoem.php';

// This site cannot be seen if user is not logged in.
if(! isset( $_SESSION['username'] ) )
	header( 'Location: index.php' );

$cPoem = new CPoem( $db, $_SESSION );

// Get poem ID and get its writer.
$id = mysql_real_escape_string( $_GET['id'] );
$owner_id = $cPoem->getPoemWriterID( $id );

// If this poem does NOT belong to logged user,
// then we cannot hide it.
if( $owner_id != $_SESSION['id'] ) 
{
	header( 'Location: index.php' );
}

$cPoem->togglePoemVisibility( $id );

$_SESSION['message_icon'] = 'graphics/32px-Crystal_Clear'
	. '_app_clean.png';
$_SESSION['message'] = 'Runo piilotettu.';

if( isset( $_GET['page'] ) && !empty( $_GET['page'] ) )
	$page = $_GET['page'];
else
	$page = 1;

header( 'Location: ownpage.php?page=' . $page );

?>
