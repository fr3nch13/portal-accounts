<?php
// app/Model/User.php

App::uses('AppModel', 'Model');

class User extends AppModel
{
	public $name = 'User';
	
	public $displayField = 'name';
	
	public $validate = array(
		'email' => array(
			'required' => array(
				'rule' => array('email'),
				'message' => 'A valid email adress is required',
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false,
			),
		),
	);
	
	public $hasOne = array(
		'UserSetting' => array(
			'className' => 'UserSetting',
			'foreignKey' => 'user_id',
		)
	);
	
	public $hasMany = array(
		'LoginHistory' => array(
			'className' => 'LoginHistory',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'ClientsUser' => array(
			'className' => 'ClientsUser',
			'foreignKey' => 'user_id',
			'dependent' => true,
		),
		'AuthorizeHistory' => array(
			'className' => 'OAuthServer.AuthorizeHistory',
			'foreignKey' => 'client_id',
			'dependent' => true,
		),
	);
	
	public $belongsTo = array(
		'OrgGroup' => array(
			'className' => 'OrgGroup',
			'foreignKey' => 'org_group_id',
			'plugin_snapshot' => true,
		),
		'AdAccount' => array(
			'className' => 'AdAccount',
			'foreignKey' => 'ad_account_id',
			'plugin_snapshot' => true,
		),
		'AssocAccount' => array(
			'className' => 'AssocAccount',
			'foreignKey' => 'assoc_account_id',
			'plugin_snapshot' => true,
		),
	);
	
	public $actsAs = array(
		'Snapshot.Stat' => array(
			'entities' => array(
				'all' => array(),
				'created' => array(),
				'modified' => array(),
				'active' => array(
					'conditions' => array(
						'User.active' => true,
					),
				),
			),
		),
		'Contacts.Contacts',
		'Utilities.Email',
    );
	
	// define the fields that can be searched
	public $searchFields = array(
		'User.name',
		'User.email',
		'User.adaccount',
		'User.role',
		'User.userid',
		'OrgGroup.name'
	);
	
	// fields that are boolean and can be toggled
	public $toggleFields = array('active');
	
	// the path to the config file.
	public $configPath = false;
	
	// Any error relating to the config
	public $configError = false;
	
	// used to store info, because the photo name is changed.
	public $afterdata = false;
	
	public $newUser = false;
	
	public $clientsUserData = array();
	
	public function beforeSave($options = array())
	{
		// hash the password before saving it to the database
		if (isset($this->data[$this->alias]['password']))
		{
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		if(isset($this->data['ClientsUser']))
		{
			$this->clientsUserData = $this->data['ClientsUser'];
			unset($this->data['ClientsUser']);
		}
		if (isset($this->data[$this->alias]['userid']))
		{
			$this->data[$this->alias]['userid'] = preg_replace('/[^\d]/', '', $this->data[$this->alias]['userid']);
		}
		if (isset($this->data[$this->alias]['phone']))
		{
			$this->data[$this->alias]['phone'] = str_replace('.', '-', $this->data[$this->alias]['phone']);
		}
		if (isset($this->data[$this->alias]['adaccount']) and $this->data[$this->alias]['adaccount'])
		{
			if($ad_account_id = $this->AdAccount->checkAdd($this->data[$this->alias]['adaccount']))
			{
				$this->data[$this->alias]['ad_account_id'] = $ad_account_id;
			}
		}
		
		return parent::beforeSave($options);
	}
	
	public function afterSave($created = false, $options = array())
	{
		// if we have Associated Clients, save them
		if($this->clientsUserData)
		{
			$this->ClientsUser->manageClientsUser($this->id, $this->clientsUserData);
		}
		
		// if we edited ourselves
		if($this->id == AuthComponent::user('id'))
		{
			$user = $this->findById(AuthComponent::user('id'));
			CakeSession::write('Auth',$user);
		}
	}
	
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
	
	public function lastLogin($user_id = null)
	{
		if($user_id)
		{
			$this->id = $user_id;
			// callback false to aviod reupdating the session that was just created
			return $this->saveField('lastlogin', date('Y-m-d H:i:s'), array('callbacks' => false));
		}
		return false;
	}
	
	public function loginAttempt($input = false, $success = false, $user_id = false, $client_id = false, $remote_user = false)
	{
		$email = false;
		if(isset($input['User']['email'])) 
		{
			$email = $input['User']['email'];
			if(!$user_id)
			{
				$user_id = $this->field('id', array('email' => $email));
			}
		}
		
		$data = array(
			'email' => $email,
			'user_agent' => env('HTTP_USER_AGENT'),
			'ipaddress' => env('REMOTE_ADDR'),
			'success' => $success,
			'user_id' => $user_id,
			'client_id' => $client_id,
			'remote_user' => $remote_user,
			'timestamp' => date('Y-m-d H:i:s'),
		);
		
		$this->LoginHistory->create();
		return $this->LoginHistory->save($data);
	}
	
	public function adminEmails()
	{
		return $this->emails('admin', true);
	}
	
	public function emails($role = false, $active = true)
	{
		$conditions = array(
			'active' => $active,
		);
		
		if($role)
		{
			$conditions['role'] = $role;
		}
		
		return $this->find('list', array(
			'recursive' => -1,
			'conditions' => $conditions,
			'fields' => array(
				'email',
			),
		));
	}
	
	public function userList($user_ids = array(), $recursive = 0)
	{
		// fill the user cache
		$_users = $this->find('all', array(
			'recursive' => $recursive,
			'conditions' => array(
				'User.id' => $user_ids,
			),
		));
		
		$users = array();
		
		foreach($_users as $user)
		{
			$user_id = $user['User']['id'];
			$users[$user_id] = $user; 
		}
		
		unset($_users);
		return $users;
	}
	
	public function userUpdate($id = false)
	{
		$this->id = $id;
		$user = $this->read(null, $this->id);
		$userInfo = array();
		if(!$userInfo and $user[$this->alias]['adaccount'])
		{
			$userInfo = $this->Contacts_getInfoByUsername($user[$this->alias]['adaccount']);
		}
		elseif(!$userInfo and $user[$this->alias]['email'])
		{
			$userInfo = $this->Contacts_getInfoByEmail($user[$this->alias]['email']);
		}
		elseif(!$userInfo and $user[$this->alias]['userid'])
		{
			$userInfo = $this->Contacts_getInfoByUserid($user[$this->alias]['userid']);
		}
		
		if($userInfo)
		{
			$this->id = $id;
			$this->data = $userInfo;
			if($this->save($this->data))
			{
				return true;
			}
		}
		
		return false;
	}
	
	public function checkUpdateSiteMinder()
	{
		$this->newUser = false;
		
		if(!$userInfo = $this->Contacts_getSiteMinderInfo())
			return false;
		
		if(!isset($userInfo['email']) and !isset($userInfo['adaccount']))
			return false;
		
		if(!isset($userInfo['assocaccount']))
			$userInfo['assocaccount'] = false;
		
		if(preg_match('/^aa(.*)/', $userInfo['adaccount']))
		{
			$userInfo['assocaccount'] = $userInfo['adaccount'];
		}
			
		if(isset($userInfo['email']) and !$userInfo['email'])
		{
			$userInfo['email'] = preg_replace('/^aa/', '', $userInfo['adaccount']). '@example.com';
		}
		
		$orConditions = array(
			$this->alias.'.adaccount' => $userInfo['adaccount'],
		);
		
		if(isset($userInfo['email']) and $userInfo['email'])
		{
			$orConditions[$this->alias.'.email'] = $userInfo['email'];
		}
		
		$thisUser = $this->find('first', array(
			'conditions' => array(
				'OR' => $orConditions,
			)
		));
		
		// update the user's record with the info from SiteMinder
		if($thisUser)
		{
			$this->id = $thisUser[$this->alias]['id'];
			
			$this->data = array_merge($thisUser[$this->alias], $userInfo);
			$this->data['id'] = $this->id;
			
			if(isset($this->data['password']))
				unset($this->data['password']);
			
			if(isset($this->data['created']))
				unset($this->data['created']);
			
			if(isset($this->data['modified']))
				unset($this->data['modified']);
		}
		// or create the new user
		else
		{
			$this->newUser = true;
			
			$this->create();
			$this->data = $userInfo;
			$this->data['active'] = true;
			$this->data['role'] = 'regular';
			$this->data['paginate_items'] = 25;
		}
		
		// see if we know the ad account
		if(isset($userInfo['adaccount']))
		{
			$adAccount_id = $this->AdAccount->checkAdd($userInfo['adaccount']);
			$this->data['ad_account_id'] = $adAccount_id;
			
			// update some of the information for this ad account
			$adData = array();
			
			if(isset($userInfo['firstname']) and isset($userInfo['lastname']))
			{
				$adData['name'] = trim(__('%s %s', $userInfo['firstname'], $userInfo['lastname']));
				if(!$thisUser)
				{
					$this->data['name'] = $adData['name'];
				}
			}
			
			if(isset($userInfo['sac']) and $userInfo['sac'])
			{
				$sac_id = $this->AdAccount->Sac->checkAdd($userInfo['sac']);
				$adData['sac_id'] = $sac_id;
			}
			
			if(isset($this->data['email']))
				$adData['email'] = $this->data['email'];
			
			if(isset($this->data['userid']))
				$adData['userid'] = $this->data['userid'];
			
			if($adData)
			{
				$this->AdAccount->id = $this->data['ad_account_id'];
				$adData['id'] = $this->AdAccount->id;
				$this->AdAccount->data = $adData;
				$this->AdAccount->save($this->AdAccount->data);
			}
			
			if($userInfo['assocaccount'] and $adAccount_id)
			{
				$assocAccount_id = $this->AdAccount->AssocAccount->checkAdd($userInfo['assocaccount'], $adAccount_id, $adData);
				$this->data['assoc_account_id'] = $assocAccount_id;
			}
		}
		
		if($this->save($this->data))
		{
			$user = $this->find('first', array(
				'contain' => array('AdAccount', 'AssocAccount'),
				'conditions' => array(
					$this->alias.'.id' => $this->id,
				),
			));
			
			if($this->newUser)
			{
				$this->newUserEmail($this->id, $user);
			}
			return $user;
		}
	}
	
	public function newUserEmail($id = false, $user = array())
	{
	 	if(!$id) return false;
	 	
	 	$this->id = $id;
	 	
	 	if(!$user)
	 	{
			$user = $this->find('first', array(
				'contain' => array('AdAccount', 'AssocAccount'),
				'conditions' => array(
					$this->alias.'.id' => $this->id,
				),
			));
	 	}
		
		// all Admin 
		$adminEmails = $this->adminEmails();
		foreach($adminEmails as $adminEmail)
		{
			$emails[$adminEmail] = $adminEmail;
		}
	 	
	 	// rebuild it to use the EmailBehavior from the Utilities Plugin
	 	$this->Email_reset();
		// set the variables so we can use view templates
		$viewVars = array(
			'user' => $user,
		);
		
		$this->Email_set('to', $emails);
		$this->Email_set('subject', __('New User created - ad account: %s - email: %s', $user[$this->alias]['adaccount'], $user[$this->alias]['email']));
		$this->Email_set('viewVars', $viewVars);
		$this->Email_set('template', 'new_user_email_admin');
		$this->Email_set('emailFormat', 'text');
		
		// send an email to the admins
		$this->Email_executeFull();
		
		$this->Email_set('to', $user[$this->alias]['email']);
		$this->Email_set('subject', __('Welcome to the Portals.'));
		$this->Email_set('viewVars', $viewVars);
		$this->Email_set('template', 'new_user_email');
		$this->Email_set('emailFormat', 'text');
		
		// send an email to the user
		$this->Email_executeFull();
	}
}

