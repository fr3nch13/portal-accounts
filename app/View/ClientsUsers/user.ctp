<?php 
// File: OAuthServer/View/Clients/user.ctp

$page_options = array();
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$page_options[] = $this->Html->link(__('Add %s to this %s', __('Clients'), __('User')), array('action' => 'add_clients', $user['User']['id']));
}

// content
$th = array();
$th['Client.client_name'] = array('content' => __('Name'), 'options' => array('sort' => 'Client.client_name'));
$th['ClientsUser.active'] = array('content' => __('Account Active'), 'options' => array('sort' => 'ClientsUser.active'));
$th['ClientsUser.role'] = array('content' => __('Role'), 'options' => array('sort' => 'ClientsUser.role', 'editable' => array('type' => 'select') ));
$th['ClientsUser.org_group_id'] = array('content' => __('Org Group'), 'options' => array('sort' => 'OrgGroup.name', 'editable' => array('type' => 'select', 'options' => $orgGroups) ));
$th['Client.redirect_uri'] = array('content' => __('Default Redirect URI'), 'options' => array('sort' => 'Client.redirect_uri'));
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
	$th['Client.client_id'] = array('content' => __('ID'), 'options' => array('sort' => 'Client.client_id'));
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
	$th['Client.client_secret'] = array('content' => __('Secret'), 'options' => array('sort' => 'Client.client_secret'));
$th['actions'] = array('content' => __('Actions'), 'options' => array('class' => 'actions'));

$td = array();
foreach ($clients as $i => $client)
{
	$actions = array(
		$this->Html->link(__('View'), array('plugin' => 'o_auth_server', 'controller' => 'clients', 'action' => 'view', $client['Client']['client_id'])),
	);
	$active = $this->Wrap->yesNo($client['ClientsUser']['active']);
	if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
	{
		$actions[] = $this->Html->link(__('Delete'),array('action' => 'delete', $client['ClientsUser']['id']), array('confirm' => __('Are you sure?')));
		$active = array(
			$this->Html->link($active, array('action' => 'toggle', 'active', $client['ClientsUser']['id']), array('confirm' => __('Are you sure?'))), 
			array('class' => 'actions'),
		);
	}
	
	$role = $this->Wrap->userRole($client['ClientsUser']['role']);
	
	$td[$i] = array();
	$td[$i]['Client.client_name'] = $this->Html->link($client['Client']['client_name'], array('controller' => 'clients', 'action' => 'view', $client['Client']['client_id'], 'plugin' => 'o_auth_server'));
	$td[$i]['ClientsUser.active'] = $active;
	$td[$i]['ClientsUser.role'] = array(
		$role,
		array('value' => $client['ClientsUser']['role'], 'options' => $client['Client']['roles']),
	);
	$td[$i]['ClientsUser.org_group_id'] = array($client['OrgGroup']['name'], array('value' => $client['OrgGroup']['id']));
	$td[$i]['Client.redirect_uri'] = $this->Html->link($client['Client']['redirect_uri'], $client['Client']['redirect_uri']);
	if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
		$td[$i]['Client.client_id'] = $client['Client']['client_id'];
	if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
		$td[$i]['Client.client_secret'] = $client['Client']['client_secret'];
	$td[$i]['actions'] = array(
		implode('', $actions), 
		array('class' => 'actions'),
	);
	$td[$i]['edit_id'] = array(
		'ClientsUser' => $client['ClientsUser']['id'],
	);
}

$use_gridedit = false;
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$use_gridedit = true;
}

echo $this->element('Utilities.page_index', array(
	'page_title' => __('Associated %s', __('Clients')),
	'page_options' => $page_options,
	'search_placeholder' => __('Associated %s', __('Clients')),
	'th' => $th,
	'td' => $td,
	'use_gridedit' => $use_gridedit,
));