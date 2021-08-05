<!DOCTYPE html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
    <title>PayPal shop</title>
</head>
<body>

Make a payment of $10
<form action="payment-listener.php" method="post" name="form-pp">
                <input type="hidden" name="image_url" value="http://paypal.local/static/logo.png">
                <input type="hidden" name="charset" value="utf8">
                <input type="hidden" name="item_name" value="Коробка">
                <input type="hidden" name="item_number" value="1">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="no_note" value="1">
                <input type="hidden" name="no_shipping" value="1">
                <input type="hidden" name="rm" value="2">
                <input type="submit" value="buy">
            </form>

</body>
</html>