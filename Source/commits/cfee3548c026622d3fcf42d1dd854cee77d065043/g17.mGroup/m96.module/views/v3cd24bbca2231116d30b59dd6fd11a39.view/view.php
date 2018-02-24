<?php
//#section#[header]
// Module Declaration
$moduleID = 96;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Content");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \UI\Html\HTMLModulePage;
use \API\Content\literals\moduleLiteral;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\notification;

//__________ Use __________//
// Get Return_url
$return_url = $_GET['return'];
// Create Module Page
$HTMLModulePage = new HTMLModulePage("simpleOneColumnCenter");
$pageTitle = moduleLiteral::get($moduleID, "pageTitle");
// Build the module
$HTMLModulePage->build($pageTitle);


/////////////////////
//Top Most Section //
/////////////////////
//Topmost Text
$topmostText = DOM::create('div', "");
layoutContainer::margin($topmostText, "b", "m");
$HTMLModulePage->appendToSection("mainContent", $topmostText );
//#header
$headerWrapper = DOM::create('h1','', '', 'ebulderTitleSet_2');
DOM::append($topmostText, $headerWrapper );
$header= moduleLiteral::get($moduleID, "ebuilderHeader");
DOM::append($headerWrapper , $header);
//#general prompt
$generalPromptWrapper= DOM::create('div', "");
/*
DOM::append($topmostText, $generalPromptWrapper);
$generalPrompt = moduleLiteral::get($moduleID, "generalPrompt");
*/
DOM::append($generalPromptWrapper, $generalPrompt);

// Temporary disclaimer for service unavailability
$disclaimer = DOM::create('div', '', '', 'disclaimer');
	$notification = new notification();
	$notification->build($type = "warning", $header = FALSE, $footer = TRUE);
	
	$message = $notification->getMessage("warning", "wrn.content_uc");
	$message = moduleLiteral::get($moduleID, "alphaWarning", FALSE);
	//$notification->append($message);
	$notification->appendCustomMessage($message);
	
	$disclaimerNotification = $notification->get();
	DOM::append($disclaimer, $disclaimerNotification);
$HTMLModulePage->appendToSection("mainContent", $disclaimer);


$mainContent = DOM::create('div', '', '', 'mainContentPagePool');
$HTMLModulePage->appendToSection("mainContent", $mainContent);


$contentPage = DOM::create('div', '','newProject', 'contentPage');
DOM::append($mainContent, $contentPage);

