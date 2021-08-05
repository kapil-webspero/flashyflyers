<!DOCTYPE html>
<html>
<body class="">
<div class="container" >
    <br>
    <div class="container">
        <br>
        <table class="table">
            <tr>
                <td style="width:150px">$30</td>
                <td style="width:150px">
                 
                    <div id="paypal-button"></div>
                </td>
            </tr>
        </table>
    </div>
</div>

<script src="https://www.paypalobjects.com/api/checkout.js"></script>
<script>
    paypal.Button.render({
        env: 'sandbox',
        /*env: 'production',*/
        client: {
            sandbox: 'ARHUUeNT_WdabZnL3AH6e5WY5sEDlj_wJawH1a7c7PkATfN3ZwyDTo0xOmAVUyDpLtO6skYM3Ooikl71'
            /*production: 'PayPalClientId'*/

        },
        payment: function (data, actions) {
            return actions.payment.create({
                transactions: [{
                    amount: {
                        total: '30',
                        currency: 'USD'
                    }
                }]
            });
        },
        onAuthorize: function (data, actions) {
            return actions.payment.execute()
                .then(function () {
                    window.location = "http://optimabranding.com/5/flashyflyers/paypal_1/orderDetails.php?paymentID=" + data.paymentID + "&payerID=" + data.payerID + "&token=" + data.paymentToken + "&pid=123456";
                });
        }
    }, '#paypal-button');
</script>
</body>
</html>


