	<?php
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';


	$FiveHours = strtotime(date("Y-m-d H:i:s", strtotime("-5 hours")));

	// echo $FiveHours;
	// $selectPrioritizedOrders = ExecCustomQuery("SELECT * FROM `".ORDER."` where AssignedTo = '16'");
	$selectPrioritizedOrders = ExecCustomQuery("SELECT * FROM `".ORDER."` where OrderStatus = '1'");
	if (!empty($selectPrioritizedOrders)) {
		// print_r(count($selectPrioritizedOrders));
		foreach($selectPrioritizedOrders as $single){
			$timerDelHours = 0;
			if (!empty($single['customeProductsFileds'])) {
					$customeProductsFiledsTimer = multidimation_to_single_array(unserialize($single['customeProductsFileds']));
					$timerDelHours = $customeProductsFiledsTimer['turnaround_time'];
			} else {
					$timerDelHours = $single['TurnAroundTime'];
			}
			$AssignOn = $single['AssignedOn'];
			$finishTime = strtotime('+ '.$timerDelHours.' day', $AssignOn);
			$reminder = strtotime('- 5 hours', $finishTime);
			// if (date("Y-m-d H", $reminder) == date("Y-m-d H") ) {
			if (true ) {
				$customerId = $single['AssignedTo'];
				$q = "SELECT * FROM `tbl_users` WHERE `UserID` = '$customerId'";
				$res = mysql_query($q);

				$user = mysql_fetch_array($res);

				$orderId = $single['Id'];
				// $dateTime = date('Y-m-d h:i:s');
				$dateTime = $reminder;
				$subject = 'Priore email notification';
				$content = 'this is content';
				// $to = $user['Email'];
				$to = 'kapil@webspero.com';

				// $query = "INSERT INTO `tbl_mail` SET `Dest` = '$to', `Subject` = '$subject', `Content` = '$content', `DateTime` = '$dateTime', `Sent` = 0, `OrderId` = $orderId";
				// $result = mysql_query($query);
				$from = 'stsveamillion@gmail.com';
				$headers .= 'From: '.$from."\r\n".
			    'X-Mailer: PHP/' . phpversion();
					
				if(mail($to, $subject, $content, $headers)){
						$query = "INSERT INTO `tbl_mail` SET `Dest` = '$to', `Subject` = '$subject', `Content` = '$content', `DateTime` = '$dateTime', `Sent` = 1,`OrderId` = '$orderId'";
						
						if (mysql_query($query))
				    echo "Your mail has been sent successfully .";

						echo "test fail";
				} else{
						$query = "INSERT INTO `tbl_mail` SET `Dest` = '$to', `Subject` = '$subject', `Content` = '$content', `DateTime` = '$dateTime', `Sent` = 0,`OrderId` = '$orderId'";
						if (mysql_query($query))
				    echo 'Unable to send email. Please try again.';
				}
				die();

				// // push to database for sending later (asynchronous)
			}
			// print_r([$single['TransactionID'], $timerDelHours,date("Y-m-d H:i:s", $AssignOn),date("Y-m-d H:i:s", $finishTime), date("Y-m-d H:i:s", $reminder)]);
		}
	}

	?>
	<script>
	var global = {
		schedule:{
		workspace:"hi"
		}
	};
console.log(global.schedule[workspace]);
		console.log('test cron');

	</script>
