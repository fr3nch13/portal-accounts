<?php
App::uses('AppModel', 'Model');
App::uses('ContactsAdAccount', 'Contacts.Model');

class AdAccount extends ContactsAdAccount 
{
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'ad_account_id',
			'dependent' => false,
		),
	);
}
