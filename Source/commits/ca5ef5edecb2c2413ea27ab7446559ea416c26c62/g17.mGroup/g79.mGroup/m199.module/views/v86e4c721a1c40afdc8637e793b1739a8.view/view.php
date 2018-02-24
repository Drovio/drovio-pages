<?php
//#section#[header]
// Module Declaration
$moduleID = 199;

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
importer::import("API", "Developer");
importer::import("API", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Html");
importer::import("DEV", "Projects");
//#section_end#
//#section#[code]
use \API\Developer\ebuilder\wsManager;
use \API\Resources\literals\moduleLiteral;
use \API\Resources\storage\session;
use \DEV\Projects\projectCategory;
use \UI\Html\HTMLContent;
use \UI\Forms\formReport\formErrorNotification;
use \UI\Forms\formReport\formNotification;

// Step need to be ordered
$steps = array();
$steps['1'] = TRUE;
$steps['2'] = FALSE;
$steps['3'] = TRUE;

// Holder selector
$holder = '.contentHolder';

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
	$HTMLContent = new HTMLContent();
	$nextStep = $_POST['step'];
	// Find current position in the array
	while(key($steps) != $nextStep)
	{
		next($steps);
	}
	$skipCount = 0;
	while($steps[$nextStep] == FALSE)
	{
		next($steps);
		$nextStep = key($steps);
		$skipCount++;
		
		
	}
	if($skipCount != 0)
	{
		// Return Skip
		$HTMLContent->addReportAction('step.skip', $skipCount);
	}
	
	switch ($nextStep)
	{ 
		case '1':	
			/* Need to move the creation script in another module*/	
			$has_error = FALSE;
			$formErrorNotification = new formErrorNotification();
			$formErrorNotification->build();
			
			// Check templateName
			$empty = (is_null($_POST['wsTitle']) || empty($_POST['wsTitle']));
			if ($empty)
			{
				$has_error = TRUE;
						
				// Header
				$header = moduleLiteral::get($moduleID, "lbl_wsTitle");
				$headerId = 'wsTitle'.'ErrorHeader';
				$err_header = $formErrorNotification->addErrorHeader($headerId, $header);
				// Description
				$description = "err.required";
				$descriptionId = 'wsTitle'.'ErrorDescription';
				$formErrorNotification->addErrorDescription($err_header, $descriptionId, $description, $extra = "");
			}
			
			// If error, show notification
			if ($has_error)
			{	
				return $formErrorNotification->getReport();
			}
			
			//No parametres error -> Continue
			$wsName = $_POST['wsName'];
			$wsTitle = $_POST['wsTitle'];
			$wsDescription = $_POST['wsDescription'];
			 
			$wsManager = new wsManager();
			
			//Try to create new layout
			//$success = $wsManager->create($wsTitle, $wsDescription);
			$success = TRUE;
			// If error, show notification
			if (!$success )
			{
				//On create error	
				$customErrMsg_hd = "";
				$customErrMsg_desc = "Could not create website";
							 		
				// ERROR NOTIFICATION
				$errorNotification = new formNotification();
				$errorNotification->build("error");
				
				// Description
				$message= "AN ERROR OCCURED : ".$customErrMsg_desc; 
				$errorNotification->appendCustomMessage($message);
						
				return $errorNotification->getReport(FALSE);		
			}
			
			// Find a Way to load the module without loading the container.	
			// Return Success
			$HTMLContent->addReportAction('step.success');		
			$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "templateSelector", array(), TRUE, '');
			$HTMLContent->buildElement($pageStructureItem);
			return $HTMLContent->getReport($holder);
			break;
		case '2':
			// Find a Way to load the module without loading the container.
			// Return Success
			$HTMLContent->addReportAction('step.success');	
			$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "extensionSelector", array(), TRUE, '');
			$HTMLContent->buildElement($pageStructureItem);
			return $HTMLContent->getReport($holder);
			break;
		case '3':
			// Find a Way to load the module without loading the container.
			// Return Success
			$HTMLContent->addReportAction('step.success');	
			$pageStructureItem = $HTMLContent->getModuleContainer($moduleID, "serverConfiguration", array(), TRUE, '');
			$HTMLContent->buildElement($pageStructureItem);
	 		return $HTMLContent->getReport($holder);
			break;
		default:
			$HTMLContent = new HTMLContent();
			$HTMLContent->build();
			return $HTMLContent->getReport($holder);
			break;
	} 
}
//#section_end#
?>