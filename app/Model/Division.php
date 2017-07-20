<?php
App::uses('AppModel', 'Model');
App::uses('ContactsDivision', 'Contacts.Model');

class Division extends ContactsDivision 
{
	public function snapshotStats()
	{
		$entities = $this->Snapshot_dynamicEntities();
		return $entities;
	}
}
