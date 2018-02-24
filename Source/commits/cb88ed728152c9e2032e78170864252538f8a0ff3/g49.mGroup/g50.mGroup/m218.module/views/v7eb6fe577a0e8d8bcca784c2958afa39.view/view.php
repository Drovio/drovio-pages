<?php
//#section#[header]
// Module Declaration
$moduleID = 218;

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
importer::import("API", "Literals");
importer::import("UI", "Presentation");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Developer\resources\documentation\classDocumentor;
use \API\Literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\togglers\toggler;
use \UI\Presentation\dataGridList;
use \UI\Presentation\notification;


// Get object information
$objectDomain = $_GET['domain'];
$library = $_GET['lib'];
$package = $_GET['pkg'];
$namespace = $_GET['ns'];
$objectName = $_GET['oid'];

// Normalize
$namespace = str_replace("_", "/", $namespace);
$namespace = str_replace("::", "/", $namespace);
$objectPath = "/".$library."/".$package."/".$namespace;

// Initialize data
$manualFilePath = "/System/Resources/Documentation/".$objectDomain."/".$objectPath."/".$objectName.".php.xml";
$classMan = new classDocumentor();

// Build content
$content = new HTMLContent();
$container = $content->build("", "sdkManual", TRUE)->get();

// Load manual
try
{
	$classMan->loadFile($manualFilePath);
}
catch (Exception $ex)
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build("error", $header = TRUE, $footer = FALSE, $timeout = FALSE);
	
	// Add header
	$header = DOM::create("h2", "Documentation Error.");
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", "The class documentation ////file not found.");
	$ntf->append($message);
	
	// Add notification to description
	$errorNtf = $ntf->get();
	$pageContent->append($errorNtf);
	
	// Exception content
	return $content->getReport("#docViewer");
}


// Get Class Info
$classInfo = $classMan->getInfo();

// Class Name
$classNameWrapper = HTML::select(".className")->item(0);
$span = DOM::create("span", $objectName);
DOM::append($classNameWrapper, $span);

$classTitleWrapper = HTML::select(".classTitle")->item(0);
$span = DOM::create("span", $classInfo['title']);
DOM::append($classTitleWrapper, $span);

$classDescWrapper = HTML::select(".classDescription")->item(0);
$span = DOM::create("span", $classInfo['description']);
DOM::append($classDescWrapper, $span);
DOM::append($paragraph, $classDesc);

// Class Info
$version = DOM::create("span", (empty($classInfo['version']) ? "0.0" : $classInfo['version'])." (V)");
$box = HTML::select(".boxInfo .version")->item(0);
DOM::append($box, $version);

$date = DOM::create("span", date("F j, Y, G:i (T)", $classInfo['datecreated'])." (C)");
$box = HTML::select(".boxInfo .created")->item(0);
DOM::append($box, $date);

$date = DOM::create("span", date("F j, Y, G:i (T)", $classInfo['daterevised'])." (C)");
$box = HTML::select(".boxInfo .modified")->item(0);
DOM::append($box, $date);

// Class extends
if (!empty($classInfo['extends'])) 
{ 
	$wrapper = HTML::select(".classExtends")->item(0);
	$title = DOM::create("span", "extends: ".$classInfo['extends']);
	DOM::append($wrapper, $title);
}
// Class extends
if (!empty($classInfo['implements'])) 
{ 
	$wrapper = HTML::select(".classImplements")->item(0);
	$title = DOM::create("span", "implements: ".$classInfo['implements']);
	DOM::append($wrapper, $title);
}

// Class throws
if (!empty($classInfo['throws'])) 
{ 
	$throws = DOM::create("div", "", "", "classThrows section");
	DOM::append($classInfo, $throws);
	
	$title = DOM::create("span", "Throws: ");
	DOM::append($throws, $title);
	
	$text = DOM::create("span", $info['throws'], "", "");
	DOM::append($throws, $text);
}

if (!empty($classInfo['deprecated']))
{
	// Create deprecated notification
	$ntf = new notification();
	$depNtf = $ntf->build("warning", $header = TRUE, $footer = FALSE, $timeout = FALSE);
	
	// Add header
	$header = DOM::create("h2", "The class is deprecated.");
	$ntf->append($header);
	
	// Add message
	$message = DOM::create("p", $classInfo['deprecated']);
	$ntf->append($message);
	
	// Add notification to description
	$container = HTML::select(".classDeclContainer")->item(0);
	$depNtf = $ntf->get();
	DOM::append($container, $depNtf);
}

