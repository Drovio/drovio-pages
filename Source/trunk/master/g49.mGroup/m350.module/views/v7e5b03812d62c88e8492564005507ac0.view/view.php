<?php
//#section#[header]
// Module Declaration
$moduleID = 350;

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
importer::import("API", "Profile");
importer::import("DEV", "Projects");
importer::import("SYS", "Comm");
importer::import("UI", "Forms");
importer::import("UI", "Presentation");
//#section_end#
//#section#[code]
use \SYS\Comm\db\dbConnection;
use \API\Literals\literal;
use \API\Literals\moduleLiteral;
use \API\Model\modules\module;
use \API\Profile\team;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Presentation\frames\dialogFrame;
use \UI\Presentation\dataGridList;
use \DEV\Projects\projectBundle;

if (engine::isPost())
{
	$has_error = FALSE;
	
	// Create form Notification
	$errFormNtf = new formErrorNotification();
	$formNtfElement = $errFormNtf->build()->get();
	
	// Check Bundle Title
	if (empty($_POST['title']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = literal::dictionary("title");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// Check bundle projects
	if (empty($_POST['bprojects']))
	{
		$has_error = TRUE;
		
		// Header
		$err_header = moduleLiteral::get($moduleID, "lbl_bundleProjects");
		$err = $errFormNtf->addHeader($err_header);
		$errFormNtf->addDescription($err, $errFormNtf->getErrorMessage("err.required"));
	}
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Create bundle
	$pb = new projectBundle();
	$projects = array_keys($_POST['bprojects']);
	$status = $pb->create($_POST['title'], $projects);
	
	if (!$status)
		return $errFormNtf->getReport();
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = FALSE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport();
}

$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_createBundle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "createBundle");
$form = $frame->getFormFactory();


$title = moduleLiteral::get($moduleID, "lbl_bundleInformation");
$hd = DOM::create("h3", $title, "", "hd");
$frame->append($hd);

// Bundle title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);


$title = moduleLiteral::get($moduleID, "lbl_bundleProjects");
$hd = DOM::create("h3", $title, "", "hd");
$frame->append($hd);

// Get team projects
$dbc = new dbConnection();
$q = module::getQuery($moduleID, "get_team_projects");
$attr = array();
$attr['tid'] = team::getTeamID();
$result = $dbc->execute($q, $attr);
$teamProjects = $dbc->fetch($result, TRUE);

// Select applications
$gridList = new dataGridList();
$projectList = $gridList->build($id = "bplist", $checkable = TRUE)->get();
$frame->append($projectList);

// Set headers
$headers = array();
$headers[] = "Project ID";
$headers[] = "Project Title";
$gridList->setHeaders($headers);

foreach ($teamProjects as $project)
{
	$row = array();
	$row[] = $project['id'];
	$row[] = $project['title'];
	
	$gridList->insertRow($row, "bprojects[".$project['id']."]");
}


// Return output
return $frame->getFrame();
//#section_end#
?>