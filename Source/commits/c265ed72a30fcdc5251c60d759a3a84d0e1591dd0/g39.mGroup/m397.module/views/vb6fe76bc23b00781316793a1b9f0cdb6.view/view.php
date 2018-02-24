<?php
//#section#[header]
// Module Declaration
$moduleID = 397;

// Inner Module Codes
$innerModules = array();
$innerModules['dSub'] = 366;

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
importer::import("UI", "Forms");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Geoloc\geoIP;
use \UI\Modules\MPage;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Build the module content
$page->build("", "landingPageContainer", TRUE);

// Load navigation bar
$landingPage = HTML::select(".landingPage")->item(0);
$navbar = $page->loadView($moduleID, $viewName = "navigationBar");
DOM::prepend($landingPage, $navbar);


// Add new team form
$form = new simpleForm();
$formContainer = HTML::select(".newTeamFormContainer")->item(0);
$newTeamForm = $form->build("", FALSE)->engageModule($innerModules['dSub'], "createTeam")->get();
DOM::append($formContainer, $newTeamForm);

$container = DOM::create("div", "", "", "createTeamContainer");
$form->append($container);

// Input container
$inpContainer = DOM::create("div", "", "", "tmcnt");
DOM::append($container, $inpContainer);

// Team name
$ph = $page->getLiteral("lbl_teamName", array(), FALSE);
$input = $form->getInput($type = "text", $name = "tname", $value = "", $class = "tminp", $autofocus = FALSE, $required = TRUE);
DOM::attr($input, "placeholder", $ph);
DOM::append($inpContainer, $input);

$inputID = DOM::attr($input, "id");
$label = $form->getLabel($text = ".drov.io", $for = $inputID, $class = "tmlbl");
DOM::append($inpContainer, $label);

// Button
$title = $page->getLiteral("lbl_create");
$createBtn = $form->getSubmitButton($title, $id = "btn_create");
HTML::addClass($createBtn, "tmbtn");
DOM::append($container, $createBtn);


// Get current country and adjust pricing symbol
$countryCode = geoIP::getCountryCode2ByIP();
if ($countryCode == "GB")
{
	// Get all symbols
	$symbols = HTML::select(".pricing-price__symbol");
	foreach ($symbols as $symElement)
		HTML::innerHTML($symElement, "£");
}


// Return output
return $page->getReport();
//#section_end#
?>