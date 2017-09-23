<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Application_Log_View_ForgetUsernameOrPassword
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: ForgetUsernameOrPassword.php 10.3.2012 7.55am ayoola $
 */

/**
 * @see Application_Log_Abstract
 */
 
//require_once 'Application/Log/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Application_Log_View_ForgetUsernameOrPassword
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Log_View_ForgetUsernameOrPassword extends Application_Log_View_Abstract
{
	
    /**
     * Table where log goes to
     * 
     * @var string
     */
	protected static $_logTable = 'Application_Log_View_ForgetUsernameOrPassword_Log';
		
    /**
     * Creates a log
     * 
     */
	public static function log( array $info )
	{
	//	var_export( $info );
		foreach( self::getLogTable()->getDataTypes() as $key => $value )
		{
			$log[$key] = @$_SERVER[strtoupper( $key )];
		}
		$log['user_id'] = @$info['user_id'] ? : $info['username'];
		$log['ip'] = implode( ':', Ayoola_Application::getRuntimeSettings( 'user_ip' ) );
		$log['time'] = time();
		$log['result'] = $info['result'];
		$result = self::getLogTable()->insert( $log );
   }
	// END OF CLASS
}
