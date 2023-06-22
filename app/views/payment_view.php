<!DOCTYPE html>
<html>
<head>
  <title>Proceed to payment</title>
  <link rel="stylesheet" href="css/payment.css">
</head>
<body>
  <div class="container">
    <div class="payment-card">
      <h1 class="payment-title">Proceed to payment</h1>
      
      <form id="payment-form" action="https://www.liqpay.ua/api/3/checkout" method="POST">
        <?php include 'php/payment.php'; ?>
      </form>
  
      <button class="pay-button" type="button" onclick="initiatePayment()">Pay Now</button>
    </div>

    <script>
      function initiatePayment() {
        document.getElementById('payment-form').submit();
      }
    </script>
  </div>
</body>
</html>
