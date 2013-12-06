<?php

class User extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	public $timestamps = true;

	protected $hidden = array('timestamps','incrementing','exists');

	protected $visible = array('id', 'name', 'username');

	public function tags()
	{
		return $this->hasMany('Tag');
	}

}