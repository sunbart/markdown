<?php

/**
* the document base class.
* @author Sergelia
* @since 30/3/2015
*/
class Document 
{
	
	function __construct($author)
	{
		$this->author 		= new Author($author);;
		$this->id 			= '';
		$this->editor 		= '';
		$this->latest		= '';
		$this->timestamp 	= new DateTime();
		$this->dbh 			= new PDO('mysql:host=localhost;dbname=wa_cms', 'root', '');
		$this->error 		= new Error();
	}

	function getDocument($id) {
		$get_doc_query = "SELECT * FROM document WHERE guid = :id";
		$get_doc_obj = $this->dbh->prepare($get_doc_query);
		$get_doc_obj->bindParam(':id', $id);
		try {
			$get_doc_res = $get_doc_obj->execute();
			$this->author = $this->author->getAuthor($get_doc_obj->author_id);
			$this->timestamp = $get_doc_obj->startdate;
			$this->latest = $get_doc_obj->latest;
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function setDocument($author, $body) {
		$set_doc_query = "INSERT INTO document (author) VALUES (:author)";
		$set_doc_obj = $this->dbh->prepare($set_doc_query);
		$set_doc_obj->bindParam(':author', $author);
		try {
			$set_doc_res = $set_doc_obj->execute();
			$this->id = $this->dbh->lastInsertID();
			$first_version = new Version($this->id, $body);
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
		}
	}
	function deleteDocument($id) {
		$delete_doc_query = "DELETE FROM document WHERE guid = :id";
		$delete_doc_obj = $this->dbh->prepare($delete_doc_query);
		$delete_doc_obj->bindParam(':id', $id);
		try {
			$delete_doc_obj->execute();
		} catch (PDOException $e) {
			$this->error->add($this->timestamp, $e->message);
			return 0;
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
				$update_author_obj->bindParams(':id' => $this->id, ':role' => $role);
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
			$update_author_obj->bindParams(':id' => $this->id, ':name' => $new_name);
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
		this->body 		= array();
	}
}
?>