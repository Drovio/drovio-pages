<?php
//#section#[header]
// Module Declaration
$moduleID = 92;

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
importer::import("API", "Model");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("ESS", "Protocol");
//#section_end#
//#section#[code]
use \ESS\Protocol\client\NavigatorProtocol;
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\literals\literal;
use \API\Resources\url;
use \UI\Forms\templates\loginForm;
use \UI\Html\HTMLModulePage;

// Build Module Page
$pageTitle = moduleLiteral::get($moduleID, "title", FALSE);
$page = new HTMLModulePage("freeLayout");
$page->build($pageTitle, "appCenterPage");

// Registration bar
$regBar = DOM::create("div", "", "", "topBar");
$page->appendToSection("main", $regBar);

$logoContainer = DOM::create("div", "", "", "logoContainer");
DOM::append($regBar, $logoContainer);

$regContainer = DOM::create("div", "", "", "topContainer");
DOM::append($regBar, $regContainer);

// Reg Button
$regButtonTitle = moduleLiteral::get($moduleID, "lbl_newApp");
$regButton = DOM::create("a", "", "", "regBtn");
$regURL = url::resolve("developer", "/");
DOM::attr($regButton, "href", $regURL);
DOM::attr($regButton, "target", "_blank");
DOM::append($regButton, $regButtonTitle);
DOM::append($regContainer, $regButton);

// Subtitle
$title = moduleLiteral::get($moduleID, "lbl_subTitle");
$subTitle = DOM::create("p");
DOM::append($subTitle, $title);
DOM::append($logoContainer, $subTitle);

// Global page container
$globalContainer = DOM::create("div", "", "globalContainer", "globalContainer");
$page->appendToSection("main", $globalContainer);


// Header Container (with navigation)
$navContainer = DOM::create("div", "", "", "navContainer");
DOM::append($globalContainer, $navContainer);

$targetcontainer = "globalContainer";
$targetgroup = "appNavGroup";
$navgroup = "appNavGroup";
$display = "none";

// Nav Guide
$title = moduleLiteral::get($moduleID, "lbl_navHome");
$header = DOM::create("h3", $title, "", "navHeader");
$ref = "vdContainer";
NavigatorProtocol::staticNav($header, $ref, $targetcontainer, $targetgroup, $navgroup, $display);
DOM::append($navContainer, $header);
DOM::addClass($header, "selected");

$title = moduleLiteral::get($moduleID, "lbl_navApps");
$header = DOM::create("h3", $title, "", "navHeader");
$ref = "appContainer";
NavigatorProtocol::staticNav($header, $ref, $targetcontainer, $targetgroup, $navgroup, $display);
DOM::append($navContainer, $header);


// Video container
$videoCont = DOM::create("div", "", "vdContainer", "vdContainer");
DOM::append($globalContainer, $videoCont);
NavigatorProtocol::selector($videoCont, $targetgroup);

$appVideo = DOM::create("video");
DOM::attr($appVideo, "width", "640");
DOM::attr($appVideo, "controls", "1");
DOM::append($videoCont, $appVideo);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/videos/appCenter/promo.webm");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/webm");
DOM::append($appVideo, $vSource);

$vSource = DOM::create("source");
$urlSource = url::resource("/Library/Media/videos/appCenter/promo.mp4");
DOM::attr($vSource, "src", $urlSource);
DOM::attr($vSource, "type", "video/mp4");
DOM::append($appVideo, $vSource);

// No support
$noSupport = DOM::create("p", "Your browser doesn't support this video yet. Please use firefox or chrome.");
DOM::append($appVideo, $noSupport);


// App Container
$appContainer = DOM::create("div", "", "appContainer", "appContainer");
DOM::append($globalContainer, $appContainer);
NavigatorProtocol::selector($appContainer, $targetgroup);

// List all user's applications
$dbc = new interDbConnection();
$q = new dbQuery("657431943", "apps");
$result = $dbc->execute($q);
$appCount = 0;
while ($app = $dbc->fetch($result))
{
	// App Container
	$appHolder = DOM::create("div", "", "", "appTile");
	DOM::append($appContainer, $appHolder);
	
	// App Icon
	$appIco = DOM::create("div", "", "", "appIco");
	DOM::append($appHolder, $appIco);
	
	// App Controls Container
	$appControls = DOM::create("div", "", "", "appControls");
	DOM::append($appHolder, $appControls);
	
	if ($app['scope'] == "public")
	{
		// VCS Control
		$control = DOM::create("a", "", "", "appCtrl vcs");
		$url = url::resolve("developer", "/apps/vcs/application.php?id=".$app['id']);
		DOM::attr($control, "href", $url);
		DOM::attr($control, "target", "_self");
		DOM::append($appControls, $control);
	}
	
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
	
	// Application count
	$appCount++;
}

if ($appCount == 0)
{
	$message = DOM::create("h3", "There are no published apps yet.");
	DOM::append($appContainer, $message);
}

return $page->getReport();
//#section_end#
?>