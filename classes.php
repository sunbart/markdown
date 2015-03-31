<?php

require_once '/lib/diff/Diff.php';
/**
* the document base class.
* @author Sergelia
* @since 30/3/2015
*/

/**************
TODO: is the document new or is it being updated? 
trigger INSERT only if new.
otherwise, update version.

TODO2: add comments for all functions

TODO3: fix updating logic and ID retrieval


**************/
class Document 
{
	
	function __construct($author)
	{
		$this->author 		= new Author($author);;
		$this->id 			= '';
		// $this->editor 		= '';
		// $this->latest		= '';
		$this->timestamp 	= new DateTime();
		$this->version 		= new Version($this->timestamp);
		$this->dbh 			= new PDO('mysql:host=localhost;dbname=wa_cms', 'root', '');
		$this->error 		= new Error();
	}

	function getDocument($id) {
		try {
			$get_doc_query = "SELECT * FROM document WHERE guid = :id";
			$get_doc_obj = $this->dbh->prepare($get_doc_query);
			$get_doc_obj->bindParam(':id', $id);
			$get_doc_obj->execute();
			$get_doc_res = $get_doc_obj->fetch();
			var_dump($get_doc_res);
			$this->author = $this->author->getAuthor($get_doc_res->author_id);
			$this->timestamp = $get_doc_res->startdate;
			$this->latest = $get_doc_res->latest;
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function getVersion() {
		try {
			$get_doc_query = "SELECT version.body FROM document, version WHERE document.guid = :id AND document.guid = version.document_guid ORDER BY version.num DESC LIMIT 1";
			$get_doc_obj = $this->dbh->prepare($get_doc_query);
			$get_doc_obj->bindParam(':id', $this->id);
			$get_doc_obj->execute();
			$get_doc_res = $get_doc_obj->fetch();
			var_dump($get_doc_obj);
			return $get_doc_res;
			// $this->latest = $get_doc_obj->latest;
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function setDocument($body) {
		$author = 1;
		if ($this->id == 0 || $this->version->id == 0) {
			try {
				$set_doc_query = "INSERT INTO document (author_id) VALUES (:author_id)";
				$set_doc_obj = $this->dbh->prepare($set_doc_query);
				$set_doc_obj->bindParam(':author_id', $author);
				$set_doc_obj->execute();
				var_dump($set_doc_obj);
				$set_doc_res = $set_doc_obj->fetch();
				var_dump($set_doc_res);
				// get document id based on the last inserted ID in the database.
				$this->id = $this->dbh->lastInsertID();
				// get the number of the last version of this document
				$this->version->getNum($this->id);
				// set new version data...
				$this->version->setData($body);
				// ... and store it in the database
				$this->version->push();
			} catch (PDOException $e) {
				$this->error->add($this->timestamp, $e->message);
				return 0;
			}
		} else {
			$this->version->getNum($this->id);
			// set new version data...
			$this->version->setData($body);
			// ... and store it in the database
			$this->version->push();
		}
	}
	
	function deleteDocument($id) {
		try {
			$delete_doc_query = "DELETE FROM document WHERE guid = :id";
			$delete_doc_obj = $this->dbh->prepare($delete_doc_query);
			$delete_doc_obj->bindParam(':id', $id);
			$delete_doc_obj->execute();
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}

}

/**
* 
*/
class Version
{
	
	function __construct($timestamp)
	{
		$this->start 	= $timestamp;
		$this->alter 	= new DateTime();
		$this->guid 	= '';
		$this->num 		= '';
		$this->data 	= '';
		$this->diff 	= (double) 0;
		$this->change 	= false;
		$this->dbh 		= new PDO('mysql:host=localhost;dbname=wa_cms', 'root', '');
	}
	function setData($data) {
		if($this->data == '') {
			$this->data = $data;
		}
	}
	function setVersion($vernum, $guid) {
		$set_dver_query = "UPDATE document (version) VALUES (:version) WHERE guid = :guid";
		$set_dver_obj = $this->dbh->prepare($set_dver_query);
		$set_dver_obj->bindParam(':version', $vernum);
		$set_dver_obj->bindParam(':guid', $this->guid);
		try {
			$set_dver_res = $set_dver_obj->execute();
			var_dump($this);
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function getNum($guid) {
		$get_ver_query = "SELECT num FROM version WHERE version.document_guid = :guid";
		$get_ver_obj = $this->dbh->prepare($get_ver_query);
		$get_ver_obj->bindParam(':guid', $guid);
		try {
			$get_ver_obj->execute();
			$get_ver_res = $get_ver_obj->fetch();
			if ($get_ver_obj->rowCount() > 0) {
			 	$this->num = $get_ver_obj->rowCount() + 1;
			} else {
				$this->guid = $guid;
			   $this->num = 0;
			}
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function getVer($num) {
		$get_ver_query = "SELECT * FROM version WHERE version.document_guid = :guid AND version.num = :num";
		$get_ver_obj = $this->dbh->prepare($get_ver_query);
		$get_ver_obj->bindParam(':guid', $guid);
		$get_ver_obj->bindParam(':num', $num);
		try {
			$get_ver_res = $get_ver_obj->execute();
			if ($get_ver_res->rowCount() > 0) {
			 	$this->num = $num;
			 	$this->data = $get_ver_res->body;
			 	$this->guid = $get_ver_res->guid;
			} else {
			   $this->num = 0;
			}
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function getDiff($prev, $data) {
		if ($prev == '' && $data == '') {
			$this->diff == 100;
		} else {
			Diff::compare($prev, $data, $this->diff);
		}
		if ($this->diff < 90.000) {
			$this->change = true;
		} else {
			$this->change = false;
		}
		return $this->change;
	}
	function push() {
		if ($this->guid !== 0 && !empty($this->guid) && !empty($this->data)) {
			$ver_query = 'INSERT INTO version (document_guid, body) VALUES (:guid, :body)';
			$ver_obj = $this->dbh->prepare($ver_query);
			$ver_obj->bindParam(':guid', $this->guid);
			$ver_obj->bindParam(':body', $this->data);
			try {
				$ver_obj->execute();
				if ($ver_obj->rowCount() > 0) {
				 	$this->setVersion($this->num, $this->guid);
				} else {
				   $this->num = 0;
				}
			} catch(PDOException $e) {
				$this->error->add($this->timestamp, $e->message);
				return 0;
			}
		}
	}
	function compare() {
		if ($this->num !== 0) {
			$old_data = getVerBody($this->num - 1);

			$diff = $this->getDiff($old_data, $this->data);
			if ($diff == true) {
				$this->push();
			}
		} else {
			$this->push();
		}
	}
}
/**
* the author base class.
* @author Sergelia
* @since 30/3/2015
*/
class Author 
{
	
	function __construct($name)
	{
		$this->name 		= $name;
		$this->id 			= '';
		$this->role 		= '';
		$this->roles_allowed =	array('author', 'editor', 'admin');
		$this->dbh 			= new PDO('mysql:host=localhost;dbname=wa_cms', 'root', '');
		$this->error 		= new Error();
	}
	function getAuthorByID($author_id) {
		$this->id = $author_id;
		$get_author_query = "SELECT * FROM author WHERE id = :id";
		$get_author_obj = $this->dbh->prepare($get_author_query);
		$get_author_obj->bindParam(':id', $this->id);
		try {
			$get_author_res = $get_author_obj->execute();
			$this->name = $get_author_res->screenname;
			$this->role = (string)$get_author_res->role;
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function getAuthorByName($author_name) {
		$this->name = $author_name;
		$get_author_query = "SELECT * FROM author WHERE screenname = :name";
		$get_author_obj = $this->dbh->prepare($get_author_query);
		$get_author_obj->bindParam(':id', $this->name);
		try {
			$get_author_res = $get_author_obj->execute();
			$this->id = $get_author_res->id;
			$this->role = (string)$get_author_res->role;
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function changeRole($role) {
		if (in_array($role, $this->roles_allowed)) {
			if (!empty($this->id) && $this->id !== '' && isset($this->id)) {
				$update_author_query = "UPDATE author (role) VALUES (:role) WHERE id = :id";
				$update_author_obj = $this->dbh->prepare($update_author_query);
				$update_author_obj->bindParam(':id', $this->id);
				$update_author_obj->bindParam(':role', $role);
				try {
					$update_author_obj->execute();
				} catch (PDOException $e) {
					$this->error->add($this->timestamp, $e->message);
					return 0;
				}
			} else {
				$this->error->add($this->timestamp, 'Unknown author ID');
			}
		} else {
			$this->error->add($this->timestamp, 'Forbidden role entry');
		}
	}
	function changeName($new_name) {
		if (!empty($this->id) && $this->id !== '' && isset($this->id)) {
			$update_author_query = "UPDATE author (screenname) VALUES (:name) WHERE id = :id";
			$update_author_obj = $this->dbh->prepare($update_author_query);
			$update_author_obj->bindParam(':id', $this->id);
			$update_author_obj->bindParam(':name', $new_name);
			try {
				$update_author_obj->execute();
			} catch (PDOException $e) {
				$this->error->add($this->timestamp, $e->message);
				return 0;
			}
		} else {
			$this->error->add($this->timestamp, 'Unknown author ID');
		}
	}
}

/*
* the error base class.
* @author Sergelia
* @since 30/3/2015
*/
class Error
{
	
	function __construct()
	{
		$this->body 	= array();
	}
	function add($timestamp, $err_message) {
		$this->body[]	= array('time' => $timestamp, 'message' => $err_message);
	}
	function reportAll() {
		return $this->body;
	}
	function reportLast() {
		$length = sizeof($this->body);
		return $this->body[$length-1];
	}
	function clear() {
		$this->body 		= array();
	}
}
?>