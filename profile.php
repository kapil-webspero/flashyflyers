<?php
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';
	if(!is_login()) {
		header("location:".SITEURL."login.php")	;
		exit();
	}
	$PageTitle = "Profile";	
	$AccessID = intval($_SESSION['userId']);
	$AccessType = $_SESSION['userType'];
	$userData = $AccessUData = GetSglRcrdOnCndi(USERS, "`UserID` = '".$_SESSION['userId']."'"); 
	
	if(isset($_REQUEST['updatepassword'])) {
		$currentpass = $_REQUEST['currentpass'];
		$newpass = $_REQUEST['newpass'];
		$confirmpass = $_REQUEST['confirmpass'];
		if(strlen($newpass)>6) {
			if($newpass == $confirmpass) {
				if($currentpass == base64_decode($userData['Password'])) {
					UpdateRcrdOnCndi(USERS, "`Password` = '".base64_encode($newpass)."'", "UserID = '$AccessID'");
					$_SESSION['SUCCESS'] = "Password successfully updated.";
				} else {
					$_SESSION['ERROR'] = "Your old password is incorrect.";
				}
			} else {
				$_SESSION['ERROR'] = "New password and confirm password does not match.";
			}
		} else {
			$_SESSION['ERROR'] = "Password should be atleast 7 characters";	
		}
	}
	
	if(isset($_REQUEST['updateprofile'])) {
        // echo "<pre>";
        // print_r($_POST);
        // die();
       if(isset($_POST['availability'])){
        $availa = 'yes';
       }else{
         $availa = 'no';
       }

    	extract($_POST);

    	$newProductNotification = 0;
        if(!empty($new_product_notification) && $new_product_notification ='on'){
            $newProductNotification = 1;
        }

		$file_type = $_FILES['image']['type']; //returns the mimetype

		$allowed = array("image/jpeg", "image/gif", "image/png");

		if(GetNumOfRcrdsOnCndi(USERS, " Email = '$email' AND UserID <> '$AccessID'") > 0) {
			$_SESSION['ERROR'] = "Email already registered with another user.";
		}elseif(!in_array($file_type, $allowed) && $file_type != '') {
		 
		  $_SESSION['ERROR'] = "Only jpg, gif, and png files are allowed.";

		} else {
            
            $name = explode(' ',$name);
            $fname = $name[0];
            $lname = !empty( $name[1] ) ? $name[1] : '';
           
			if(!empty($_FILES['image']['name'])) {
				$File_Name          = strtolower($_FILES['image']['name']);
				$imageFileType 		= strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
				$NewFileName 		= "profile_image_".$AccessID.".".$File_Name; 
				$UpDir				= 'uploads/profileImages/';
				$file_link 			= $UpDir.$NewFileName;
				$tmp_name = $_FILES['image']['tmp_name'];
				$data = "FName = '$fname', LName = '$lname', Email= '$email', Address= '$address', City= '$city', State= '$state', Zip= '$zip', new_product_notification= '$newProductNotification',availability= '$availa'";

				$escapedGet = array_map('mysql_real_escape_string', $_POST);

				//if(move_uploaded_file($tmp_name, "$UpDir/$NewFileName"))
				if(in_array($file_type, $allowed)) {
					if(move_uploaded_file($tmp_name, $UpDir.$NewFileName ))
					{
						$data = $data.", ProfilePhoto='$NewFileName'";
					}
				}
				
			} else {
				$data = "FName = '$fname', LName = '$lname', Email= '$email', Address= '$address', City= '$city', State= '$state', Zip= '$zip', new_product_notification= '$newProductNotification',availability= '$availa'";
			}
			
			UpdateRcrdOnCndi(USERS, $data, "UserID = '$AccessID'");
			$_SESSION['SUCCESS'] = "Profile data successfully updated";
			
		}
	}
	$userData = GetSglRcrdOnCndi(USERS, "`UserID` = '".$_SESSION['userId']."'"); 
	
?>

<!DOCTYPE html>
<html lang="en">

