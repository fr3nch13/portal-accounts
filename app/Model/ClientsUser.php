<?php
App::uses('AppModel', 'Model');

class ClientsUser extends AppModel 
{

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Client' => array(
			'className' => 'OAuthServer.Client',
			'foreignKey' => 'client_id',
		),
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
		),
	);
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
		'User.adaccount',
		'User.userid',
		'OrgGroup.name',
		'Client.client_name',
		'ClientsUser.role',
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	public function afterFind($results = array(), $primary = false)
	{
		$globalOrgGroup = $this->OrgGroup->read(null, 0);
		foreach($results as $i => $result)
		{
			if(isset($result['OrgGroup']) and array_key_exists('id', $result['OrgGroup']) and !$result['OrgGroup']['id'])
			{
				$results[$i]['OrgGroup'] = $globalOrgGroup['OrgGroup'];
			}
		}
		return parent::afterFind($results, $primary);
	}
	
	public function checkAddUpdate($user_id = false, $client_id = false, $extra = array())
	{
		if(!$user_id) return false;
		if(!$client_id) return false;
		
		if($id = $this->field($this->primaryKey, array($this->alias.'.user_id' => $user_id, $this->alias.'.client_id' => $client_id)))
		{
			$this->id = $id;
		}
		else
		{
			$this->create();
		}
		$this->data = $extra;
		$this->data = array_merge(array('user_id' => $user_id, 'client_id' => $client_id), $extra);
		if($this->save($this->data))
		{
			return $this->id;
		}
		return false;
	}
	
	public function addUsers($data = array())
	{
		$alias = $this->Client->alias;
		if(!isset($data[$alias]['client_id']))
		{
			$this->modelError = __('Unknown %s', __('Client'));
			return false;
		}
		$client_id = $data[$alias]['client_id'];
		
		if(!isset($data[$alias]['user_ids']))
		{
			$this->modelError = __('No %s selected. (1)', __('Users'));
			return false;
		}
		
		if(!$data[$alias]['user_ids'])
		{
			$this->modelError = __('No %s selected. (2)', __('Users'));
			return false;
		}
		
		$user_ids = $data[$alias]['user_ids'];
		
		// filter out the ones that are already assigned to this client
		$existing_user_ids = $this->find('list', array(
			'contain' => array('User'),
			'conditions' => array(
				'ClientsUser.client_id' => $client_id,
			),
			'fields' => array('User.id', 'User.org_group_id'),
		));
		
		$new_users = $this->User->find('list', array(
			'conditions' => array('User.id' => $user_ids),
			'fields' => array('User.id', 'User.org_group_id'),
		));
		
		$saveData = array();
		foreach($data[$alias]['user_ids'] as $user_id)
		{
			if(isset($existing_user_ids[$user_id]))
				continue;
			if(!isset($new_users[$user_id]))
				continue;
			
			$saveData[] = array(
				'client_id' => $client_id,
				'user_id' => $user_id,
				'org_group_id' => $new_users[$user_id],
				'active' => true,
			);
		}
		return $this->saveMany($saveData);
	}
	
	public function addClients($data = array())
	{
		$alias = $this->User->alias;
		if(!isset($data[$alias]['id']))
		{
			$this->modelError = __('Unknown %s - 1', __('User'));
			return false;
		}
		$user_id = $data[$alias]['id'];
		
		if(!$user = $this->User->read(null, $user_id))
		{
			$this->modelError = __('Unknown %s - 2', __('User'));
			return false;
		}
		
		if(!isset($data[$alias]['client_ids']))
		{
			$this->modelError = __('No %s selected. (1)', __('Clients'));
			return false;
		}
		
		if(!$data[$alias]['client_ids'])
		{
			$this->modelError = __('No %s selected. (2)', __('Clients'));
			return false;
		}
		
		$client_ids = $data[$alias]['client_ids'];
		
		// filter out the ones that are already assigned to this client
		$existing_client_ids = $this->find('list', array(
			'conditions' => array(
				'ClientsUser.user_id' => $user_id,
			),
			'fields' => array('ClientsUser.client_id', 'ClientsUser.client_id'),
		));
		
		$saveData = array();
		foreach($data[$alias]['client_ids'] as $client_id)
		{
			if(isset($existing_client_ids[$client_id]))
				continue;
			
			$saveData[] = array(
				'client_id' => $client_id,
				'user_id' => $user_id,
				'org_group_id' => $user['User']['org_group_id'],
				'active' => true,
			);
		}
		return $this->saveMany($saveData);
	}
	
	// from the add/edit User forms
	public function manageClientsUser($user_id = null, $clients = array())
	{
		if(!$user_id)
			return false;
		if(!$clients)
			return false;
		
		foreach($clients as $client_id => $data)
		{
			if($client_id == 'all')
				continue;
			if(!$data['allow'])
			{
				$this->deleteAll(array(
					'ClientsUser.user_id' => $user_id,
					'ClientsUser.client_id' => $client_id,
				));
				continue;
			}
			
			$this->checkAddUpdate($user_id, $client_id, $data);
		}
	}
}
