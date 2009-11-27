<?php
/*
User registeration class. Part of Runosydan.net.
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

	class CRegister
	{
		private $db = '';

		// *********************************************
		//	__construct
		//
		//	@brief Class constructor.
		//
		//	@param $db Database class instance
		//
		//	@param $data POST-data 
		//
		// *********************************************
		public function __construct( $db, $data )
		{
			$this->db = $db;
			$this->data = $data;

			$this->checkData();
		}

		// *********************************************
		//	checkData
		//
		//	@brief Checks if all required fields are set
		//
		// *********************************************
		private function checkData()
		{
			$d = $this->data;

			// Is username given
			if(! isset( $d['username'] ) || $d['username'] == '' )
				throw new Exception( 'Käyttäjätunnusta ei olla annettu!' );

			// Is password and retyped password set and are those same?
			if( isset( $d['password'] ) 
				&& isset( $d['password_again'] ) )
			{
				if( $d['password'] != $d['password_again'] )
				{
					throw new Exception( 'Salasanat eivät täsmää' );
				}
			}
			else
			{
				throw new Exception( 'Salasanaa ei olla annettu!' );
			}

			// Does username already exists?
			if( $this->userExists( $d['username'] ) )
			{
				throw new Exception( 'Käyttäjänimi oli jo olemassa!' );
			}

			// Register new user.
			$this->registerNew();
		}

		// *********************************************
		//	registerNew
		//
		//	@brief Register new user
		//
		// *********************************************
		private function registerNew()
		{
			$d = $this->data;

			// Username and password fields are here just for 
			// mysql_real_escape_string function, because we do not
			// want to SQL-injections.
			// Username and password fields cannot be empty, but
			// that is checked in function checkData!
			$fields = array( 'username', 'password', 'password_again',
				'city', 'homepage', 'firstname', 
				'lastname', 'birthdate', 'email' );

			// Check if values are set and if not, set them.
			// Also, remember to prevent SQL-injection possibilities.
			foreach( $fields as $value )
			{
				if(! isset( $d[$value] ) )
					$d[$value] = '';
				else
					$d[$value] = mysql_real_escape_string( $d[$value] );
			}

			// Convert birthdate to correct format for MySQL.
			if( $d['birthdate'] != '' )
			{
				$tmp = explode( '.', $d['birthdate'] );
				$d['birthdate'] = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0];
			}

			$q = 'INSERT INTO rs_users VALUES( "", "'
				. $d['username'] . '", "' 
				. sha1( $d['password'] ) . '", "'
				. $d['firstname'] . '", "'
				. $d['lastname'] . '", "'
				. $d['city'] . '", "'
				. $d['homepage'] . '", "'
				. $d['email'] . '", "'
				. $d['birthdate'] . '"'
				. ')';

			$this->db->query( $q );
		}

		// *********************************************
		//	userExists
		//
		//	@brief Checks if user exists 
		//
		//	@param $username Username
		//
		//	@return True if user exists, false if not
		//
		// *********************************************
		private function userExists( $username )
		{
			// We do not want SQL-injections.
			$username = mysql_real_escape_string( $username );

			$q = 'SELECT id FROM rs_users WHERE username = "'
				. $username . '"';

			$ret = $this->db->query( $q );

			// If there was more rows than zero, then there is
			// already a user with that name. 
			if( $this->db->numRows( $ret ) > 0 )
				return true;

			return false;
		}
	}

?>
