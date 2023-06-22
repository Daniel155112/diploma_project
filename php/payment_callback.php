<?php
$publicKey = 'sandbox_i29127371807';
$privateKey = 'sandbox_XqKC2RBxUEPmSpKFLFrIhZwMjMSTaivb5116tIUI';

$servername = 'localhost';
$username = 'diploma';
$password = 'diplomX';
$dbname = 'orders';

$connect = new mysqli($servername, $username, $password, $dbname);

$data = $_POST['data'];
$signature = $_POST['signature'];

$expectedSignature = base64_encode(sha1($privateKey . $data . $privateKey, true));

if ($signature === $expectedSignature) {
    $decodedData = base64_decode($data);

    $paymentInfo = json_decode($decodedData, true);

    $paymentId = $paymentInfo['payment_id'];
    $paymentStatus = $paymentInfo['status'];
    $orderDate = $paymentInfo['end_date'];

    $sql = "INSERT INTO order_list (payment_id, payment_status, order_date) VALUES ('$paymentId', '$paymentStatus', '$orderDate')";
    $result = $connect->query($sql);

    if ($paymentInfo['status'] === 'sandbox') {
        $postData = [
            'action' => 'payment-successful'
        ];
        
        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting...</title>
        </head>
        <body>
            <p>Redirecting...</p>
            <form id="redirectForm" action="/index.php?route=payment-callback-form" method="POST">
HTML;

        foreach ($postData as $key => $value) {
            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        }
        echo <<<HTML
            </form>
            <script type="text/javascript">
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
HTML;
    } else {
        $postData = [
            'action' => 'payment-failed'
        ];
        
        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <title>Redirecting...</title>
        </head>
        <body>
            <p>Redirecting...</p>
            <form id="redirectForm" action="/index.php?route=payment-callback-form" method="POST">
HTML;

        foreach ($postData as $key => $value) {
            echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        }
        echo <<<HTML
            </form>
            <script type="text/javascript">
                document.getElementById('redirectForm').submit();
            </script>
        </body>
        </html>
HTML;
    }
} else {
    echo 'Payment error!';
}
$mysqli->close();
?>