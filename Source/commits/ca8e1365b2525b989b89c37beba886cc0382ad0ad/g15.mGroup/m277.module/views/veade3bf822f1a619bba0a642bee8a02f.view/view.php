<?php
//#section#[header]
// Module Declaration
$moduleID = 277;

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
importer::import("API", "Model");
importer::import("SYS", "Comm");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Model\modules\module;
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "adminOverviewPage", TRUE);

$dbc = new dbConnection();


// Get teams count
$q = module::getQuery($moduleID, "get_teams_count");
$result = $dbc->execute($q);
$row = $dbc->fetch($result);
$teamsCount = $row['count'];
$hd_count = HTML::select(".box.teams .header .total.count")->item(0);
HTML::innerHTML($hd_count, $teamsCount);


// Get projects count
$q = module::getQuery($moduleID, "get_projects_count");
$result = $dbc->execute($q);
$pcounts = array();
$totalCount = 0;
$openCount = 0;
while ($pr = $dbc->fetch($result))
{
	// Total count
	$totalCount += $pr['count'];
	
	// Open count
	if ($pr['public'])
		$openCount += $pr['count'];
	
	// Type count
	$pcounts[$pr['projectType']] += $pr['count'];
}

// Total count
$hd_count = HTML::select(".box.projects .header .total.count")->item(0);
HTML::innerHTML($hd_count, $totalCount);

// Applications count
$c_count = HTML::select(".box.projects .apps .count")->item(0);
HTML::innerHTML($c_count, $pcounts[4]);

// Websites count
$c_count = HTML::select(".box.projects .websites .count")->item(0);
HTML::innerHTML($c_count, $pcounts[5]);

// Open projects count
$c_count = HTML::select(".box.projects .open .count")->item(0);
HTML::innerHTML($c_count, $openCount);


$q = module::getQuery($moduleID, "get_accounts_count");
$result = $dbc->execute($q);
$totalCount = 0;
$adminCount = 0;
while ($pr = $dbc->fetch($result))
{
	// Total count
	$totalCount += $pr['count'];
	
	// Admins count
	if ($pr['administrator'])
		$adminCount += $pr['count'];
}

// Total count
$hd_count = HTML::select(".box.people .header .total.count")->item(0);
HTML::innerHTML($hd_count, $totalCount);

// Administrators count
$c_count = HTML::select(".box.people .admin .count")->item(0);
HTML::innerHTML($c_count, $adminCount);

// Managed count
$c_count = HTML::select(".box.people .managed .count")->item(0);
HTML::innerHTML($c_count, $totalCount - $adminCount);


$q = module::getQuery($moduleID, "get_persons_count");
$result = $dbc->execute($q);
$data = $dbc->fetch($result);
$personCount = $data['count'];

// Person count
$hd_count = HTML::select(".box.people .persons .count")->item(0);
HTML::innerHTML($hd_count, $personCount);

// Return output
return $pageContent->getReport("", FALSE);
//#section_end#
?>