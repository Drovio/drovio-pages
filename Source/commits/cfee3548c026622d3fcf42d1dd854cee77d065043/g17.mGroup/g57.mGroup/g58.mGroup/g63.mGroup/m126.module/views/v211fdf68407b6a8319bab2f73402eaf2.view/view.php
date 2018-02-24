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
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

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
use \API\Developer\ebuilder\template; 
use \UI\Forms\formControls\formItem;
use \UI\Html\HTMLContent;
use \UI\Presentation\frames\windowFrame;
use \UI\Presentation\layoutContainer;
use \UI\Presentation\tabControl;
use \UI\Presentation\togglers\expander;


$templateID = $_GET['tplID'];

// Load Template
$template = new template();
$template->load($templateID);

// Array of common module load variable
$derAttr = array();
$derAttr['templateId'] = $templateID;

// Create Module Page
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();

$globalContainer = DOM::create('div', '', '', 'sibarContentWrapper');
$HTMLContent->buildElement($globalContainer );




	
	
$topDownContent = DOM::create('div', '', '', 'sideTosideSpace topDownContent');
	
	$controlBar = DOM::create('div', '', '', 'refreshControl');	
		$content = DOM::create('span', ' ');
		DOM::append($controlBar, $content);
	DOM::append($topDownContent, $controlBar);
	
	
	// #Page Structure Overview Item
	$container = DOM::create('div','','pageStructureOverview', 'overviewSection');
	DOM::append($topDownContent, $container); 
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
	$pageStructureArray = $template->getAllStructures();
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
	DOM::append($topDownContent, $container); 
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
	
	$themesArray = $template->getAllThemes();
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
	
DOM::append($globalContainer, $topDownContent);	
	
$bottomUpContent = DOM::create('div', '', '', 'sideTosideSpace bottomUpContent ');

	$expander = new expander();

	// #Templaate info
	$container = DOM::create('div','','templateInfoOverview', 'templateInfo expanded');
	//DOM::append($bottomUpContent, $container); 
	$headerWrapper = DOM::create('div', "", "", "headerBar");
	DOM::append($container, $headerWrapper);
		$headerContent = DOM::create('h2', "", "", "headerContent");
		DOM::append($headerWrapper, $headerContent);
		$headerText = DOM::create('span', "Tamplate Info");//moduleLiteral::get($moduleID, "lbl_templateTitle");
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
	
	$miniature = DOM::create('div', '', '', 'templateInfo miniature');
	$content = DOM::create('span', 'Template Info');
	DOM::append($miniature , $content );
	
	$expander->build($miniature, $container, FALSE);
	DOM::append($bottomUpContent, $expander->get());
	
DOM::append($globalContainer, $bottomUpContent);	

// Return output
return $HTMLContent->getReport();
//#section_end#
?>