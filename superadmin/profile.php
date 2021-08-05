<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Profile";
	$userData = $AccessUData;
	
	if(isset($_REQUEST['updatepassword'])) {
		$currentpass = $_REQUEST['currentpass'];
		$newpass = $_REQUEST['newpass'];
		$confirmpass = $_REQUEST['confirmpass'];
		if($newpass == $confirmpass) {
			if(strlen($newpass)>6) {
				if($currentpass == base64_decode($userData['Password'])) {
					UpdateRcrdOnCndi(USERS, "`Password` = '".base64_encode($newpass)."'", "UserID = '$AccessID'");
					$_SESSION['SUCCESS'] = "Password successfully updated.";
				} else {
					$_SESSION['ERROR'] = "Your old password is incorrect.";
				}
			} else {
				$_SESSION['ERROR'] = "Password should be atleast 7 characters";	
			}
		} else {
			$_SESSION['ERROR'] = "New password and confirm password does not match.";
		}
	}
	
	if(isset($_REQUEST['updateprofile'])) {
		extract($_POST);
		if(GetNumOfRcrdsOnCndi(USERS, " Email = '$email' AND UserID <> '$AccessID'") > 0) {
			$_SESSION['ERROR'] = "Email already registered with another user.";
		} else {

            $name = explode(' ',$name);
            $fname = $name[0];
            $lname = !empty( $name[1] ) ? $name[1] : '';
            
			if(!empty($_FILES['image']['name'])) {
				$File_Name          = strtolower($_FILES['image']['name']);
				$imageFileType 		= strtolower(pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION));
				$NewFileName 		= "profile_image_".$AccessID."_".mt_rand(0,999).".".$imageFileType; 
				$UpDir				= '../uploads/profileImages/';
				$file_link 			= $UpDir.$NewFileName;
	
				$data = "FName = '$fname', LName = '$lname', Email= '$email'";
				
				$escapedGet = array_map('mysql_real_escape_string', $_POST);
				if(move_uploaded_file($_FILES['image']['tmp_name'], $UpDir.$NewFileName ))
				{
					$data = $data.", ProfilePhoto='$NewFileName'";
				}
			} else {
				$data = "FName = '$fname', LName = '$lname', Email= '$email'";
			}
			UpdateRcrdOnCndi(USERS, $data, "UserID = '$AccessID'");
			$_SESSION['SUCCESS'] = "Profile data successfully updated";
			$userData = GetSglRcrdOnCndi(USERS, "`UserID` = '$AccessID'");
		}
	}
	
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
                <h1 class="page-heading mb-4">Admin Profile
                </h1>

                <div class="row brd-bottom mt-5">
                    <div class="col-lg-6 brd-lg-right">
                        <h2 class="blue text-center mb-4">Contact details </h2>
                        <form method="post" class="profile-form pl-md-5 pr-md-5 pb-5" enctype="multipart/form-data">
                            <div class="profile-pic">
                            	<div style="float:left">
                                	<img src="<?php echo (!empty($userData['ProfilePhoto'])) ? SITEURL."uploads/profileImages/".$userData['ProfilePhoto'] : "../images/user.png"; ?>" alt="">
                                </div>
                                <div style="overflow:hidden; text-align:center">
                                	<a class="btn btn-blue uploadbtn" style="display:block">Upload</a>
                                	<input type="file" name="image" id="filefield" data-name="file" style="display: none;" />
                                    <label for="file"><span class="file">Select file</span></label>
                               	</div>
                            </div>
                            <?php $userName = !empty($userData['LName']) ? $userData['FName']." ".$userData['LName'] : $userData['FName']; ?>
                            <label>Name</label>
                            <input type="text" name="name" placeholder="Name" value="<?=$userName;?>" class="form-control mb-4" required>
                            <label>Email</label>
                            <input type="email" name="email" placeholder="Email" value="<?=$userData['Email'];?>" class="form-control mb-4" required>
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

            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>

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