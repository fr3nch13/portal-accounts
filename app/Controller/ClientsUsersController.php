<?php
// app/Controller/UsersController.php

class ClientsUsersController extends AppController
{
	public $allowAdminDelete = true;

	public function client($client_id = false)
	{
		if (!$client = $this->ClientsUser->Client->read(null, $client_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Client')));
		};
		
		$this->set('client', $client);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ClientsUser.client_id' => $client_id,
		);
		
		$this->ClientsUser->recursive = 0;
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->ClientsUser->conditions($conditions, $this->passedArgs); 
		$this->paginate['contain'] =  array(
			'User', 'Client', 'OrgGroup',
			'User.AdAccount', 'User.AdAccount.Sac', 'User.AdAccount.Sac.Branch', 'User.AdAccount.Sac.Branch.Division', 'User.AdAccount.Sac.Branch.Division.Org',
		);
		$this->set('users', $this->paginate('ClientsUser'));
		
		$orgGroups = $this->ClientsUser->OrgGroup->typeFormList();
		$this->set('orgGroups', $orgGroups);
	}

	public function user($user_id = false)
	{
		if (!$user = $this->ClientsUser->User->read(null, $user_id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		$this->set('user', $user);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'ClientsUser.user_id' => $user_id,
		);
		
		$this->ClientsUser->recursive = 0;
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->ClientsUser->conditions($conditions, $this->passedArgs); 
		$this->set('clients', $this->paginate('ClientsUser'));
		
		$orgGroups = $this->ClientsUser->OrgGroup->typeFormList();
		$this->set('orgGroups', $orgGroups);
	}
	
	public function admin_index()
	{
		return $this->redirect($this->referer());
	}
	
	public function admin_client($client_id = false)
	{
		return $this->client($client_id);
	}
	
	public function admin_user($user_id = false)
	{
		return $this->user($user_id);
	}
	
	public function admin_add_users($client_id = null) 
	{
		$this->ClientsUser->Client->id = $client_id;
		if (!$client = $this->ClientsUser->Client->read(null, $this->ClientsUser->Client->id))
		{
			throw new NotFoundException(__('Invalid %s', __('Client')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->ClientsUser->addUsers($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated.', __('Client')));
				$this->bypassReferer = true;
				return $this->redirect(array('plugin' => 'o_auth_server', 'controller' => 'clients', 'action' => 'view', $this->ClientsUser->Client->id));
			}
			else
			{
				$this->Session->setFlash($this->ClientsUser->modelError);
			}
		}
		else
		{
			$this->request->data = $client;
		}
		
		// get a list of users that aren't already assigned to this client
		$existing_user_ids = $this->ClientsUser->find('list', array(
			'conditions' => array(
				'ClientsUser.client_id' => $client_id,
			),
			'fields' => array('ClientsUser.user_id', 'ClientsUser.user_id'),
		));
		
		$users = $this->ClientsUser->User->find('list', array(
			'conditions' => array(
				'User.id !=' => $existing_user_ids,
			),
			'order' => array('User.name' => 'asc'),
			'fields' => array('User.id', 'User.name'),
		));
		$this->set('users', $users);
	}
	
	public function admin_add_clients($user_id = null) 
	{
		$this->ClientsUser->User->id = $user_id;
		if (!$user = $this->ClientsUser->User->read(null, $this->ClientsUser->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->ClientsUser->addClients($this->request->data))
			{
				$this->Session->setFlash(__('The %s has been updated.', __('User')));
				$this->bypassReferer = true;
				return $this->redirect(array('controller' => 'users', 'action' => 'view', $this->ClientsUser->User->id));
			}
			else
			{
				$this->Session->setFlash($this->ClientsUser->modelError);
			}
		}
		else
		{
			$this->request->data = $user;
		}
		
		// get a list of clients that aren't already assigned to this user
		$existing_client_ids = $this->ClientsUser->find('list', array(
			'conditions' => array(
				'ClientsUser.user_id' => $user_id,
			),
			'fields' => array('ClientsUser.client_id', 'ClientsUser.client_id'),
		));
		
		$clients = $this->ClientsUser->Client->find('list', array(
			'conditions' => array(
				'Client.client_id !=' => $existing_client_ids,
			),
			'order' => array('Client.client_name' => 'asc'),
			'fields' => array('Client.client_id', 'Client.client_name'),
		));
		$this->set('clients', $clients);
	}

	public function admin_delete($id = null) 
	{
		$this->ClientsUser->id = $id;
		if (!$this->ClientsUser->exists()) 
		{
			throw new NotFoundException(__('Invalid %s', __('Client/User relationship')));
		}

		if ($this->ClientsUser->delete()) 
		{
			$this->Session->setFlash(__('%s Deleted', __('Client/User relationship')));
			return $this->redirect($this->referer());
		}
		
		$this->Session->setFlash(__('%s was not deleted', __('Client/User relationship')));
		return $this->redirect($this->referer());
	}
}