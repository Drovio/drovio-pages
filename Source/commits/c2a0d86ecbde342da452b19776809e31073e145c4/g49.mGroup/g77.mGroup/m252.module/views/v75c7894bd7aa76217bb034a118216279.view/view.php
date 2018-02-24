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
importer::import("API", "Geoloc");
importer::import("API", "Model");
importer::import("UI", "Modules");
importer::import("SYS", "Comm");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \SYS\Resources\url;
use \API\Geoloc\locale;
use \API\Model\modules\module;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "locOverviewContent", TRUE);


// Project ID
$projectID = $_REQUEST['id'];

// Get total literals
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "total_literal_count");
$attr['project_id'] = $projectID;
$result = $dbc->execute($q, $attr);
$data = $dbc->fetch($result);

$totalCount = $data['count'];
$countTitle = HTML::select(".otile.lit .title")->item(0);
HTML::nodeValue($countTitle, $totalCount);


// For each active locale, show percentage completed
$default = locale::getDefault();
$locales = locale::available();
$locList = HTML::select(".localeList")->item(0);
$totalLocaleCount = 0;
$localeCountNum = 0;
foreach ($locales as $locale)
{
	if ($locale['locale'] == $default)
		continue;
	
	// Get translation percentage for locale
	$dbc = new dbConnection();
	$q = module::getQuery($moduleID, "locale_percentage");
	
	$attr = array();
	$attr['locale'] = $locale['locale'];
	$attr['project_id'] = $projectID;
	$result = $dbc->execute($q, $attr);
	$data = $dbc->fetch($result);
	$localeCount = $data['count'];
	$percentage = round($localeCount / $totalCount, 2);
	
	
	$lct = DOM::create("div", "", "", "lct");
	DOM::append($locList, $lct);
	
	$img = DOM::create("img", "", "", "lcimg");
	$src = url::resolve("www", "/Library/Media/c/geo/flags/".$locale['imageName']);
	DOM::attr($img, "src", $src);
	DOM::append($lct, $img);
	
	$desc = DOM::create("span", $locale['uniDescription'], "", "lcdesc");
	DOM::append($lct, $desc);
	
	$perc = DOM::create("span", $percentage." %", "", "lcperc");
	DOM::append($lct, $perc);
	
	$sbar = DOM::create("div", "", "", "sb");
	DOM::append($lct, $sbar);
	
	// Sum up
	$totalLocaleCount += $localeCount;
	$localeCountNum++;
}

// Set total translation percentage
$totalPercentage = round($totalLocaleCount / ($totalCount*$localeCountNum), 2);
$countTitle = HTML::select(".otile.perc .title")->item(0);
HTML::nodeValue($countTitle, $totalPercentage." %");


// Return output
return $pageContent->getReport();
//#section_end#
?>