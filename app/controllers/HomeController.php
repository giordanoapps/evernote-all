<?php

class HomeController extends BaseController {

	private $evernote;
	private $user;

 /**
	* Index route.
	*/
	public function index()
	{
		if(Session::has('user'))
		{
			$this->user = unserialize(Session::get('user'));
		}
		else
		{
			$this->user = null;
		}

		$evernote = new EvernoteHelper($this->user);

		if($evernote->auth->Token == null)
		{
			return $this->login($evernote);
		}

		ConversionHelper::toJson($evernote);

		return $evernote;
	}

 /**
	* Notes route.
	*/
	public function notes()
	{
		$evernote = new EvernoteHelper();

		if($evernote->auth->Token == null)
		{
			return $this->login($evernote);
		}

		return $evernote;
	}

 /**
 	* Login function.
 	*/
 	public function login($evernote)
 	{
		ConversionHelper::toArray($evernote);

		return $evernote;
	}
}