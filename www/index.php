<?php  

/**
* Reads the config file and grabs the x10 modules for a specific user
 *
 * @param username $username Username of the current user
 * @return Modules the current user has access too
 */   
function getModules($userName) 
{
  $xml = new DOMDocument(); 
  $xml->load('conf/'.$userName.'.xml'); 
  $modules = array();
  foreach($xml->getElementsByTagName('module') as $module)
  { 
    $modules[$module->getAttribute('name')]= array(
      'name' => $module->getAttribute('name'),
      'house' => $module->getAttribute('house'),
      'unit' => $module->getAttribute('unit'),
      'type' => $module->getAttribute('type'));
  }
  return $modules;
}  

/**
 * Get the HTML links for the modules the current user has access too
 *
 * @param username $username Username of the current user
 * @return HTML links of modules the current user has access too
 */     
function getModulesHTML($userName)
{
  $modules = getModules($userName);
  $moduleHTML="";
  
  foreach($modules as $module)
  {
    $moduleHTML .= '<a href="/?userName='.$userName.'&moduleName='.$module['name'].'" data-role="button">'.$module['name'].'</a>';
    $count = $count + 1;
  }

  return $moduleHTML; 

}  
  
  
/**
 * Get the HTML action links for the active module
 *
 * @param username $username Username of the current user
 * @param moduleName Name of the active module
 * @return HTML links of the active module
 */       
function getModuleActionHTML($userName,$moduleName,$count=0)    
{
  $modules = getModules($userName);
  
  $module = $modules[$moduleName];
  
  $moduleHTML="";
  $count +=1;
  
  $moduleHTML .= '<a href="/?userName='.$userName.'&moduleName='.$module['name'].'&house='.$module['house'].'&unit='.$module['unit'].'&type='.$module['type'].'&action=ON&count='.$count.'" data-role="button">ON</a>';
  
  if($module['type']=='light')
  {
    $moduleHTML .= '<a href="/?userName='.$userName.'&moduleName='.$module['name'].'&house='.$module['house'].'&unit='.$module['unit'].'&type='.$module['type'].'&action=DIM&count='.$count.'" data-role="button">DIM</a>';
    
    $moduleHTML .= '<a href="/?userName='.$userName.'&moduleName='.$module['name'].'&house='.$module['house'].'&unit='.$module['unit'].'&type='.$module['type'].'&action=BRIGHT&count='.$count.'" data-role="button">BRIGHT</a>';
  }
  if($module['type']!='button')
  {
    $moduleHTML .= '<a href="/?userName='.$userName.'&moduleName='.$module['name'].'&house='.$module['house'].'&unit='.$module['unit'].'&type='.$module['type'].'&action=OFF&count='.$count.'" data-role="button">OFF</a>';
  }

  return $moduleHTML;
}

  
/**
 * Get the HTML header links for active module
 *
 * @param username $username Username of the current user
 * @param moduleName Name of the active module
 * @return HTML header for the active module
 */         
function getHeaderHTML($userName, $moduleName)
{
  $headerHTML = '<a href="/?userName='.$userName.'" data-icon="arrow-l">Back</a>';
  $headerHTML .= '<h1>'.$moduleName.'</h1>';
  return $headerHTML;
}
  
  
/**
 * Writes the values through the serial port to the arduino where
 * the arduino handles the writing to the x10 modules
 *
 * @param letter $letter Letter value of the x10 module
 * @param unit $unit Unit number of the x10 module
 * @param action $action Action that needs to be sent to the x10 module
 * @return Nothing
 */     
