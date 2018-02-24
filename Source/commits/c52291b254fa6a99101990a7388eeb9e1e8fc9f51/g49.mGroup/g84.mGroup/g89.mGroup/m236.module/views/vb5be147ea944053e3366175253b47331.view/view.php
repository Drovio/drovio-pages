<?php
//#section#[header]
// Module Declaration
$moduleID = 236;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("DEV", "Core");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\notification;
use \UI\Presentation\frames\dialogFrame;
use \DEV\Core\ajax\ajaxPage;

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	// Check requirements
	$errorNtf = new formErrorNotification();
	$errorNtf->build();
	$hasError = FALSE;
	
	if (empty($_POST['pageName']))
	{
		$hasError = TRUE;
		$header = $errorNtf->addErrorHeader("err", "Page Name");
		$errorNtf->addErrorDescription($header, "errDesc", "Page name cannot be empty.", $extra = "");
	}
	
	if ($hasError)
		return $errorNtf->getReport();
	
	$ajaxPage = new ajaxPage();
	$status = $ajaxPage->create($_POST['pageName'], $_POST['dirName']);
	
	if (!$status)
	{
		$header = $errorNtf->addErrorHeader("err", "Create Error");
		$errorNtf->addErrorDescription($header, "errDesc", "Error creating ajax page.", $extra = "");
		return $errorNtf->getReport();
	}
	
	// Return form report
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
	
}

$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hdr_createNewPage", array(), FALSE);
$frame->build($title, $moduleID, "createPage", FALSE);
$form = new simpleForm();

// Form Inputs

// Parent Directory
$title = moduleLiteral::get($moduleID, "lbl_parent");
$input = $form->getInput($type = "text", $name = "dirName", $value = "", $class = "", $autofocus = TRUE);
$libRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($libRow);

// Directory Name
$title = moduleLiteral::get($moduleID, "lbl_pageName");
$input = $form->getInput($type = "text", $name = "pageName", $value = "", $class = "", $autofocus = FALSE);
$libRow = $form->buildRow("Name (.php)", $input, $required = TRUE, $notes = "");
$frame->append($libRow);

// Return the report
return $frame->getFrame();
//#section_end#
?>