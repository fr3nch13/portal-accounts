<?php
App::uses('AppController', 'Controller');

class OrgGroupsController extends AppController 
{
	public $allowAdminDelete = true;
	
	public function admin_index() 
	{
		$this->Prg->commonProcess();
		
		$conditions = array();
		$conditions = array_merge($conditions, $this->conditions);
		
		$this->OrgGroup->recursive = -1;
		$this->paginate['order'] = array('OrgGroup.name' => 'asc');
		$this->paginate['conditions'] = $this->OrgGroup->conditions($conditions, $this->passedArgs); 
		$this->set('org_groups', $this->paginate());
	}
	
	public function admin_view($id = 0)
	{
		if(!$org_group = $this->OrgGroup->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Org Group')));
		};
		
		$this->set('org_group', $org_group);
	}
	
	public function admin_add() 
	{
		if($this->request->is('post'))
		{
			$this->OrgGroup->create();
			
			if($this->OrgGroup->save($this->request->data))
			{
				$this->Flash->success(__('The %s has been saved', __('Org Group')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Org Group')));
			}
		}
	}
	
	public function admin_edit($id = null) 
	{
		if(!$org_group = $this->OrgGroup->read(null, $id))
		{
			throw new NotFoundException(__('Invalid %s', __('Org Group')));
		};
		
		if($this->request->is('post') || $this->request->is('put')) 
		{
			if($this->OrgGroup->save($this->request->data)) 
			{
				$this->Flash->success(__('The %s has been saved', __('Org Group')));
				return $this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Flash->error(__('The %s could not be saved. Please, try again.', __('Org Group')));
			}
		}
		else
		{
			$this->request->data = $org_group;
		}
	}
}
