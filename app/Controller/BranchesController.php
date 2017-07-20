<?php

App::uses('ContactsBranchesController', 'Contacts.Controller');

class BranchesController extends ContactsBranchesController
{
	public function db_block_overview()
	{
		$branches = $this->Branch->find('all');
		$this->set(compact('branches'));
	}
}
