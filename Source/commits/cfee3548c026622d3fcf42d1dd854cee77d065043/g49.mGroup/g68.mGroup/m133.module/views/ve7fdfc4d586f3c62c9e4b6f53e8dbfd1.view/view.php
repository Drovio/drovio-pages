<?php
//#section#[header]
// Module Declaration
$moduleID = 133;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Comm");
importer::import("API", "Developer");
importer::import("API", "Model");
importer::import("API", "Security");
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\url;
use \API\Security\account;
use \UI\Html\HTMLModulePage;

// Create Module Page
$page = new HTMLModulePage("OneColumnCentered");
$actionFactory = $page->getActionFactory();

// Build the module
$title = moduleLiteral::get($moduleID, "title", FALSE);
$page->build($title, "devAppPage");

// Page Header
$pageHeader = DOM::create("div", "", "", "pageHeader");
$page->appendToSection("mainContent", $pageHeader);

// Create new Application
$title = moduleLiteral::get($moduleID, "lbl_newApp");
$ico = DOM::create("span", "", "", "ico");
$createNewTitle = DOM::create("div", $ico, "", "newAppButton");
DOM::append($createNewTitle, $title);
DOM::append($pageHeader, $createNewTitle);
$actionFactory->setPopupAction($createNewTitle, $moduleID, "CreateNewApp");

// Header title
$content = moduleLiteral::get($moduleID, "lbl_pageTitle");
$title = DOM::create("h1", $content);
DOM::append($pageHeader, $title);

// Create application container
$appContainer = DOM::create("div", "", "", "appContainer");
$page->appendToSection("mainContent", $appContainer);

// Application title
$content = moduleLiteral::get($moduleID, "lbl_myApps");
$title = DOM::create("h2", $content);
DOM::append($appContainer, $title);

// List all user's applications
$dbc = new interDbConnection();
$q = new dbQuery("1348554260", "apps");
$attr = array();
$attr['accountID'] = account::getAccountID();
$result = $dbc->execute($q, $attr);

if ($dbc->get_num_rows($result) == 0)
{
	$content = moduleLiteral::get($moduleID, "lbl_noApps");
	$title = DOM::create("h3", $content);
	DOM::append($appContainer, $title);
}

while ($app = $dbc->fetch($result))
{
	// App Container
	$appHolder = DOM::create("div", "", "", "devApp");
	DOM::append($appContainer, $appHolder);
	
	// App Icon
	$appIco = DOM::create("div", "", "", "appIco");
	DOM::append($appHolder, $appIco);
	
	// App Controls Container
	$appControls = DOM::create("div", "", "", "appControls");
	DOM::append($appHolder, $appControls);
	
	// Edit Control
	$control = DOM::create("a", "", "", "appCtrl edit");
	$url = url::resolve("developer", "/apps/application.php?id=".$app['id']);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($appControls, $control);
	
	// VCS Control
	$control = DOM::create("a", "", "", "appCtrl vcs");
	$url = url::resolve("developer", "/apps/vcs/application.php?id=".$app['id']);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($appControls, $control);
	
	// Play Control
	$control = DOM::create("a", "", "", "appCtrl play");
	$url = url::resolve("apps", "/application.php?id=".$app['id']);
	DOM::attr($control, "href", $url);
	DOM::attr($control, "target", "_self");
	DOM::append($appControls, $control);
	
	// Application title
	$appTitle = DOM::create("h3", $app['fullName'], "", "appTitle");
	DOM::append($appHolder, $appTitle);
	
	// Application tags
	$appTitle = DOM::create("p", $app['tags'], "", "appTags");
	DOM::append($appHolder, $appTitle);
	
	// Application description
	$appTitle = DOM::create("p", $app['description'], "", "appDesc");
	DOM::append($appHolder, $appTitle);
}

// Support Container
$supportContainer = DOM::create("div", "", "", "supportContainer");
$page->appendToSection("mainContent", $supportContainer);

// Support Title
$content = moduleLiteral::get($moduleID, "lbl_appSupport");
$title = DOM::create("h2", $content);
DOM::append($supportContainer, $title);

// Documentation
$title = moduleLiteral::get($moduleID, "lbl_appcenterDocs");
$webC = DOM::create("a", $title);
$url = url::resolve("developer", "/docs/appCenter/");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($supportContainer, $header);

// Guide
$title = moduleLiteral::get($moduleID, "lbl_applicationGuide");
$webC = DOM::create("a", $title);
$url = url::resolve("developer", "/docs/appCenter/guide.php");
DOM::attr($webC, "href", $url);
DOM::attr($webC, "target", "_blank");
$header = DOM::create("h3", $webC);
DOM::append($supportContainer, $header);


// Return output
return $page->getReport();
//#section_end#
?>