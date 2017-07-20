<?php
// app/Controller/UsersController.php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class UsersController extends AppController
{
	
	public function beforeFilter()
	{
		$this->Auth->allow(array(
			'login',
			'logout',
			'admin_login',
			'admin_logout',
		));
		
		return parent::beforeFilter();
	}

	public function login()
	{
		$OAuthServerParams = array();
		if($this->request->query)
		{
			$OAuthServerParams = $this->request->query;
		}
		elseif($this->Session->check('OAuthServer.params'))
		{
			$OAuthServerParams = $this->Session->read('OAuthServer.params');
		}
		
		$client = array();
		$client_id = false;
		if(isset($OAuthServerParams['client_id']))
		{
			$client_id = $OAuthServerParams['client_id'];
			$client = $this->User->ClientsUser->Client->read(null, $OAuthServerParams['client_id']);
		}
		
		// check to see if they're authenticated by SiteMinder
		$user = null;
		$remote_user = (isset($_SERVER['REMOTE_USER'])?$_SERVER['REMOTE_USER']:(isset($_SERVER['HTTP_USER_DN'])?$_SERVER['HTTP_USER_DN']:false));
		
		if($thisUser = $this->User->checkUpdateSiteMinder())
		{
			$user = $thisUser['User'];
			$this->request->data['User'] = $user;
		}
		
		// log them in
		if($this->Auth->login($user))
		{
			$redirect = $this->Auth->redirect();
			if($this->User->newUser)
			{
				$redirect = array('action' => 'new_user');
			}
			
			// logging in using oauth for another app
			if($OAuthServerParams)
			{
				$this->Session->write('OAuthServer.params', $OAuthServerParams);
				// the plugin isn't defined here as the routes loaded by the OAuthServer plugin effectivy takes care of this
				$redirect = array('controller' => 'oauth', 'action' => 'authorize', '?' => $OAuthServerParams);
			}
			
			// Log their last login as now
			$this->User->lastLogin(AuthComponent::user('id'));
			$this->User->loginAttempt($this->request->data, true, AuthComponent::user('id'), $client_id, $remote_user);
			$this->Flash->success(__('Welcome back, %s', (AuthComponent::user('name')?AuthComponent::user('name'):AuthComponent::user('email'))));
			return $this->redirect($redirect);
		}
		else
		{
			if($this->request->is('post'))
			{
				$this->User->loginAttempt($this->request->data, false, false, $client_id, $remote_user);
				$this->Flash->error(__('Invalid email or password, or your account is inactive. Please try again.'));
			}
		}
		
		$this->set(compact('OAuthServerParams', 'client'));
	}
	
	public function admin_login() 
	{
		return 	$this->setAction('login');
	}

	public function logout()
	{
		// kill the smsession (the siteminder single signon cookie) cookie as well.
		if(isset($_COOKIE['SMSESSION']))
		{
			unset($_COOKIE['SMSESSION']);
			setcookie("SMSESSION", "", time()-3600);
		}
		if(isset($_COOKIE['newuser']))
		{
			unset($_COOKIE['newuser']);
			setcookie("newuser", "", time()-3600);
		}
		$this->Auth->logout();
		
		$this->Flash->success(__('You have successfully logged out.'));
		$this->bypassReferer = true;
		return $this->redirect('https://example.com/logout');
	}
	
	public function admin_logout() 
	{
		return 	$this->setAction('logout');
		
	}
	
	public function help()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		
		$client_id = false;
		$fail_reason = false;
		$client = array();
		if(isset($this->request->params['named']))
		{
			extract($this->request->params['named']);
		}
		
		if($client_id)
		{
			$client = $this->User->ClientsUser->Client->find('first', array(
				'conditions' => array(
					'Client.'. $this->User->ClientsUser->Client->primaryKey => $client_id,
				),
			));
		}
		
		$helpContent = '';
		$files_dir = WWW_ROOT. 'files';
		$help_file = $files_dir. DS. 'user_help.html';
		$dir = new Folder($files_dir, true, 0777);
		$file = new File($help_file, true, 0666);
		
		if($file->readable())
		{
			$helpContent = $file->read();
		}
		
		$this->set(compact('user', 'client', 'fail_reason', 'helpContent'));
	}

	public function view($id = null)
	{
		if(!$id)
		{
			$id = AuthComponent::user('id');
		}
		
		$this->User->contain(array(
			'OrgGroup', 'AdAccount',
			'AdAccount.Sac', 'AdAccount.Sac.Branch', 'AdAccount.Sac.Branch.Division', 'AdAccount.Sac.Branch.Division.Org',
		));
		if(!$user = $this->User->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		if(isset($this->request->query['redirect_uri']))
			return $this->redirect($this->request->query['redirect_uri']);
		
		$this->set('user', $user);
	}

	public function edit()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		unset($user['User']['password']);
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->saveAssociated($this->request->data))
			{
				// update the Auth session data to reflect the changes
				if (isset($this->request->data['User']))
				{
					foreach($this->request->data['User'] as $k => $v)
					{
						if ($this->Session->check('Auth.User.'. $k))
						{
							$this->Session->write('Auth.User.'. $k, $v);
						}
					}
				}
				if (isset($this->request->data['UsersSetting']))
				{
					foreach($this->request->data['UsersSetting'] as $k => $v)
					{
						$this->Session->write('Auth.User.UsersSetting.'. $k, $v);
					}
				}
				
				$this->Flash->success(__('Your settings have been updated.'));
				// go back to this form 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Flash->error(__('We could not update your settings. Please, try again.'));
			}
		}
		else
		{
			$this->request->data = $user;
			
			$referer = array('action' => 'edit');
			
			if(isset($this->request->query['referer']))
			{
				$referer = $this->request->query['referer'];
			}
			
			if($this->Session->read('PWD_Referer'))
			{
				$referer = $this->Session->read('PWD_Referer');
			}
			else
			{
				$this->Session->write('PWD_Referer', $referer);
			}
		}
		$this->set('referer', $referer);
	}

	public function password()
	{
		$this->User->id = AuthComponent::user('id');
		$this->User->recursive = 0;
		if (!$user = $this->User->read(null, $this->User->id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->save($this->request->data))
			{
				$flashMsg = __('Your password has been updated.');
				
				$this->Flash->success($flashMsg);
				
				// go back to the settings page 
				return $this->redirect(array('action' => 'edit'));
			}
			else
			{
				$this->Flash->error(__('The password validation for the %s did not match. Please, try again.', __('User')));
				// go back to the settings page 
				return $this->redirect(array('action' => 'edit'));
			}
		}
		else
		{
			// if they're trying to view this page, send them to the settings form
			return $this->redirect(array('action' => 'edit'));
		}
	}

	public function admin_index()
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->paginate['order'] = array('User.name' => 'asc');
		$this->paginate['conditions'] = $this->User->conditions($conditions, $this->passedArgs); 
		$this->paginate['contain'] = array(
			'OrgGroup', 'AdAccount',
			'AdAccount.Sac', 'AdAccount.Sac.Branch', 'AdAccount.Sac.Branch.Division', 'AdAccount.Sac.Branch.Division.Org',
		);
		$this->set('users', $this->paginate());
		
		$roles = $this->User->ClientsUser->Client->getServerUserRoles();
		$orgGroups = $this->User->OrgGroup->typeFormList();
		$this->set(compact('roles', 'orgGroups'));
	}
	
	public function admin_org_group($org_group_id = null)  
	{
		$org_group = $this->User->OrgGroup->read(null, $org_group_id);
		if (!$org_group) 
		{
			throw new NotFoundException(__('Invalid %s 2', __('Org Group')));
		}
		$this->set('org_group', $org_group);
		
		$conditions = array(
			'User.org_group_id' => $org_group_id,
		);
		
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->set('page_subtitle', __('Under the %s of %s', __('Org Group'), $org_group['OrgGroup']['name']));
		$this->conditions = $conditions;
		$this->admin_index();
	}

	public function admin_view($id = null)
	{
		if(!$id)
		{
			$id = AuthComponent::user('id');
		}
		
		$this->User->contain(array(
			'OrgGroup', 'AdAccount',
			'AdAccount.Sac', 'AdAccount.Sac.Branch', 'AdAccount.Sac.Branch.Division', 'AdAccount.Sac.Branch.Division.Org',
		));
		if(!$user = $this->User->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		
		$this->set('user', $user);
	}

	public function admin_add()
	{
		$this->bypassReferer = true;
		if ($this->request->is('post'))
		{
			$this->User->create();
			if ($this->User->save($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved.', __('User')));
				return $this->redirect(array('action' => 'view', $this->User->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		
		$clients = $this->User->ClientsUser->Client->find('all', array('order' => array('Client.client_name' => 'asc')));
		
		$activeClientsUsers = array();
		$roleClientsUsers = array();
		$orgGroupClientsUsers = array();
		$orgGroups = $this->User->OrgGroup->typeFormList();
		
		$this->set(compact('clients', 'activeClientsUsers', 'roleClientsUsers', 'orgGroups', 'orgGroupClientsUsers'));
	}

	public function admin_edit($id = null)
	{
		$this->bypassReferer = true;
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->save($this->request->data))
			{
				if($this->User->id == AuthComponent::user('id'))
				{
					// update the Auth session data to reflect the changes
					if (isset($this->request->data['User']))
					{
						foreach($this->request->data['User'] as $k => $v)
						{
							if ($this->Session->check('Auth.User.'. $k))
							{
								$this->Session->write('Auth.User.'. $k, $v);
							}
						}
					}
				}
				$this->Flash->success(__('The %s has been saved', __('User')));
				return $this->redirect(array('action' => 'view', $this->User->id));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('User')));
			}
		}
		else
		{
			$this->User->recursive = 0;
			$this->request->data = $this->User->read(null, $this->User->id);
			unset($this->request->data['User']['password']);
		}
		
		$clients = $this->User->ClientsUser->Client->find('all', array('order' => array('Client.client_name' => 'asc')));
		$clientsUsers = $this->User->ClientsUser->find('all', array(
			'conditions' => array(
				'ClientsUser.user_id' => $id,
			),
		));
		
		$activeClientsUsers = array();
		$roleClientsUsers = array();
		$orgGroupClientsUsers = array();
		foreach($clientsUsers as $clientsUser)
		{
			$clientId = $clientsUser['ClientsUser']['client_id'];
			$activeClientsUsers[$clientId] = ($clientsUser['ClientsUser']['active']?true:false);
			$roleClientsUsers[$clientId] = $clientsUser['ClientsUser']['role'];
			$orgGroupClientsUsers[$clientId] = $clientsUser['ClientsUser']['org_group_id'];
		}
		
		$orgGroups = $this->User->OrgGroup->typeFormList();
		
		$this->set(compact('clients', 'activeClientsUsers', 'roleClientsUsers', 'orgGroups', 'orgGroupClientsUsers'));
	}
	
	public function admin_nedupdate($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		
		if($this->User->nedUpdate($id))
		{
			$this->Flash->success(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Flash->error($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}

	public function admin_password($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		if ($this->request->is('post') || $this->request->is('put'))
		{
			if ($this->User->save($this->request->data))
			{
				$this->Flash->success(__('The password for the %s has been saved.', __('User')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The password validation for the %s did not match. Please, try again.', __('User')));
				// go back to the settings page 
				return $this->redirect(array('action' => 'edit'));
			}
		}
		else
		{
			// if they're trying to view this page, send them to the edit form
			return $this->redirect(array('action' => 'edit', $id));
		}
	}

	public function admin_toggle($field = null, $id = null)
	{
		if ($this->User->toggleRecord($id, $field))
		{
			$this->Flash->success(__('The %s has been updated.', __('User')));
		}
		else
		{
			$this->Flash->error($this->User->modelError);
		}
		
		return $this->redirect($this->referer());
	}

	public function admin_delete($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists())
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		}
		if ($this->User->delete())
		{
			$this->Flash->success(__('The %s has been deleted.', __('User')));
			return $this->redirect(array('action' => 'index'));
		}
		$this->Flash->error(__('The %s was NOT deleted.', __('User')));
		return $this->redirect(array('action' => 'index'));
	}
	
	/// Config for the app
	public function admin_config()
	{
		// check that we can read/write to the config
		if(!$this->User->configCheck())
		{
			throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			// check that we can read/write to the config
			if(!$this->User->configCheck(true))
			{
				throw new InternalErrorException(__('Error with the config file: "%s". Error: %s. Please check the permissions for writing to this file.', $this->User->configPath, $this->User->configError));
			}
			if ($this->User->configSave($this->request->data))
			{
				$this->Flash->success(__('The config has been saved'));
				return $this->redirect(array('action' => 'config'));
			}
			else
			{
				$this->Flash->error(__('The config could not be saved. Please, try again.'));
				return $this->redirect(array('action' => 'config'));
			}
		}
		
		$this->set('fields', $this->User->configKeys());
		
		$this->request->data = $this->User->configRead();
	}
	
	public function admin_edit_help()
	{
		$files_dir = WWW_ROOT. 'files';
		$html_file = $files_dir. DS. 'user_help.html';
		$md_file = $files_dir. DS. 'user_help.md';
		$dir = new Folder($files_dir, true, 0777);
		$mdFile = new File($md_file, true, 0666);
		$htmlFile = new File($html_file, true, 0666);
		
		if(!$mdFile->writable())
		{
			$this->Flash->error(__('Unable to write to the user help file: %s', $md_file));
			return;
		}
		if(!$htmlFile->writable())
		{
			$this->Flash->error(__('Unable to write to the user help file: %s', $html_file));
			return;
		}
		
		if ($this->request->is('post') || $this->request->is('put'))
		{
			
			if(isset($this->request->data['User']['user_info_md']) and isset($this->request->data['User']['user_info_html']))
			{
				if($mdFile->write($this->request->data['User']['user_info_md']) and 
				$htmlFile->write($this->request->data['User']['user_info_html']) )
				{
					$this->Flash->success(__('UPDATED the user help file.'));
					$this->bypassReferer = true;
					return $this->redirect(array('action' => 'help', 'admin' => false));
				}
				else
				{
					$this->Flash->error(__('UNABLE to update the user help file.'));
				}
			}
		}
		else
		{
			$this->request->data['User']['user_info_md'] = '';
			$this->request->data['User']['user_info_html'] = '';
			
			if($mdFile->readable())
			{
				$this->request->data['User']['user_info_md'] = $mdFile->read();
			}
			if($htmlFile->readable())
			{
				$this->request->data['User']['user_info_html'] = $htmlFile->read();
			}
		}
	}
}