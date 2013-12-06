<?php

class User extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	public $timestamps = true;

	public function tags()
	{
		return $this->hasMany('Tag');
	}

}