<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Repositorio Interno</title>
    <style type="text/css">
.body{
            width: 60%;
            text-align: center;
            margin: auto;
        }
        .fuente{
            text-align: start;
            color: #6E799F;
            font-size: 16px; 
        }
        .btn{
            padding: 10px 60px;
            background-color: #00b6f0;
            color: white;
            font-size: 16px;
            margin: auto;
            display: inline-block;
            cursor: pointer;
            text-decoration: none;
        }
        A:link {text-decoration: none }
        .space{
            margin-bottom: 10px;
        }
        .image{
            width: 200px;
        }
        .divisor{
            background-color: #a1a1a1;
            height: 4px;
        }
  
        .flexContainer { 
   
            margin: 2px 10px;
            display: flex;
        } 
 
 .left {
   flex-basis : 30%;
 }
 
 .right {
   flex-basis : 30%;
   margin-left: 170px;
 }
        .row{
            font-size: 14px;
            color: #6d6868;
            padding: 0px 15px;
            margin-top: 0px;
        }
        .columna{
            display: inline-block;
            margin-top: 0px;
        }
        .respuesta{
            display: inline-block;
            margin-top: 0px;
            font-family: source_sans_pro_regular;
            font-size: 14px;
            border: 0px;
            border-bottom: 1px #6d6868 solid;
            width: auto;
        }
        @media screen and (max-width: 600px){
            .body{
            width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="body">
        <div class="flexContainer">
            <div class="left">  
            <img src="https://afavor.mx/wp-content/uploads/2021/09/a-favor-logo-1.png" class="image">
            </div>
            <div class="right">
                <h2 >Repositorio Interno A Favor</h2>
            </div>
        </div>
        <form id="enviar_post_correo">
            <hr class="divisor">
            <h4 class="fuente">Estimado equipo: {{$equipo}}</h4>
            <p class="fuente">Muchas gracias por estar al tanto de las notificaciones de la plataforma les informamos los siguientes cambios para el documento.</p>
            <br>
            <h4 class="fuente">Documento: {{$archivo}}</h4>
            <p class="fuente">{{$observaciones}}</p>
            <br>
            <p class="fuente">Sin otro particular reciban un cordial saludo.</p>
        </form>

        <br>
        <hr class="divisor">
        <div>
             <p class="fuente">Este correo es informativo, no es necesario responder. <br>
                Grupo a favor!<br>
        </div>
    </div>    
</body>
</html>