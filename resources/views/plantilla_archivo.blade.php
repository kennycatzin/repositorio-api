<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Repositorio Interno</title>
    <style type="text/css">
        .body {
            padding: 5%;
            width: 70%;
            text-align: center;
            margin: auto;
            background-image: url('https://us.123rf.com/450wm/gritsalak/gritsalak1703/gritsalak170300063/74357419-plantilla-de-fondo-para-la-publicidad-y-el-vector-de-presentaci%C3%B3n.jpg?ver=6');
            background-size: cover;
            background-repeat: no-repeat;
            border-radius: 15px;
        }

        .fuente {
            text-align: start;
            color: #393d42;
            font-size: 16px;
        }
        .archivo {
            text-align: center;
            color: #447AFF;
            font-size: 18px;
            font-weight: bold;

        }

        .btn {
            padding: 10px 60px;
            background-color: #00b6f0;
            color: white;
            font-size: 16px;
            margin: auto;
            display: inline-block;
            cursor: pointer;
            text-decoration: none;
        }

        A:link {
            text-decoration: none
        }

        .space {
            margin-bottom: 10px;
        }

        .image {
            width: 200px;
        }

        .divisor {
            background-color: #a1a1a1;
            height: 4px;
        }

        .flexContainer {

            margin: 2px 10px;
            display: flex;
        }

        .left {
            flex-basis: 30%;
            margin-left: 30%;
        }

        .right {
            flex-basis: 30%;
            width: 40%;
            height: 20%;
            border-top: 6px solid #3ACFD5;
            border-bottom: 6px solid #3a4ed5;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            background-position: 0 0, 100% 0;
            background-repeat: no-repeat;
            -webkit-background-size: 6px 100%;
            -moz-background-size: 6px 100%;
            background-size: 6px 100%;
            background-image: -webkit-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%), -webkit-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%);
            background-image: -moz-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%), -moz-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%);
            background-image: -o-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%), -o-linear-gradient(top, #3acfd5 0%, #3a4ed5 100%);
            background-image: linear-gradient(to bottom, #3acfd5 0%, #3a4ed5 100%), linear-gradient(to bottom, #3acfd5 0%, #3a4ed5 100%);
            border-radius: 14px;

        }

        .row {
            font-size: 14px;
            color: #6d6868;
            padding: 0px 15px;
            margin-top: 0px;
        }

        .columna {
            display: inline-block;
            margin-top: 0px;
        }

        .respuesta {
            display: inline-block;
            margin-top: 0px;
            font-family: source_sans_pro_regular;
            font-size: 14px;
            border: 0px;
            border-bottom: 1px #6d6868 solid;
            width: auto;
        }

        @media screen and (max-width: 600px) {
            .body {
                width: 100%;
            }
        }

    </style>
</head>

<body>
    <div class="body">
        <div class="flexContainer">
            
            <div class="right">
                <h2>Actualización de documento</h2>
            </div>
            <div class="left">
                <img src="https://afavor.mx/wp-content/uploads/2021/09/a-favor-logo-1.png" class="image">
            </div>
        </div>
        <form id="enviar_post_correo">
            <h4 class="fuente">Buen día Equipo </h4>
            <p class="fuente">Por este medio, hago de su conocimiento que el documento:</p>
            <h4 class="archivo">{{ $archivo }}</h4>
            <p class="fuente">{{ $observaciones }}</p>
            <br>
            <p class="fuente">Sin otro particular reciban un cordial saludo.</p>
            
        </form>

        <br>
        <hr class="divisor">
        <div>
            <p class="fuente">Este correo es informativo, no es necesario responder. 
        </div>
        <br>
    </div>
</body>

</html>
