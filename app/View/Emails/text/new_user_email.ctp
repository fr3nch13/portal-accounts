<?php 
// File: app/View/Emails/text/new_user_email.ctp

$this->Html->setFull(true);
$this->Html->asText(true);

$page_options = array(
	$this->Html->link(__('View My Account'), array('action' => 'view', $user['User']['id'])),
	$this->Html->link(__('Edit My Details'), array('action' => 'edit', $user['User']['id'])),
);

$details_blocks = array();

$details_blocks[1] = array();
$details_blocks[1]['details'][] = array('name' => __('ID'), 'value' => $user['User']['id']);
$details_blocks[1]['details'][] = array('name' => __('AD Account'), 'value' => $user['User']['adaccount']);
$details_blocks[1]['details'][] = array('name' => __('Email'), 'value' => $user['User']['email']);

echo $this->element('Utilities.email_text_view_columns', array(
	'page_title' => __('New User'),
	'page_options' => $page_options,
	'details_blocks' => $details_blocks,
));
$this->Html->setFull(false);