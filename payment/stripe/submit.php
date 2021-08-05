<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('stripe-php/init.php');

if(!empty($_POST['stripeToken'])) {
    try {

    
    //get token, card and user info from the form
    $token  = $_POST['stripeToken'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $card_num = $_POST['card_num'];
    $card_cvc = $_POST['cvc'];
    $card_exp_month = $_POST['exp_month'];
    $card_exp_year = $_POST['exp_year'];
    
    //include Stripe PHP library
    
    
    //set api key
    $stripe = array(
      "secret_key"      => "sk_test_GsrCiBLCFOiFAtpWFHIaIDop",
      "publishable_key" => "pk_test_h4GjDiaHBrOtKnFG7WEsI9oD"
    );
    
    \Stripe\Stripe::setApiKey($stripe['secret_key']);
    
    //add customer to stripe
    $customer = \Stripe\Customer::create(array(
        'email' => $email,
        'source'  => $token
    ));
    
    //item information
    $cents=55;
    $itemName = "Premium Script CodexWorld";
    $itemNumber = "PS123457";
    $itemPrice = $cents*100;
    $currency = "USD";
    $orderID = "SKA92712382140";
    
    //charge a credit or a debit card
    $charge = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount'   => $itemPrice,
        'currency' => $currency,
        'description' => $itemName,
        'metadata' => array(
            'order_id' => $orderID
        )
    ));
    
    //retrieve charge details
    $chargeJson = $charge->jsonSerialize();

    //check whether the charge is successful
    if($chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1){
        //order details 
        $amount = $chargeJson['amount'];
        $balance_transaction = $chargeJson['balance_transaction'];
        $currency = $chargeJson['currency'];
        $status = $chargeJson['status'];
        $date = date("Y-m-d H:i:s");
        
        //insert tansaction data into the database
        
        //if order inserted successfully
        if($status == 'succeeded'){
            $statusMsg = "<h2>The transaction was successful.</h2>";
        }else{
            $statusMsg = "Transaction has been failed";
        }
    }else{
        $statusMsg = "Transaction has been failed";
    }
    } catch(Exception $e) {
       $statusMsg = $e->getMessage();    
    }
}
else{
    $statusMsg = "Form submission error.......";
}

//show success or error message
echo $statusMsg;