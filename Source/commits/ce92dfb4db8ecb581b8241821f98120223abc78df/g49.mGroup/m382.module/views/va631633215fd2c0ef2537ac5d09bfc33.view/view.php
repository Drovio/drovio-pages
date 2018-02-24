<?php
//#section#[header]
// Module Declaration
$moduleID = 382;

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
importer::import("DEV", "Profile");
importer::import("UI", "Modules");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \UI\Modules\MContent;
use \UI\Presentation\popups\popup;
use \DEV\Profile\team as devTeam;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Build the module content
$pageContent->build("", "developerPlanDialogContainer", TRUE);

// Get plan
$teamPlanID = devTeam::getCurrentPlan($live = TRUE);
$plan = devTeam::getPlanName($teamPlanID);

// Set header class (for color)
$header = HTML::select(".developerPlanDialog .pheader")->item(0);
HTML::addClass($header, $plan);

// Set header title
$title = $pageContent->getLiteral("lbl_planTitle_".$plan);
$hdTitle = HTML::select(".developerPlanDialog .pheader .title")->item(0);
HTML::append($hdTitle, $title);

// Set body title and descriptions
$title = $pageContent->getLiteral("lbl_bTitle_".$plan);
$bTitle = HTML::select(".developerPlanDialog .pbody .title")->item(0);
HTML::append($bTitle, $title);

$title = $pageContent->getLiteral("lbl_bDesc_".$plan);
$bDesc = HTML::select(".developerPlanDialog .pbody .description")->item(0);
HTML::append($bDesc, $title);

// Delete feautures
$features = HTML::select(".developerPlanDialog .features .ft");
foreach ($features as $ft)
{
	if (!HTML::hasClass($ft, $plan))
		HTML::addClass($ft, "disabled");
}

// Create popup
$pp = new popup();
$pp->type($type = popup::TP_PERSISTENT, $toggle = FALSE);
$pp->background(TRUE);
$pp->build($pageContent->get());

return $pp->getReport();
//#section_end#
?>