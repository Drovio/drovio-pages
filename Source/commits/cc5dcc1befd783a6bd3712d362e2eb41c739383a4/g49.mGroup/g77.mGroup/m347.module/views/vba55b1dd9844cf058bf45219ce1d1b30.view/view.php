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
use \DEV\Literals\translator;

// Get requested scope
$projectID = engine::getVar('id');
$literalScope = engine::getVar('scope');
$literalName = engine::getVar('name');

// Create Module Content
$pageContent = new MContent($moduleID);
$actionFactor = $pageContent->getActionFactory();

// Build the module
$pageContent->build("", "literalTranslationsContainer", TRUE);

// Set environment
$defaultLocale = locale::getDefault();

// Get default locked literal
$lockedList = HTML::select(".literalTranslations .ltgroup.locked .ltlist")->item(0);
$literalValue = literal::get($projectID, $literalScope, $literalName, array(), FALSE, $defautLocale);
$ltrow = getLiteralRow($moduleID, $literalValue, $defaultLocale, $editable = FALSE, $class = "default");
DOM::append($lockedList, $ltrow);

// Get all translations
$locales = locale::available();
$translationList = HTML::select(".literalTranslations .ltgroup.translations .ltlist")->item(0);
foreach ($locales as $locale)
{
	// Skip default locale
	if ($locale['locale'] == $defaultLocale)
		continue;
	
	// Get locked translated literals
	$translatedLiteralsAll = literalController::get($projectID, $literalScope, $locale['locale']);
	$translatedLiterals = $translatedLiteralsAll['translated'];
	if (isset($translatedLiterals[$literalName]))
	{
		$attr = array();
		$attr['projectID'] = $projectID;
		$attr['scope'] = $literalScope;
		$attr['name'] = $literalName;
		$attr['locale'] = $locale['locale'];
		$ltrow = getLiteralRow($moduleID, $translatedLiterals[$literalName], $locale['locale'], $editable = FALSE, $class = "unlock", $attr);
		DOM::append($lockedList, $ltrow);
		
		continue;
	}
	
	// Get literal translations
	$translations = translator::getTranslations($projectID, $literalScope, $literalName, $locale['locale']);
	$selfTranslation = FALSE;
	foreach ($translations as $translation)
	{
		// Get normal translation
		if (!empty($translation['value']))
		{
			$attr = array();
			$attr['projectID'] = $projectID;
			$attr['translation_id'] = $translation['translation_id'];
			$ltrow = getLiteralRow($moduleID, $translation['value'], $locale['locale'], $editable = FALSE, $class = "lock", $attr);
			DOM::append($translationList, $ltrow);
		}
		
		if ($translation['translator_id'] == account::getAccountID())
		{
			// Set indicator for self translation
			$selfTranslation = TRUE;
			
			// Get editable translation
			$attr = array();
			$attr['projectID'] = $projectID;
			$attr['scope'] = $literalScope;
			$attr['name'] = $literalName;
			$attr['locale'] = $locale['locale'];
			$ltrow = getLiteralRow($moduleID, $translation['value'], $locale['locale'], $editable = TRUE, $class = "translate", $attr);
			DOM::append($translationList, $ltrow);
		}
	}
	if (empty($translations) || !$selfTranslation)
	{
		$attr = array();
		$attr['projectID'] = $projectID;
		$attr['scope'] = $literalScope;
		$attr['name'] = $literalName;
		$attr['locale'] = $locale['locale'];
		$ltrow = getLiteralRow($moduleID, "", $locale['locale'], $editable = TRUE, $class = "edit", $attr);
		DOM::append($translationList, $ltrow);
	}
}

// Return output
return $pageContent->getReport();


