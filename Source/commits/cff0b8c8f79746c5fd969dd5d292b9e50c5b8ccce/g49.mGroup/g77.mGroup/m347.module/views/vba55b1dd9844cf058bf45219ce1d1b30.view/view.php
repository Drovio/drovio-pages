<?php
//#section#[header]
// Module Declaration
$moduleID = 347;

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
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("DEV", "Literals");
importer::import("ESS", "Environment");
importer::import("SYS", "Geoloc");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Geoloc\locale;
use \API\Literals\moduleLiteral;
use \API\Profile\account;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\accordion;
use \UI\Forms\templates\simpleForm;
use \DEV\Literals\literal;
use \DEV\Literals\translator as literalTranslator;
use \DEV\Literals\literalController;

// Get requested scope
$projectID = engine::getVar('id');
$literalScope = engine::getVar('scope');
$literalName = engine::getVar('name');

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactor = $pageContent->getActionFactory();

// Build the module
$pageContent->build("", "literalTranslations", TRUE);


// Set literal content
$defaultLiteral = HTML::select(".translationContent .default")->item(0);
$literalValue = literal::get($projectID, $literalScope, $literalName, array(), FALSE);
$literalContent = DOM::create("div", $literalValue, "", "defv");
DOM::append($defaultLiteral, $literalContent);

// Get translations container
$trContainer = HTML::select(".translationContent .translations")->item(0);

$locales = locale::available();
foreach ($locales as $locale)
{
	// Skip default locale
	if ($locale['locale'] == locale::getDefault())
		continue;
		
	// Get locked translated literals
	$translatedLiteralsAll = literalController::get($projectID, $literalScope, $locale['locale']);
	$translatedLiterals = $translatedLiteralsAll['translated'];

	// Create translation row
	$tRow = DOM::create("div", "", "", "tRow");
	DOM::append($trContainer, $tRow);
	
	$tHead = DOM::create("div", "", "", "tHead");
	DOM::append($tRow, $tHead);
	// Add flag on the left
	$img = DOM::create("img", "", "", "trimg");
	$src = url::resolve("cdn", "/media/geo/flags/".$locale['imageName']);
	DOM::attr($img, "src", $src);
	DOM::append($tHead, $img);
	//
	$localeName = DOM::create("span", $locale['friendlyName']);
	DOM::append($tHead, $localeName);
	
	$rgt = DOM::create("div", "", "", "rgt");
	DOM::append($tHead, $rgt);
	$ico = DOM::create("span", "", "", "ico");
	DOM::append($rgt, $ico);
	$status = DOM::create("span", "", "", "status");
	DOM::append($rgt, $status);
	
	// Check if literal is locked
	if (isset($translatedLiterals[$literalName]))
	{
		$title = moduleLiteral::get($moduleID, "lbl_showTranslation");
		DOM::append($status, $title);
		
		$tContent = HTML::create("div", $translatedLiterals[$literalName], "", "tContent");
		DOM::append($tRow, $tContent);
		
		// Add locked class and continue to next
		HTML::addClass($tRow, "locked");
		continue;
	}
	
	
	// Get translations
	$translations = literalTranslator::getTranslations($projectID, $literalScope, $literalName, $locale['locale']);
	$attr['count'] = count($translations);
	$translationsStatus = moduleLiteral::get($moduleID, "lbl_translate", $attr);
	DOM::append($status, $translationsStatus);
	
	$attr = array();
	$attr['id'] = $projectID;
	$attr['pid'] = $projectID;
	$attr['scope'] = $literalScope;
	$attr['name'] = $literalName;
	$attr['lc'] = $locale['locale'];
	$tID = str_replace(".", "_", "tr_".$literalScope."_".$literalName);
	$tContent = $pageContent->getModuleContainer($moduleID, $action = "translations", $attr, $startup = FALSE, $containerID = $tID);
	HTML::addClass($tContent, "tContent");
	DOM::append($tRow, $tContent);
}


// Reset all translations form
$resetFormContainer = HTML::select(".literalTranslations .resetFormContainer")->item(0);
$form = new simpleForm();
$resetForm = $form->build($moduleID, "clearTranslations", TRUE)->get();
DOM::append($resetFormContainer, $resetForm);

// Literal ProjectID
$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal ProjectID
$input = $form->getInput($type = "hidden", $name = "pid", $value = $projectID, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal Scope
$input = $form->getInput($type = "hidden", $name = "scope", $value = $literalScope, $class = "", $autofocus = FALSE);
$form->append($input);

// Literal Name
$input = $form->getInput($type = "hidden", $name = "name", $value = $literalName, $class = "", $autofocus = FALSE);
$form->append($input);

// Account authentication
$title = moduleLiteral::get($moduleID, "lbl_accountPassword");
$input = $form->getInput($type = "password", $name = "password", $value = "", $class = "", $autofocus = FALSE, $required = TRUE);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport();
//#section_end#
?>