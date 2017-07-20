<?php

class MetricsShell extends AppShell
{
	// the models to use
	public $uses = array('Media', 'FismaInventory');
	
	public function startup() 
	{
//		$this->clear();
		$this->Media->shellOut('Metrics Shell');
		$this->hr();
		return parent::startup();
	}
	
	public function getOptionParser()
	{
	/*
	 * Parses out the options/arguments.
	 * http://book.cakephp.org/2.0/en/console-and-shells.html#configuring-options-and-generating-help
	 */
	
		$parser = parent::getOptionParser();
		
		$parser->description(__d('cake_console', 'The Metrics Shell runs metrics on the different objects.'));
		
		$parser->addSubcommand('yearly', array(
			'help' => __d('cake_console', 'Metrics Reports for object from the beginning of this year, to today.'),
		));
		
		return $parser;
	}
	
	public function yearly()
	{
		$created = date('Y'). '-01-01 00:00:00';
		//$created = '2013-01-01 00:00:00';
		
		
		$this->Media->shellOut(__('Metrics Counts since %s', $created), 'metrics');
		
		$counts = array();
		
		$medias = $this->Media->find('all', array(
			'recursive' => 0,
			'order' => array('Media.created DESC'),
			'conditions' => array(
				'Media.created >' => $created,
			),
		));
		
		$counts['Media Added'] = count($medias);
		$this->Media->shellOut(__('Found %s Media.', $counts['Media Added']), 'metrics');
		
		$media_statuses = array();
		$received_orgs = array();
		$obtain_reasons = array();
		$opened = 0;
		foreach($medias as $media)
		{
			if($media['Media']['state']) $opened++;
			$media_status_name = $media['MediaStatus']['name'];
			if(!$media_status_name) $media_statuses['Unassigned'] = (isset($media_statuses['Unassigned'])?++$media_statuses['Unassigned']:1);
			else $media_statuses[$media_status_name] = (isset($media_statuses[$media_status_name])?++$media_statuses[$media_status_name]:1);
			
			$received_org_name = $media['ReceivedOrg']['name'];
			if(!$received_org_name) $received_orgs['Unassigned'] = (isset($received_orgs['Unassigned'])?++$received_orgs['Unassigned']:1);
			else $received_orgs[$received_org_name] = (isset($received_orgs[$received_org_name])?++$received_orgs[$received_org_name]:1);
			
			$obtain_reason_name = $media['ObtainReason']['name'];
			if(!$obtain_reason_name) $obtain_reasons['Unassigned'] = (isset($obtain_reasons['Unassigned'])?++$obtain_reasons['Unassigned']:1);
			else $obtain_reasons[$obtain_reason_name] = (isset($obtain_reasons[$obtain_reason_name])?++$obtain_reasons[$obtain_reason_name]:1);
		}
		
		$counts['Open Media'] = $opened;
		
		arsort($media_statuses);
		$counts['Media Counts by Media Statuses'] = $media_statuses;
		
		arsort($received_orgs);
		$counts['Media Counts by Received Orgs'] = $received_orgs;
		
		arsort($obtain_reasons);
		$counts['Media Counts by Obtain Reasons'] = $obtain_reasons;
		
		unset($medias);
		unset($media_statuses);
		unset($received_orgs);
		unset($obtain_reasons);
		
pr($counts);
	}
}