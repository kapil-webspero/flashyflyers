<br><br>

<style>
.container12 {
/*  border: 2px solid #dedede;*/
  background-color: #f1f1f1;
  border-radius: 5px;
  padding: 10px;
  margin: 10px 0;
}

.container12::after {
  content: "";
  clear: both;
  display: table;
}

.time-right12 {
  float: right;
  color: #aaa;
}

</style>


<h3>Disscussion about order</h3><br>
<div id="orderdiscussionid">
<?php


if(isset($_REQUEST['order_id']))
{
	$orderid=$_REQUEST['order_id'];
}
else{
	$orderid=$_SESSION['ORDERUID'];
}
$result120 = mysql_query("SELECT * FROM ".ORDER_DISCUSSTIONS." WHERE order_id='$orderid'");

$showData120 = mysql_fetch_array($result120);
while($showData120 = mysql_fetch_array($result120))
{ 
	$userid=$showData120['user_id'];
	$msg=$showData120['Msg'];
	$CurrentDate=$showData120['CurrentDate'];
	$userquery = mysql_query("SELECT * FROM ".USERS." WHERE UserID='$userid'");
	$userdata = mysql_fetch_array($userquery);
	$name=$userdata['FName'].' '.$userdata['LName'];
	?>
	<div class="container12">
	<?php echo $name; ?>
	<p><?php echo $msg; ?></p>
	<span class="time-right12"><?php echo date("F d, Y h:i:s A", strtotime($CurrentDate)); ?></span>
	</div>
	<?php
}
?>
</div>

<hr>
<textarea style="height: 100%;width: 100%;" name="msg" id="ordermessage" placeholder="Write you message here"></textarea> 

<br />
<button name="addcomment" class="btn-blue" id="sendordermessage">Send</button>
<div id="orderError" style="color:red;"></div>

<script>
jQuery(document).ready(function(e) {
	jQuery(document).on("click","#sendordermessage",function(){
	var message = $('#ordermessage').val();
	var orderid="<?php echo $orderid; ?>";
	var AssignedToid="<?php echo $AssignTo; ?>";
	$('#orderError').html('');
	jQuery.ajax({
		type: "POST",
		url: "<?=SITEURL;?>ajax/orderDiscussion.php",
		data: "orderid="+orderid+'&message='+message+'&AssignedToid='+AssignedToid,

		success: function(regResponse) {

			if(regResponse=="SqlError"){
				$('#orderError').html('Something went wrong.Please try again.');
			}
			else
			{   
				$('#ordermessage').val('');
				$('#orderdiscussionid').append(regResponse);
			}    
		}
	});
});
});
</script>