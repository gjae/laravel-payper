<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/laravelpayper/css/card.css') }}">
    <title>Proceso de pago</title>
    <style>
        body {
            background-color: #F3F3F3;
        }

        .input-field input[type=text]:focus + label {
            color: #000;
        }
        .input-field input[type=text]:focus {
            border-bottom: 1px solid #000;
            box-shadow: 0 1px 0 0 #000;
        }

        .btn {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="row">
    <div class="col s12 m7 offset-m1">
        <div class="card white darken-1">
            <div class="card-content black-text">
                <form action="#" method="POST">
                    <div class="card-wrapper">

                    </div>
                    <div class="row">
                        <div class="input-field col s8">
                            <input id="number" name="card_number" type="text" class="validate">
                            <label for="number">Número de tarjeta</label>
                        </div>
                        <div class="input-field col s4">
                            <input type="text" class="validate" id="cvc"  name="card_cvc">
                            <label for="cvc">CVC</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s10">
                            <input type="text" name="card_name" class="validate" id="name">
                            <label for="name">Nombre del titular de la tarjeta</label>
                        </div>
                        <div class="input-field col s2">
                            <input type="text" name="expiration" class="validate" id="expiry">
                            <label for="expiry">Expiración</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col s12 m4">
        <div class="row">
            <div class="col s12 ">
                <div class="card white darken-1">
                    <div class="card-content black-text">
                        <span class="card-title">Transacción</span>
                        <div class="row">
                            <div class="col s9 text-left" style="text-align: left;">
                                <strong>{{ $transaction->getDescription() }}</strong>
                            </div>
                            <div class="col s3" style="text-align: right;">
                                <strong>$ {{ $transaction->getTotal() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 card-action">
                            <button class="btn orange">PAGAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
        
    

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="{{ asset("vendor/laravelpayper/js/jquery.card.js") }}"></script>
<script src="{{ asset("vendor/laravelpayper/js/card.js") }}"></script>
<script>

var card = new Card({
    // a selector or DOM element for the form where users will
    // be entering their information
    form: 'form', // *required*
    // a selector or DOM element for the container
    // where you want the card to appear
    container: '.card-wrapper', // *required*

    formSelectors: {
        numberInput: 'input#number', // optional — default input[name="number"]
        expiryInput: 'input#expiry', // optional — default input[name="expiry"]
        cvcInput: 'input#cvc', // optional — default input[name="cvc"]
        nameInput: 'input#name' // optional - defaults input[name="name"]
    },

    width: 200, // optional — default 350px
    formatting: true, // optional - default true

    // Strings for translation - optional
    messages: {
        validDate: 'valid\ndate', // optional - default 'valid\nthru'
        monthYear: 'mm/yyyy', // optional - default 'month/year'
    },

    // Default placeholders for rendered fields - optional
    placeholders: {
        number: '•••• •••• •••• ••••',
        name: 'Titular',
        expiry: '••/••',
        cvc: '•••'
    },

    masks: {
        cardNumber: '' // optional - mask card number
    },

    // if true, will log helpful messages for setting up Card
    debug: false // optional - default false
});


</script>
</body>
</html>