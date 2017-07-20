<?php

App::uses('ContactsDivisionsController', 'Contacts.Controller');

class DivisionsController extends ContactsDivisionsController
{
	public function db_block_overview()
	{
		$divisions = $this->Division->find('all');
		$this->set(compact('divisions'));
	}
}
