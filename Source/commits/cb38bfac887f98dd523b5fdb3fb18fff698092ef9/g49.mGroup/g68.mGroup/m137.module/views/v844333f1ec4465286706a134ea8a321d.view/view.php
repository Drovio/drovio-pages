<?php
//#section#[header]
// Module Declaration
$moduleID = 137;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("UI", "Html");
importer::import("UI", "Apps");
importer::import("ESS", "Protocol");
importer::import("DEV", "Apps");
//#section_end#
//#section#[code]
use \ESS\Protocol\loaders\AppLoader;
use \UI\Modules\MPage;
use \UI\Apps\APPContent;
use \DEV\Apps\test\appTester;
use \DEV\Apps\application;

// Create Module Page
$page = new MPage($moduleID);
$actionFactory = $page->getActionFactory();

// Get application id
$appID = $_GET['id'];
$appName = $_GET['name'];
$app = new application($appID, $appName);
$appID = $app->getID();

// Build the module for a valid application
$page->build("", "applicationTester", TRUE);
$applicationContainer = HTML::select(".applicationTester #applicationContainer")->item(0);

// Activate tester mode for application
appTester::setPublisherLock(FALSE);

// Get initial application view
$appOutput = AppLoader::load($appID);
$output = json_decode($appOutput, TRUE);
foreach ($output['body'] as $body)
{
	$type = $body['type'];
	$context = $body['context'];
	if ($type == "action")
	{
		$pageHelper = DOM::find("pageHelper");
		$actionContainer = DOM::create("span", "", "", "actionContainer");
		DOM::data($actionContainer, "action", $body['context']);
		DOM::append($pageHelper, $actionContainer);
	}
	else if ($type == "data")
	{
		// Get method and holder
		$method = $body['method'];
		$holder = $body['holder'];
		// Get holder and append context
		if (!empty($holder))
		{
			$holderElement = HTML::select($holder)->item(0);
			if (is_null($holderElement))
			{
				$holder = APPContent::HOLDER;
				$holderElement = HTML::select($holder)->item(0);
			}
			
			DOM::innerHTML($holderElement, $context);
		}
	}
}

// Load application resources action
$page->addReportAction("application.loadResources");

// Return output
return $page->getReport();
//#section_end#
?>