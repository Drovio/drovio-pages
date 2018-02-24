<?php
//#section#[header]
// Module Declaration
$moduleID = 67;

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
importer::import("API", "Model");
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Login");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Geoloc\locale;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Resources\forms\inputValidator;
use \API\Profile\account;
use \UI\Login\registerDialog;
use \UI\Modules\MPage;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\templates\simpleForm;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get return url
$return_url = engine::getVar('return_url');

// Check registered user
if (account::validate())
{
	// Check for return url or redirect to my
	if (!empty($return_url))
		return $actionFactory->getReportRedirect($return_url);
	
	// Redirect to my
	return $actionFactory->getReportRedirect("/profile/", "www");
}

// Build Page
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "registerPageContainer", TRUE, TRUE);

$whiteBox = HTML::select(".whiteBox")->item(0);

$regd = new registerDialog();
$dialogElement = $regd->build($logintype = registerDialog::REG_TYPE_PAGE, $return_url)->get();
DOM::append($whiteBox, $dialogElement);

// Footer year
$trade = HTML::select(".pgFooter .left")->item(0);
$y = DOM::create("span", "".date('Y'));
DOM::append($trade, $y);

// Footer locale
$a_locale = HTML::select("a.locale")->item(0);
$localeInfo = locale::info();
$content = DOM::create("span", $localeInfo['friendlyName']);
DOM::append($a_locale, $content);

return $page->getReport();
//#section_end#
?>