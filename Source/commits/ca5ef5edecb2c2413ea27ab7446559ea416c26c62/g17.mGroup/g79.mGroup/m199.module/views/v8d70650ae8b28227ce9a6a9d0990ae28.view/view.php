<?php
//#section#[header]
// Module Declaration
$moduleID = 199;

// Inner Module Codes
$innerModules = array();

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
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\wsManager;
use \API\Developer\ebuilder\templateManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\storage\session;
use \DEV\Projects\projectCategory;
use \UI\Html\HTMLContent;
use \UI\Presentation\gridSplitter;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;


// Create Module
$HTMLContent = new HTMLContent();
$actionFactory = $HTMLContent->getActionFactory();

// Return Success
$HTMLContent->addReportAction('step.success');	

$splitter = new gridSplitter();
$splitter->build($orientation = "horizontal", $layout = gridSplitter::SIDE_LEFT, $closed = FALSE, "SideBar");
$HTMLContent->buildElement($splitter->get());

// Side Bar Menu
$categories = projectCategory::getCategories(templateManager::PROJECT_TYPE);
$sidebar  = DOM::create("div", "", "", "sidebar");
// Search bar

// The All element
$all = DOM::create("div", "All", "", "");
DOM::append($sidebar, $all);

// Categories
if(!empty($categories))
{
	foreach($categories as $key => $value)
	{
		$category = DOM::create("div", $key, "", "");
		DOM::append($sidebar, $category);
	}
}
else
{
	$emptyList = DOM::create("div", "Nothing", "", "");
	DOM::append($sidebar, $emptyList);
}
	
$splitter->appendToSide($sidebar);

// Main Content
$mainContent = DOM::create("div", "", "", "mainContent");
// Top Bar
$topBar = DOM::create("div", "", "", "");
DOM::append($mainContent, $topBar);

// Templates well
$well = DOM::create("div", "", "", "well");
DOM::append($mainContent, $well);

$templates = array();
foreach($templates as $template)
{
	$templateTile = DOM::create("div", "", "", "");
	DOM::append($well, $templateTile);
	$sForm = new simpleForm();
	$sForm->build($moduleID, "wizardBroker", FALSE);
	DOM::append($well, $sForm->get);
	
	// [Hidden] - step
	$input = $sForm->getInput($type = "hidden", $name = "step", $value = "3");
	$sForm->append($input);
	
	// Create form controls
	$formControls = DOM::create("div", "", "", "formControls");
	DOM::append($form, $formControls);
	$row = $sForm->getRow();
	DOM::append($formControls, $row);
	// Preview Button
	$btn = $sForm->getResetButton(moduleLiteral::get($moduleID, "btn_templatePreview"));
	DOM::append($row, $btn);
	// Add Button
	$btn = $sForm->getSubmitButton(moduleLiteral::get($moduleID, "btn_templateAdd"));
	DOM::append($row, $btn);
	
	
	
	
}
$splitter->appendToMain($mainContent);


// Return output
return $HTMLContent->getReport();
//#section_end#
?>