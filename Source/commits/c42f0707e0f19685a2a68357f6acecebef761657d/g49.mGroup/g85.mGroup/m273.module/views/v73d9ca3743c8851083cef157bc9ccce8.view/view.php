<?php
//#section#[header]
// Module Declaration
$moduleID = 273;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

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
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Modules\MContent;

// Get data
$literalModuleID = engine::getVar('mid');

// Build module content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "mLtList");


if (engine::isPost())
{
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Validate form post
	if (!simpleForm::validate())
	{
		// Add form post error header
		$err_header = DOM::create("div", "ERROR");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.invalidate"));
		
		return $errFormNtf->getReport();
	}
	
	// Check empty module id
	if (empty($literalModuleID))
	{
		$err_header = DOM::create("span", "Literal Manager Error");
		$err = $errFormNtf->addHeader($err_header);
		$err_message = DOM::create("span", "There was an error while trying to save literals. Please reload the Literal Manager and try again.");
		$errFormNtf->addDescription($err, $err_message);
		return $errFormNtf->getReport();
	}
	
	// Initial literal values
	$initLiterals = moduleLiteral::get($literalModuleID, "", array(), FALSE, locale::getDefault());
	
	// Delete literals
	$unset = engine::getVar('del');
	
	// Create New Literals
	$newLiterals = array();
	$lts = engine::getVar("lt");
	foreach ($lts as $pair)
	{
		if (empty($pair['id']) || empty($pair['value']))
			continue;
		
		$newLiterals[$pair['id']] = $pair['value'];
	}
	
	// For each "to delete" literal, unset it from init and new literals
	// The literal is marked for deletion anyways.
	foreach ($unset as $id => $state)
	{
		unset($initLiterals[$id]);
		unset($newLiterals[$id]);
	}
	
	// For each new literal, decide if it needs to be added or updated
	foreach ($newLiterals as $lName => $lValue)
	{
		// If value is empty, literal is for delete
		if (empty($lValue))
			continue;
			
		// If literal doesn't exist in initial, then it needs to be added as a new literal.
		// If literal exists in initial and has different value or description, update.
		if (empty($initLiterals[$lName]))
			moduleLiteral::add($literalModuleID, $lName, $lValue);
		else 
		{
			if ($initLiterals[$lName] != $lValue)
				moduleLiteral::update($literalModuleID, $lName, $lValue);
			unset($initLiterals[$lName]);
		}
	}
	
	// For any literal left in initLiterals, 
	// that literal needs to be deleted, along with the "to delete" literals
	$delete = array_merge((array)$unset, (array)$initLiterals);
	foreach ($delete as $lName => $mixed)
		moduleLiteral::remove($literalModuleID, $lName);
}

// Get project literals
$projectLiterals = moduleLiteral::get($literalModuleID, "", array(), FALSE, locale::getDefault());
if (count($projectLiterals) > 0)
	$editable = TRUE;

// Project literals list
$gridList = new dataGridList();
$literalsList = $gridList->build("ltlistcontainer", $editable)->get();
$pageContent->append($literalsList);

// Set headers
$headers = array();
$headers[] = "Name";
$headers[] = "Value";
$gridList->setHeaders($headers);


// Add literals
$form = new simpleForm();
foreach ($projectLiterals as $lName => $lValue)
{
	$gridRow = array();
	
	// Name
	$input = $form->getInput($type = "text", $name = "lt[".$lName."][id]", $value = $lName, $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $form->getTextarea($name = "lt[".$lName."][value]", $value = "", $class = "");
	DOM::nodeValue($txtArea, $lValue);
	$gridRow[] = $txtArea;
	
	$gridList->insertRow($gridRow, "del[".$lName."]");
}

// Create 5 extra rows for new literals
for ($i = 0; $i < 5; $i++)
{
	$gridRow = array();
	
	// Name
	$input = $form->getInput($type = "text", $name = "lt[".$i."][id]", $value = "", $class = "", $autofocus = FALSE);
	$gridRow[] = $input;
	
	// Value
	$txtArea = $form->getTextarea($name = "lt[".$i."][value]", $value = "", $class = "");
	$gridRow[] = $txtArea;
	
	$gridList->insertRow($gridRow);
}

return $pageContent->getReport("#ltListContainer");
//#section_end#
?>