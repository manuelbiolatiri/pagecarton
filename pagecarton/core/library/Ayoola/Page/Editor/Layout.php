<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    Ayoola_Page_Editor_Layout
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)  
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Layout.php 10-26-2011 9.13pm ayoola $
 */

/**
 * @see Ayoola_Page_Editor_Abstract
 */
 
require_once 'Ayoola/Page/Editor/Abstract.php';


/**
 * @category   PageCarton CMS
 * @package    Ayoola_Page_Editor_Layout
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Ayoola_Page_Editor_Layout extends Ayoola_Page_Editor_Abstract  
{
	
    /**
     * 
     * 
     * @var string 
     */
	protected static $_objectTitle = 'Layout Editor'; 

    /**
     * 
     * The objects available as viewable
     *
     * @var string Mark-Up to Display Viewable Objects List
     */
	protected $_viewableObjects = null;
	
    /**
     * Markup to display the layout editor
     * 
     * @var string 
     */
	protected $_layoutRepresentation = null;
	
    /**
     * Switch whether to update layout on page load
     * 
     * @var boolean 
     */
	protected $_updateLayoutOnEveryLoad = false;
	
    /**
     * Switch whether to update layout on page load. Duplicating this so I could make use of it in Ayoola_Page_Creator
     * 
     * @var boolean 
     */
	public $updateLayoutOnEveryLoad = false;
	
    /**
     * 
     * @var array 
     */
	protected static $_objectInfo;
		
    /**
     * Gets a page info and creates a page if its not available.
     *
     * @param void
     * @return return array $pageInfo;
     */	
    public function sourcePage( $url = null )
    {
		if( $url )
		{ 
			//	source for this specific url
			$this->_dbWhereClause['url'] = $url;
		}
	//	var_export( __LINE__ );
		//	var_export( $page );  
		//	var_export( $this->getPageInfo() );    
	//	return false;

	//	var_export( $url );
//	var_export( $this->getPageInfo() );
		if( ! $page = $this->getPageInfo() )
		{			
	//		var_export( $page );
			//	Page not found, see if we can create a local copy of this page
			if( ! $this->_dbWhereClause['url'] )
			{
				if( ! $url )
				{
					//	If this is no URL id we can help
					return false; 
				}
				else
				{
					$this->_dbWhereClause['url'] = $url;
				}

			}
		//	var_export( $this->_dbWhereClause['url'] );
			$parentContent = array();
		//	$isNotLayoutPage = stripos( $page['url'], '/layout/' ) !== 0;
			$pageToCopy = null;
			if( ! $page = Ayoola_Page::getInfo( $this->_dbWhereClause['url'] ) )
			{
/* 				//	If this is not a URL not available in parent application then we cant help
				$this->setViewContent( '<p>You need to first create a new page: "' . $this->_dbWhereClause['url'] . '" </p>', true );
				$this->setViewContent( '<p class="boxednews goodnews"><a rel="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Creator/?url=' . $this->_dbWhereClause['url'] . '">Create</a></p>' );
				return false;
			//	throw new Ayoola_Page_Exception( 'PAGE URL IS NOT AVAILABLE IN PARENT APP.' ); 
 */			
								
				//	Auto create now...
				$page = $this->_dbWhereClause; 
			//	$pageToCopy = $page;
			}
 			else
			{
				$pageToCopy = $page;
				if( $defaultPage = Ayoola_Page::getInfo( '/' . trim( $this->_dbWhereClause['url'] . '/default', '/' ) ) )
				{  
				//	var_export( $defaultPage );
				//	$page = $defaultPage;			
					$pageToCopy = $defaultPage;
				}				
				$this->setViewContent( '<div class="">This page with url - "' . $this->_dbWhereClause['url'] . '" exists as part of the preset pages in PageCarton or in a parent website. The system need to make a copy of the page before you can edit it. Do you want to do that right now?</div>', true );
		//		$this->createConfirmationForm( 'Continue...' );
			//	$this->setViewContent( $this->getForm()->view() );
			//	if( ! $values = $this->getForm()->getValues() ){ return false; }
			//	var_export( Ayoola_Page::getPagePaths( $page['url'] ) );
				
				//	Copy the parent files
				foreach( Ayoola_Page::getPagePaths( $pageToCopy['url'] ) as $key => $each )
				{
					if( ! $each = Ayoola_Loader::checkFile( $each ) )
					{
						$this->setViewContent( '<p>A new page could not be created because: Some of the files could not be copied. Please go to <a rel="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Creator/?url=' . $this->_dbWhereClause['url'] . '">Create a fresh page at ' . $this->_dbWhereClause['url'] . '.</a></p>', true );
					}
					else
					{
						$parentContent[$key] = file_get_contents( $each );
					}
				}
				$pageToCopy['url'] = $page['url'];
				$page = $pageToCopy;
			}
		//	self::v( __LINE__ . '<br>' );
			//	Create a new page using the values of the parent application
			$class = new Ayoola_Page_Creator();

			//	make sure this is a system file.
			$pageToCopy = array_merge( $pageToCopy ? : $page, array( 'system' => 1 ) );
		//	var_export( $pageToCopy );
			$class->fakeValues = $pageToCopy;


			$class->init();
	//		var_export( $class->getForm()->getValues() );
		//	var_export( $pageToCopy );
		//	self::v( $page );
		//	self::v( __LINE__ . '<br>' );
			if( ! $class->getForm()->getValues() || $class->getForm()->getBadnews() )
			{
		//	self::v( __LINE__ . '<br>' );
				$this->setViewContent( '<p>A new page could not be created because: ' . array_shift( $class->getForm()->getBadnews() ) . '. Please go to <a rel="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Creator/?url=' . $this->_dbWhereClause['url'] . '">Create a fresh page at ' . $this->_dbWhereClause['url'] . '.</a></p>', true );
				return false;
			}
	//		self::v( __LINE__ . '<br>' );
			//	save parent template into the new page
			if( $parentContent )
			{
				foreach( Ayoola_Page::getPagePaths( $page['url'] ) as $key => $each )
				{
				//	if( $each = Ayoola_Loader::checkFile( $each ) )
					{
	//		self::v( $each . '<br>' );
	//		self::v( $parentContent[$key] );
						$savePath = Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . $each;
						
						@Ayoola_Doc::createDirectory( dirname( $savePath ) );
						file_put_contents( $savePath, @$parentContent[$key] ); 
					}
				}

				// sanitize so it could refresh with latest template
			//	$class = new Ayoola_Page_Editor_Sanitize();

				//	create this page if not available.
		//		$class->refresh( $page['url'] );	     		
			}
		//	$_POST = array();

			//	Long journey
		//	$this->setViewContent( '<span class="boxednews">Page successfully created.</span> <span class="boxednews goodnews"><a href="' . Ayoola_Application::getUrlPrefix() . '/ayoola/page/edit/layout/?url=' . $page['url'] . '"> Edit</a></span>', true );
		//	header( 'Location: /ayoola/page/edit/layout/?url=' . $page['url'] );
		//	return false;
		
			//	reload the page settings to get settings for new page.
			$this->setPageInfo();
			$page = $this->getPageInfo();
		//	$_POST = array();
		//	unset( $_POST );
		}
		return $page;

	}

	//		var_export( $_POST );		
    /**
     * Performs the layout process
     *
     * @param void
     * @return boolean
     */	
    public function init()
    {
	//		var_export( $_POST );
		if( ! $page = $this->sourcePage() )
		{
	//		var_export( $page );
			return false;
		}
	//	var_export( $page );

		//	Allows the htmlHeader to get the correct layout name to use for <base>
		Ayoola_Page::$layoutName = @$page['layout_name'] ? : Application_Settings_Abstract::getSettings( 'Page', 'default_layout' ); 
			
		//	var_export( $_POST ); 
		
		$this->getLayoutRepresentation();
		if( ! @$_POST )
		{
			//	Change Title
			if( stripos( $page['url'], '/layout/' ) !== 0 )
			{
				$title = 'Editing "' . $page['url'] . '"';
			}
			else
			{
				$title = 'Editing Theme';
			}

			if( strpos( Ayoola_Page::getCurrentPageInfo( 'title' ), $title ) === false )
			{
				$pageInfo = array(
					'title' => trim( $title . ' - ' .  Ayoola_Page::getCurrentPageInfo( 'title' ), '- ' )
				);
				Ayoola_Page::setCurrentPageInfo( $pageInfo );
			}
			
 			//	Create TMP file for the template
			$path = $this->getPagePaths();
	//	 	var_export( $path );   
			$tmp = tempnam( sys_get_temp_dir(), __CLASS__ );           
		//	$tmp = $path['template'] . '.tmp';
		//	Application_Style::addFile( '/js/objects/webReferenceDragNDrop/css.css' );
			Application_Javascript::addFile( '/js/objects/lukeBreuerDragNDrop.js' );
			Application_Javascript::addFile( '/js/objects/webReferenceDragNDrop.js' );
			Application_Javascript::addFile( '/js/objects/dragNDrop.js' );
			Application_Javascript::addCode( $this->javascript() );
			if( $this->_layoutRepresentation )
			{
				file_put_contents( $tmp, $this->_layoutRepresentation );
			//	var_export( $tmp );
			//	var_export( $this->_layoutRepresentation );
				
			//	exit();
		//		echo strlen( $this->_layoutRepresentation ) ;
		//		exit();
				include_once $tmp;
				unlink( $tmp );
			//	exit();
			}
			else
			{
			
				return false;
			}
 		}
	//	exit();
		if( ! $this->_updateLayoutOnEveryLoad && ! $this->updateLayoutOnEveryLoad ){ exit(); }  
		
		
		

    } 
		
    /**
     * 
     * @param void
     * @return string
     */
    public static function getDefaultLayout()   
    {
		if( $defaultLayout = Application_Settings_Abstract::getSettings( 'Page', 'default_layout' ) )     
		{
			return $defaultLayout;
		}
	//	return 'bootstrapmini';
	//	return 'alissa-coming-soon';
		return 'bootstrapbasic';
	}
	
    /**
     * Produces the layout representation and also proccess POSTed data
     * 
     * @param void
     * @return mixed
     */
    public function getLayoutRepresentation()
    {
	//	var_export( $values );
		$page = $this->getPageInfo();
	//	var_export( $page );
	//	if( ! $values = $this->getValues() ){ return false; }
	//	var_export( $this->getPagePaths() );
		$values = $this->getValues();
	//	$previousValues = $values;
		
		//	debug 
		if( $values == array ( 0 => false, ) )
		{
			$values = array();
		}
		$sectionalValues = array();
	
		if( ! $paths = $this->getPagePaths() )
		{
			return false;
		}
	//	var_export( $values );
		// Initialize my contents
		$base = basename( $paths['include'] );
		$date = date('l jS \of F Y h:i:s A');
		$generated = __CLASS__;
		$username = Ayoola_Application::getUserInfo( 'email' );
		$copyright = "/**\n* PageCarton Page Generator\n*\n* LICENSE\n*\n* @category PageCarton\n* @package {$page['url']}\n* @generated {$generated}\n* @copyright  Copyright (c) PageCarton. (http://www.PageCarton.com)\n* @license    http://www.PageCarton.com/license.txt\n* @version \$Id: {$base}	{$date}	{$username} \$ \n*/";
		$comment['template'] = "<?php\n$copyright\n//	Template Content ?>\n";
		$comment['include'] = "<?php\n$copyright\n//	Page Include Content\n";
		
		//	We are working on two files
		$content = array();
		$content['template'] = null;
		$content['include'] = null;
		
		require_once 'Ayoola/Filter/LayoutIdToPath.php';
		$filter = new Ayoola_Filter_LayoutIdToPath( $page );   
	//	$defaultLayout = $filter->filter( $defaultLayout );

/* 		//	Get the layout file if any
		if( ! @$page['pagelayout_filename'] )	//	Compatibility
		{
			$page['pagelayout_filename'] = self::getDefaultLayout();
		//	var_export( $page['pagelayout_filename'] );
		}
		if( ! $filePath = Ayoola_Loader::checkFile( $page['pagelayout_filename'] ) )
		{ 
			$filePath = Ayoola_Loader::checkFile( self::getDefaultLayout() );
		}
 */		//	Get the layout file if any
//		$dir = Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS;
//		if( ! is_file( $dir . @$layoutData['pagelayout_filename'] ) )

		$theme = @$_REQUEST['pc_page_editor_layout_name'] ? : $page['layout_name'];
		$theme = $theme ? : self::getDefaultLayout();
		if( ! Ayoola_Loader::checkFile( @$page['pagelayout_filename'] ) )	//	Compatibility
		{
			$page['pagelayout_filename'] = $filter->filter( $theme );
		}
		if( ! $filePath = Ayoola_Loader::checkFile( $page['pagelayout_filename'] ) )
		{ 
			$filePath = Ayoola_Loader::checkFile( $filter->filter( self::getDefaultLayout() ) );
		}
//		var_export( $pageThemeFile );
	//	$filePath = self::getLayoutTemplateFilePath( $page )
//		var_export( self::getDefaultLayout() );
//		var_export( $filePath );
//		var_export( $page['pagelayout_filename'] );
		$page['pagelayout_filename'] = $filePath; 
		$this->hashListForJs = NULL;
		$this->hashListForJsFunction = NULL;
	// 	var_export( $filter->filter( self::getDefaultLayout() ) );
		if( ! $content['template'] = @file_get_contents( $filePath ) )
		{
			$this->setViewContent( '<p class="boxednews badnews">You need to select a default page "template" layout. </p><a  class="boxednews goodnews" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Settings_Editor/settingsname_name/Page/?previous_url=/ayoola/page/edit/layout/?url=' . $page['url'] . '">Choose a template</a>.' );
			return false;
		}
		
		if( $layoutPreloadedContents = @include $page['pagelayout_filename'] . 'sections' )
		{
		//	var_export( $layoutPreloadedContents );
		}
	//	var_export( $layoutPreloadedContents );
 	//	$this->_layoutRepresentation = $xml->saveXml() ? : $content['template'];
//		var_export( $content['template'] );   
//		exit();

		// check if theres a page specific theme file
		$pageThemeFileUrl = $page['url'];
		if( $pageThemeFileUrl == '/' )
		{
			$pageThemeFileUrl = '/index';
		}
		$pageThemeFile = '/layout/' . $theme . '' . $pageThemeFileUrl . '.html';
//		var_export( $pageThemeFile );
	//	var_export( $page );
		$whereToGetPlaceholders = $content['template'];
	//	$whitespaces = array( " ", "\r", "\n", "\t", "\s", );
	//	var_export( $values );
			
		
		//	Add to the layout on the fly
		//	must be a word because its used as variable in the page files
	//	preg_match_all( "/@@@([\w+]+)@@@/", $this->_layoutRepresentation, $placeholders );
		preg_match_all( "/@@@([0-9A-Za-z_]+)@@@/", $content['template'], $placeholders );
		preg_match_all( "/%%([A-Za-z]+)%%/", $content['template'], $placeholders2 );
	//	var_export( $placeholders );
		$placeholders = array_unique( $placeholders[1] );
		$placeholders2 = array_unique( $placeholders2[1] );
//		var_export( $placeholders );
	//	var_export( array_fill_keys( $placeholders, '1' ) );
	//	$placeholders = array_merge( array_fill_keys( $placeholders, null ), $this->getLayouts() );
		$danglingPlaceholders = array();
		$sectionsForPreservation = explode( ',', trim( @$values['section_list'], ',' ) );
		$sectionsForPreservation = array_combine( $sectionsForPreservation, $sectionsForPreservation );
		unset( $sectionsForPreservation[''] );
		foreach( $placeholders as $each )
		{
			//	make sure the hash is also there
			$placeholders[] = self::hashSectionName( $each );
		}
		$danglingPlaceholders = array_merge( array_diff( $sectionsForPreservation, $placeholders ), $danglingPlaceholders );
	//	var_export( $danglingPlaceholders );
	//	var_export( $sectionsForPreservation );

		if( 
			( empty( $values ) || ! empty( $_REQUEST['pc_load_theme_defaults'] ) )
			AND $pageThemeFile = Ayoola_Loader::checkFile( Ayoola_Doc_Browser::getDocumentsDirectory() . $pageThemeFile ) )
		{
			//	We have a page-specific themefile
			// 	we use it to build the default content
			$table = new Ayoola_Page_PageLayout();
			$whereToGetPlaceholders = file_get_contents( $pageThemeFile );
		//	var_export( $whereToGetPlaceholders );
			$themeInfo = $table->selectOne( null, array( 'layout_name' => $theme ) );
		//	var_export( $themeInfo );
			$whereToGetPlaceholders = Ayoola_Page_Layout_Abstract::sanitizeTemplateFile( $whereToGetPlaceholders, $themeInfo  );

			//		look for dangling placeholders in page theme file
			preg_match_all( "/@@@([0-9A-Za-z_]+)@@@/", $whereToGetPlaceholders, $placeholdersInPageThemeFile );
			$placeholdersInPageThemeFile = $placeholdersInPageThemeFile[1];

			$danglingPlaceholders = array_merge( array_diff( $placeholdersInPageThemeFile, $placeholders ), $danglingPlaceholders );

		//	var_export( $placeholdersInPageThemeFile );
	//		var_export( $placeholders );
	//		var_export( $danglingPlaceholders );
	//		$inversePlaceholders = array_flip( $placeholders );
	//		$inverseThemePlaceholders = array_flip( $placeholdersInPageThemeFile );
		//	$danglingPlaceholders = implode( '@@@')


//		var_export( $placeholdersInPageThemeFile[1] );
			

			//	compare the contents here with the original file to discard information that's present in the theme file
			$originalFile = Ayoola_Doc_Browser::getDocumentsDirectory() . '/layout/' . $theme . '/template';
		//	var_export( $originalFile );
			if( $originalFile = file_get_contents( $originalFile ) )
			{
			//	$originalFile = str_replace( $whitespaces, '', $originalFile );
		//		var_export( $originalFile );

			}
	//		var_export( $originalFile );
		//	var_export( $whereToGetPlaceholders );
		}
	//	var_export( $danglingPlaceholders );
		// inject the dangling placeholders here. 
		foreach( $danglingPlaceholders as $key => $each )
		{
			$basePlaceholder = '@@@lastoneness@@@';
			if( ! stripos( $content['template'], $basePlaceholder ) )
			{
				$basePlaceholder = '@@@oneness@@@';
			}
			$newPlaceholder = '@@@' . $each . '@@@';
			$placeholders[] = $each;
			$content['template'] = str_ireplace( $basePlaceholder, $basePlaceholder . ' ' . $newPlaceholder,  $content['template'] );
		}
		$placeholders = array_merge( array_fill_keys( $placeholders, null ), array_fill_keys( $placeholders2, null ) );
	//	var_export( array_keys( $placeholders ) );
//		$this->sectionListForJs = implode( ',', array_keys( $placeholders ) );
		// 
		$counter = 0;
		$noOfDanglingObjects = 0;
		$hasDanglingObjects = false;
		$totalPlaceholders = count( $placeholders );
					//	var_export( $this->_layoutRepresentation );
		
		//	record remainders so we stop loosing contents if sections missing
	//	$valuesRecord = $values;
//		var_export( $theme );
//		var_export( $page['url'] );
		$this->_layoutRepresentation = $content['template'];
		foreach( $placeholders as $section => $v )
		{
			$section = strtolower( $section );
			
			$sectionContent = array();
			$sectionalObjectCollection = null;
			
			//	We are working on two files
			$sectionContent['template'] = null;
			$sectionContent['include'] = null;
			$hashSectionName = self::hashSectionName( $section );
			
	//	var_export( $hashSectionName );
	//	var_export( $sectionsForPreservation );
/*			if( ++$counter >= $totalPlaceholders )
			{
				$noOfDanglingObjects = count( $sectionsForPreservation );
				$hasDanglingObjects = count( $sectionsForPreservation );
			//	var_export( $noOfDanglingObjects . '<br>' ); 
			//	var_export( $sectionsForPreservation );
			}
*/			unset( $sectionsForPreservation[$hashSectionName] );
			
			//	set max no of objects in a section
			$maxObjectsPerSection = 10;
		//	$this->hashListForJs .= ltrim( ', ' . $hashSectionName, ',' );
			$this->hashListForJs .= ',' . $hashSectionName;
			$this->hashListForJsFunction .= ',"' . $hashSectionName . '"';
			do
			{
				for( $i = 0; $i < $maxObjectsPerSection; $i++ )
				{
					//	Need to hash so the element ID won't conflict in Js
					$numberedSectionName = $hashSectionName . $i;
					
					$templateDefaults = array();
					if( ! isset( $values[$numberedSectionName] ) && $i )
					{
						//	don't continue till ten when we don't have values
						break;
					}
					if( ! isset( $values[$numberedSectionName] ) )
					{ 
						//	compatibility
						$numberedSectionName = $section . $i;
				//		$sectionalValues[$numberedSectionName] = $values[$numberedSectionName];
						if( ! isset( $values[$numberedSectionName] ) )
						{ 
							preg_match( '/{@@@' . $section . '([\S\s]*)' . $section . '@@@}/i', $whereToGetPlaceholders, $sectionPlaceholder );

//								var_export( $placeholders );
		//						var_export( $this->_layoutRepresentation );
							$defaultPlaceHolder = @$sectionPlaceholder[1];
						//		var_export( $originalFile );
						//		var_export( $whereToGetPlaceholders );
						//		var_export( $section );
						//		var_export( $sectionPlaceholder[1] );
							if( ! empty( $originalFile ) && $sectionPlaceholder[1]  )
							{
								$check = $sectionPlaceholder[1];
						//		$check = str_replace( $whitespaces, '', $check );
							//	var_export( stripos( $check, '</nav>'  ) );
							//	var_export( $check );
								if( stripos( $check, '</nav>' ) && empty( $firstNav ) )
								{
									$firstNav = true;
									$defaultPlaceHolder = null;
								}
								if( stripos( $originalFile, $check  ) || stripos( $check, '©' ) || stripos( $check, '&copy;' ) )
								{
									//	Don't duplicate whats in theme file and navigation'
							//		var_export( $sectionPlaceholder[1] );
									$defaultPlaceHolder = null;
								}
							}
							if( $defaultPlaceHolder )
							{
							//	var_export( __LINE__ );
								//	clean the code out here so that <php dont show in new themes
			//					$defaultPlaceHolder = str_ireplace( Ayoola_Page_Layout_Abstract::getPlaceholders(), Ayoola_Page_Layout_Abstract::getPlaceholderValues2(), $defaultPlaceHolder );
							}

						//	var_export( $defaultPlaceHolder );
						//	var_export( $i );
						
							//	only show this if  && empty( $values )
						//	if( )
							if( ! empty( $_GET['pc_load_theme_defaults'] ) || empty( $values ) || $values[0] === false )
							{
					//		var_Export( $values );
					//				var_export( $layoutPreloadedContents[$section] );
								if( $i == 0 && $defaultPlaceHolder )
//								if( $i == 0 && $defaultPlaceHolder && ( empty( $previousValues ) ) )
								{ 	
								//	var_export( $defaultPlaceHolder );
								//	var_export( $numberedSectionName );
									//	allow templates to inject default content
									//	This is first and only
									$i = $maxObjectsPerSection;
									$defaultPlaceHolder = str_ireplace( Ayoola_Page_Layout_Abstract::getPlaceholderValues(), Ayoola_Page_Layout_Abstract::getPlaceholderValues2(), $defaultPlaceHolder );
								//	var_export( $defaultPlaceHolder );
									$templateDefaults = array( 'editable' => $defaultPlaceHolder,  );
									$sectionalValues[$numberedSectionName . '_template_defaults'] = $templateDefaults;
									$sectionalValues[$numberedSectionName] = 'Ayoola_Page_Editor_Text';
								}
								elseif( ! empty( $layoutPreloadedContents[$section][$i] ) )
					//			elseif( ! empty( $layoutPreloadedContents[$section][$i] && empty( $values ) ) )
								{ 	
							//		var_export( $layoutPreloadedContents[$section][$i] );
								//	var_export( $values );
									$templateDefaults = $layoutPreloadedContents[$section][$i];
									$sectionalValues[$numberedSectionName . '_template_defaults'] = $templateDefaults;
									$sectionalValues[$numberedSectionName] = $layoutPreloadedContents[$section][$i]['object_name'];
								}
								else
								{
									continue 1; 
								}
							}
							else
							{
								continue 1; 
							}
						} 
					}
				//	var_export( $values );
					$eachObject = $this->getObjectInfo( $values[$numberedSectionName] ? : $sectionalValues[$numberedSectionName] );
				//	var_export( $eachObject );
					if( ! isset( $eachObject['object_name'] ) )
					{ 
						continue; 
					} 
			//		var_export( $templateDefaults );  
			//		var_export( $eachObject );  
					$objectName = 'obj' . $numberedSectionName . $eachObject['object_name'];
					
					
					//	The objectname is conflicting in templates and the page
					//	Let's make it a function of the url to fix this
					$objectName .= $page['url'];

					//	the same url is theme urls on subdomains
					$objectName .= Ayoola_Page::getDefaultDomain();

				//	var_export( $objectName );

					$objectName = '_' . md5( $objectName );  
					
					$objectParametersAvailable = array_map( 'trim', explode( ',', @$values[$numberedSectionName . '_parameters'] ) );
					$parameters = array();
					foreach( $objectParametersAvailable as $each )
					{
						$parameters[$each] = $values[$numberedSectionName . $each];
					}
					/* For Layout representation */
					$sectionalValues[$numberedSectionName . '_template_defaults'] = $values[$numberedSectionName . '_template_defaults'] ? : $sectionalValues[$numberedSectionName . '_template_defaults'];
					$sectionalValues[$numberedSectionName . '_template_defaults'] = $sectionalValues[$numberedSectionName . '_template_defaults'] ? : array();

					//	add this here so it can be available in the the include and template files for new theme
					$parameters = $parameters + $sectionalValues[$numberedSectionName . '_template_defaults'];
					$eachObject = array_merge( $eachObject, $parameters );
					$sectionalObjectCollection .= $this->getViewableObject( $eachObject );
				//	while( false );
				//	Inject the parameters.
				
					//	Calculate advanced parameters at this level so that access levels might work
					if( ! empty( $parameters['advanced_parameters'] ) )
					{ 
						parse_str( $parameters['advanced_parameters'], $advanceParameters );
						@$injectedValues = array_combine( $advanceParameters['advanced_parameter_name'], @$advanceParameters['advanced_parameter_value'] ) ? : array();
						unset( $advanceParameters['advanced_parameter_name'] );
						unset( $advanceParameters['advanced_parameter_name'] );
					//	var_export( $advanceParameters );
						$parameters += $advanceParameters ? : array();
						$parameters += $injectedValues;
						unset( $parameters['advanced_parameters'] );
					}
					$parametersArray = $parameters;
					$parameters = var_export( $parameters, true );
				//	$sectionContent['include'] .= "\n\${$objectName}->setParameter( {$parameters} );\n";  
					@$parametersArray['wrapper_name'] = $parametersArray['wrapper_name'] ? : null;
					if( @$parametersArray['object_access_level'] )
					{
					//	Begin to populate the content of the template file
						$accessLevelStr = var_export( $parametersArray['object_access_level'], true );
						$sectionContent['include'] .= "
							if( Ayoola_Page::hasPriviledge( {$accessLevelStr}, array( 'strict' => true ) ) )
							{
								if( Ayoola_Loader::loadClass( '{$eachObject['class_name']}' ) )
								{
									\n\${$objectName} = new {$eachObject['class_name']}( {$parameters} );\n
								}
								else
								{
									\n\${$objectName} = null;\n
								}
							}    
							";
						//	Insert the view method in the "template"
						$sectionContent['template'] .= "
							if( Ayoola_Page::hasPriviledge( {$accessLevelStr}, array( 'strict' => true ) ) && ! empty( \${$objectName} ) && is_object( \${$objectName} ) )
							{
								echo Ayoola_Object_Wrapper_Abstract::wrap( \${$objectName}->view(), '{$parametersArray['wrapper_name']}' );
							}
							";
					}
					else
					{
						//	Begin to populate the content of the template file
						$sectionContent['include'] .= "
							if( Ayoola_Loader::loadClass( '{$eachObject['class_name']}' ) )
							{
								\n\${$objectName} = new {$eachObject['class_name']}( {$parameters} );\n
							}
							else
							{
								\n\${$objectName} = null;\n
							}
							";  
							
						//	Insert the view method in the "template"
						$sectionContent['template'] .= "\necho ( ! empty( \${$objectName} ) &&  is_object( \${$objectName} ) ? Ayoola_Object_Wrapper_Abstract::wrap( \${$objectName}->view(), '{$parametersArray['wrapper_name']}' ) : null );\n";
					} 
					
				
					//	We need to work on the layout template file if there is any
				}
				
				if( $hasDanglingObjects )
				{
		//		var_export( $noOfDanglingObjects );
		//		var_export( $sectionsForPreservation );
					$hashSectionName = array_pop( $sectionsForPreservation );
			//	var_export( $hashSectionName );
					$noOfDanglingObjects--;
				}
			}
		//	while( false );
			while( $hasDanglingObjects && $hashSectionName );
			
			//	refresh this here because its been tampered with in  $noOfDanglingObjects
			$hashSectionName = self::hashSectionName( $section );
			
			if( is_file( $page['pagelayout_filename'] ) ) 
			{
				//	 Try to replace contents of the layout
				$search = array( '%%' . $section . '%%', '@@@' . $section . '@@@' );
				
				//	ALLOWING ADMINISTRATORS TO EDIT TEMPLATES ON THE FLY ( MARCH 29, 2014 )
			//	$editLink = "<a style='' class='badnews' title='Edit the \"{$section}\" section of this page.' href='javascript:' onClick='ayoola.spotLight.showLinkInIFrame( \"/ayoola/page/edit/layout/?url={$page['url']}\" );'>[edit]</a>";
				$urlPrefix = Ayoola_Application::getUrlPrefix();
				$editLink =  "\" . Ayoola_Application::getUrlPrefix() . \"/ayoola/page/edit/layout/?url={$page['url']}";
				    
				$replace = "<?php\n//{$section} Begins Here\n";
				$replace .= "{$sectionContent['template']}";
					
				$replace .= "\n//{$section} Ends Here\n?>";
				
				if( stripos( $page['url'], '/layout/' ) === 0 )
				{
					//	Template editor need this so pages could use the generated template to build their own templates
					$replace = array( $replace . "%%{$section}%%", $replace . "@@@{$section}@@@" );  
				}
				elseif( stripos( $page['url'], '/tools' ) !== 0 && stripos( $page['url'], '/pc-admin' ) !== 0 && stripos( $page['url'], '/ayoola' ) !== 0 )
				{
					// admin users get the edit button   
				}				
				
				$content['template'] = str_ireplace( $search, $replace, $content['template'] );
								
				/* For Layout representation */
				$replace = "<div title='This is the \"{$section}\" section. Drag objects from the draggable pane and drop it here.' class='DragContainer' id='{$hashSectionName}'>$sectionalObjectCollection</div>\n";			
	//			var_export( $sectionalObjectCollection );
			//	$replace = "<div class='DragContainer' id='{$section}'>$sectionalObjectCollection</div>\n";			
				$this->_layoutRepresentation = str_ireplace( $search, $replace, $this->_layoutRepresentation );
				
				//	Clear our the orphan placeholders 
				//	This is affecting <section data-pc-all-sections="1"> if it is two in a theme
			//	$this->_layoutRepresentation = preg_replace( '/{?@@@' . $section . '([\S\s]*)' . $section . '@@@}?/i', '', $this->_layoutRepresentation );
			
				$this->_layoutRepresentation = preg_replace( '/{@@@' . $section . '([\S\s]*)' . $section . '@@@}/i', '', $this->_layoutRepresentation );
				$content['template'] = preg_replace( '/{@@@' . $section . '([\S\s]*)' . $section . '@@@}/i', '', $content['template'] );
	//			var_export( $this->_layoutRepresentation );
			//	echo strlen( $this->_layoutRepresentation ) ;
			//	echo "<br>" ;
		//		exit();
			}  
			else
			{
				$content['template'] .= "<?php\n//{$section} Begins Here\n{$sectionContent['template']}\n//{$section} Ends Here\n?>";
			}
			
			//	Add the new sectional data to the main content
		//	$content['template'] .= $sectionContent['template'];
			$content['include'] .= $sectionContent['include'];
			
		}
		//	Add the Copyright and page description
		$content['template'] = $comment['template'] . $content['template'];
		$content['include'] = $comment['include'] . $content['include'];
		
			//		$rPaths = Ayoola_Page::getPagePaths( $page['url'] );
		//		var_export( $rPaths );  

		
		//	save files
		if( $_POST || $this->_updateLayoutOnEveryLoad || $this->updateLayoutOnEveryLoad ) //	create template for POSTed data
		{
			//	Clear our the orphan placeholders
		//	$this->_layoutRepresentation = preg_replace( '/{?@@@' . $section . '([\S\s]*)' . $section . '@@@}?/', '', $this->_layoutRepresentation );
		//	$content['template'] = preg_replace( '/{?@@@([\S\s]*)@@@}?/', '', $content['template'] );
			
			//	var_export( $values );  
			
			//	Get new relative paths
			$rPaths = Ayoola_Page::getPagePaths( $page['url'] );
			$rPaths['data-backup'] = PAGE_PATH . DS . 'data-backup' . $page['url'] . '.backup/' . time();
			//	change the place themes are being saved.
			if( stripos( $page['url'], '/layout/' ) === 0 )
			{
				list(  , $themeName ) = explode( '/', trim( $page['url'], '/' ) );
				$rPaths['include'] = 'documents/layout/' . $themeName . '/theme/include';
				$rPaths['template'] = 'documents/layout/' . $themeName . '/theme/template';
				$rPaths['data'] = 'documents/layout/' . $themeName . '/theme/data';
				$rPaths['data_php'] = 'documents/layout/' . $themeName . '/theme/data_php';
				$rPaths['data_json'] = 'documents/layout/' . $themeName . '/theme/data_json';
				$rPaths['data-backup'] ='documents/layout/' . $themeName . '/theme/data-backup/' . time();
			}
			elseif( ! empty( $_REQUEST['pc_page_editor_layout_name'] ) )
			{
				$themeName = strtolower( $_REQUEST['pc_page_editor_layout_name'] );
				$rPaths['include'] = 'documents/layout/' . $themeName . '/theme' . $pageThemeFileUrl . '/include';
				$rPaths['template'] = 'documents/layout/' . $themeName . '/theme' . $pageThemeFileUrl . '/template';
				$rPaths['data_json'] = 'documents/layout/' . $themeName . '/theme' . $pageThemeFileUrl . '/data_json';
				$rPaths['data-backup'] ='documents/layout/' . $themeName . '/theme' . $pageThemeFileUrl . '/data-backup/' . time();
			}

			foreach( $rPaths as $eachItem => $eachFile )
			{
				//	hardcode the localized  filename
				$rPaths[$eachItem] = Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . $rPaths[$eachItem];
		//	var_export( $rPaths[$eachItem] );
				@Ayoola_Doc::createDirectory( dirname( $rPaths[$eachItem] ) );
			}
			
			//	Let's store this for future reference.
		//	$storagePath = Ayoola_Application::getDomainSettings( APPLICATION_PATH ) . DS . PAGE_PATH . DS . 'data-backup' . $page['url'] . '.backup/' . time();
		//	@Ayoola_Doc::createDirectory( dirname( $storagePath ) );
			if( $previousData = @file_get_contents( $rPaths['data_json'] ) )  
			{
				file_put_contents( $rPaths['data-backup'], $previousData );
			}


			file_put_contents( $rPaths['include'], $content['include'] );
			file_put_contents( $rPaths['template'], $content['template'] );				
		//	file_put_contents( $rPaths['data_php'] , '<?php return ' . var_export( $this->getValues(), true ) . ';' );
		//	$sectionalValues
	//		var_export( $rPaths );
	//		var_export( $values );
	//		var_export( $sectionalValues );
	//		file_put_contents( $rPaths['data_json'] , json_encode( $this->getValues() ) );

			//	save default values if no value is set so we can preload themes.
			file_put_contents( $rPaths['data_json'] , json_encode( $values ? : $sectionalValues ) );
			
			
			//	Sanitize theme pages!
			if( stripos( $page['url'], '/layout/' ) === 0 )     
			{
				$class = new Ayoola_Page_Editor_Sanitize(); 
			//	$class->setParameter( array( 'where' => array( '' ) ) );
				$class->sanitize( $themeName ); 
			}
			
		}
		else //	develop draggable boxes with the saved data
		{
			
		}
		return $this->_layoutRepresentation;
	} 
			
    /**
     * Overall DB operation 
     * @param void
     * @return boolean
     */
    public function getObjectInfo( $object_name )
    {
	//	$rand = md5( json_encode( $randomizationOptions ) );
		if( ! empty( self::$_objectInfo[$object_name] ) )
		{
			return self::$_objectInfo[$object_name];  
		}
		$table = new Ayoola_Object_Table_ViewableObject();
		if( ! self::$_objectInfo[$object_name] = $table->selectOne( null, array( 'object_name' => $object_name ) ) )
		{
			self::$_objectInfo[$object_name] = $table->selectOne( null, array( 'class_name' => $object_name ) );
		}
		//var_export( $data );
		return self::$_objectInfo[$object_name];
	} 
	
    /**
     * Contains the Javascript code as string
     * 
     * @param void
     * @return string
     */
    public function javascript()
    {
		if( ! $page = $this->getPageInfo() )
		{ 
			return false; 
		}
/* 		$sections = implode( ',', $this->getLayoutHash() );
		$portion = implode( '", "', $this->getLayoutHash() );
 */		$sections = trim( $this->hashListForJs, ',' );
		$portion = trim( $this->hashListForJsFunction, ',' );
	//	$portion = '"' . $portion . '"';
	//	var_export( $portion );
	//	var_export( $sections );
	//	var_export( $this->hashListForJs );
	//	var_export( $this->hashListForJsFunction ); 
		$isNotLayoutPage = stripos( $page['url'], '/layout/' ) !== 0;
		if( $isNotLayoutPage )
		{
			//	Always have this here so we can have a template editor link
			$page['layout_name'] = $page['layout_name'] ? : @$_REQUEST['pc_page_editor_layout_name'];
			$page['layout_name'] = $page['layout_name'] ? : self::getDefaultLayout();
			
			//	List URL so it can be easy to change editing URL
			$option = new Ayoola_Page_Page;
			$option = $option->select();
		//	var_export( $option );
			$option = self::sortMultiDimensionalArray( $option, 'url' );
			$optionHTML = ' <span style="display:inline-block;padding: 0 5px 0 5px;"> <select style="display: inline-block;width: initial;width: unset;" onChange="location.href= \\\'?url=\\\' + this.value;">';
			foreach( $option as $eachPage )
			{
				$selected = null;
				if( $eachPage['url'] == $page['url'] )
				{
					$selected = 'selected=selected';
				}
				$optionHTML .= '<option ' . $selected . '>' . $eachPage['url'] . '</option>';
			}
			$optionHTML .= '</select></span>';
		}
		else
		{
			list( , , $page['layout_name'] ) = explode( '/', $page['url'] );
		//	var_export( $page['layout_name'] );
			$optionHTML = ' <span style="display:inline-block;padding: 0 5px 0 5px;"> Theme Editor </span>';
		}
	//	foreach
		
		// Add object from checkbox to selectlist
		$js = '
		ayoola.events.add
		(
			window,
			"load",
			function()
			{
				CreateDragContainer( ' . $portion . ' );
				CreateDragContainer( "viewable_objects" );
		//		ayoola.dragNDrop.makeDraggable( "viewable_objects" );
		//		ayoola.xmlHttp.setAfterStateChangeCallback( ayoola.dragNDrop.init );
			}
		);
			window.onbeforeunload = function()
			{
			//	alert( "Are you sure you want to close this page?" );
			//	confirm( "Are you sure you want to close this page?" );
			}
		var topBarForButtons = document.createElement( "span" );
		topBarForButtons.style.cssText = "width:auto;max-height:100%;overflow:auto;top:0px;left:0px;background-color:#fff;color:#000;position:fixed;padding:0.5em;cursor:move;border:0.1em solid #ccc;z-index:200000;";
	//	topBarForButtons.innerHTML = \'<p style="">Editing "<a target="_blank" href="' . $page['url'] . '" style="">' . $page['url'] . '</a>"</p>\';
	//	topBarForButtons.innerHTML = \'' . $optionHTML . '\';
	//	topBarForButtons.className = "drag, pc-hide-children-parent";
		topBarForButtons.className = "drag";
		topBarForButtons.title = "You can drag this box to anywhere you want on the screen.";
		document.body.appendChild( topBarForButtons );		

		//	Produce a random id for use
		var getRandomId = function()
		{  
			return "' . md5( rand( 1, 100 ) ) . '";
		}
			
		//	Display viewable objects
		var displayViewableObjects = document.createElement( "a" );
		displayViewableObjects.style.cssText = "display:none;";
		displayViewableObjects.href = "javascript:";
		displayViewableObjects.innerHTML = "<button type=\"button\">+</button>";
		displayViewableObjects.title = "Show Viewable Objects";
		topBarForButtons.appendChild( displayViewableObjects );
		
		var showViewableObjects = function( e )
		{  
			hideViewableObjects();
			var randomId = getRandomId();
			var viewableObjects = document.getElementById( randomId );
			if( ! viewableObjects )
			{
				//	Viewable Object list
				var viewableObjects = document.createElement( "span" );
				viewableObjects.style.cssText = "display:none;";
				viewableObjects.className = "drag";
				viewableObjects.id = randomId;		
				topBarForButtons.appendChild( viewableObjects );
				viewableObjects.style.cssText = "";
			}
			viewableObjects.innerHTML = "' . addcslashes( $this->getViewableObjects(), "\"\r\n" ) . '";		
			CreateDragContainer( "viewable_objects" );
			viewableObjects.style.display = "";
		}
		ayoola.events.add( displayViewableObjects, "click", showViewableObjects );
		
		
		//	Hide viewable objects
		var hideViewableObject = document.createElement( "a" );
		hideViewableObject.style.cssText = "display:none;";
		hideViewableObject.href = "javascript:";
		hideViewableObject.innerHTML = "<button type=\"button\"> x </button>";
		hideViewableObject.title = "Hide Viewable Objects";
		topBarForButtons.appendChild( hideViewableObject );

		var hideViewableObjects = function()
		{  
			var randomId = getRandomId();
			var viewableObjects = document.getElementById( randomId );
			
			//	Delete previous, if available
			if( viewableObjects )
			{
				viewableObjects.style.display = "none";
			//	if( viewableObjects ){ viewableObjects.parentNode.removeChild( viewableObjects ); }
			}
		}
		ayoola.events.add( hideViewableObject, "click", hideViewableObjects );  
		
		//	lets view the object so the sectional inserters can work.
		showViewableObjects();
		
		//	Hide again
		hideViewableObjects();
		
		//	Loops through layout sections
		var sectionList = "' . $sections . '";
		var sections = sectionList.split( "," );
		var addANewItemToContainer = function( e )
		{
		//	var target = addItem;
			
			var target = ayoola.events.getTarget( e, "addItemButton" );
	//		var target = e.target || e.srcElement;
			var select = document.createElement( "select" );
			select.innerHTML = "<option>Please select an object</option>' . $this->_viewableSelect . '";
			ayoola.events.add
			( 
				select, 
				"change", 
				function()
				{ 
					var a = document.getElementById( select.value );
					var b = select.parentNode;
				//	alert( select.value );
				//	alert( a );
				//	alert( b );
					//	Clone the node to replenish the main viewable objects.
					if( a && b && b.parentNode )
					{
						c = a.cloneNode( true );
						c.id = "";
/* 						
						//	add delete button
						var deleteButton = ayoola.div.getDelete( c, deleteButton );
						deleteButton.title = "Delete Object";
						deleteButton.innerHTML = " x ";
						c.appendChild( deleteButton );
 */						b.parentNode.appendChild( c );     
					}
				//	target.innerHTML = "Add another object";
					ayoola.events.add( target, "click", addANewItemToContainer ); 
					select.parentNode.appendChild( target );
					select.parentNode.removeChild( select );
				} 
			); 
		//	target.innerHTML = "";
			target.parentNode.appendChild( select );
			ayoola.events.remove( target, "click", addANewItemToContainer ); 
			target.parentNode.removeChild( target );
		}
		for( var a = 0; a < sections.length; a++ )
		{  
			var sectionName = sections[a];
			var section = document.getElementById( sectionName ); // e.g. header
			if( ! section ){ continue; }
			
			//	ADDING A LINK TO ADD A NEW OBECT TO THE SECTION
			//
			var addItemContainer = document.createElement( "div" );
			addItemContainer.style.cssText = "text-align:center;";  
			addItemContainer.title = "Click here to select an object to insert into this container";
		//	addItemContainer.innerHTML = "<a href=\"javascript:\">Add an object</a>";
			addItemContainer.name = "add_a_new_item_to_parent_section";
	//		ayoola.events.add( addItemContainer, "click", addANewItemToContainer ); 
	
			var addItemButton = document.createElement( "span" );
		//	addItemButton.className = "addItemButton greynews boxednews centerednews";
			addItemButton.className = "addItemButton greynews boxednews centerednews pc-btn pc-btn-small";
		//	addItemContainer.className = "";
			addItemButton.title = "Click here to select an object to insert into this container";
			addItemButton.innerHTML = "Add Object Here";
	//		addItemButton.name = "add_a_new_item_to_parent_section";
			ayoola.events.add( addItemButton, "click", addANewItemToContainer ); 
			addItemContainer.appendChild( addItemButton );  
			section.appendChild( addItemContainer );
		
		}  
		
		//	button to save the layout
		var saveButton = document.createElement( "a" );
		saveButton.style.cssText = "";
		saveButton.href = "javascript:";
		saveButton.title = "Save the layout template for this page.";
		saveButton.className = "pc-btn pc-btn-small pc-bg-color";  
		saveButton.innerHTML = "Save";  
		topBarForButtons.appendChild( saveButton );
		
		var functionToSaveTemplate = function()
		{  
			//	Autoclose HTML editor  
			if ( ayoola.div.wysiwygEditor )
			{
			//	ayoola.div.wysiwygEditor.destroy();
			}
		//	for( name in CKEDITOR.instances )
			{
			//	CKEDITOR.instances[name].destroy();
			} 
			var addParameterOptions = function( x )
			{
				var p = "";
				var q = Array();
				for( var c = 0; c < x.childNodes.length; c++ ) 
				{
					var parameterOrOption = x.childNodes[c];
					if( ! parameterOrOption || parameterOrOption.nodeName == "#text" ){ continue; }
					if( ! parameterOrOption.dataset || ! parameterOrOption.dataset.parameter_name )
					{ 
						continue; 
					}
					if( parameterOrOption.dataset.parameter_name == "parent"  )
					{
						var g = addParameterOptions( parameterOrOption );
						if( g.content ) 
						{
							p += g.content;
						}
						if( g.list ) 
						{
							q = q.concat( g.list );
						}
					//	alert( parameterOrOption );
						continue;
					}
					var parameterName = parameterOrOption.dataset.parameter_name;
					p += "&" + numberedSectionName + parameterName + "=";
				//	alert( parameterOrOption.outerHTML );
					var pattern = /\(/ig;
					var pattern = "x-x-x-xXXXxx";
					if( parameterOrOption.value != undefined )
					{ 
						//	encode so that & in links wont be affected.
						p += encodeURIComponent( parameterOrOption.value ).replace( pattern, "PC_SAFE_ITEMS_OPENING_BRACKET" ); 
					}
					else if( parameterOrOption.tagName.toLowerCase() == "form" )
					{ 
					//	alert( ayoola.div.getFormValues( { form: parameterOrOption, dontDisable: true } ) );
						p += encodeURIComponent( ayoola.div.getFormValues( { form: parameterOrOption, dontDisable: true } ) ).replace( pattern, "PC_SAFE_ITEMS_OPENING_BRACKET" ); 
					}
					else
					{ 
						p += encodeURIComponent( parameterOrOption.innerHTML ).replace( pattern, "PC_SAFE_ITEMS_OPENING_BRACKET" ); 
					}
					q.push( parameterName );
				}
			//	alert( q );
			//	alert( p );
				return { content: p, list: q };
			}
			var url = location.href;
			var postContent = "";
			var sectionListForPreservation = "";
			for( var a = 0; a < sections.length; a++ )
			{  
				var z = 0; // Use this for real concurrent numbering.
				var sectionName = sections[a];
				var section = document.getElementById( sectionName ); // e.g. header
				if( ! section ){ continue; }
			//	alert( section.id );
				
				//	Construct the URL for POSTing
				//	Loops through objects
				var sectionHasContent = false;
				for( var b = 0; b < section.childNodes.length; b++ )
				{
					
					var object = section.childNodes[b];
		
					if( ! object || object.nodeName == "#text" ){ continue; }
					if( ! object.dataset || ! object.dataset.object_name ){ continue; }
					var objectName = object.dataset.object_name;
				//	if( ! ayoola.dragNDrop.isDraggable( object ) ){ continue; }
					var numberedSectionName = sectionName + String( z ); // e.g. header2
					z++; //	Count the real concurrent numbering system.
					postContent = postContent ? ( postContent + "&" ) : "";
				//	alert( object );
					postContent +=  numberedSectionName + "=" + objectName;
				//	alert( objectName );
			//		alert( object.getAttribute( "data-object_name" ) );

					// Add View parameters and options
					//	Loops through parameters
					var parameterList = Array();
					var h = addParameterOptions( object );
					
					//	Add for interior Separately because of wrappers blocking it.
					var k = addParameterOptions( object.getElementsByClassName( "object_interior" )[0] );
				//	alert( object.getElementsByClassName( "object_interior" )[0] );
					if( k.content ) 
					{
						h.content += k.content;
					}
					if( k.list ) 
					{
						h.list = h.list.concat( k.list );
					}
			//		alert( h.content );
			//		alert( h.list );  
					
					if( h.content ) 
					{
					//	alert( h.content );
						postContent += h.content;
					}
					if( h.list ) 
					{
					//	alert( h.list );
						parameterList = parameterList.concat( h.list );
					}
					
					parameterList = encodeURIComponent( parameterList.join( "," ) );
					postContent += "&" + numberedSectionName + "_parameters=" + parameterList;
					sectionHasContent = true;
				}
				sectionListForPreservation += sectionHasContent ? ( sectionName + "," ) : "";
			
			}
			postContent = postContent ? postContent : "a=b";
			
			//	need to save section list to preserve contents
			postContent = postContent + "&section_list=" + sectionListForPreservation;
		//	alert( postContent );
		//	return false;
			var uniqueNameForAjax = "' . __CLASS__ . rand( 0, 500 ) . '";
			ayoola.xmlHttp.fetchLink( url, uniqueNameForAjax, postContent );
			
			//	Set a splash screen to indicate that we are loading.
			var splash = ayoola.spotLight.splashScreen();
			
		//	alert( arguments.length );
			var ajax = ayoola.xmlHttp.objects[uniqueNameForAjax];
			//	alert( ayoola.xmlHttp.isReady( ajax ) );	
			var ajaxCallback = function()
			{
			//	alert( ajax );
			//	alert( ajax.readyState ); 
			//	alert( ajax.status ); 
				if( ayoola.xmlHttp.isReady( ajax ) )
				{ 
				//	alert( ajax.responseText ); 
				
					// Close splash screen
					splash.close();
				//	alert( "Page Saved." ); 
					
				} 
			}
			ayoola.events.add( ajax, "readystatechange", ajaxCallback );
		}
	//	alert( functionToSaveTemplate );
		ayoola.events.add( saveButton, "click", functionToSaveTemplate );
		
		
		
		//	button to reload page
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.href = "";
		a.title = "Click here to reload the page editor.";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Reload";  
	//	topBarForButtons.appendChild( a );
		' 
		. 
		( $isNotLayoutPage ? 
		'
		//	button to preview page
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.title = "Click here to preview the LIVE version on this page.";
		a.href = "javascript:";
	//	a.target = "_blank";
	//	a.rel = "spotlight";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Preview";  
		topBarForButtons.appendChild( a );		
		ayoola.events.add( a, "click", function(){ ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '' . $page['url'] . '' . ( $page['layout_name'] == self::getDefaultLayout() ? null : ( '?pc_page_layout_name=' . $page['layout_name'] ) ) . '\' ); } );
'
.
		( 
			empty( $_REQUEST["pc_page_editor_layout_name"] ) 

			? 

			null 

			: 

'			
		//	button to load layout defaults HTML
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.href = "javascript:window.location.search = window.location.search + \'&pc_load_theme_defaults=1\'";
		a.onclick = "";
		a.title = "Load default HTML Content to this theme";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Load Default";  
		topBarForButtons.appendChild( a );
'			
		)
.

'

		//	button to edit page info
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.href = "javascript:";
		a.title = "Edit page title, description and other information.";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Options";  
	//	topBarForButtons.appendChild( a );
		ayoola.events.add( a, "click", function(){ ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Editor/?url=' . $page['url'] . '\' ); } );

		//	button to delete page
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.href = "javascript:";
		a.title = "Delete page";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Delete";  
//		topBarForButtons.appendChild( a );
		ayoola.events.add( a, "click", function(){ ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Delete/?url=' . $page['url'] . '\' ); } );

		//	button to edit template
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.href = "javascript:";
//		a.href = "?url=/layout/' . strtolower( $page['layout_name'] ) . '/template";
//		a.target = "_blank";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Theme";  
		topBarForButtons.appendChild( a );
		ayoola.events.add( a, "click", function(){ ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Ayoola_Page_Editor_Layout/?url=/layout/' . strtolower( $page['layout_name'] ) . '/template\' ); } );

	//	alert( "hello" );
		'  
		:
		
		
		'
		//	button to preview page
		var a = document.createElement( "a" );
		a.style.cssText = "";
		a.title = "Click here to preview the LIVE version on this theme.";
		a.href = "javascript:";
	//	a.target = "_blank";
	//	a.rel = "spotlight";
		a.className = "pc-hide-children-children pc-btn pc-btn-small";  
		a.innerHTML = "Preview";  
		topBarForButtons.appendChild( a );		
		ayoola.events.add( a, "click", function(){ ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/object/name/Ayoola_Page_Layout_Preview/?layout_name=' . $page['layout_name'] . '\' ); } );
		
		//	button to load layout defaults HTML
		var loadButton = document.createElement( "a" );
		loadButton.style.cssText = "";
		loadButton.href = "javascript:window.location.search = window.location.search + \'&pc_load_theme_defaults=1\'";
		loadButton.onclick = "";
		loadButton.title = "Load default HTML Content to this theme";
		loadButton.className = "pc-hide-children-children pc-btn pc-btn-small";  
		loadButton.innerHTML = "Load Default";  
		topBarForButtons.appendChild( loadButton );

		'		
		
		)
		
		.
		
		'		
		//	Add options bar
		var optionbar = document.createElement( "span" );
		optionbar.innerHTML = \' ' . $optionHTML . '\';
		optionbar.className = "pc-hide-children-children";
		optionbar.title = "Select page to edit";
		topBarForButtons.appendChild( optionbar );
		
		//	Add show more options button
		var optionbar = document.createElement( "span" );
		optionbar.innerHTML = \'&raquo;&raquo;\';
		optionbar.className = " pc-btn pc-btn-small";
		optionbar.title = "Show or hide more options";
		optionbar.onclick = function()
		{
			var a = this.parentNode.getElementsByClassName( "pc-hide-children-children" );
			for( var b = 0; b < a.length; b++ )  
			{
				switch( a[b].style.display )
				{
					case "inline-block":
						a[b].style.display = "";
						this.innerHTML = \'&raquo;&raquo;\';      
					break;
					default:
						a[b].style.display = "inline-block";
						this.innerHTML = \'&laquo;&laquo;\';
					break;
				}
			}
		};
		topBarForButtons.appendChild( optionbar );
		';
		;
	//	$script = "\n<script src='/js/objects/dragNDrop.js'>\n</script>\n<script>\n{$js}\n</script>\n";
		return $js;
	} 

    /**
     * Produce the mark-up for each draggable object
     *
     * @param string | array viewableObject Information
     * @return string Mark-Up to Display Viewable Objects
     */
    protected function getViewableObject( $object )
    {
		// We can accept object name too
		$object =  is_string( $object ) ? $this->getObjectInfo( $object ) : $object;
		//	echo __LINE__;
	//	var_export( $object );
		//	exit();
		
		$html = null;
		
		//	We may send whatever info we want to be overwritten in array.
		$defaultObjectInfo = $this->getObjectInfo( $object['object_name'] );
	//	var_export( $defaultObjectInfo );
		$object = array_merge( $defaultObjectInfo, $object );
		
				
		//	Retrieving object "interior" from the object class
		$getHTMLForLayoutEditor = 'getViewableObjectRepresentation';
		if( method_exists( $object['class_name'], $getHTMLForLayoutEditor ) )
		{
			$html .= $object['class_name']::$getHTMLForLayoutEditor( $object ); 
		}
//		$html .= "<div onmouseout='this.removeChild( this.lastChild );' onmouseover='this.appendChild( ayoola.div.getDelete( this ) );'>0</div>";
		return $html;
    }

    /**
     * returns viewable object property
     *
     * @param void
     * @return string Mark-Up to Display Viewable Objects List
     */
    protected function getViewableObjects()
    {	
		if( null === $this->_viewableObjects )
		{
			$this->setViewableObjects();
		}
	//	var_export( $this->_viewableObjects );
		return $this->_viewableObjects;
    }

    /**
     * Builds viewable object property
     *
     * @param void
     * @return string Mark-Up to Display Viewable Objects List
     */
    protected function setViewableObjects()
    {	
		// Bring the objects from db
		$table = new Ayoola_Object_Table_ViewableObject();
		if( ! $objects = (array) $table->select() )
		{
		//	var_export( $objects );
			return false;
		}
/* 		// Bring the PageLayout from db
		require_once 'Ayoola/Dbase/Table/PageLayout.php';
		$modules = new Ayoola_Dbase_Table_Module;
		$modules = $modules->select();
		require_once 'Ayoola/Filter/SelectListArray.php';
		$filter = new Ayoola_Filter_SelectListArray( 'module_id', 'name');
		$modules = $filter->filter( $modules );
 */		//var_export( $modules );
		$html = "<div id='viewable_objects'>";
	//	$select = "<select>";
		$this->_viewableSelect = null;
		//	var_export( $object );
/* 		if( ! $object['object_name'] )
		{
			var_export( $object );
		}
 */		
		$objects = self::sortMultiDimensionalArray( $objects, 'view_parameters' );
	//	var_export( $objects );
		foreach( $objects as $object )
		{
			//$xml = new Ayoola_Xml();			
			//$moduleName = $modules[$object['module_id']];
			$object['object_unique_id'] = 'object_unique_id_' . md5( $object['object_name'] );
			$html .= $this->getViewableObject( $object );
			$this->_viewableSelect .= "<option value='{$object['object_unique_id']}'>" . htmlspecialchars( $object['view_parameters'] ) . "</option>";
			
		}
				//	var_export( $objects );
		//	echo __LINE__;
		//	exit();

		unset( $objects ); // Free memory
		$html .= "</div>";
		return $this->_viewableObjects = $html;
    }
		
    /**
     * This method saves the layout into the page data file
     *
     * @param 
     * @return 
     */
    public function saveXml()
    {
		if( ! $paths = $this->getPagePaths() ){ return false; }
		if( ! $_POST ){ return false; }
//		$values = $this->getPageInfo();
		$values['pageLayout'] = json_encode( $this->getValues() ); 
		
	//	echo json_decode( $values['pageLayout'], true );
	//	var_export( json_decode( $values['pageLayout'], true ) );
		
		// Retrieve the previous layout data from the page data file
		require_once 'Ayoola/Xml.php';
		$xml = new Ayoola_Xml();
	//	$xml->load( $paths['data'] );
		require_once 'Ayoola/Page.php';
		$default = Ayoola_Page::getDefaultPageFiles();
	//		var_export( $default );
		$xml->load( $default['data'] );
		$xml->arrayAsCData( $values );
		$xml->save( $paths['data'] );
	} 
	// END OF CLASS
}