// Class Synopsis
$title = moduleLiteral::get($moduleID, "lbl_man_classSynopsis");
$header = HTML::select(".synopsis .header")->item(0);
DOM::append($header, $title);
		
		
// Get constant list
$classConstants = $classMan->getConstants();
if (count($classConstants) > 0)
{
	// Add header
	$header = HTML::select(".constantsContainer .subHeader")->item(0);
	$title = moduleLiteral::get($moduleID, "lbl_constantsHeader");
	DOM::append($header, $title);
	
	$gridList = new dataGridList();
	$gridList->build();
	
	$ratios = array();
	$ratios[] = 0.1;
	$ratios[] = 0.3;
	$ratios[] = 0.6;
	$gridList->setColumnRatios($ratios);
	
	$headers = array();
	$headers[] = "Type";
	$headers[] = "Name";
	$headers[] = "Description";
	$gridList->setHeaders($headers);
	
	// __Constants
	foreach ($classConstants as $constant)
	{
		$contents = array();	
		
		// Type
		$title = DOM::create("span", $constant['type'], "", "cType modifier");
		$contents[] = $title;
		
		// Name
		$title = DOM::create("span", $constant['name'], "", "cName token");
		$contents[] = $title;
		
		// Description
		$desc = DOM::create("span", $constant['description'], "", "cDesc");
		$contents[] = $desc;
		
		$gridList->insertRow($contents);	
	}
	
	// Append gridList
	$container = HTML::select(".constantsContainer .sectionBody")->item(0);
	$constantList = $gridList->get();
	DOM::append($container, $constantList);
}

// Get properties list
$allClassProperties = $classMan->getProperties();

// Get properties not private
$classProperties = array();
foreach ($allClassProperties as $scope => $properties)
	if ($scope != "private")
		$classProperties[$scope] = $properties;

if (count($classProperties) > 0)
{
	// Add header
	$header = HTML::select(".propertiesContainer .subHeader")->item(0);
	$title = moduleLiteral::get($moduleID, "lbl_propertiesHeader");
	DOM::append($header, $title);
	
	$gridList = new dataGridList();
	$gridList->build();
	
	$ratios = array();
	$ratios[] = 0.1;
	$ratios[] = 0.1;
	$ratios[] = 0.3;
	$ratios[] = 0.5;
	$gridList->setColumnRatios($ratios);
	
	$headers = array();
	$headers[] = "Scope";
	$headers[] = "Type";
	$headers[] = "Name";
	$headers[] = "Description";
	$gridList->setHeaders($headers);
	
	// List properties
	foreach ($classProperties as $scope => $properties)
		foreach ($properties as $propName => $propInfo) 
		{
			$contents = array();
			
			// Scope
			$element = DOM::create("span", $scope, "", "pScope modifier");
			$contents[] = $element;
			
			// Type
			$element = DOM::create("span", $propInfo['type'], "", "pType modifier");
			$contents[] = $element;
			
			// Name
			$element =  DOM::create("span", $propInfo['name'], "", "pName token");
			$contents[] = $element;
			
			// Description
			$element = DOM::create("span", $propInfo['description'], "", "pDesc");
			$contents[] = $element;
			
			$gridList->insertRow($contents);
		}
	
	// Append gridList
	$container = HTML::select(".propertiesContainer .sectionBody")->item(0);
	$constantList = $gridList->get();
	DOM::append($container, $constantList);
}


// Get class methods
$allClassMethods = $classMan->getMethods();

// Get methods not private
$classMethods = array();
foreach ($allClassMethods as $scope => $methods)
	if ($scope != "private")
		$classMethods[$scope] = $methods;

