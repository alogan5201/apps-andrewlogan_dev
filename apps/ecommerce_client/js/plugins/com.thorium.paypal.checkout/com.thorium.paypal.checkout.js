/* --- A place where you can add your own code -- */

/*-- <script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD" data-sdk-integration-source="button-factory"></script> --*/
var payPalCheckoutGateway = {
    name: "Thorium Builder Paypal Checkout Plugin",
    price: 0,
    createdby: "",
    createddate: "",
    qty: 0,
    fullname: "",
    address: "",
    zipcode: "",
    email: "",
    city: "",
    country: "",
    notes: "",

    processPayment: function () {
        app.preloader.hide();
        $('#paymentprocessor').html("");

        if (typeof paypal == 'undefined') {
            var m="Payment Gateway Paypal is not available";
            $('.paymentprocessor').html("<h4>"+m+"</h4>");
            thoriumCorePlugin.logEvent(1,m);
            app.dialog.alert(m);
            return;
        }

        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'blue',
                layout: 'vertical',
                label: 'checkout',
            },
            onInit: function (data, actions) {
                thoriumCorePlugin.logEvent(0, "Paypal Checkout Init...");
                if (thoriumCorePlugin.isLocal() == true) {
                    actions.disable();
                    $('.paymentprocessor').addClass("disabled");
                    const m="Paypal CheckOut is not available in Local Mode because of Sandbox Security. Run it from a Web Server";
                    thoriumCorePlugin.logEvent(1,m);
                    app.dialog.alert(m);
                } else {
                    actions.enable();
                }
                
            },

            onClick: function () {   
                if (thoriumCorePlugin.isLocal() == true) {
                   const m="Paypal CheckOut not available in Local Mode because of Sandbox Secutity. Run it from a Web Server";
                   thoriumCorePlugin.logEvent(1,m);
                   app.dialog.alert(m);
                }
            },

            createOrder: function (data, actions) {
                return po = actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: payPalCheckoutGateway.price
                        },
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    thoriumCorePlugin.logEvent(0, 'Transaction completed by ' + details.payer.name.given_name + '!');
                    thoriumCorePlugin.logEvent(0, "Calling paymentCallback");
                    eCommerceFirestorePlugin.paymentCallback(details);
                }
                );
            },
            onError: function (err) {
                thoriumCorePlugin.logEvent(2, "Paypal Checkout Error ["+err+"]");
            }
        }).render('#paymentprocessor');

    },

    initialize: function () {
        if (payPalCheckoutGateway) {
            if (payPalCheckoutGateway.price == 0) {
                thoriumCorePlugin.logEvent(2, "Price not set");
                return;
            }
            const pp=document.getElementById("paymentprocessor");
            if (pp) {
                pp.innerHTML="<p><small>Loading Paypal Checkout... Please Wait...</small></p>";
                 setTimeout(function () { payPalCheckoutGateway.processPayment(); }, 2000);
            }
        }
    },

}




