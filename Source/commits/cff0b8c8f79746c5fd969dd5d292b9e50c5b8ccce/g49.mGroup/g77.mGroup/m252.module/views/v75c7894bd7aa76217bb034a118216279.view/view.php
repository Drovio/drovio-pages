<?php
//#section#[header]
// Module Declaration
$moduleID = 252;

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
importer::import("API", "Model");
importer::import("DEV", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Resources");
importer::import("ESS", "Environment");
importer::import("SYS", "Comm");
importer::import("SYS", "Geoloc");
importer::import("SYS", "Resources");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \ESS\Environment\url;
use \SYS\Comm\db\dbConnection;
use \SYS\Geoloc\locale;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \DEV\Projects\projectLibrary;
use \DEV\Projects\project;
use \DEV\Modules\modulesProject;
use \DEV\Resources\paths;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "locOverviewContent", TRUE);

// Get project id and name
$projectID = engine::getVar('id');
$projectName = engine::getVar('name');

// Get project info
$project = new project($projectID, $projectName);
$projectInfo = $project->info();
	
// Get project data
$projectID = $projectInfo['id'];

// Get total literals
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "total_literal_count");
$attr['project_id'] = $projectID;
$result = $dbc->execute($q, $attr);
$data = $dbc->fetch($result);
$totalCount = $data['count'];

// For each active locale, show percentage completed
$defaultLocale = locale::getDefault();
$locales = locale::available();
$locList = HTML::select(".localeList")->item(0);
$totalLocaleCount = 0;
$localeCountNum = 0;
foreach ($locales as $locale)
{
	if ($locale['locale'] == $defaultLocale)
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
	
	// Get flag url
	$flagUrl = url::resolve("cdn", "/media/geo/flags/".$locale['imageName']);
	
	// Get locale viewer
	$percentage = number_format(($localeCount / $totalCount) * 100, 2);
	$countersText = $localeCount." Translated literal(s) / ".($percentage)."%";
	$localeViewer = getLocaleViewer($locale['friendlyName'], $flagUrl, $countersText, $percentage);
	DOM::append($locList, $localeViewer);
	
	// Sum up
	$totalLocaleCount += $localeCount;
	$localeCountNum++;
	
}


// Total statistics
$stats = HTML::select(".stats")->item(0);
$percentage = number_format(($totalLocaleCount / ($totalCount*$localeCountNum)) * 100, 2);
$countersText = ($totalCount)." Total literal(s) / ".($percentage)."%";
$localeViewer = getLocaleViewer("Overall Status", "", $countersText, $percentage);
DOM::append($stats, $localeViewer);

// Return output
return $pageContent->getReport();

function getLocaleViewer($name, $imageUrl, $countersText, $percentage)
{
	// Create commit viewer
	$localeViewer = DOM::create("div", "", "", "lcViewer");
	
	// Create header
	$lcHeader = DOM::create("div", "", "", "lcHeader");
	DOM::append($localeViewer, $lcHeader);
	
	// Set header image
	$lcImg = DOM::create("div", "", "", "lcImg");
	DOM::append($lcHeader, $lcImg);
	
	if (!empty($imageUrl))
	{
		$img = DOM::create("img");
		DOM::attr($img, "src", $imageUrl);
		DOM::append($lcImg, $img);
	}
	
	// Commit description
	$lcName = DOM::create("h3", $name, "", "lcName");
	DOM::append($lcHeader, $lcName);
	
	// Counters
	$counters = DOM::create("div", $countersText, "", "counters");
	DOM::append($localeViewer, $counters);
	
	// Set percentage bar
	$bar = DOM::create("span", "", "", "bar");
	DOM::attr($bar, "style", "width: ".$percentage."%");
	DOM::append($localeViewer, $bar);
	
	return $localeViewer;
}
//#section_end#
?>