if (count($classMethods) > 0)
{
	// Add header
	$header = HTML::select(".methodsContainer .subHeader")->item(0);
	$title = moduleLiteral::get($moduleID, "lbl_methodsHeader");
	DOM::append($header, $title);
	
	foreach ($classMethods as $scope => $methods)
		foreach ($methods as $methodName => $methodInfo)
		{
			// Build toggler
			$toggler = new toggler();
			$methodHead = DOM::create("div", "", "", "mHead");
			$methodBody = DOM::create("div", "", "", "mBody");
			
			$methodDeclaration = getMethodDeclaration($scope, $methodInfo);
			DOM::append($methodHead, $methodDeclaration);	
			
			
			if (!empty($methodInfo['deprecated']))
			{
				// Create deprecated notification
				$ntf = new notification();
				$depNtf = $ntf->build("warning", $header = TRUE, $footer = FALSE, $timeout = FALSE);
				
				// Add header
				$header = DOM::create("h2", "This method is deprecated.");
				$ntf->append($header);
				
				// Add message
				$message = DOM::create("p", $methodInfo['deprecated']);
				$ntf->append($message);
				
				// Add notification to method body
				$depNtf = $ntf->get();
				DOM::append($methodBody, $depNtf);
			}
			
			// Method Description
			$title = moduleLiteral::get($moduleID, "lbl_methodDescription");
			$header = DOM::create("h4", $title, "", "mh");
			DOM::append($methodBody, $header);
			
			$text = DOM::create("p", $methodInfo['description'], "", "pre");
			DOM::append($methodBody , $text);
			
			if (array_key_exists('parameters', $methodInfo))
			{
				$title = moduleLiteral::get($moduleID, "lbl_methodParameters");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				// Create parameters description
				$paramDescriptions = DOM::create("div", "", "", "parameters");
				DOM::append($methodBody, $paramDescriptions);
			
				foreach ($methodInfo['parameters'] as $pName => $paramInfo)
				{
					$paramElement = DOM::create("div", "", "", "parameter");
					DOM::append($paramDescriptions, $paramElement);
					
					$paramName = DOM::create("div", $paramInfo['type'], "", "paramName modifier");
					DOM::append($paramElement, $paramName);
					
					$paramName = DOM::create("div", $pName, "", "paramType modifier");
					DOM::append($paramElement, $paramName);
					
					$paramDesc = DOM::create("div", $paramInfo['description'], "", "paramDesc");
					DOM::append($paramElement, $paramDesc);
				}
			}
			
			// Return and Throws
			if (!empty($methodInfo['returndescription']))
			{ 
				$title = moduleLiteral::get($moduleID, "lbl_methodReturns");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				$text = DOM::create("p", $methodInfo['returndescription'], "", "pre");
				DOM::append($methodBody , $text);
			}
			if (!empty($methodInfo['throws']))
			{ 			
				$title = moduleLiteral::get($moduleID, "lbl_methodThrows");
				$header = DOM::create("h4", $title, "", "mh");
				DOM::append($methodBody, $header);
				
				$text = DOM::create("p", $methodInfo['throws'], "", "pre");
				DOM::append($methodBody, $text);
			}
							
			$toggler->build($id = "", $methodHead, $methodBody, FALSE);
			
			// Append gridList
			$container = HTML::select(".methodsContainer .sectionBody")->item(0);
			$methodTog = $toggler->get();
			DOM::append($container, $methodTog);
		}
}

function getMethodDeclaration($scope, $method)
{
	$methodDeclaration = DOM::create("div", "", "", "methodDeclaration decline");
	
	if (!empty($method['deprecated']))
	{
		$deprecationIdentifier = DOM::create("span", "{Deprecated}", "", "mDepr modifier");
		DOM::append($methodDeclaration, $deprecationIdentifier);
	}
	
	$scopeElement = DOM::create("span", $scope, "", "mScope modifier");
	DOM::append($methodDeclaration, $scopeElement);
	
	$typeElement = DOM::create("span", $method['returntype'], "", "mType modifier");
	DOM::append($methodDeclaration, $typeElement);
	
	$nameElement = DOM::create("span", $method['name'], "", "mName token");
	DOM::append($methodDeclaration, $nameElement);
	
	$parenthesisStart = DOM::create("span", " (", "", "modifier");
	DOM::append($methodDeclaration, $parenthesisStart);
	
	$counter = 0;
	if (array_key_exists('parameters', $method))
		foreach ($method['parameters'] as $pName => $paramInfo)
		{
			$paramElement = DOM::create("span", "", "", "parameter");
			DOM::append($methodDeclaration, $paramElement);
			
			$paramType = DOM::create("span", ($counter != 0 ? ", " : "").$paramInfo['type'], "", "paramType modifier");
			DOM::append($paramElement, $paramType);
			
			$paramName = DOM::create("span", $pName, "", "paramName token");
			DOM::append($paramElement, $paramName);
			$counter++;
		}
	
	$parenthesisEnd = DOM::create("span", ")", "", "modifier");
	DOM::append($methodDeclaration, $parenthesisEnd);

	return $methodDeclaration;
}


// Changelog
$title = moduleLiteral::get($moduleID, "lbl_man_changelog");
$header = HTML::select(".changelog .header")->item(0);
DOM::append($header, $title);


// Return the report
return $content->getReport("#docViewer");
//#section_end#
?>