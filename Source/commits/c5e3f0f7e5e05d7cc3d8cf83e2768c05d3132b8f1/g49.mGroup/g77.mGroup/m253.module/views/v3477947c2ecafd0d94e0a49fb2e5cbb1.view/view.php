<?php
//#section#[header]
// Module Declaration
$moduleID = 253;

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
importer::import("API", "Resources");
importer::import("API", "Literals");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("ESS", "Protocol");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \API\Resources\forms\inputValidator;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;
use \UI\Presentation\dataGridList;
use \UI\Presentation\frames\dialogFrame;
use \UI\Modules\MContent;
use \DEV\Literals\literal;


// Get data
$projectID = engine::getVar('id');
$scope = engine::getVar('scope');

// Build module content
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();
$pageContent->build("", "scopeLiteralsContainer", TRUE);

$holder = "";
if (engine::isPost())
{
	// Set report holder
	$holder = ".literalsContent";
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// If no project or scope specified there's been an error on page... Return Unknown Error
	if (empty($projectID) || empty($scope))
	{
		$err_header = DOM::create("span", "Literal Manager Error");
		$err = $errFormNtf->addErrorHeader("nomo_h", $err_header);
		$err_message = DOM::create("span", "There was an error while trying to save literals. Please reload the Literal Manager and try again.");
		$errFormNtf->addErrorDescription($err, "nomo_desc", $err_message);
		return $errFormNtf->getReport();
	}
	
	// Update literal scope
	if ($_POST['newScope'] != "scope")
	{
		$newScope = $_POST['newScope'];
		$status = literal::updateScope($projectID, $scope, $newScope);
		if ($status)
			$scope = $newScope;
	}
	
	// Initial literal values
	$initLiterals = literal::get($projectID, $scope, "", array(), FALSE, locale::getDefault());
	
	// Delete literals
	$unset = $_POST['del'];
	
	// Create New Literals
	$newLiterals = array();
	foreach ($_POST['lt'] as $pair)
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
			literal::add($projectID, $scope, $lName, $lValue);
		else 
		{
			if ($initLiterals[$lName] != $lValue)
				literal::update($projectID, $scope, $lName, $lValue);
			unset($initLiterals[$lName]);
		}
	}
	
	// For any literal left in initLiterals, 
	// that literal needs to be deleted, along with the "to delete" literals
	$delete = array_merge((array)$unset, (array)$initLiterals);
	foreach ($delete as $lName => $mixed)
		literal::remove($projectID, $scope, $lName);
	
	
	// Delete scope if checked
	if (isset($_POST['delScope']))
	{
		$status = literal::removeScope($projectID, $scope);
		if ($status)
		{
			$succFormNtf = new formNotification();
			$succFormNtf->build($type = "success", $header = TRUE, $footer = FALSE);
			
			// Notification Message
			$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
			$succFormNtf->append($errorMessage);
			$pageContent->addReportContent($succFormNtf->get(), $holder, "replace");
			return $pageContent->getReport();
		}
	}
}

$form = new simpleForm();
$literalsForm = $form->build($moduleID, $action = "scopeLiterals", $controls = FALSE)->get();
$pageContent->append($literalsForm);

// Project ID
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Scope name
$input = $form->getInput($type = "hidden", $name = "scope", $value = $scope, $class = "", $autofocus = FALSE);
$form->append($input);


// Get project literals
$projectLiterals = literal::get($projectID, $scope, "", array(), FALSE, locale::getDefault());
if (count($projectLiterals) > 0)
	$editable = TRUE;
	
// Literals Container
$literalsContainer = DOM::create("div", "", "", "literalsInnnerContainer");
$form->append($literalsContainer);

$literalsGridListContainer = DOM::create("div", "", "", "literalsGridListContainer");
DOM::append($literalsContainer, $literalsGridListContainer);

$literalsGridList = DOM::create("div", "", "", "literalsGridList");
DOM::append($literalsGridListContainer, $literalsGridList);

// Project literals list
$gridList = new dataGridList();
$literalsList = $gridList->build("ltglist", $editable)->get();
DOM::append($literalsGridList, $literalsList);

// Set headers
$headers = array();
$headers[] = "Name";
$headers[] = "Value";
$gridList->setHeaders($headers);


// Add literals
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



// Add controls row
$controls = DOM::create("div", "", "", "ltControls");
DOM::append($literalsContainer, $controls);

// Scope name
$input = $form->getInput($type = "text", $name = "newScope", $value = $scope, $class = "", $autofocus = FALSE);
DOM::append($controls, $input);

// Add delete scope checkbox
$input = $form->getInput($type = "checkbox", $name = "delScope", $value = "", $class = "", $autofocus = FALSE);
DOM::append($controls, $input);
$inputID = DOM::attr($input, "id");

$attr['scope'] = $scope;
$title = moduleLiteral::get($moduleID, "lbl_delScope", $attr);
$label = $form->getLabel($title, $for = $inputID, $class = "");
DOM::append($controls, $label);

// Submit Button
$title = moduleLiteral::get($moduleID, "lbl_updateLiterals");
$submit = $form->getSubmitButton($title);
DOM::append($controls, $submit);

return $pageContent->getReport($holder);
//#section_end#
?>