<head>
<title>Profile</title>
    <?php require_once 'files/headSection.php'; ?>
	<link rel="stylesheet" href="css/style-2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<style>
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<body class="MyProfilePage">
    <?php require_once 'files/headerSection.php'; ?>
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
                <h1 class="page-heading mb-4">Profile (<?=$userData['FName'].' '.$userData['LName']?>)
                </h1>

                <div class="row brd-bottom mt-5">
                    <div class="col-lg-6 brd-lg-right">
                        <h2 class="blue text-center mb-4">Contact details </h2>
                        <form method="post" class="profile-form pl-md-5 pr-md-5 pb-5" enctype="multipart/form-data">
                            <div class="profile-pic">
                            	<div style="float:left">
                                	<img src="<?php if(!empty($userData['ProfilePhoto'])) { echo SITEURL."uploads/profileImages/".$userData['ProfilePhoto']; } else if(!empty($userData['oauth_provider'])) { echo "https://graph.facebook.com/".$userData['oauth_uid']."/picture?type=large"; } else { echo "images/user.png"; } ?>" alt="">
                                </div>
                                <div style="overflow:hidden; text-align:center">
                                	<a class="btn btn-blue uploadbtn" style="display:block">Upload</a>
                                    <input type="file" name="image" id="filefield" data-name="file" style="display: none;" />
                                    <label for="file"><span class="file">Select file</span></label>
                              	</div>
                            </div>
                            <label>Name</label>
                            <?php $userName = !empty($userData['LName']) ? $userData['FName']." ".$userData['LName'] : $userData['FName']; ?>
                            <input type="text" name="name" placeholder="Name" value="<?=$userName;?>" class="form-control mb-4" required>
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Email" value="<?=$userData['Email'];?>" class="form-control mb-4" required>
                            <label>Address</label>
                            <input type="text" name="address" placeholder="Address" value="<?=$userData['Address'];?>" class="form-control mb-4" >
                            <label>City</label>
                            <input type="text" name="city" placeholder="City" value="<?=$userData['City'];?>" class="form-control mb-4" >
                            <label>State</label>
                            <select class="form-control mb-4" name="state">
                                <option value="">Select State</option>
                                <?php
                                    foreach($statesarr as $key=>$val) { ?>
                                        <option value="<?=$val?>" <?=($userData['State']==$val) ? 'selected':''?>><?=$val?></option>
                                <?php } ?>
                            </select>
                            <label>Zip Code	</label>
                            <input type="text" name="zip" placeholder="Zip Code" value="<?=$userData['Zip'];?>" class="form-control mb-4" >

                            
                           <label>Availability</label><br>
                             <label class="switch">
                              <input type="checkbox"  name="availability" <?php if($userData['availability'] == 'yes'){ echo 'checked';}?>>
                              <span class="slider round"></span>
                            </label>

                            <label class="custom-control custom-checkbox new-product-notification">Check here to be alerted about our new templates
                                <input type="checkbox" name="new_product_notification" class="custom-control-input otherSizeCheck" value="1" <?php echo ($userData['new_product_notification'] == 1 ) ? "checked" : ""; ?> >
                                <span class="custom-control-indicator"></span>
                            </label>

                            <div class="text-center">
                                <button type="submit" name="updateprofile" class="btn-block form-btn-grad">Update</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <h2 class="blue text-center mb-4">Change password</h2>
                        <form method="post" class="profile-form pl-md-5 pr-md-5 pb-5">
                            <label>Enter your old password</label>
                            <input type="password" name="currentpass" class="form-control mb-4" required>
                            <label>Enter your new password</label>
                            <input type="password" name="newpass" class="form-control mb-4" min="7" required>
                            <label>Enter your new password again</label>
                            <input type="password" name="confirmpass" class="form-control mb-4" min="7">
                            <div class="text-center">
                                <button type="submit" name="updatepassword" class="btn-block form-btn-grad">Update</button>
                            </div>
                        </form>
                    </div>

                </div>
                
                <?php if($_SESSION['userType'] != "user") { ?>
                <div class="row user-stats mt-5 pl-md-5 pr-md-5">
                    <div class="col-lg-6 col-sm-6 mb-4">
                        <p>Account#:</p>
                        <h3><?=$_SESSION['userId'];?></h3>
                        <a href="#" class="btn btn-blue">send email</a>
                    </div>
                    <div class="col-lg-6 col-sm-6 mb-4">
                        <p>Projects assigned:</p>
                        <h3><?php $query = "SELECT COUNT(DISTINCT TransactionID) as num FROM ".ORDER." WHERE `AssignedTo` = '".$_SESSION['userId']."'"; $total_pages = mysql_fetch_array(mysql_query($query));
	echo $total_pages = $total_pages[num]; ?></h3>
                        <a href="<?=SITEURL;?>my-work.php" class="btn btn-blue">view projects
                            </a>
                    </div>
                </div>
                <?php } ?>
            </div>


        </div>
    </main>

    <?php include "includes/footerSection.php"; ?>
	<?php //require_once 'files/footer.php' ?>
    
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/script.js"></script>
    <script>
		$(".uploadbtn").click(function() {
			$("#filefield").click();
		});
	
        $(document).ready(function () {
			$('.close-ntf').click(function() {
				$(this).parent().fadeOut(300, function() {
					$(this).hide();
				});
			});
			setTimeout(function(){ $('.close-ntf').click(); }, 12000);
			
			function readURL(input) {
				if (input.files && input.files[0]) {
					var fileName = input.files[0].name;
					var bname = $(input).data('name');
					$("."+bname).text(fileName);
				}
			}
			$("#filefield").on('change', function(){
				var bname = $(this).data('name');
				$("."+bname).text("Select file");
				readURL(this);
			});
        });
    </script>
    
</body>

</html>