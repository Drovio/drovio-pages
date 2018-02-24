<?php
//#section#[header]
// Module Declaration
$moduleID = 85;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("API", "Developer", "profiler::logger");
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");

// Use
use \API\Developer\profiler\logger;
use \UI\Html\DOM;
use \UI\Html\HTML;

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
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Comm\database\connections\interDbConnection;
use \API\Model\units\sql\dbQuery;
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formNotification;
use \UI\Forms\templates\simpleForm;
use \UI\Presentation\dataGridList;

$dbc = new interDbConnection();

$accountID = $_REQUEST['aid'];

// get module groups
$dbq = new dbQuery("547558037", "units.groups");

$attr = array();
$groups = $dbc->execute($dbq, $attr);

$groupsInfo = array();
$groupsPost = array();
while ($row = $dbc->fetch($groups))
	$groupsInfo[$row['id']]['description'] = $row['description'];

// get user's module groups with developer privileges
$dbq = new dbQuery("677677266", "security.privileges.developer");

$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['developer'] = TRUE;
	$groupsPost['developer'][$row['id']] = 'on';
}

// get user's module groups with master privileges
$dbq = new dbQuery("785059449", "security.privileges.developer");

$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['master'] = TRUE;
	$groupsPost['master'][$row['id']] = 'on';
}

// get user's module groups with tester privileges
$dbq = new dbQuery("1253567185", "security.privileges.tester");

$attr = array();
$attr['aid'] = $accountID;
$groups = $dbc->execute($dbq, $attr);

while ($row = $dbc->fetch($groups))
{
	$groupsInfo[$row['id']]['tester'] = TRUE;
	$groupsPost['tester'][$row['id']] = 'on';
}

//__________ SERVER CONTROL __________//
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	//__________ [Page POST Variables] __________//
	// get values from $_POST
	// escape etc...

	$types = array();
	$types['developer']['grant'] = "184651428";
	$types['developer']['revoke'] = "1513206737";
	$types['developer']['qdomain'] = "security.privileges.developer";
	$types['master']['grant'] = "638584576";
	$types['master']['revoke'] = "1114590665";
	$types['master']['qdomain'] = "security.privileges.developer";
	$types['tester']['grant'] = "1622807987";
	$types['tester']['revoke'] = "1289382855";
	$types['tester']['qdomain'] = "security.privileges.tester";
	
	foreach ($types as $t => $info)
	{
		if (!isset($groupsPost[$t]))
			$groupsPost[$t] = array();
		if (!isset($_POST['privs'][$t]))
			$_POST['privs'][$t] = array();
	}
	
	$grant = array();
	$revoke = array();
	// Check what needs to be updated
	foreach ($groupsPost as $type => $privileges)
	{	
		$grant[$type] = array_diff_assoc($_POST['privs'][$type], $privileges);
		$revoke[$type] = array_diff_assoc($privileges, $_POST['privs'][$type]);
	}
	
	// For developer, master, and tester
	foreach ($types as $type => $qinfo)
	{			
		foreach ($revoke[$type] as $gid => $value)
		{
			// Revoke access
			$dbq = new dbQuery($qinfo['revoke'], $qinfo['qdomain']);
			
			$attr = array();
			$attr['aid'] = $accountID;
			$attr['gid'] = $gid;
			$result = $dbc->execute($dbq, $attr);
			
			if(is_bool($result) && !$result)
			{
				//error exists
				$formNotification = new formNotification();
				$formNotification->build("error");
				
				// Description
				$message = 'Revoke access Error';
				$formNotification->appendCustomMessage($message);
				
				return $formNotification->getReport(FALSE);		
			}
		}
		foreach ($grant[$type] as $gid => $value)
		{	
			// Grant access
			$dbq = new dbQuery($qinfo['grant'], $qinfo['qdomain']);
			
			$attr = array();
			$attr['aid'] = $accountID;
			$attr['gid'] = $gid;
			$result = $dbc->execute($dbq, $attr);
			
			// Check For error In Query execution
			if(is_bool($result) && !$result)
			{
				//error exists
				$formNotification = new formNotification();
				$formNotification->build("error");
				
				// Description
				//$message = $formNotification->getMessage("error", "err.save_error");
				$message = 'Grant access Error';
				$formNotification->appendCustomMessage($message);
				
				return $formNotification->getReport(FALSE);		
			}
		}
	}
	
	$successNotification = new formNotification();
	$successNotification->build("success");
	
	// Description
	$message= $successNotification->getMessage( "success", "success.save_success");
	$successNotification->appendCustomMessage($message);
	
	return $successNotification->getReport(FALSE);
}

// Create Module Page
$HTMLContentBuilder = new HTMLContent(); 

$simpleForm = new simpleForm();
$devPrivilegesForm = $simpleForm->build($moduleID, "privilegesInfo", $controls = TRUE);

// Appent form to content
$globalContainer = $HTMLContentBuilder->buildElement($simpleForm->get())->get();
 
// userId (Hidden)
$input = $simpleForm->getInput("hidden", "aid", $accountID, $class = "", $autofocus = FALSE);
$simpleForm->append($input);

// Form Header
$headerContent = moduleLiteral::get($moduleID, "lbl_devInfo");
$header = DOM::create('h2', "", "", "lhd hd2");
DOM::append($header, $headerContent);
$simpleForm->append($header);

$formContentWrapper = DOM::create("div", "", "", "devPrivilegesFormContents");
$simpleForm->append($formContentWrapper);

// Create grid
$dtGridList = new dataGridList();
$glist = $dtGridList->build("", FALSE)->get();
DOM::append($formContentWrapper, $glist);

$headers = array();
$headers[] = "Programmer";
$headers[] = "Master Programmer";
$headers[] = "Tester";
$headers[] = "Group ID";
$headers[] = "Description";

$dtGridList->setHeaders($headers);

// Present Info
foreach ($groupsInfo as $id => $group)
{
	$gridRow = array();
	
	// Create Checkboxes
	// ___ developer	
	$developer = $simpleForm->getInput($type = "checkbox", $name = 'privs[developer]['.$id.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($group['developer']))
	{
		DOM::attr($developer, "checked", "checked");
	}
	$gridRow[] = $developer;
	
	
	// ___ master	
	$master = $simpleForm->getInput($type = "checkbox", $name = 'privs[master]['.$id.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($group['master']))
	{
		DOM::attr($master, "checked", "checked");
	}
	$gridRow[] = $master;
	
	
	// ___ tester	
	$tester = $simpleForm->getInput($type = "checkbox", $name = 'privs[tester]['.$id.']', $value = "", $class = "", $autofocus = FALSE);
	if (isset($group['tester']))
	{
		DOM::attr($tester, "checked", "checked");
	}
	$gridRow[] = $tester;
	
	// ID
	$gridRow[] = DOM::create("span",  strval($id));
	
	// Description
	$gridRow[] = DOM::create("span", $group['description']);
		
	$dtGridList->insertRow($gridRow);
}
// Return output
return $HTMLContentBuilder->getReport();
//#section_end#
?>