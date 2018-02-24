<?php
//#section#[header]
// Module Declaration
$moduleID = 252;

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
importer::import("UI", "Modules");
importer::import("DEV", "Literals");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Literals\literal;
use \DEV\Literals\translator;

$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

$projectID = $_REQUEST['pid'];
$literalScope = $_REQUEST['scope'];
$literalName = $_REQUEST['name'];
$literalLocale = $_REQUEST['lc'];

$reportHolder = "";

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$tID = str_replace(".", "_", "tr_".$literalScope."_".$literalName);
	$reportHolder = "#".$tID;
		
	// Check Action
	if ($_POST['action'] == "translate")
		translator::translate($projectID, $literalScope, $literalName, $_POST['translationValue'], $literalLocale);
	else if ($_POST['action'] == "removeTranslation")
	{
		// Remove translation here
	}
	else if ($_POST['action'] == "lock")
	{
		// Lock translation
		translator::lock($_POST['translation_id']);
		
		// Create new content with message
		$pageContent->build("translationLocked", "literalTranslationsLocked");
		$trContent = moduleLiteral::get($moduleID, "lbl_translationLockSuccess");
		$slContent = DOM::create("p", $trContent);
		$pageContent->append($slContent);
		return $pageContent->getReport($reportHolder);
	}
}

// Build module
$pageContent->build("literal_translations", "translationContent", TRUE);

// Get literal description
$info = literal::info($projectID, $literalScope, $literalName);
$ltDesc = $info['description'];
if (empty($ltDesc))
{
	$ltDesc = HTML::select(".ltDescription")->item(0);
	HTML::replace($ltDesc, NULL);
}
else
{
	$descVal = HTML::select(".descValue")->item(0);
	HTML::nodeValue($descVal, $ltDesc);
}

// Get translations
$translationList = HTML::select(".translation_list .list")->item(0);
$translations = translator::getTranslations($projectID, $literalScope, $literalName, $literalLocale);
foreach ($translations as $translation)
{
	$t = DOM::create("div", $translation['value'], "", "translation");
	DOM::append($translationList, $t);
}


// Translate form
$translateFormContainer = HTML::select(".translate_formContainer")->item(0);
$form = new simpleForm();
$translateForm = $form->build($moduleID, "translations")->get();
DOM::append($translateFormContainer, $translateForm);

// Literal ProjectID
$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal Scope
$input = $form->getInput($type = "hidden", $name = "scope", $value = $literalScope, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal Name
$input = $form->getInput($type = "hidden", $name = "name", $value = $literalName, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal locale
$input = $form->getInput($type = "hidden", $name = "lc", $value = $literalLocale, $class = "", $autofocus = FALSE);
$form->append($input);

// Action Type
$input = $form->getInput($type = "hidden", $name = "action", $value = "translate", $class = "", $autofocus = FALSE);
$form->append($input);

// Insert Translation input
$title = moduleLiteral::get($moduleID, "lbl_translationValue");
$input = $form->getTextarea($name = "translationValue", "", $class = "");
$form->insertRow($title, $input, $required = FALSE, $notes = "");

if (count($translations) > 0)
{
	// Lock translation
	$lockFormContainer = HTML::select(".lock_formContainer")->item(0);
	$form = new simpleForm();
	$lockForm = $form->build($moduleID, "translations")->get();
	DOM::append($lockFormContainer, $lockForm);
	
	// Hidden Action type
	$input = $form->getInput($type = "hidden", $name = "action", $value = "lock", $class = "", $autofocus = FALSE);
	$form->append($input);
	
	// Literal Scope
	$input = $form->getInput($type = "hidden", $name = "scope", $value = $literalScope, $class = "", $autofocus = FALSE);
	$form->append($input);
	
	// Literal Name
	$input = $form->getInput($type = "hidden", $name = "name", $value = $literalName, $class = "", $autofocus = FALSE);
	$form->append($input);
	
	// Lock Value
	$title = moduleLiteral::get($moduleID, "lbl_lockTranslateValue");
	$translationsResource = array();
	foreach ($translations as $tr)
		$translationsResource[$tr['translation_id']] = $tr['value'];
	$input = $form->getResourceSelect($name = "translation_id", $multiple = FALSE, $class = "", $translationsResource, $selectedValue = NULL);
	$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
	$form->append($inputRow);
}
else
{
	$lockContainer = HTML::select(".translations .lock")->item(0);
	HTML::replace($lockContainer, NULL);
}

return $pageContent->getReport($reportHolder);
//#section_end#
?>