function getLiteralRow($moduleID, $literalValue, $locale, $editable = FALSE, $iconClass = "default", $attr = array())
{
	// Create literal row
	$ltrow = DOM::create("div", "", "", "ltrow");
	
	// Get locale info
	$localeInfo = locale::info($locale);
	
	// Add flag
	$img = DOM::create("img", "", "", "trimg");
	$src = url::resolve("cdn", "/media/geo/flags/".$localeInfo['imageName']);
	DOM::attr($img, "src", $src);
	$flag = DOM::create("div", $img, "", "lcflag");
	DOM::append($ltrow, $flag);
	
	// Locale friendly name
	$fname = DOM::create("div", $localeInfo['friendlyName'], "", "lcfname");
	DOM::append($ltrow, $fname);
	
	if (!$editable)
	{
		// Set translation form
		if ($iconClass == "lock")
		{
			// Create form
			$form = new simpleForm();
			$trForm = $form->build("", FALSE)->engageModule($moduleID, "translate")->get();
			HTML::addClass($trForm, "lform");
			DOM::append($ltrow, $trForm);
			
			$input = $form->getInput($type = "hidden", $name = "id", $value = $attr['projectID'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			$input = $form->getInput($type = "hidden", $name = "translation_id", $value = $attr['translation_id'], $class = "", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			// Action Type
			$input = $form->getInput($type = "hidden", $name = "action", $value = "lock", $class = "", $autofocus = FALSE);
			$form->append($input);
			
			$submit = $form->getSubmitButton($title = "", $id = "", $name = "");
			HTML::addClass($submit, "sticon ".$iconClass);
			$form->append($submit);
		}
		// Set translation form
		else if ($iconClass == "unlock")
		{
			// Create form
			$form = new simpleForm();
			$trForm = $form->build("", FALSE)->engageModule($moduleID, "translate")->get();
			HTML::addClass($trForm, "lform");
			DOM::append($ltrow, $trForm);
			
			$input = $form->getInput($type = "hidden", $name = "id", $value = $attr['projectID'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			$input = $form->getInput($type = "hidden", $name = "scope", $value = $attr['scope'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			$input = $form->getInput($type = "hidden", $name = "name", $value = $attr['name'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			$input = $form->getInput($type = "hidden", $name = "locale", $value = $attr['locale'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
			$form->append($input);
			
			// Action Type
			$input = $form->getInput($type = "hidden", $name = "action", $value = "reset", $class = "", $autofocus = FALSE);
			$form->append($input);
			
			$submit = $form->getSubmitButton($title = "", $id = "", $name = "");
			HTML::addClass($submit, "sticon ".$iconClass);
			$form->append($submit);
		}
		else
		{
			// Add status icon
			$statusIcon = DOM::create("div", "", "", "sticon");
			HTML::addClass($statusIcon, $iconClass);
			DOM::append($ltrow, $statusIcon);	
		}
		
		// Add literal
		$ltValue = DOM::create("div", $literalValue, "", "ltval");
		DOM::append($ltrow, $ltValue);
	}
	else
	{
		// Create form
		$form = new simpleForm();
		$trForm = $form->build("", FALSE)->engageModule($moduleID, "translate")->get();
		DOM::append($ltrow, $trForm);
		
		$input = $form->getInput($type = "hidden", $name = "id", $value = $attr['projectID'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "scope", $value = $attr['scope'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "name", $value = $attr['name'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		$input = $form->getInput($type = "hidden", $name = "locale", $value = $attr['locale'], $class = "trinput", $autofocus = FALSE, $required = FALSE);
		$form->append($input);
		
		// Action Type
		$input = $form->getInput($type = "hidden", $name = "action", $value = "translate", $class = "", $autofocus = FALSE);
		$form->append($input);
		
		$ph = moduleLiteral::get($moduleID, "lbl_setTranslation", array(), FALSE);
		$input = $form->getInput($type = "text", $name = "translation", $value = $literalValue, $class = "trinput", $autofocus = FALSE, $required = FALSE);
		DOM::attr($input, "placeholder", $ph);
		$form->append($input);
		
		$submit = $form->getSubmitButton($title = "", $id = "", $name = "");
		HTML::addClass($submit, "sticon ".$iconClass);
		$form->append($submit);
	}
	
	return $ltrow;
}
//#section_end#
?>