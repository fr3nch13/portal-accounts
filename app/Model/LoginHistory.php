<?php
class LoginHistory extends AppModel 
{
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Client' => array(
			'className' => 'OAuthServer.Client',
			'foreignKey' => 'client_id',
		),
	);

	// define the fields that can be searched
	public $searchFields = array(
		'LoginHistory.email',
		'LoginHistory.ipaddress',
		'LoginHistory.user_agent',
		'Client.client_name',
		'User.name',
	);
	
	public function failedLogins($minutes = 5)
	{
		$minutes = '-'. $minutes. ' minutes';
		
		return $this->find('all', array(
			'recursive' => '0',
			'contain' => array('User'),
			'conditions' => array(
				'LoginHistory.success' => 0,
				'LoginHistory.timestamp >' => date('Y-m-d H:i:s', strtotime($minutes)),
			),
		));
	}
}