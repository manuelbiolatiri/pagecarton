<?php
/**
 * PageCarton
 *
 * LICENSE
 *
 * @category   PageCarton
 * @package    Ayoola_Doc_Adapter_Pdf
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Pdf.php 5.15.12 8.49 ayoola $
 */

/**
 * @see Ayoola_Doc_Adapter_Abstract
 */
 
require_once 'Ayoola/Doc/Adapter/Abstract.php';


/**
 * @category   PageCarton
 * @package    Ayoola_Doc_Adapter_Pdf
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Doc_Adapter_Pdf extends Ayoola_Doc_Adapter_Abstract
{

    /**
     * The Default Content Type to be used for the Documents
     *
     * @var string
     */
	protected $_defaultContentType = 'application/pdf';
	
	// END OF CLASS
}
