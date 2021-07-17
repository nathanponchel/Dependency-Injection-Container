<?php


namespace Tests;


class Repertory {

	private User $user;

	public function __construct(User $user, Database $db, Database $lol)
	{
		$this->user = $user;
		$this->user->hello();
	}
}