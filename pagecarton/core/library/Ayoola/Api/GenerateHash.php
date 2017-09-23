<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Api_GenerateHash
 * @copyright  Copyright (c) 2011-2012 Ayoola Online Inc. (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: GenerateHash.php 11.01.2011 9.23am ayoola $
 */

/**
 * @see Ayoola_
 */
 
//require_once 'Ayoola/.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Api_GenerateHash
 * @copyright  Copyright (c) 2011-2012 Ayoola Online Inc. (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Api_GenerateHash extends Ayoola_Api_Abstract
{
	
    /**
     * Whether class is playable or not
     *
     * @var boolean
     */
	protected static $_playable = true;
	
    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 99;
	
	
    /**
     * 
     */
	public function init()
    {
		try
		{ 
			if( ! $data = self::getIdentifierData() ){ return false; }
			$this->createConfirmationForm( 'Hash for ' . $data['api_label'] . '',  'This will display (in plain text) the hash required to authenticate this API on ' . $data['api_label'] . '. This is not recommended except if you want to export this value into the whitelist of another application.' );
			$this->setViewContent( $this->getForm()->view(), true );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
			$hash = self::getKeyHash( $data );
	//		var_export( $data );
			$this->setViewContent( "<p>{$hash}</p>", true ); 
		}
		catch( Application_Blog_Exception $e ){ return false; }
     
    } 
	// END OF CLASS
}
