<?php
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class CronShell extends AppShell
{
	// the models to use
	public $uses = array('User', 'LoginHistory');
	
	public function startup() 
	{
		$this->clear();
		$this->out('Cron Shell');
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
		
		$parser->description(__d('cake_console', __('The Cron Shell runs all needed cron jobs') ));
		
		$parser->addSubcommand('failed_logins', array(
			'help' => __d('cake_console', 'Emails a list of failed logins to the admins and users every 10 minutes'),
			'parser' => array(
				'options' => array(
					'minutes' => array(
						'help' => __d('cake_console', 'Change the time frame from 10 minutes ago.'),
						'short' => 'm',
						'default' => 10,
					),
				),
			),
		));
		
		$parser->addSubcommand('issue_reporter', array(
			'help' => __d('cake_console', __('Scans log files for all of the portals, apache, and the siteminder logs to see if we have had any issues.') ),
		));
		
		$parser->addSubcommand('login_loop_issue', array(
			'help' => __d('cake_console', __('Scans log files for all of a possible login loop issue.') ),
		));
		
		return $parser;
	}
	
	public function failed_logins()
	{
	/*
	 * Emails a list of failed logins to the admins every 5 minutes
	 * Only sends an email if there was a failed login
	 * Everything is taken care of in the Task
	 */
		$FailedLogins = $this->Tasks->load('Utilities.FailedLogins')->execute($this);
	}
	
	public function login_loop_issue()
	{
		Configure::write('debug', 1);
		
		$this->out(__('Searching for possible login loops'), 1, Shell::QUIET);
		
		$logFile = TMP. 'logs'. DS. 'apache-access.log';
		$lockFile = $logFile.'.lock';
		
		$LogFile = new File($logFile);
		$LockFile = new File($lockFile);
		// see if a lock file exists
		if(!$LockFile->exists())
		{
			// if not, copy the current file to a lock file
			$LockFile->create();
			$LockFile->write($LogFile->read());
			chmod($lockFile, 0666);
			$LockFile = new File($lockFile);
			
			// and touch/empty the current file.
			$LogFile->write('');
		}
		
		// scan the lock file for what we need
		$data = $LockFile->read();
		$lines = explode("\n", $data);
		
		$apache_regex = '/^(?P<IP>\S+)
			\ (?P<ident>\S)
			\ (?P<auth_user>.*?) # Spaces are allowed here, can be empty.
			\ (?P<date>\[[^]]+\])
			\ "(?P<http_start_line>.+ .+)" # At least one space: HTTP 0.9
			\ (?P<status_code>[0-9]+) # Status code is _always_ an integer
			\ (?P<response_size>(?:[0-9]+|-)) # Response size can be -
			\ "(?P<referrer>.*?)" # Referrer can contains everything: its just a header
			\ "(?P<user_agent>.*)"$
			/x';
		
		$possibleFailures = array();
		foreach($lines as $i => $line)
		{
			if(!preg_match('/authorize\.oauth/', $line))
				continue;
			
			$matches = array();
			preg_match($apache_regex ,$line, $matches);
			
			if(isset($matches['auth_user']) and $matches['auth_user'] != '-')
				continue;
			
			// uncomment after testing
			if(isset($matches['referrer']) and $matches['referrer'] != '-')
				continue;
			
			// not firefox
			if(isset($matches['user_agent']) and preg_match('/Firefox/', $matches['user_agent']))
					continue;
			
			// not chrome
			if(isset($matches['user_agent']) and preg_match('/\s+Chrome/', $matches['user_agent']))
					continue;
			
			// not safari
			if(isset($matches['user_agent']) and preg_match('/\s+Safari/', $matches['user_agent']))
					continue;
			
			$possibleFailures[] = $line;
		}
		
		$this->out(__('Possible login loops found: %s', count($possibleFailures)), 1, Shell::QUIET);
		
		if($possibleFailures)
		{
			$subject = __('Possible Login Loops: %s', count($possibleFailures));
			
			$EmailTask = $this->Tasks->load('Utilities.Email');
			$EmailTask->set('subject', $subject);
			$EmailTask->set('to', 'user@example.com');
			$EmailTask->set('body', implode("\n", $possibleFailures));
			$EmailTask->execute();
			
			$this->out(__('Email sent'), 1, Shell::QUIET);
		}
		$LockFile->delete();
	}
	
	public function issue_reporter()
	{
		$issues = array();
		$now = time();
		
		$app = dirname(APP);
		$portalsRoot = dirname($app);
		$portalsRootDir = new Folder($portalsRoot);
		if(!$portalsRootFiles = list($folders, $files) = $portalsRootDir->read(true, array('*.bbprojectd'), true))
		{
			$this->out(__('Failed to read the %s folder.', $portalsRoot));
			return false;
		}
		// find the error logs for each of the portals
		$portals = array();
		foreach($folders as $folder)
		{
			$folderPath = $folder;
			$folder = new Folder($folderPath);
			if(!$folder->find('composer.json'))
				continue;
			$portals[$folderPath] = $folder;
		}
		
		if(!$portals)
		{
			$this->out(__('No portals were found at: %s', $portalsRoot));
			return false;
		}
		
		foreach($portals as $portal)
		{
		}
	}
}