<?php
if(isset($_POST['txn_id']) && !empty($_POST['txn_id'])) {
echo "Payment is successful";
  echo "<pre>";
      print_r($_POST);
  echo "</pre>";
}
