<?php
//#section#[header]
// Module Declaration
$moduleID = 137;

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
importer::import("API", "Profile");
importer::import("API", "Resources");
importer::import("DEV", "Apps");
importer::import("ESS", "Environment");
importer::import("ESS", "Protocol");
importer::import("UI", "Apps");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \ESS\Protocol\BootLoader;
use \ESS\Protocol\BootLoader2;
use \ESS\Protocol\loaders\AppLoader;
use \ESS\Protocol\reports\HTMLServerReport;
use \ESS\Environment\url;
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
	switch ($type)
	{
		case HTMLServerReport::CONTENT_ACTION:
			$pageHelper = DOM::find("pageHelper");
			$actionContainer = DOM::create("span", "", "", "actionContainer");
			DOM::data($actionContainer, "action", $body['context']);
			DOM::append($pageHelper, $actionContainer);
			
			// Break
			break;
		case HTMLServerReport::CONTENT_DATA:
		case HTMLServerReport::CONTENT_HTML:
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
			}
			
			// Select method of append
			switch ($method)
			{
				case HTMLServerReport::APPEND_METHOD:
					$oldInnerHTML = DOM::innerHTML($holderElement);
					$newInnerHTML = $oldInnerHTML.$context;
					DOM::innerHTML($holderElement, $newInnerHTML);
					break;
				case HTMLServerReport::REPLACE_METHOD:
					DOM::innerHTML($holderElement, $context);
			}
	}
}


// Get application resources
$params = array();
$params['id'] = $appID;
$cssUrl = BootLoader2::getTesterResourceUrl("/ajax/apps/css.php", "Application", $appID);
$jsUrl = BootLoader2::getTesterResourceUrl("/ajax/apps/js.php", "Application", $appID);

// Add Header
$header = BootLoader2::getResourceArray(4, "ApplicationTester", $appID, $cssUrl, $jsUrl, $tester = TRUE);
$page->addResourceHeader($header['id'], $header);

// Return output
return $page->getReport();
//#section_end#
?>