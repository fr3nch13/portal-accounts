<?php
App::uses('AppModel', 'Model');
App::uses('ContactsAssocAccount', 'Contacts.Model');

class AssocAccount extends ContactsAssocAccount 
{
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'assoc_account_id',
			'dependent' => false,
		),
	);
}
