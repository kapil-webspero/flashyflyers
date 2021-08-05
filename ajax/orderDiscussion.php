<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';

	$message=trim($_REQUEST['message']);
	$orderid=$_REQUEST['orderid'];
	$userid=$_SESSION['userId'];
	$dt=date('Y-m-d h:i:s');

	$AssignedToid=$_REQUEST['AssignedToid'];

	// echo "js===".$AssignedToid;
	// die();


	$data = "user_id = '$userid',order_id='$orderid',Msg='$message',CurrentDate='$dt'";
	$lastInsertId=InsertRcrdsByData(ORDER_DISCUSSTIONS, $data);

	if($AssignedToid!="")
	{
		$data = "notification_type='OrderComment', user_id = '$AssignedToid',order_id='$orderid',description='New comment',read_flag='no',sale_read_flag='no',admin_read_flag='no',created_at='$dt'";
		InsertRcrdsByData(NOTIFICATIONS2, $data);
	}


	$result120 = mysql_query("SELECT * FROM ".USERS." WHERE UserID='$userid'");
    $showData120 = mysql_fetch_array($result120);
	$name=$showData120['FName'].' '.$showData120['LName'];

	if($lastInsertId!=""){

        $readdate=date("F d, Y h:i:s A", strtotime($dt));
		echo '<div class="container12">
		'.$name.'
		<p>'.$message.'</p>
		<span class="time-right12">'.$readdate.'</span>
		</div>';
		die();
	}
	else
	{
		echo 'SqlError';
		die();

	}
?>