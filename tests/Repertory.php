<?php
namespace Tests;


class Repertory {

	private User $user;
	private Database $db;

	public function __construct(User $user, DatabaseInterface $db)
	{
		$this->user = $user;
		$this->db = $db;
	}
}