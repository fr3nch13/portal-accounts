<?php 

$page_options = array(
	$this->Html->link(__('Add User'), array('action' => 'add')),
	$this->Html->link(__('Login History'), array('controller' => 'login_histories')),
);

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name', 'editable' => array('type' => 'text') )),
	'User.adaccount' => array('content' => __('Path/AD Account'), 'options' => array('sort' => 'User.adaccount')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email', 'editable' => array('type' => 'text') )),
	'User.phone' => array('content' => __('Phone'), 'options' => array('sort' => 'User.phone', 'editable' => array('type' => 'text') )),
	'User.role' => array('content' => __('Role'), 'options' => array('sort' => 'User.role', 'editable' => array('type' => 'select', 'options' => $roles) )),
	'User.org_group_id' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name', 'editable' => array('type' => 'select', 'options' => $orgGroups) )),
	'User.clients' => array('content' => __('# Clients')),
	'User.lastlogin' => array('content' => __('Last Login'), 'options' => array('sort' => 'User.lastlogin')),
	'User.active' => array('content' => __('Active'), 'options' => array('sort' => 'User.active')),
	'User.created' => array('content' => __('Created'), 'options' => array('sort' => 'User.created')),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($users as $i => $user)
{
	$actions = array(
		$this->Html->link(__('View'), array('action' => 'view', $user['User']['id'])),
		$this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])),
	);
	
	$actions[] = $this->Html->link(__('Delete'),array('action' => 'delete', $user['User']['id']), array('confirm' => 'Are you sure?'));
	
	$td[$i] = array(
		'User.name' => $this->Html->link($user['User']['name'], array('controller' => 'users', 'action' => 'view', $user['User']['id'])),
		'User.adaccount' => $this->Contacts->makePath($user),
		'User.email' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		'User.phone' => $user['User']['phone'],
		'User.role' => array(
			$this->Local->userRole($user['User']['role']),
			array('value' => $user['User']['role'])
		),
		'User.org_group_id' => array(
			$this->Html->link($user['OrgGroup']['name'], array('controller' => 'org_groups', 'action' => 'view', $user['OrgGroup']['id'])),
			array('value' => $user['OrgGroup']['id'])
		),
		'User.clients' => array('.', array(
			'ajax_count_url' => array('controller' => 'clients_users', 'action' => 'user', $user['User']['id']), 
			'url' => array('action' => 'view', $user['User']['id'], '#' => 'ui-tabs-1'),
		)),
		'User.lastlogin' => $this->Wrap->niceTime($user['User']['lastlogin']),
		'User.active' => array(
			$this->Html->link($this->Wrap->yesNo($user['User']['active']), array('action' => 'toggle', 'active', $user['User']['id']), array('confirm' => 'Are you sure?')), 
			array('class' => 'actions'),
		),
		'User.created' => $this->Wrap->niceTime($user['User']['created']),
		'actions' => array(
			implode('', $actions),
			array('class' => 'actions'),
		),
	);
	$td[$i]['edit_id'] = array(
		'User' => $user['User']['id'],
	);
}

$use_gridedit = false;
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$use_gridedit = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Manage Users'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $use_gridedit,
));