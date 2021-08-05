<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Users Details";

	if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])) {
		unset($_SESSION['USERID']);
		$_SESSION['USERID'] = intval($_REQUEST['user_id']);
	}
	if(!isset($_SESSION['USERID']) && empty($_SESSION['USERID'])) {
		echo "<script> window.location.href = '".ADMINURL."users.php';</script>";
		exit();
	}
	$UserID = intval($_SESSION['USERID']);
	if(isset($_REQUEST['banUser']) && !empty($_REQUEST['banUser'])) {
		if($_REQUEST['banUser'] == "yes") {
			UpdateRcrdOnCndi(USERS,"`Status` = 'inactive'", "UserID = '$UserID'");
			$_SESSION['SUCCESS'] = "User successfully banned from login and other activities.";
		} elseif($_REQUEST['banUser'] == "no") {
			UpdateRcrdOnCndi(USERS,"`Status` = 'active'", "UserID = '$UserID'");
			$_SESSION['SUCCESS'] = "User successfully un-banned.";
		}
		echo "<script> window.location.href = '".ADMINURL."user-details.php';</script>";
		exit();
	}
	$userData = GetSglRcrdOnCndi(USERS, "`UserID` = '$UserID'");
	
	if(isset($_REQUEST['sendMail']) && !empty($_REQUEST['sendMail'])) {
		$userEmail = $userData['Email'];
		$messageD = $_POST['message'];
		$curl = curl_init();
		// Set some options - we are passing in a useragent too here
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => SITEURL.'EmailTemplate/contactMail.php?UID='.$userData['UserID']."&messageD=".$messageD,
			CURLOPT_USERAGENT => 'Curl Test'
		));
		// Send the request & save response to $resp
		$resp = curl_exec($curl);
		// Close request to clear up some resources
		curl_close($curl);
	
		$status = 'success';
		$Message = "We have sent password reset instructions to your email.";
		
	}	
	if(isset($_REQUEST['updatepassword'])) {
		$newpass = $_REQUEST['newpass'];
		$confirmpass = $_REQUEST['confirmpass'];
		if(strlen($newpass)>6) {
			if($newpass == $confirmpass) {
				UpdateRcrdOnCndi(USERS, "`Password` = '".base64_encode($newpass)."'", "UserID = '$UserID'");
				$_SESSION['SUCCESS'] = "Password successfully updated.";
			} else {
				$_SESSION['ERROR'] = "New password and confirm password does not match.";
			}
		} else {
			$_SESSION['ERROR'] = "Password should be atleast 7 characters";	
		}
		echo "<script> window.location.href = '".ADMINURL."user-details.php';</script>";
		exit();
	}
	
	if(isset($_REQUEST['updateprofile'])) {
		extract($_POST);

		$name = explode(' ',$name);
        $fname = $name[0];
        $lname = !empty( $name[1] ) ? $name[1] : '';

		$data = "FName = '$fname', LName = '$lname', Address= '$address', State= '$State',City= '$city',Zip= '$Zip',Email= '$email'";
		UpdateRcrdOnCndi(USERS, $data, "UserID = '$UserID'");
		$_SESSION['SUCCESS'] = "Profile data successfully updated";
		echo "<script> window.location.href = '".ADMINURL."user-details.php';</script>";
		exit();
	}
	$userData = GetSglRcrdOnCndi(USERS, "`UserID` = '$UserID'");	
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
    <?php if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])) { ?>
    <div class="notification error">
        <div class="d-flex"><i class="fas fa-times-circle"></i></div>
        <span>Error: <?=$_SESSION['ERROR'];?></span><button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['ERROR']); } if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { ?>	
    <div class="notification success">
        <div class="d-flex"><i class="fas fa-check"></i></div>
        <span><?=$_SESSION['SUCCESS'];?></span>
        <button class="close-ntf"><i class="fas fa-times"></i></button>
    </div>
    <?php unset($_SESSION['SUCCESS']); } ?>
    <main class="main-content-wrap">
        <div class="container">
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4"><?=$userData['FName']." ".$userData['LName'];?></h1>

                <div class="row pt-4 pb-5">
                    <div class="col-lg-6 pl-lg-5 pr-lg-5 udt udt-brd-right">
                        <h2 class="blue text-center mb-4">Contact details</h2>
                        <div class="pl-sm-4 pr-sm-4">
                            <p><span class="blue"><strong><?=$userData['FName']." ".$userData['LName'];?></strong></span> <br> <?=$userData['Email'];?> <br><?=$userData['Phone'];?></p>
                            <p><span class="blue"><strong>Address:</strong></span> <br>
                            <?php 
							$addressF = "";
							if(!empty($userData['Address'])) { 
								$addressF .= $userData['Address'];
							}
							if(!empty($userData['City'])) { 
								$addressF .= ", ".$userData['City'];
							}
							if(!empty($userData['State'])) {
								$addressF .= ", ".$userData['State'];
							}
							if(!empty($userData['Zip'])) {
								$addressF .= ", ".$userData['Zip'];
							}
							echo ltrim($addressF,",");
							?></p>
                            <div class="w-50">
                                    <button type="button" class="form-btn-grad btn-lg btn-block mb-2" data-toggle="modal" data-target="#contactuser">Contact user</button>
                                    <?php if($userData['Status'] == "active") { ?>
                                    <a href="?banUser=yes&userID=<?=$UserID;?>" class="btn btn-primary btn-lg btn-block" onClick="return confirm('Are you sure you want to perform this action?');">Ban user</a>
                                    <?php } else { ?>
                                    <a href="?banUser=no&userID=<?=$UserID;?>" class="btn btn-primary btn-lg btn-block" onClick="return confirm('Are you sure you want to perform this action?');">Un-Ban user</a>
                                    <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 pl-lg-5 pr-lg-5 udt">
                        <h2 class="blue text-center mb-4">Account</h2>
                        <p><span class="blue"><strong>Joined on:</strong></span> <br> <?=date('M d, Y',$userData['CreatedDate']);?></p>
                        <p><span class="blue"><strong>Account#:</strong></span> <br>  <?=$userData['UserID'];?></p>
                        <p><span class="blue"><strong>Transactions#:</strong></span> <br> <?=GetNumOfRcrdsOnCndi(TRANSACTION,"`UserID` = '".$UserID."'");?></p>
                        <p><span class="blue"><strong>Purchases#:</strong></span> <br> <span id="payment-total">$<?=GetSumOnCndi(TRANSACTION,"Amount", "UserID=".$UserID);?></span></p>
                        <a href="<?=ADMINURL?>transactions.php?order_id=&order_date=&customer_email=<?=$userData['Email']?>&customer_name=&product_name=&product_type=all&product_status=all" class="form-btn-grad btn-lg btn-block w-75">View transactions</a>
                    </div>
                </div>

                <hr>
                
                <div class="row pt-5">
                    <div class="col-lg-6 pl-lg-5 pr-lg-5 udt-brd-right mb-4">
                        <h2 class="blue text-center mb-4">Change password</h2>

                        <form method="post" name="updatepasswordform" class="udt-form">
                            <input type="password" name="newpass" class="form-control mb-2" placeholder="New password" required>
                            <input type="password" name="confirmpass" class="form-control mb-4" placeholder="Confirm password" required min="7">
                            <button type="submit" name="updatepassword" class="form-btn-grad btn-lg w-50">Update</button>
                        </form>
                    </div>
                    <div class="col-lg-6 pl-lg-5 pr-lg-5 mb-4">
                            <h2 class="blue text-center mb-4">Contact info</h2>
                            <form method="post" name="updateprofileform" class="udt-form">
                                <?php $userName = !empty($userData['LName']) ? $userData['FName']." ".$userData['LName'] : $userData['FName']; ?>
                                <input type="text" name="name" class="form-control mb-2" placeholder="Name" value="<?=$userName;?>" required>
                                <input type="text" name="address" class="form-control mb-2" placeholder="Address" value="<?=$userData['Address'];?>">
                                <input type="text" name="city" class="form-control mb-2" placeholder="City" value="<?=$userData['City'];?>">
                                <select class="form-control mb-2" name="State">
                                	<option value="">Select State</option>
                                    <?php
										foreach($statesarr as $key=>$val) { ?>
                                        	<option value="<?=$val?>" <?=($userData['State']==$val) ? 'selected':''?>><?=$val?></option>
                                    	<?php } ?>
                                </select>
                                <input type="text" name="Zip" class="form-control mb-2" placeholder="Zip Code" value="<?=$userData['Zip'];?>">
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email" value="<?=$userData['Email'];?>" required>
                                <button type="submit" name="updateprofile" class="form-btn-grad btn-lg w-50">Update</button>
                            </form>
                    </div>
                </div>                

            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>

    <div class="modal fade" id="contactuser" tabindex="-1" role="dialog" aria-labelledby="contactuser" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Contact user</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form method="post">
                  <input type="text" class="form-control" placeholder="Email" value="<?=$userData['Email'];?>" disabled>
                  <textarea name="message" class="form-control" placeholder="Message"></textarea>
                  <button type="submit" name="sendMail" class="form-btn-grad btn-lg btn-block w-50">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
    
</body>

</html>