<?php


namespace Tests;


class Repertory {

	private User $user;

	public function __construct(User $user)
	{
		$this->user = $user;
		$this->user->hello();
	}
}