function sendx10Command($house, $unit, $action)
{

  //
  // Default Settings for writing to serial
  //
  $serialPath = "/dev/ttyUSB0"; 
  $houseVal = $house;
  $unitVal = "A";
  $actionVal = "OFF";
  
  //
  // Get the asscii value that needs to be written to serial in order for the
  // arduino to control the proper x10 module
  //
  switch ($unit)
  {
    case 1:
      $unitVal="A";
      break;
    case 2:
      $unitVal="B";
      break;
    case 3:
      $unitVal="C";
      break;
    case 4:
      $unitVal="D";
      break;
    case 5:
      $unitVal="E";
      break;
    case 6:
      $unitVal="F";
      break;
    case 7:
      $unitVal="G";
      break;
    case 8:
      $unitVal="H";
      break;
    case 9:
      $unitVal="I";
      break;
    case 10:
      $unitVal="J";
      break;
    case 11:
      $unitVal="K";
      break;
    case 12:
      $unitVal="L";
      break;
    case 12:
      $unitVal="M";
      break;
    case 13:
      $unitVal="N";
      break;
    case 14:
      $unitVal="O";
      break;
    case 15:
      $unitVal="P";
      break;
    case 16:
      $unitVal="Q";
      break;
    default:
      $unitVal="A";
      break;
  }

  //
  // Get the asscii value that needs to be written to serial in order for the
  // arduino send the proper command to the x10 module
  //
  switch ($action) 
  {
    case "ON":
      $actionVal="q";
      break;
    case "OFF":
      $actionVal="r";
      break;
    case "DIM":
      $actionVal="s";
      break;
    case "BRIGHT":
      $actionVal="t";
      break;
    default:
      $actionVal="r";
  }

  $fp =fopen($serialPath, "w");
  fwrite($fp, $houseVal);
  fwrite($fp, $unitVal);
  fwrite($fp, $actionVal);
  fclose($fp);
}
  
//
// Default Values
//
  
$siteTitle="House Control";  
  
$modules=array();
$content="";

  
//
// If the username is set proceed with getting content and allowing x10
// commands to be sent over serial
//
if(isset($_REQUEST['userName']))
{
  
  $userName = $_REQUEST['userName'];
  //
  // If all of the variables required to send a x10 request are present proceed
  //

  $house ="";
  $unit="";
  $action="";

  $house .= $_REQUEST['house'];
  $unit .= $_REQUEST['unit'];
  $action .= $_REQUEST['action'];


  if($house !="" && $unit !="" && $action !="")
  {
    // Send the x10 command
    sendx10Command($house,$unit,$action);
    echo "HERE";
  }

  //
  // Get the content for controlling the x10 moudles from the app
  //
  if(!isset($_REQUEST['moduleName']))
  {
    //
    // Set the header html
    //
    $header = "<h1>".$siteTitle."</h1>";
    
    $content = getModulesHTML($userName);
  }
  else
  { 
    $count = "";
  	$count .= $_REQUEST['count'];
    if($count == ""){$count = 0;}


    $moduleName = $_REQUEST['moduleName'];
    
    //
    // Set the page title based on module name
    //
    $siteTitle = $moduleName." | ".$siteTitle;
    
    //
    // Set the header html for a specific module
    //
    $header = getHeaderHTML($userName,$moduleName);
    
    //
    // get the action buttons for a specific module
    //
    $content = getModuleActionHTML($userName,$moduleName,$count);  
  }
}  

  
  
  
  
  
  
?>

<!DOCTYPE html> 
<html> 
	<head> 
<title><?php echo $siteTitle;?></title> 
	
	<meta name="viewport" content="width=device-width, maximum-scale=1"> 

  <link rel="apple-touch-icon" href="house-icon.png"/>
  <meta name="apple-mobile-web-app-capable" content="yes" />

	<link rel="stylesheet" href="jquery.mobile-1.0b2/jquery.mobile-1.0b2.css" />
	<script type="text/javascript" src="jquery.mobile-1.0b2/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="jquery.mobile-1.0b2/jquery.mobile-1.0b2.min.js"></script>
<script>
 	$(document).ready(function() {
  	// disable ajax nav
  	$.mobile.ajaxLinksEnabled = false;
 	});
	</script>
</head> 
<body onload="initialize()">

<div data-role="page">

	<div data-role="header" data-theme="e">
    <?php echo $header;?>
	</div>

  <div data-role="content">
    <?php echo $content;?>
	</div>

  
</div>
</body>
</html>
