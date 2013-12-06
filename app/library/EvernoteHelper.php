<?php

class EvernoteHelper extends AuthHelper
{
 /**
 	* @var Evernote\User $user
 	*/
 	public $user;

 /**
 	* @var User $userModel
 	*/
 	private $userModel;

 /**
 	* @var NoteList $notes
 	*/
 	public $notes;

 /**
 	* @var List $tags
 	*/
 	public $tags;

 /**
 	* @var string $controlTag
 	*/
 	private $controlTag;

 /**
 	* @var UserStore $userStore
 	*/
 	protected $userStore;

 /**
 	* @var NoteStore $noteStore
 	*/
 	protected $noteStore;

 /**
 	* Constructor method
 	*/
 	public function EvernoteHelper($user)
 	{
 		parent::AuthHelper();

 		$this->doAuth();

 		if($this->auth->token != null)
 		{
 			$this->client = new Evernote\Client(
 												array('token' => $this->auth->token)
 											);

 			$this->userStore = $this->client->getUserStore();
 			$this->noteStore = $this->client->getNoteStore();

 			if($user == null)
 			{
 				$this->doUser($user);
 			}
 			else
 			{
 				$this->user = $user;
 			}
 		}
 	}

 /**
 	* Execute user
 	*/
 	private function doUser($user)
 	{
 		$this->user = $this->userStore->getUser();

 		$user = User::find($this->user->id);

 		if($user == null)
 		{
 			$user = new User();

 			$user->id = $this->user->id;
 			$user->username = $this->user->username;
 			$user->name = $this->user->name;

 			$user->save();

 			$this->doTags();
 			$this->doNotes();
 		}

 		Session::put('user',serialize($user));

 		$this->userModel = $user;
 	}

 /**
 	* Execute tags
 	*/
 	private function doTags()
 	{
 		$this->tags = $this->noteStore->listTags($this->auth->token);

 		foreach($this->tags as $tag)
 		{
 			if($tag->name == "share it")
 			{
 				$this->controlTag = $tag;
 			}

	 		$tagModel = Tag::where('guid','=',$tag->guid)->first();

	 		if($tagModel == null)
	 		{
	 			$tagModel = new Tag();

	 			$tagModel->guid = $tag->guid;
	 			$tagModel->name = $tag->name;
	 			$tagModel->user_id = $this->user->id;

	 			$tagModel->save();
	 		}
 		}
 	}

 /**
 	* Execute notes
 	*/
 	private function doNotes()
 	{
 		$filter = new EDAM\NoteStore\NoteFilter();

 		$filter->tagGuids = array();

 		array_push($filter->tagGuids, $this->controlTag->guid);

 		$resultSpec = new EDAM\NoteStore\NotesMetadataResultSpec();

 		$resultSpec->includeTitle = true;
 		$resultSpec->includeTagGuids = true;

 		$this->notes = $this->noteStore->findNotesMetadata(
											$this->auth->token,
											$filter,
											0,
											9999,
											$resultSpec
 									 );

 		foreach($this->notes->notes as $note)
 		{
	 		$noteModel = Note::where('guid','=',$note->guid)->first();

	 		if($noteModel == null)
	 		{
	 			$noteModel = new Note();

	 			$share_link = $this->noteStore->shareNote($this->auth->token, $note->guid);

	 			$noteModel->guid = $note->guid;
	 			$noteModel->title = $note->title;
	 			$noteModel->user_id = $this->user->id;
	 			$noteModel->share_link = $share_link;

	 			$noteModel->save();
	 		}
 		}
 	}

 /**
 	* Getters
 	*/
 	public function getControlTag()
 	{
 		return $this->controlTag;
 	}

 	public function getNoteStore()
 	{
 		return $this->noteStore;
 	}

 	public function getUserStore()
 	{
 		return $this->userStore;
 	}

 	public function getUserModel()
 	{
 		return $this->userModel;
 	}
}