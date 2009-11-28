<?php
/*
Database creator. Part of Runosydan.net.
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

require 'CMySQL.php';

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
echo '<html>';
echo '<head>';
echo '<title>Runosydan database generator</title>';
echo '<meta http-equiv="Content-Type" content="text/xhtml;charset=utf-8">';
echo '</head>';
echo '<body>';

if( isset( $_POST['db_server'] ) )
{
	$db = new CMySQL();
	$db->connect( $_POST['db_server'], $_POST['db_username'], $_POST['db_password'] );
	$db->selectDatabase( $_POST['db_database'] );

	// Table for poems
	$q = 'CREATE TABLE rs_poem( id INT UNSIGNED NOT NULL AUTO_INCREMENT, user_id INT UNSIGNED, title VARCHAR(255), poem text, visible TINYINT, added DATETIME, PRIMARY KEY(id) )';
	$db->query( $q );

	// Table for comments
	$q = 'CREATE TABLE rs_comments( id INT UNSIGNED NOT NULL AUTO_INCREMENT, poem_id INT UNSIGNED, commenter_id INT UNSIGNED, comment TEXT, date_added DATETIME, is_seen INT UNSIGNED, poet_id INT UNSIGNED, PRIMARY KEY(id) );';
	$db->query( $q );

	// Table for users
	$q = 'CREATE TABLE rs_users( id INT UNSIGNED NOT NULL AUTO_INCREMENT, username VARCHAR(30), password VARCHAR(50), firstname VARCHAR(50), lastname VARCHAR(50), city VARCHAR(50), homepage VARCHAR(255), email VARCHAR(60), birthdate DATE, PRIMARY KEY(id) );';
	$db->query( $q );
}
else
{
	echo '<h2>Tietokannan luonti</h2>';
	echo '<form action="install.php" method="POST">';
	echo '<table>';
	echo '<tr><td>Tietokantapalvelin:</td>';
	echo '<td><input type="text" name="db_server"></td></tr>';

	echo '<tr><td>Tietokannan käyttäjätunnus:</td>';
	echo '<td><input type="text" name="db_username"></td></tr>';

	echo '<tr><td>Tietokannan salasana:</td>';
	echo '<td><input type="text" name="db_password"></td></tr>';

	echo '<tr><td>Tietokannan nimi:</td>';
	echo '<td><input type="text" name="db_database"></td></tr>';
	echo '<tr><td colspan="2"><input type="submit" value="Luo kanta"></td></tr>';
	echo '</table>';
	echo '</form>';
}


echo '</body>';
echo '</html>';
?>
