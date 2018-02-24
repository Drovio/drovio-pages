<?php
//#section#[header]
// Module Declaration
$moduleID = 39;

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
importer::import("API", "Geoloc");
importer::import("API", "Profile");
importer::import("API", "Security");
importer::import("API", "Literals");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \API\Profile\account;
use \API\Profile\person;
use \API\Profile\team;
use \API\Security\accountKey;
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

$container = HTML::select(".registration_mail .value")->item(0);
$value = $personInfo['mail'];
DOM::nodeValue($container, $value);


// Simple settings
$container = HTML::select(".first_name .value")->item(0);
$value = $personInfo['firstname'];
DOM::nodeValue($container, $value);

$container = HTML::select(".middle_name .value")->item(0);
$value = $personInfo['middle_name'];
if (empty($value))
	DOM::replace($container->parentNode, NULL);
else
	DOM::nodeValue($container, $value);

$container = HTML::select(".last_name .value")->item(0);
$value = $personInfo['lastname'];
DOM::nodeValue($container, $value);

$container = HTML::select(".language .value")->item(0);
DOM::nodeValue($container, locale::get());



// Relations
// Get teams (and companies in a later version)
$teams = team::getAccountTeams();
$keys = accountKey::get();
$teamList = HTML::select(".myHome section.box.relations .info")->item(0);
foreach ($teams as $team)
{
	// Add team to list
	$info_row = DOM::create("div", "", "", "info_row roles");
	DOM::append($teamList, $info_row);
	
	// Add team roles
	$roles = array();
	foreach ($keys as $key)
		if ($key['type_id'] == 1 && $key['context'] == $team['id'])
			$roles[] = $key['groupName'];
	
	$roleContext = implode(", ", $roles);
	$gn = DOM::create("div", $roleContext, "", "label");
	DOM::append($info_row, $gn);
	
	// Add team name
	$tn = DOM::create("div", $team['name'], "", "value");
	DOM::append($info_row, $tn);
}

return $page->getReport();
//#section_end#
?>