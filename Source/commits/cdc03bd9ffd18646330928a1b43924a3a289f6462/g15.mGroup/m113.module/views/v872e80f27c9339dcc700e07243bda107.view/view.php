<?php
//#section#[header]
// Module Declaration
$moduleID = 113;

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
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\profiler\tester;
use \API\Developer\profiler\activityLogger;
use \API\Profile\person;
use \API\Resources\archive\zipManager;
use \API\Resources\filesystem\directory;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Security\account;
use \UI\Html\HTMLContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\special\formCaptcha;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;


// Build the page content
$pageContent = new HTMLContent();
$actionFactory = $pageContent->getActionFactory();
$pageContent->build();


$title = moduleLiteral::get($moduleID, "lbl_publishTitle");
$desc = DOM::create("h2", $title);
$pageContent->append($desc);


// Create steps to publish

// Step 1. Check DB Schema
$stepTitle = "Check Database Schema";
$stepRow = getStepRow(1, $stepTitle, $actionFactory, $moduleID, $action = "checkDbSchema", $contextId = "dbSchema");
$pageContent->append($stepRow);

// Step 2. Sync DB Data
$stepTitle = "Sync Database Data";
$stepRow = getStepRow(2, $stepTitle, $actionFactory, $moduleID, $action = "syncDbData", $contextId = "dbData");
HTML::addClass($stepRow, "disabled");
$pageContent->append($stepRow);

// Step 3. Create release package
$stepTitle = "Create Release Package";
$stepRow = getStepRow(3, $stepTitle, $actionFactory, $moduleID, $action = "rbPublish", $contextId = "rbPublisher");
$pageContent->append($stepRow);
/*
// Step 4. Upload package
$stepTitle = "Upload Release Package";
$stepRow = getStepRow(4, $stepTitle, $actionFactory, $moduleID, $action = "sitePublish", $contextId = "");
HTML::addClass($stepRow, "disabled");
$pageContent->append($stepRow);

// Step 5. Run redback.gr Publisher
$stepTitle = "Run redback.gr Publisher";
$stepRow = getStepRow(5, $stepTitle, $actionFactory, $moduleID, $action = "sitePublish", $contextId = "");
HTML::addClass($stepRow, "disabled");
$pageContent->append($stepRow);
*/

function getStepRow($count, $stepTitle, $actionFactory, $module, $action = "", $contextId = "")
{
	$row = DOM::create("div", "", "", "stepRow step".$count);
	
	$stepHeader = DOM::create("div", "", "", "stepHeader");
	DOM::append($row, $stepHeader);
	
	$rowCount = DOM::create("div", "#".$count, "", "rowCount cl");
	DOM::append($stepHeader, $rowCount);
	
	$rowHeader = DOM::create("div", $stepTitle, "", "rowHeader cl");
	DOM::append($stepHeader, $rowHeader);
	
	$stepButton = DOM::create("div", "", "", "stepButton cl");
	DOM::append($stepHeader, $stepButton);
	$buttonAction = DOM::create("div", "Go", "", "buttonAction");
	DOM::append($stepButton, $buttonAction);
	$actionFactory->setModuleAction($buttonAction, $module, $action);
	
	$stepStatus = DOM::create("div", "", $contextId."Status", "stepStatus cl");
	DOM::append($stepHeader, $stepStatus);
	
	$stepContext = DOM::create("div", "", $contextId."Context", "stepContext");
	DOM::append($row, $stepContext);
	
	return $row;
}

// Return content report
return $pageContent->getReport();
//#section_end#
?>