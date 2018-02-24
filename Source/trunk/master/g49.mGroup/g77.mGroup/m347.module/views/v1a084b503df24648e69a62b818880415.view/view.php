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
importer::import("API", "Geoloc");
importer::import("DEV", "Profile");
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\locale;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \DEV\Profile\translator;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "translatorPreferencesContainer", TRUE);
$projectID = engine::getVar("id");

if (engine::isPost())
{
	// Update translator
	translator::join($_POST['locale']);
}

// Check translator profile
$translatorProfile = translator::profile();
$profileContainer = HTML::select(".translatorPreferences .profileContainer")->item(0);
if (empty($translatorProfile))
{
	$title = $pageContent->getLiteral("lbl_noTranslatorProfile");
	$hd = DOM::create("h2", $title, "", "hd");
	DOM::append($profileContainer, $hd);
}
else
{
	// Display profile info
	$attr = array();
	$attr['tr_locale'] = $translatorProfile['translation_locale'];
	$title = $pageContent->getLiteral("lbl_translatorProfile", $attr);
	$hd = DOM::create("h2", $title, "", "hd");
	DOM::append($profileContainer, $hd);
}

// Create form to join/change translator locale value
$form = new simpleForm();
$trForm = $form->build()->engageModule($moduleID, "trPreferences")->get();
$formContainer = HTML::select(".translatorPreferences .formContainer")->item(0);
DOM::append($formContainer, $trForm);

$input = $form->getInput($type = "hidden", $name = "id", $value = $projectID, $class = "trinput", $autofocus = FALSE, $required = FALSE);
$form->append($input);

// Get all locale
$defaultLocale = locale::getDefault();
$avLocale = locale::available();
$localeResource = array();
foreach ($avLocale as $lcInfo)
	if ($lcInfo['locale'] != $defaultLocale)
		$localeResource[$lcInfo['locale']] = $lcInfo['friendlyName'];
	
$title = $pageContent->getLiteral("lbl_translatorLocale");
$input = $form->getResourceSelect($name = "locale", $multiple = FALSE, $class = "", $localeResource, $selectedValue = $translatorProfile['translation_locale']);
$form->insertRow($title, $input, $required = TRUE, $notes = "");

// Return output
return $pageContent->getReport("#preferences_ref", MContent::REPLACE_METHOD);
//#section_end#
?>