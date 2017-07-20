<?php 
// File: app/View/Users/view.ctp
$page_options = array(
);
if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$page_options[] = $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id']));
}
$details = array(
	array('name' => __('Email'), 'value' => $this->Html->link($user['User']['email'], 'mailto:'. $user['User']['email'])),
	array('name' => __('Path/AD Account'), 'value' => $this->Contacts->makePath($user)),
	array('name' => __('Phone'), 'value' => $user['User']['phone']),
	array('name' => __('Created'), 'value' => $this->Wrap->niceTime($user['User']['created'])),
);

$stats = array();
$tabs = array();
$tabs['clients_users'] = $stats['clients_users'] = array(
	'id' => 'clients_users',
	'name' => __('Associated %s', __('Clients')), 
	'ajax_url' => array('controller' => 'clients_users', 'action' => 'user', $user['User']['id']),
);

if($this->Common->roleCheck('admin') and $this->Common->isAdmin())
{
	$tabs['login_history'] = $stats['login_history'] = array(
		'id' => 'login_history',
		'name' => __('Login History'), 
		'ajax_url' => array('controller' => 'login_histories', 'action' => 'user', $user['User']['id']),
	);
	$tabs['authorize_history'] = $stats['authorize_history'] = array(
		'id' => 'authorize_history',
		'name' => __('Authorize History'), 
		'ajax_url' => array('plugin' => 'o_auth_server', 'controller' => 'authorize_histories', 'action' => 'user', $user['User']['id']),
	);
}

echo $this->element('Utilities.page_view', array(
	'page_title' => __('User: %s', $user['User']['name']),
	'page_options' => $page_options,
	'details_title' => ' ',
	'details' => $details,
	'stats' => $stats,
	'tabs' => $tabs,
));