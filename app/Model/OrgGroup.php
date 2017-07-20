<?php
App::uses('AppModel', 'Model');

class OrgGroup extends AppModel 
{
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
			),
		),
	);
	
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
		'ClientsUser' => array(
			'className' => 'ClientsUser',
			'foreignKey' => 'org_group_id',
			'dependent' => false,
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'OrgGroup.name'
	);
	
	public $includeGlobal = false;
	
	public function beforeFind($query = array())
	{
		// if this is empty, assume they want the full list, so it should include the global
		if(isset($query['conditions']) and !$query['conditions'])
			$this->includeGlobal = true;
		
		// for some weird reason, if they decided to use OrgGroup::find('first');
		if(isset($query['limit']) and $query['limit'] == 1)
			$this->includeGlobal = false;
		
		return parent::beforeFind($query);
	}
	
	public function afterFind($results = array(), $primary = false)
	{
		if($this->includeGlobal)
		{
			$this->includeGlobal = false;
			$global = $this->read(null, 0);
			array_unshift($results, $global);
		}
		return parent::afterFind($results, $primary);
	}
	
	public function read($fields = null, $id = null)
	{
		if($id == 0)
		{
			return $this->Common_readGlobalObject();
		}
		return parent::read($fields, $id);
	}
}
