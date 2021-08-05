<?php

require("../sendgrid-php/sendgrid-php.php");
// require("../sendgrid-php/lib/SendGrid.php");
// require("../sendgrid-php-7.9.2/sendgrid-php.php");


// $sendgrid = new SendGrid('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');

$email = new \SendGrid\Mail\Mail();
$email->setFrom("kapil@webspero.com", "Example User");
$email->setSubject("Sending with SendGrid is Fun");
$email->addTo("garima@webspero.com", "Example User");
$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
);
$sendgrid = new \SendGrid('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');
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    print_r($response->headers());
    print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '. $e->getMessage() ."\n";
}
?>
