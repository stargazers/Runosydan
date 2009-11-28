<?php

/*
User handling class. Part of Runosydan.net.
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
	class CUsers
	{
		private $db;

		public function __construct( $db )
		{
			$this->db = $db;
		}

		// *********************************************
		//	getUsername
		//
		//	@brief Get username by user ID.
		//
		//	@param $id User ID.
		//
		// *********************************************
		public function getUsername( $id )
		{
			$q = 'SELECT username FROM rs_users WHERE id="' . $id . '"';

			try
			{
				$ret = $this->db->query( $q );
			}
			catch( Exception $e )
			{
				echo 'Virhe tietokantakyselyssä!';
			}

			// Poet found.
			if( $this->db->numRows( $ret ) > 0 )
			{
				$row = $this->db->fetchAssoc( $ret );
				return $row[0]['username'];
			}

			return null;
		}

		public function countUnseenComments( $id )
		{
			$q = 'select c.id, p.id, p.user_id FROM rs_comments c LEFT JOIN '
				. 'rs_poem p ON c.poem_id = p.id WHERE p.user_id=' . $id 
				. ' AND c.is_seen IS NULL OR c.is_seen != 1';

			try 
			{
				$ret = $this->db->query( $q );

				if( $this->db->numRows( $ret ) > 0 )
					return $this->db->numRows( $ret );

			}
			catch( Exception $e )
			{
				echo 'Virhe tietokantakyselyssä!';
			}
		}


		// *********************************************
		//	getUserInfo
		//
		//	@brief Get user information by user ID.
		//
		//	@param $id User ID.
		//
		// *********************************************
		public function getUserInfo( $id )
		{
			$id = mysql_real_escape_string( $id );

			$q = 'SELECT * FROM rs_users WHERE id="' . $id . '"';

			$ret = $this->db->query( $q );
			
			if( $this->db->numRows( $ret ) > 0 )
			{
				$ret = $this->db->fetchAssoc( $ret );
				return $ret;
			}

			return null;
		}
	}
?>
