<?php
// echo "pass: ".base64_decode('RG9taW5vczgwOQ==');
// die();
//Dominos809
	ob_start();
	require_once 'function/constants.php';
	require_once 'function/configClass.php';
	require_once 'function/siteFunctions.php';



	

if(isset($_REQUEST['bugsubmit']))
{
	$name = $_REQUEST['name'];
	$email = $_REQUEST['emailAddress'];
	$phone = $_REQUEST['phone'];
	$title = $_REQUEST['title'];
	$description = $_REQUEST['description'];

	$errors='';
	if($name==""){
		$errors.="Please enter name. <br>";
	}
	if (!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,})$/i",$email)){
		$errors.= "Invalid Email.<br>"; 
	}

	if($phone==""){
		$errors.="Please enter phone. <br>";
	}

	if(validate_phone_number($phone)=="no"){
		$errors.="Please enter valid phone. <br>";
	}

	if($title==""){
		$errors.="Please enter title. <br>";
	}

	if($description==""){
		$errors.="Please enter description. <br>";
	}

	if($errors!="")
	{
		$_SESSION['name']=$name;
		$_SESSION['email']=$email;
		$_SESSION['phone']=$phone;
		$_SESSION['title']=$title;
		$_SESSION['description']=$description;
		$_SESSION['ERROR']=$errors;
		echo "<script> window.location.href = '".SITEURL."bug-report.php';</script>";
		exit();
	}

	$date=date('Y-m-d h:i:s');
	$data = "name = '$name',email='$email',phone='$phone', title='$title',description='$description', CreatedOn='$date',bug_status='0'";
	$lastinsertId=InsertRcrdsByData(BUG_REPORT, $data);
	
	$file_path='';
	if(!empty($_FILES))
	{
		$extension=array("jpeg","jpg","png","gif");
		foreach($_FILES["files"]["tmp_name"] as $key=>$tmp_name) {
		$file_name=$_FILES["files"]["name"][$key];
		$file_tmp=$_FILES["files"]["tmp_name"][$key];
		$ext=pathinfo($file_name,PATHINFO_EXTENSION);

			if(in_array($ext,$extension)) {
				if(!file_exists("images/bugs/".$file_name))
				{

					move_uploaded_file($file_tmp=$_FILES["files"]["tmp_name"][$key],"images/bugs/".$file_name);
					$file_path=$file_name;
				}
				else
				{
					$filename=basename($file_name,$ext);
					$newFileName=$filename.time().".".$ext;
					move_uploaded_file($file_tmp=$_FILES["files"]["tmp_name"][$key],"images/bugs/".$newFileName);
					$file_path=$newFileName;
				}
				$data2 = "file_path = '$file_path',bug_id='$lastinsertId'";
	            InsertRcrdsByData(BUG_IMAGES, $data2);
			}
		}
	}

	$_SESSION['SUCCESS'] = "Your bug report successfully submmited";
	echo "<script> window.location.href = '".SITEURL."bug-report.php';</script>";
	exit();
}

?>
<style>
	.bugForm-form{
	background-color: #fff;
    border-radius: 38px;
    display: block;
    width: 100%;
    max-width: 500px;
    margin: 30px auto;
	}
	</style>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bug Report | Flashy Flyers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   	
    <?php require_once 'files/headSection.php'; ?> 

</head>

<body>
	
    <?php require_once 'files/headerSection.php'; ?>

    <div class="page-wrap bubble-bg-1 pb-5 pt-3">
		
        <div class="container">
        
            <form id="bugForm" class="bugForm-form bx-shadow clearfix p-4" method="post" enctype="multipart/form-data">
                <h1 class="text-center udr-heading mb-4">Bug Report</h1>
                <label>Name*</label>
                <input type="text" name="name" class="form-control mb-3" required value="<?php echo $_SESSION['name'];unset($_SESSION['name']); ?>">

                <label>Email*</label>
                <input type="email" name="emailAddress" class="form-control mb-3" required value="<?php echo $_SESSION['email'];unset($_SESSION['email']); ?>">


				<label>Phone*</label>
                <input type="text" name="phone" class="form-control mb-3" required value="<?php echo $_SESSION['phone'];unset($_SESSION['phone']); ?>">

                <label>Bug Title*</label>
                <input type="text" name="title" class="form-control mb-3" required value="<?php echo $_SESSION['title'];unset($_SESSION['title']); ?>">

				<label>Bug Descripton*</label>
				<textarea name="description" class="form-control mb-3" required><?php echo $_SESSION['description'];unset($_SESSION['description']); ?></textarea>

				<label>Bug image</label>
                <input type="file" name="files[]" class="form-control mb-3" multiple >
                <button type="submit" name="bugsubmit" class="btn-grad btn-lg float-right mb-2">Submit</button><br />
				<?php 
				if(isset($_SESSION['SUCCESS'])){ 
					echo '<span style="color:green;">'.$_SESSION['SUCCESS'].'</span>';
					unset($_SESSION['SUCCESS']);
				}

				if(isset($_SESSION['ERROR'])){ 
					echo '<span style="color:red;">'.$_SESSION['ERROR'].'</span>';
					unset($_SESSION['ERROR']);
				}
				
				?>

            </form>
			
        </div>
    </div>
    <?php require_once 'files/footerSection.php'; ?>
</body>
</html>