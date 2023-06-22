<?php
require_once 'LiqPay/LiqPay.php';

$publicKey = 'sandbox_i29127371807';
$privateKey = 'sandbox_XqKC2RBxUEPmSpKFLFrIhZwMjMSTaivb5116tIUI';

$amount = '150'; 

$liqpay = new LiqPay($publicKey, $privateKey);

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

$callbackUrl = 'http://diploma.alucard.bio/php/payment_callback.php';

$params = [
    'version' => '3',
    'action' => 'pay',
    'amount' => $amount,
    'currency' => 'UAH',
    'description' => 'Coffee Shop Gen Order', 
    'result_url' => $callbackUrl,
    'sandbox' => 1, 
];

$paymentForm = $liqpay->cnb_form($params);

echo $paymentForm;

if (isset($_POST['data']) && isset($_POST['signature'])) {
    $data = $_POST['data'];
    $signature = $_POST['signature'];

    $isSignatureValid = $liqpay->cnb_signature_verify($data, $signature);

    if ($isSignatureValid) {
        $decodedData = base64_decode($data);
        $response = json_decode($decodedData, true);

        if (isset($response['status']) && $response['status'] === 'success') {

            header('Location: download_page.html');
            exit();
        }
        else {
            header('Location: index.html');
            exit();
        }
    }
}

?>