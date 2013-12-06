<?php

class Note extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'notes';

	public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User');
	}

}