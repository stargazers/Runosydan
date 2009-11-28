<?php
/*
Poem class. Part of Runosydan.net.
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

	class CPoem
	{
		private $db;
		private $sessionData;
		private $poemsPerPage = 5;

		public function __construct( $db, $sessionData )
		{
			$this->db = $db;
			$this->sessionData = $sessionData;
		}

		public function getPoemsPerPage()
		{
			return $this->poemsPerPage;
		}

		public function addPoem( $data )
		{
			// We do not want SQL-injections.
			$title = mysql_real_escape_string( $data['title'] );
			$poem = mysql_real_escape_string( $data['poem'] );

			// Poems are all visible, until we make possibility
			// to edit visiblity.
			$visible = 1;

			$s = $this->sessionData;
			$q = 'INSERT INTO rs_poem VALUES( "", "'
				. $s['id'] . '", "' . $title . '", "'
				. $poem . '", "' . $visible . '", "'
				. date( 'Y-m-d H:i:s' ) . '");';

			$ret = $this->db->query( $q );
		}

		// *********************************************
		//	getRandomPoem
		//
		//	@brief Get a random poem.
		//
		// *********************************************
		public function getRandomPoem()
		{
			$q = 'SELECT p.id, p.title, p.poem, p.added, u.id as user_id, u.username FROM rs_poem p LEFT JOIN rs_users u ON u.id=p.user_id ORDER BY RAND() LIMIT 1';

			// Get random poem
			try
			{
				$ret = $this->db->query( $q );

				// If we found random poem, return it.
				if( $this->db->numRows( $ret ) > 0 )
				{
					$ret = $this->db->fetchAssoc( $ret );
					return $ret;
				}

				return array();
			}
			catch( Exception $e )
			{
				echo 'Virhe tietokantakyselyssä!';
			}

			return array();
		}

		public function getPoemsWithUnseenConnents( $user_id )
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
		//	getPoems
		//
		//	@brief Get user poems.
		//
		//	@param $id User ID.
		//
		//	@param $page Result page. Results are splitted
		//		on pages if there is too many results
		//		to fit in one page. 
		//
		// *********************************************
		public function getPoems( $id, $page )
		{
			$id = mysql_real_escape_string( $id );
			$page = mysql_real_escape_string( $page );

			// First poem for LIMIT-parameter.
			$first = $this->poemsPerPage * ( $page -1 );

			$q = 'SELECT id, title, poem, added FROM rs_poem '
				. 'WHERE user_id=' . $id . ' ORDER BY added DESC '
				. 'LIMIT ' . $first . ', ' . $this->poemsPerPage;

			$ret = $this->db->query( $q );

			// Return found poems.
			if( $this->db->numRows( $ret ) > 0 )
			{
				$ret = $this->db->fetchAssoc( $ret );
				return $ret;
			}
			
			// No poems found at all :(
			return null;
		}

		public function getComments( $poem_id )
		{
			$q = 'SELECT c.id, c.commenter_id, c.comment, c.date_added, u.username FROM rs_comments c LEFT JOIN rs_users u ON u.id=c.commenter_id WHERE c.poem_id=' . $poem_id . ' ORDER BY c.date_added';

			try
			{
				$ret = $this->db->query( $q );

				if( $this->db->numRows( $ret ) > 0 )
				{
					$ret = $this->db->fetchAssoc( $ret );
					return $ret;
				}
			}
			catch( Exception $e ) 
			{

			}
		}

		// *********************************************
		//	getPoemByID
		//
		//	@brief Get one poem by its ID.
		//
		//	@param $id Poem ID.
		//
		// *********************************************
		public function getPoemByID( $id )
		{
			// Say no to SQL injections.
			$id = mysql_real_escape_string( $id );

			$q = 'SELECT id, title, poem, added FROM rs_poem '
				. 'WHERE id="' . $id . '"';

			$ret = $this->db->query( $q );

			// Poem found
			if( $this->db->numRows( $ret ) > 0 )
			{
				$ret = $this->db->fetchAssoc( $ret );
				return $ret;
			}

			// Failed! No poem found with given ID.
			return null;
		}

		// *********************************************
		//	getPoemWriterID
		//
		//	@brief Get poem writer user ID.
		//
		//	@param $poem_id ID of Poem.
		//
		// *********************************************
		public function getPoemWriterID( $poem_id )
		{
			// We do not want to let possibility to SQL-injection.
			$poem_id = mysql_real_escape_string( $poem_id );

			$q = 'SELECT user_id FROM rs_poem WHERE id="'
				. $poem_id . '"';

			$ret = $this->db->query( $q );

			// Poem with given ID is found. Return writer ID.
			if( $this->db->numRows( $ret ) > 0 )
			{
				$ret = $this->db->fetchAssoc( $ret );
				return $ret[0]['user_id'];
			}

			// There was no poem with given ID.
			return null;
		}

		// *********************************************
		//	editPoem
		//
		//	@brief Modify poem
		//
		//	@param $data Data what should contain array
		//		indexes title, id and poem.
		//
		// *********************************************
		public function editPoem( $data )
		{
			// Only ID is must have value. Throw exception if
			// poem ID is not given.
			if(! isset( $data['id'] ) )
				throw new Exception( 'Poem id is not given!' );

			// Title can be empty, so if it is not set, then
			// just add empty string.
			if(! isset( $data['title'] ) )
				$data['title'] = '';

			// Also, poem is optional. Yeah, it is quite stupid but
			// if someone is so perverted that wants to add only a title,
			// then why not. I am not here to judge others visions.
			if(! isset( $data['poem'] ) )
				$data['poem'] = '';

			// No SQL-injections wanted.
			$id = mysql_real_escape_string( $data['id'] );
			$title = mysql_real_escape_string( $data['title'] );
			$poem = mysql_real_escape_string( $data['poem'] );

			// Crete update-query.
			$q = 'UPDATE rs_poem SET title="' . $title . '", '
				. 'poem="' . $poem . '" WHERE id="' . $id . '"';

			// Try and catch. If you fall I'll catch... (Sonata Arctica <3)
			// So, if something is going wrong, then
			// the page surely will fail but the whole exception crap
			// is not shown on the page.
			try
			{
				$this->db->query( $q );
			}
			catch( Exception $e )
			{
				echo 'Failed to update poem! Error in database!';
			}
		}	

		// *********************************************
		//	removePoem
		//
		//	@brief Remove a poem from database.
		//
		//	@param $id Poem ID.
		//
		// *********************************************
		public function removePoem( $id )
		{
			$id = mysql_real_escape_string( $id );
			$q = 'DELETE FROM rs_poem WHERE id="'  . $id . '" LIMIT 1';

			try
			{
				$this->db->query( $q );
			}
			catch( Exception $e )
			{
				echo 'Failed to remove poem from database!';
			}
		}

		// *********************************************
		//	numPoems
		//
		//	@brief Get number of poems by user ID.
		//
		//	@param $id User ID.
		//
		// *********************************************
		public function numPoems( $id )
		{
			$id = mysql_real_escape_string( $id );

			// Get number of poems
			$q = 'SELECT COUNT(*) FROM rs_poem WHERE user_id="' 
				. $id . '"';
			
			$ret = $this->db->query( $q );
			$ret = $this->db->fetchAssoc( $ret );

			return $ret[0]['COUNT(*)'];
		}
	}

?>
