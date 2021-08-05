<?php
	ob_start();
	require_once '../function/constants.php';
	require_once '../function/configClass.php';
	require_once '../function/siteFunctions.php';
	require_once '../function/adminSession.php';
	
	$PageTitle = "Bug List";

    if(isset($_REQUEST['deleteID']) && !empty($_REQUEST['deleteID'])) {
		$deleteID = intval($_REQUEST['deleteID']);
		DltSglRcrd(BUG_REPORT, "`TicketNo` = '$deleteID'");
        $_SESSION['SUCCESS']="Record deleted";
        echo "<script> window.location.href = '".ADMINURL."bugs';</script>";
        exit();
        
	}
    if(isset($_REQUEST['BugID']) && isset($_REQUEST['status'])) {
		$BugID = intval($_REQUEST['BugID']);
        $status = intval($_REQUEST['status']);
        $data = "bug_status = '$status'";
        UpdateRcrdOnCndi(BUG_REPORT, $data, "TicketNo = '$BugID'");
        $_SESSION['SUCCESS']="Status Updated";
        echo "<script> window.location.href = '".ADMINURL."bugs';</script>";
        exit();
        
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/head.php"; ?>
</head>

<body>
    <?php include "includes/header.php"; ?>
 
    <main class="main-content-wrap">
        <div class="container">

        <?php
        if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) {
        
        ?>
        <div class="notification success" <?php if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])) { echo 'style="display:flex;"'; } ?>	>
                <div class="d-flex"><i class="fas fa-check"></i></div>
                <span><?php echo $_SESSION['SUCCESS'];  unset($_SESSION['SUCCESS']);?></span>
                <button class="close-ntf"><i class="fas fa-times"></i></button>
            </div>
            <?php
         }
            ?>
            <div class="main-content bx-shadow pl-60 pr-60">
                <h1 class="page-heading mb-4">Bug List</h1>
				
                <div class="table-responsive">
                    <table class="table sorting users-table table-1">
                        <thead>
                            <tr>
                           
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Bug Title</th>
                                <th scope="col">Bug Status</th>
                                <th scope="col">Action</th>

                            </tr>
                            <tbody>
<?php
	$sql = "SELECT * FROM ".BUG_REPORT; 
    $result = mysql_query($sql);
    if(mysql_num_rows($result) > 0 ){
	$sr = 1;
	while($showData = mysql_fetch_array($result))
	{
        $bugstatus='Pending';
        if($showData['bug_status']==1){
            $bugstatus='Completed';
        }
	?>
                            
        <tr>
            
            <td class="data-<?=$showData['name'];?>"><?=$showData['name'];?></td>
            <td class="data-<?=$showData['email'];?>"><?=$showData['email'];?></td>
            <td class="data-<?=$showData['phone'];?>"><?=$showData['phone'];?></td>
            <td class="data-<?=$showData['title'];?>"><?=$showData['title'];?></td>
            <td class="data-<?=$bugstatus;?>"><?=$bugstatus;?></td>

            <td class="data-view">

            <?php
            if($showData['bug_status']==0){
                ?>
                <a href="?BugID=<?=$showData['TicketNo'];?>&status=1" class="view">Complete</a>
                <?php
            }
            ?>
                <a href="bug-details?viewID=<?=$showData['TicketNo'];?>" class="view">view</a>
                <div class="data-delete">
                    <a onclick="return confirm('Are you sure you want to delete?');" href="?deleteID=<?=$showData['TicketNo'];?>"><i class="fas fa-trash-alt"></i></a></div>
            </td>
        </tr>
  	<?php
	}
} else {
	echo "<tr>
			<td colspan=\"8\">No Search Result Found.</td>
		</tr>";
}
?>                              
                            </tbody>
                        </thead>
                    </table>
                </div>


            </div>


        </div>
    </main>

    <?php include "includes/footer.php"; ?>


    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/datepicker.min.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/script.js"></script>
<style>
.data-view a{ display:inline-block !important;}
.data-delete{ display:inline-block;}
</style>
</body>

</html>