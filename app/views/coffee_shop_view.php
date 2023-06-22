<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="css/coffee.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="js/checkout.js"></script>
</head>

<body>

<div class="logo-container">
    <img src="<?php echo $logo->getImage(); ?>" alt="Coffee Shop Logo">
</div>

<input type="hidden" name="totalPrice" value="" id="totalPrice">

<div class="wrapper">
<ul class="menu">
  <?php echo $menuItems->getProcessedItems(); ?>
</ul>
</div>

    </br>
    <button id="checkoutbtn" class="checkoutbtn">Checkout</button>

</body>
</html>