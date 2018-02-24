<?php
//#section#[header]
// Module Declaration
$moduleID = 39;

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
importer::import("API", "Connect");
importer::import("API", "Geoloc");
importer::import("API", "Literals");
importer::import("API", "Profile");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Connect\invitations;
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \API\Profile\account;
use \API\Profile\person;
use \UI\Modules\MPage;

// Create Module page
$page = new MPage($moduleID);

$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$page->build($title, "myHomePage", TRUE);

// Get person information
$personInfo = person::info();

// Set account information
$container = HTML::select(".display_name .value")->item(0);
$value = account::getAccountTitle();
DOM::nodeValue($container, $value);

$container = HTML::select(".username .value")->item(0);
$value = account::getUsername();
DOM::nodeValue($container, $value);


// Personal Information
if (!account::isLocked())
{
	$container = HTML::select(".registration_mail .value")->item(0);
	$value = $personInfo['mail'];
	DOM::nodeValue($container, $value);
	
	$container = HTML::select(".language .value")->item(0);
	DOM::nodeValue($container, locale::get());
}
else
{
	$personalRows = HTML::select(".info_row.personal");
	foreach ($personalRows as $row)
		HTML::replace($row, NULL);
}


// Check if there are pending invitations for the current account/person
$pendingInvitations = invitations::getAccountInvitations();
if (empty($pendingInvitations))
{
	// Remove relations box section
	$relationsBox = HTML::select(".myHome section.box.relations")->item(0);
	HTML::replace($relationsBox, NULL);
}

// Remove action buttons if managed account
if (!account::isAdmin())
{
	$actions = HTML::select(".box .header .action");
	foreach ($actions as $action)
		HTML::replace($action, NULL);
}

return $page->getReport();
//#section_end#
?>