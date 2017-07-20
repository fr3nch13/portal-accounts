<?php
App::uses('AppController', 'Controller');

class LoginHistoriesController extends AppController 
{
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		
		$this->LoginHistory->recursive = 0;
		$this->paginate['order'] = array('LoginHistory.timestamp' => 'desc');
		$this->paginate['conditions'] = $this->LoginHistory->conditions($conditions, $this->passedArgs); 
		$this->set('loginHistories', $this->paginate());
	}
	
	public function admin_user($user_id = false) 
	{
		$this->LoginHistory->User->id = $user_id;
		if (!$user = $this->LoginHistory->User->read(null, $user_id))
		{
			throw new NotFoundException(__('Invalid %s', __('User')));
		};
		$this->set('user', $user);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'LoginHistory.user_id' => $user_id
		);
		
		$this->LoginHistory->recursive = 0;
		$this->paginate['order'] = array('LoginHistory.timestamp' => 'desc');
		$this->paginate['conditions'] = $this->LoginHistory->conditions($conditions, $this->passedArgs); 
		$this->set('loginHistories', $this->paginate());
	}
	
	public function admin_client($client_id = false) 
	{
		$this->LoginHistory->Client->id = $client_id;
		if (!$client = $this->LoginHistory->Client->read(null, $client_id))
		{
			throw new NotFoundException(__('Invalid %s', __('Client')));
		};
		$this->set('client', $client);
		
		$this->Prg->commonProcess();
		
		$conditions = array(
			'LoginHistory.client_id' => $client_id
		);
		
		$this->LoginHistory->recursive = 0;
		$this->paginate['order'] = array('LoginHistory.timestamp' => 'desc');
		$this->paginate['conditions'] = $this->LoginHistory->conditions($conditions, $this->passedArgs); 
		$this->set('loginHistories', $this->paginate());
	}
	
	public function admin_delete($id = null) 
	{
		if (!$this->request->is('post')) 
		{
			throw new MethodNotAllowedException();
		}
		$this->LoginHistory->id = $id;
		if (!$this->LoginHistory->exists()) 
		{
			throw new NotFoundException(__('Invalid login history'));
		}
		if ($this->LoginHistory->delete()) 
		{
			$this->Session->setFlash(__('Login history deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Login history was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
