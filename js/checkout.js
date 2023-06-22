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
    let itemTotalPrice = 0;
    $('.name').each(function() {
      itemNames.push($(this).text());
    });
    $('.price').each(function() {
      const price = $(this).text().replace(/[^\d.]/g, '');
      itemPrices.push(parseFloat(price));
    });
    const checkoutData = [];
    var totalPrice = 0;
    for (let i = 0; i < itemQuantities.length; i++) {
      if (itemQuantities[i] > 0) {
        const itemName = itemNames[i];
        const itemQuantity = itemQuantities[i];
        const itemPrice = itemPrices[i];
        itemTotalPrice = itemQuantity * itemPrice;
        totalPrice += itemTotalPrice;
        checkoutData.push(`${itemName} x ${itemQuantity} - ${itemTotalPrice} UAH`);
      }
    }

    const orderId = generateOrderId();
    const message = `Order ID: ${orderId}\n${checkoutData.join('\n')}\nTotal price: ${totalPrice} UAH`;
    alert('Order has been placed successfully! This is just a preview, so you won\'t receive a notification to your chosen notification method, but here is a preview of how it would look like:\n' + message);

    function generateOrderId() {
      const timestamp = Date.now();
      const orderId = (timestamp % 1000).toString().padStart(3, '0');
      return orderId;
    }
  });
});