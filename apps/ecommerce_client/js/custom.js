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
                    thoriumCorePlugin.logEvent(0,'Transaction completed by ' + details.payer.name.given_name + '!');
                    thoriumCorePlugin.logEvent(0, "Calling paymentCallback");
                    eCommerceFirestorePlugin.paymentCallback(details);    
                });
            }
        }).render('#paymentprocessor');  

    },

    initialize: function () {
        if (payPalCheckoutGateway.price == 0) {
            thoriumCorePlugin.logEvent(2, "Price not set");
            return;
        }
        $('#paymentprocessor').html("<h4>Starting Paypal... Please Wait...</h4>");
        app.preloader.show();
        setTimeout(function () { payPalCheckoutGateway.processPayment(); }, 1000);
    },

}


$(document).on('click', '#toto', function (e) {
    eCommerceFirestorePlugin.ConvertCartToHistory();
});

$(document).on('page:afterin', '.page[data-name="history"]', function (e,page) {
    if (app.device.cordova == true) {
        $(".page-current").css("width", "99%");
        $(".page-current").css("width", "100%");
        $(".page-current").css("border", "1px solid transparent");
        setTimeout(function () {
            $(".page-current").css("width", "99%");
            $(".page-current").css("width", "100%");
            $(".page-current").css("border", "1px solid transparent");
            $(".page-current").css("border", "0px solid transparent");
        }, 100);
    }
});
