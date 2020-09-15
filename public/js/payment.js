const stripe = Stripe(stripePublicKey);

const elements = stripe.elements();

const card = elements.create("card");
// Stripe injects an iframe into the DOM
card.mount("#card-element");
card.on("change", function (event) {
  // Disable the Pay button if there are no card details in the Element
  document.querySelector("button").disabled = event.empty;
  document.querySelector("#card-error").textContent = event.error
    ? event.error.message
    : "";
});

const form = document.getElementById("payment-form");

form.addEventListener("submit", function (event) {
  event.preventDefault();
  // Complete payment when the submit button is clicked
  stripe
    .confirmCardPayment(clientSecret, {
      payment_method: {
        card: card,
      },
    })
    .then(function (result) {
      if (result.error) {
        // Show error to your customer
        console.log(result.error.message);
      } else {
        // The payment succeeded!
        window.location.href = redirectAfterSuccessUrl;
      }
    });
});
