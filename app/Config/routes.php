<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

// The default route
//	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

	Router::connect('/', array('controller' => 'users', 'action' => 'view', 'admin' => false, 'manager' => false, 'reviewer' => false, 'basic' => false, 'plugin' => false));
	
	/// fix login, and logout issues
	$login_path = array('controller' => 'users', 'action' => 'login', 'admin' => false, 'manager' => false, 'reviewer' => false, 'basic' => false, 'plugin' => false);
	$logout_path = array('controller' => 'users', 'action' => 'logout', 'admin' => false, 'manager' => false, 'reviewer' => false, 'basic' => false, 'plugin' => false);
	
	// login
	/*
	Router::connect('/:prefix/:plugin/:controller/login', $login_path); // like /admin/tags/tags/login
	Router::connect('/:prefix/:controller/login', $login_path); // like /admin/users/login
	Router::connect('/:controller/login', $login_path); // like /media/login
	Router::connect('/:prefix/login', $login_path); // like /admin/login
	*/
	Router::connect('/login', $login_path);
	// logout
	Router::connect('/:prefix/:plugin/:controller/logout', $logout_path); // like /admin/tags/tags/login
	Router::connect('/:prefix/:controller/logout', $logout_path); // like /admin/users/login
	Router::connect('/:controller/logout', $logout_path); // like /media/login
	Router::connect('/:prefix/logout', $logout_path); // like /admin/login
	Router::connect('/logout', $logout_path);
			
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Load all plugin routes.  See the CakePlugin documentation on 
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();
Router::parseExtensions();

/**
 * Load the CakePHP default routes. Remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
