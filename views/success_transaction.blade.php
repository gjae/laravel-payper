<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/laravelpayper/css/card.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>Transacción satisfactoria</title>
    <style>

        html, body, h1, h2, h3, h4, h5, h6, div {
            padding: 0;
            border: 0;
            margin: 0;
        }
        .top {
            padding: 0;
            height: 170px;
            background-color: #ef9a9a;
            margin: 0px;
            height: 330px;
        }
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

        .clearfix:before,
        .clearfix:after {
            content: "";
            display: table;
        }

        .clearfix:after {
            clear: both;
        }

        .clearfix {
            zoom: 1; /* ie 6/7 */
        }

        @media (max-width: 500px){
            .top{
                height: 250px;
            }
        }
    </style>
</head>
<body>


<div class="clearfix">
    <div class="row top" style="background-color: #388e3c;">
        <div class="row" style="padding-top: 4em;" >
            <div class="s12" style="text-align: center;">
                <i class="material-icons" style="color: #ffffff; font-size: 12em;">beenhere</i>
            </div>
        </div>
        <div class="row row-card" style=" margin-top: 1em;">
            <div class="col s10 offset-s1">
                <div class="card-panel white">
                    <span class="white-black">
                        Pago realizado satisfactoriamente bajo la referencia <strong>{{ $transaction->reference }}</strong> bajo el codigo bancario <strong>{{ $transaction->tran_nbr }}</strong>,
                        recibira una notificación via email de su operación. <br>
                        gracias por su confianza.
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="">

</div>
</div>
    

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="{{ asset("vendor/laravelpayper/js/jquery.card.js") }}"></script>
<script src="{{ asset("vendor/laravelpayper/js/card.js") }}"></script>
</body>
</html>