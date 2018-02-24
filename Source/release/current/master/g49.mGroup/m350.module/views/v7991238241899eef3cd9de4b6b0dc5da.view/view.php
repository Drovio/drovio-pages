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

$bundleID = engine::getVar('id');

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
	
	// If error, show notification
	if ($has_error)
		return $errFormNtf->getReport();
	
	// Initialize bundle
	$pb = new projectBundle($bundleID);
	
	// Check if bundle is to delete or to update
	if (isset($_POST['delete']))
		$status = $pb->remove();
	else
	{
		$status = $pb->update($_POST['title'], $_POST['description'], $_POST['tags'], $_POST['price']);
		
		// Set projects
		$projects = array_keys($_POST['bprojects']);
		$pb->setProjects($projects);
	}
	
	if (!$status)
		return $errFormNtf->getReport();
	
	// Create notification
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Get bundle information
$pb = new projectBundle($bundleID);
$bundleInfo = $pb->info();


$frame = new dialogFrame();
$title = moduleLiteral::get($moduleID, "hd_editBundle");
$frame->build($title, "", FALSE)->engageModule($moduleID, "");
$form = $frame->getFormFactory();

$input = $form->getInput($type = "hidden", $name = "id", $value = $bundleID, $class = "", $autofocus = FALSE, $required = FALSE);
$frame->append($input);

$title = moduleLiteral::get($moduleID, "lbl_bundleInformation");
$hd = DOM::create("h3", $title, "", "hd");
$frame->append($hd);

// Bundle id
$title = moduleLiteral::get($moduleID, "lbl_bundleID");
$label = $form->getLabel($bundleID, $for = "", $class = "");
$inputRow = $form->buildRow($title, $label, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Bundle title
$title = literal::dictionary("title");
$input = $form->getInput($type = "text", $name = "title", $value = $bundleInfo['title'], $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = TRUE, $notes = "");
$frame->append($inputRow);

// Bundle description
$title = literal::dictionary("description");
$input = $form->getTextarea($name = "description", $value = $bundleInfo['description'], $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
$frame->append($inputRow);

// Bundle tags
$title = moduleLiteral::get($moduleID, "lbl_bundleTags");
$notes = moduleLiteral::get($moduleID, "lbl_bundleTags_notes", array(), FALSE);
$input = $form->getTextarea($name = "tags", $value = $bundleInfo['tags'], $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes);
$frame->append($inputRow);

// Delete Bundle
$title = moduleLiteral::get($moduleID, "lbl_deleteBundle");
$input = $form->getInput($type = "checkbox", $name = "delete", $value = "", $class = "", $autofocus = FALSE, $required = FALSE);
$inputRow = $form->buildRow($title, $input, $required = FALSE, $notes = "");
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
$bundleProjects = $pb->getProjects();
$bundleProjectsIDs = array();
foreach ($bundleProjects as $projectInfo)
	$bundleProjectsIDs[$projectInfo['id']] = 1;
foreach ($teamProjects as $project)
{
	$row = array();
	$row[] = $project['id'];
	$row[] = $project['title'];
	
	$gridList->insertRow($row, "bprojects[".$project['id']."]", isset($bundleProjectsIDs[$project['id']]));
}

// Return output
return $frame->getFrame();
//#section_end#
?>