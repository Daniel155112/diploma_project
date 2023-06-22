<!DOCTYPE html>
<html>
<head>
  <title>Coffee shop generator</title>
  <link rel="stylesheet" href="css/generator.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="js/generator.js"></script>
</head>
<body>
  <h1>Coffee shop generator</h1>

  <form method="POST" action="/?route=process-generating-form" enctype="multipart/form-data" onsubmit="return validateForm()">
  <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <h2>Logo Image <a href="https://www.remove.bg">(without background)</a></h2>
    <input type="file" id="logo-image" name="logo" accept="image/*" required>

    <h2>Menu Items</h2>
    <div id="menu-items">

    <?php if (!$menuItems->getItems()) {
      $menuItems->addItem('', 0, '');
    } ?>

    <?php foreach ($menuItems->getItems() as $index => $menuItem) { ?>
    <div class="menu-item">
        <label for="item<?php echo $index + 1; ?>-name">Item <?php echo $index + 1; ?> Name:</label>
        <input type="text" id="item<?php echo $index + 1; ?>-name" name="menu_items[<?php echo $index; ?>][name]" required value="<?php echo $menuItem['name']; ?>">

        <label for="item<?php echo $index + 1; ?>-image">Item <?php echo $index + 1; ?> Image:</label>
        <input type="file" id="item<?php echo $index + 1; ?>-photo" name="menu_items[<?php echo $index; ?>][photo]" accept="image/*">

        <label for="item<?php echo $index + 1; ?>-price">Item <?php echo $index + 1; ?> Price (UAH):</label>
        <input type="number" id="item<?php echo $index + 1; ?>-price" name="menu_items[<?php echo $index; ?>][price]" required value="<?php echo $menuItem['price']; ?>">
    </div>
<?php } ?>
    </div>

    <button type="button" onclick="addMenuItem()">Add Menu Item</button>

    <h2>
      Receive new order notifications via email
      <input type="checkbox" id="emailCheckbox" name="notificationType" value="email">
    </h2>
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" placeholder="emailaddress@gmail.com" required>

    <h2>
      Receive new order notifications via Telegram 
      <a href="https://docs.google.com/document/d/1w7oWOlbp6_t0nf5GBepalrJ4FdXmNVEnwXG_YMKZ4FE/edit?usp=sharing">(guide)</a>
      <input type="checkbox" id="telegramCheckbox" name="notificationType" value="telegram">
    </h2>
    <label for="telegramGroupId">Telegram Group ID:</label>
    <input type="text" id="telegramGroupId" name="telegramGroupId" placeholder="Group Chat ID" required>
  </br> </br>
    <div class="form-actions">
    <div class="left-buttons">
    <button type="submit" name="action" value="payment" id="generate-button">Proceed to checkout</button>
    <button type="submit" name="action" value="preview" class="preview-button">Preview</button>
</div>
    <div class="right-buttons">
        <input type="text" id="payment-id" name="payment_id" placeholder="Liqpay payment ID">
        <button type="submit" name="action" value="existing-client-generate" id="existing-client-generate">Generate (for existing clients only)</button>
    </div>
    </div>
  </form>

<script>
  let itemcounter = <?php echo count($menuItems->getItems()) + 1; ?>;

function addMenuItem() {

  const menuItems = document.getElementById('menu-items');
  const newItem = document.createElement('div');
  newItem.classList.add('menu-item');

  const itemNumber = menuItems.children.length + 1;

  const nameLabel = document.createElement('label');
  nameLabel.for = `item${itemNumber}-name`;
  nameLabel.textContent = `Item ${itemNumber} Name:`;
  newItem.appendChild(nameLabel);

  const nameInput = document.createElement('input');
  nameInput.type = 'text';
  nameInput.id = `item${itemNumber}-name`;
  nameInput.required = true;
  nameInput.name = `menu_items[${itemcounter}][name]`;
  newItem.appendChild(nameInput);

  const imageLabel = document.createElement('label');
  imageLabel.for = `item${itemNumber}-image`;
  imageLabel.textContent = `Item ${itemNumber} Image:`;
  newItem.appendChild(imageLabel);

  const imageInput = document.createElement('input');
  imageInput.type = 'file';
  imageInput.id = `item${itemNumber}-image`;
  imageInput.name = `menu_items[${itemcounter}][photo]`;
  imageInput.accept = 'image/*';
  imageInput.required = false;
  newItem.appendChild(imageInput);

  const priceLabel = document.createElement('label');
  priceLabel.for = `item${itemNumber}-price`;
  priceLabel.textContent = `Item ${itemNumber} Price (UAH):`;
  newItem.appendChild(priceLabel);

  const priceInput = document.createElement('input');
  priceInput.type = 'number';
  priceInput.id = `item${itemNumber}-price`;
  priceInput.name = `menu_items[${itemcounter}][price]`;
  priceInput.required = true;
  newItem.appendChild(priceInput);

  menuItems.appendChild(newItem);
  itemcounter++;

}

var notificationMethod = "<?php echo $notificationMethod->getMethod(); ?>";

if (notificationMethod === "email") {
document.getElementById("emailCheckbox").checked = true;
document.getElementById("telegramCheckbox").disabled = true;
document.getElementById("telegramGroupId").disabled = true;
} else if (notificationMethod === "telegram") {
document.getElementById("telegramCheckbox").checked = true;
document.getElementById("emailCheckbox").disabled = true;
document.getElementById("email").disabled = true;
}

document.getElementById('emailCheckbox').addEventListener('change', function() {
const telegramCheckbox = document.getElementById('telegramCheckbox');
const telegramGroupId = document.getElementById('telegramGroupId');

if (this.checked) {
telegramCheckbox.disabled = true;
telegramGroupId.disabled = true;
} else {
telegramCheckbox.disabled = false;
telegramGroupId.disabled = false;
}
});

document.getElementById('telegramCheckbox').addEventListener('change', function() {
const emailCheckbox = document.getElementById('emailCheckbox');
const emailTextbox = document.getElementById('email');


if (this.checked) {
emailCheckbox.disabled = true;
emailTextbox.disabled = true;
} else {
emailCheckbox.disabled = false;
emailTextbox.disabled = false;
}
});

function validateEmail() {
            var email = document.getElementById('email').value;
            var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            var isValid = emailRegex.test(email);

            if (!isValid) {
              alert('Invalid email address!');
              return false;
            }
            return true;
        }

function validateForm() {
const emailCheckbox = document.getElementById('emailCheckbox');
const telegramCheckbox = document.getElementById('telegramCheckbox');

if (!emailCheckbox.checked && !telegramCheckbox.checked) {
  alert('Please choose at least one notification option (email or Telegram).');
  return false;
}

if (emailCheckbox.checked && !validateEmail()) {
  return false;
}

return true;
}
</script>

</body>
</html>