$sectionHeaderWrapper = DOM::create('h2','', '', 'ebulderTitleSet_2 header');
DOM::append($sectionHeaderWrapper, moduleLiteral::get($moduleID, "actSet_ebldDevsHeader"));
DOM::append($contentPage, $sectionHeaderWrapper);
	
	$tilePool = DOM::create('div', '', '', 'tilePool');
	DOM::append($contentPage, $tilePool);
	
	// Build Action Tile
	$tileWrapper = DOM::create('div', '', '', 'tileWrapper');
	DOM::append($tilePool, $tileWrapper);	
	$tileContent = DOM::create('div', '', '', 'tileContent');
	DOM::append($tileWrapper, $tileContent);	
	$leftSideWrapper = DOM::create('div', '', '', 'leftSideWrapper aCenter');
		$content = DOM::create('div', '', '', 'content aCenter');
		DOM::append($leftSideWrapper, $content);
		//######Create Image Wrapper
		$imageWrapper = DOM::create('div','', '', 'imageWrapper');
		layoutContainer::floatPosition($imageWrapper, "left");
		DOM::append($content, $imageWrapper);
		$image = DOM::create("img");
		DOM::attr($image, "src", "/Library/Media/images/pages/ebuilder/actionIco90x90_def.png");
		DOM::append($imageWrapper, $image);
		//#######Create Title Text
		$titleWrapper = DOM::create('div','', '', 'title');
		DOM::append($titleWrapper, moduleLiteral::get($moduleID, "templateCreator_hd"));	
		DOM::append($content, $titleWrapper);	
	DOM::append($tileContent, $leftSideWrapper);
	
	$rightSideWrapper = DOM::create('div', '', '', 'rightSideWrapper');
		//Hd
		$headweWrapper = DOM::create('h4','', '', 'ebulderTitleSet_2 underBorder');
		DOM::append($headweWrapper, moduleLiteral::get($moduleID, "templateCreator_hd"));
		DOM::append($rightSideWrapper, $headweWrapper);		
		//#######Create Content
		$textWrapper = DOM::create('div','', '', 'content');
		DOM::append($textWrapper, moduleLiteral::get($moduleID, "templateCreator_content"));	
		DOM::append($rightSideWrapper, $textWrapper);		
	DOM::append($tileContent, $rightSideWrapper);
	
	$tileControlBar = DOM::create('div', '', '', 'tileControlBar');
		//#######Create Link
		/*
		$link = DOM::create('div','', '', 'link');
		DOM::append($link, moduleLiteral::get($moduleID, "goToBuilder"));	
		DOM::append($tileControlBar, $link);
		*/
	DOM::append($tileWrapper, $tileControlBar);
	
	// Build Action Tile
	$tileWrapper = DOM::create('div', '', '', 'tileWrapper');
	DOM::append($tilePool, $tileWrapper);	
	$tileContent = DOM::create('div', '', '', 'tileContent');
	DOM::append($tileWrapper, $tileContent);	
	$leftSideWrapper = DOM::create('div', '', '', 'leftSideWrapper aCenter');
		$content = DOM::create('div', '', '', 'content aCenter');
		DOM::append($leftSideWrapper, $content);
		//######Create Image Wrapper
		$imageWrapper = DOM::create('div','', '', 'imageWrapper');
		layoutContainer::floatPosition($imageWrapper, "left");
		DOM::append($content, $imageWrapper);
		$image = DOM::create("img");
		DOM::attr($image, "src", "/Library/Media/images/pages/ebuilder/actionIco90x90_def.png");
		DOM::append($imageWrapper, $image);
		//#######Create Title Text
		$titleWrapper = DOM::create('div','', '', 'title');
		DOM::append($titleWrapper, moduleLiteral::get($moduleID, "extensionCreator_hd"));	
		DOM::append($leftSideWrapper, $titleWrapper);
	DOM::append($tileContent, $leftSideWrapper);
	
	$rightSideWrapper = DOM::create('div', '', '', 'rightSideWrapper');
		//Hd
		$headweWrapper = DOM::create('h4','', '', 'ebulderTitleSet_2 underBorder');
		DOM::append($headweWrapper, moduleLiteral::get($moduleID, "extensionCreator_hd"));
		DOM::append($rightSideWrapper, $headweWrapper);		
		//#######Create Content
		$textWrapper = DOM::create('div','', '', 'content');
		DOM::append($textWrapper, moduleLiteral::get($moduleID, "extensionCreator_content"));	
		DOM::append($rightSideWrapper, $textWrapper);			
	DOM::append($tileContent, $rightSideWrapper);
	
	$tileControlBar = DOM::create('div', '', '', 'tileControlBar');
		//#######Create Link
		/*
		$link = DOM::create('div','', '', 'link');
		DOM::append($link, moduleLiteral::get($moduleID, "goToBuilder"));	
		DOM::append($tileControlBar, $link);	
		*/
	DOM::append($tileWrapper, $tileControlBar);
	
	
	
	

////////////////
// report
// Return output
return $HTMLModulePage->getReport();


function insertMenuItem($parent, $content)
{
	$menuBarLi1 = DOM::create('li');
	DOM::append($parent, $menuBarLi1);
	$borderDiv1 = DOM::create('div', '','', 'border');
	DOM::append($menuBarLi1, $borderDiv1 );
	
	$menuItem_actions = DOM::create('div', '');
	DOM::append($borderDiv1 , $menuItem_actions);

	DOM::append($menuItem_actions, $content);
	
	return $menuItem_actions;
}
//#section_end#
?>