<?php
//#section#[header]
// Module Declaration
$moduleID = 253;

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
importer::import("API", "Literals");
importer::import("UI", "Navigation");
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
importer::import("DEV", "Projects");
importer::import("DEV", "Literals");
importer::import("SYS", "Resources");
//#section_end#
//#section#[code]
use \SYS\Resources\url;
use \API\Literals\moduleLiteral;
use \API\Geoloc\locale;
use \UI\Modules\MContent;
use \UI\Navigation\navigationBar;
use \UI\Presentation\frames\windowFrame;
use \DEV\Literals\literal;
use \DEV\Projects\project;

// Get project id
$projectID = $_REQUEST['pid'];

$project = new project($projectID);
$projectInfo = $project->info();
$projectTitle = $projectInfo['title'];


// Build MContent
$pageContent = new MContent($moduleID);
$pageContent->build("", "literalEditorContainer", TRUE);


// Get all project scopes
$scopeExplorer = HTML::select(".scopeExplorer")->item(0);
$attr = array();
$attr['pid'] = $projectID;
$scopeContainer = $pageContent->getModuleContainer($moduleID, $action = "scopeExplorer", $attr, $startup = TRUE, $containerID = "");
DOM::append($scopeExplorer, $scopeContainer);

// Locale notification
$localeInfo = locale::info();
$ntfIco = HTML::select(".literalEditor .lcNtf .ico")->item(0);

// Create region image
$lcImg = DOM::create("img", "", "", "region");
$src = url::resource("/Library/Media/c/geo/flags/".$localeInfo['imageName']);
DOM::attr($lcImg, "src", $src);
DOM::attr($lcImg, "title", $localeInfo['friendlyName']);
DOM::attr($lcImg, "alt", $localeInfo['friendlyName']);
DOM::append($ntfIco, $lcImg);

$ntfTitle = HTML::select(".literalEditor .lcNtf .ntf")->item(0);
$attr = array();
$attr['locale'] = locale::getDefault();
$title = moduleLiteral::get($moduleID, "lbl_localeNotification", $attr);
DOM::append($ntfTitle, $title);



// Create window frame
$id = "plte".md5("literalEditor_project_".$projectID);
$frame = new windowFrame($id);

// Build frame
$title = moduleLiteral::get($moduleID, "title", array(), FALSE);
$frame->build($projectTitle." | ".$title);


// Append the module content
$frame->append($pageContent->get());

// Return the frame report
return $frame->getFrame();
//#section_end#
?>