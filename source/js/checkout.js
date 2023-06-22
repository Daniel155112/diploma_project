$(document).ready(function() {

  const itemQuantities = [];
  let itemsChosen = false;

  $('.additembtn').click(function() {
    const index = $(this).closest('li').index();
    if (itemQuantities[index]) {
       itemQuantities[index]++;
    } else {
       itemQuantities[index] = 1;
    }
    $(this).text(`+ (${itemQuantities[index]})`);
    itemsChosen = true;
 });

  $('#checkoutbtn').click(function() {
  	if (!itemsChosen) {
      alert("You haven't added any items. Please add at least 1 item before proceeding.");
      return;
    }
    const itemPrices = [];
    const itemNames = [];
    $('.name').each(function() {
      itemNames.push($(this).text());
    });
    $('.price').each(function() {
      const price = $(this).text().replace(/[^\d.]/g, '');
      itemPrices.push(parseFloat(price));
    });
    const checkoutData = [];
    for (let i = 0; i < itemQuantities.length; i++) {
      if (itemQuantities[i] > 0) {
        const itemName = itemNames[i];
        const itemQuantity = itemQuantities[i];
        const itemPrice = itemPrices[i];
        const itemTotalPrice = itemQuantity * itemPrice;
        checkoutData.push([itemName, itemQuantity, itemPrice]);
      }
    }

    const orderId = generateOrderId();

    $.ajax({
      type: "POST",
      url: "php/notification.php",
      data: { orderId: orderId, checkoutData: checkoutData },
      success: function(response) {
        alert('Order has been placed successfully! Order ID: ' + orderId);
      },
      error: function() {
        alert('An error occurred');
      }
    });

    function generateOrderId() {
      const timestamp = Date.now();
      const orderId = (timestamp % 1000).toString().padStart(3, '0');
      return orderId;
    }
  });
});