<?php
ob_start();
require_once '../function/constants.php';
require_once '../function/configClass.php';
require_once '../function/siteFunctions.php';
$auto_assign =$_POST['auto_assign'];
$data="`auto_assign` = '".$auto_assign."'";
UpdateRcrdOnCndi( SETTINGS, $data, "id = 1" );
if($auto_assign=='Yes')
{
   $query = "SELECT id FROM ".ORDER." WHERE AssignedTo=0";
    $arr_order=mysql_query($query);
    
    while($getOrder = mysql_fetch_array($arr_order))
    {
        $id=$getOrder["id"];
         $query_user = "SELECT UserID FROM ".USERS." WHERE UserType= 'designer'";
         $fetch_arr=mysql_query($query_user);
        $usersArr = mysql_fetch_array($fetch_arr);
       
        $UserID=  $usersArr[0];
        $data = "AssignedTo = '$UserID',AssignedOn = '$systemTime'";
       
        UpdateRcrdOnCndi( ORDER, $data, "id = '$id'" );
    }
}

echo json_encode('Auto assign updated successfully.');

?>