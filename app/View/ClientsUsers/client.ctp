<?php

$page_options = array();

if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$page_options[] = $this->Html->link(__('Add %s to %s', __('Users'), __('Client')), array('action' => 'add_users', $client['Client']['client_id']));
}

// content
$th = array(
	'User.name' => array('content' => __('Name'), 'options' => array('sort' => 'User.name')),
	'User.adaccount' => array('content' => __('Path/AD Account'), 'options' => array('sort' => 'User.adaccount')),
	'User.email' => array('content' => __('Email'), 'options' => array('sort' => 'User.email' )),
	'User.phone' => array('content' => __('Phone'), 'options' => array('sort' => 'User.phone' )),
	'ClientsUser.active' => array('content' => __('Active'), 'options' => array('sort' => 'ClientsUser.active')),
	'ClientsUser.role' => array('content' => __('Role'), 'options' => array('sort' => 'ClientsUser.role', 'editable' => array('type' => 'select', 'options' => $client['Client']['roles']) )),
	'ClientsUser.org_group_id' => array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name', 'editable' => array('type' => 'select', 'options' => $orgGroups) )),
	'actions' => array('content' => __('Actions'), 'options' => array('class' => 'actions')),
);

$td = array();
foreach ($users as $i => $user)
{
	$active = $this->Wrap->yesNo($user['ClientsUser']['active']);
	$role = $this->Wrap->userRole($user['ClientsUser']['role']);
	$actions = array();
	
	if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
	{
		$actions[] = $this->Html->link(__('View'),array('controller' => 'users', 'action' => 'view', $user['User']['id']));
		$actions[] = $this->Html->link(__('Delete'),array('action' => 'delete', $user['ClientsUser']['id']), array('confirm' => __('Are you sure?')));
		$active = array(
			$this->Html->link($active, array('action' => 'toggle', 'active', $user['ClientsUser']['id']), array('confirm' => __('Are you sure?'))), 
			array('class' => 'actions'),
		);
	}
	
	$td[$i] = array(
		'User.name' => $this->Html->link($user['User']['name'], array('controller' => 'users', 'action' => 'view', $user['User']['id'])),
		'User.adaccount' => $this->Contacts->makePath($user['User']),
		'User.email' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email']),
		'User.phone' => $user['User']['phone'],
		'ClientsUser.active' => $active,
		'ClientsUser.role' => array($role, array('value' => $user['ClientsUser']['role'])),
		'ClientsUser.org_group_id' => array($user['OrgGroup']['name'], array('value' => $user['OrgGroup']['id'])),
		'actions' => array(
			implode('', $actions), 
			array('class' => 'actions'),
		),
	);
	$td[$i]['edit_id'] = array(
		'ClientsUser' => $user['ClientsUser']['id'],
	);
}

$use_gridedit = false;
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$use_gridedit = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Users'),
	'page_options' => $page_options,
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $use_gridedit,
));