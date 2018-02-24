<?php
//#section#[header]
// Module Declaration
$moduleID = 126;

// Inner Module Codes
$innerModules = array();
$innerModules['templateObject'] = 117;
$innerModules['templateViewer'] = 131;

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Developer");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\templateManager; 
use \UI\Forms\formControls\formItem;
use \UI\Html\HTMLContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\tabControl;


$templateID = $_GET['tplID'];

// Try to load Object
$templateManager = new templateManager();
$templateObject = $templateManager->getTemplate($templateID);


// Array of common module load variable
$derAttr = array();
$derAttr['templateId'] = $templateID;

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();

$tabControl = new tabControl();
$tabControl->build($id = "", FALSE);
$HTMLContent->buildElement($tabControl->get());


// Template Contents
$selected = TRUE;
$id = "tplContents";
	$tabContent = DOM::create('div', '', '', 'sideTosideSpace');
	
	// #Page Structure Overview Item
	$container = DOM::create('div','','pageStructureOverview', 'overviewSection');
	DOM::append($tabContent, $container); 
	$headerWrapper = DOM::create('div', "", "", "headerBar");
	DOM::append($container, $headerWrapper);
		$headerContent = DOM::create('h2', "", "", "headerContent");
		DOM::append($headerWrapper, $headerContent);
		$headerText = DOM::create('span', "Page Structure");
		DOM::append($headerContent, $headerText);
	$headerControls = DOM::create('div', '', '', 'headerControls');
	DOM::append($headerWrapper, $headerControls);
	
	$formItem = new formItem();
	$formItem->build("button", $name, $id."_addPs", "", "uiFormButton".($positive ? " positive" : ""));
	$button = $formItem->get();
	$addNewControl  = DOM::create('span', "[+] Add New");
	DOM::append($button, $addNewControl  );
	
	windowFrame::setAction($button , $innerModules['templateObject'], 'newPageStructure', array('templateId' => $templateID));
	DOM::append($headerControls, $button );
	//$addNewControl  = DOM::create('span', "Add New");
	//windowFrame::setAction($addNewControl, $innerModules['templateObject'], 'newPageStructure', array('templateId' => $templateID));
	
	//DOM::append($itemHeaderControls, $addNewControl);
	$pageStructureArray = $templateObject->getAllStructures();
	$scroll = DOM::create('div', '', '', "scroll");
	
	$itemContent = DOM::create('div', '', 'psBodyContent', "bodyContent");
	if(empty($pageStructureArray))
	{
		$msg = DOM::create('span', 'Nothing to Display');			
		DOM::append($itemContent, $msg);
	}
	else
	{
		$attr = $derAttr;
		foreach($pageStructureArray as $pageStructureObject)
		{	
			$attr['objectName'] = $pageStructureObject;
			$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "psItemSnippet", $attr, TRUE);
			DOM::append($itemContent, $pageStructureItem);	
		}
	}
	DOM::append($scroll , $itemContent);
	DOM::append($container, $scroll );
	
	// #Themes Overview Item
	$container = DOM::create('div','','themesOverview', 'overviewSection');
	DOM::append($tabContent, $container); 
	$headerWrapper = DOM::create('div', "", "", "headerBar");
	DOM::append($container, $headerWrapper);
		$headerContent = DOM::create('h2', "", "", "headerContent");
		DOM::append($headerWrapper, $headerContent);
		$headerText = DOM::create('span', "Themes");
		DOM::append($headerContent, $headerText);
	$headerControls = DOM::create('div', '', '', 'headerControls');
	DOM::append($headerWrapper, $headerControls);
	
	$formItem = new formItem();
	$formItem->build("button", $name, $id."_addTh", "", "uiFormButton".($positive ? " positive" : ""));
	$button = $formItem->get();
	$addNewControl  = DOM::create('span', "[+] Add New");
	DOM::append($button, $addNewControl);
	
	windowFrame::setAction($button , $innerModules['templateObject'], 'newTheme', array('templateId' => $templateID));
	DOM::append($headerControls, $button);
	
	$themesArray = $templateObject->getAllThemes();
	$itemContent  = DOM::create('div', '', 'thBodyContent', "bodyContent");
	if(empty($themesArray))
	{
		$msg = DOM::create('span', 'Nothing to Display');			
		DOM::append($itemContent, $msg);
	}
	else
	{
		$attr = $derAttr;
		foreach($themesArray as $themeObject)
		{	
			$attr['objectName'] = $themeObject;
			$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "thItemSnippet", $attr, TRUE);
			DOM::append($itemContent, $pageStructureItem);	
		}
	}
	DOM::append($container, $itemContent);
	
$header = DOM::create('span', 'Template Contents');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);
	
// Template Info
$selected = FALSE;
$id = "tplInfo";
	$tabContent = DOM::create('div', '', '', 'sideTosideSpace');
 
	// #Templaate info
	$container = DOM::create('div','','templateInfoOverview', 'templateInfo  overviewSection');
	DOM::append($tabContent, $container); 
	$headerWrapper = DOM::create('div', "", "", "headerBar");
	DOM::append($container, $headerWrapper);
		$headerContent = DOM::create('h2', "", "", "headerContent");
		DOM::append($headerWrapper, $headerContent);
		$headerText = DOM::create('span', "Template Info");
		DOM::append($headerContent, $headerText);
	$headerControls = DOM::create('div', '', '', 'headerControls');
	DOM::append($headerWrapper, $headerControls);
	
	$formItem = new formItem();
	$formItem->build("button", $name, $id."_addTh", "", "uiFormButton".($positive ? " positive" : ""));
	$button = $formItem->get();
	$addNewControl  = DOM::create('span', "[/] Edit");
	DOM::append($button, $addNewControl);
	
	//windowFrame::setAction($button , $innerModules['templateObject'], 'newTheme', array('templateId' => $templateID));
	DOM::append($headerControls, $button);	
	
	$itemContent = DOM::create('div', '', '', "bodyContent");
	$pageStructureItem = $HTMLContent->getModuleContainer($innerModules['templateViewer'], "templateInfo", $attr, TRUE);
	DOM::append($itemContent, $pageStructureItem);
	DOM::append($container, $itemContent);
	
$header = DOM::create('span', 'Template Info');//moduleLiteral::get($moduleID, "lbl_literalManager");
$tabControl->insertTab($id, $header, $tabContent, $selected);

// Return output
return $HTMLContent->getReport();
//#section_end#
?>