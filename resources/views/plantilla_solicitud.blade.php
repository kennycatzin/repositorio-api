<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Document</title>

    </head>
    <body>
        <style type="text/css">
            .body {
                padding: 2%;
                font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
                /* background-image: url('https://us.123rf.com/450wm/gritsalak/gritsalak1703/gritsalak170300063/74357419-plantilla-de-fondo-para-la-publicidad-y-el-vector-de-presentaci%C3%B3n.jpg?ver=6');
                background-size: cover;
                background-repeat: no-repeat;
                border-radius: 15px; */
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
            .firma {
                width: 450px;
            }
        
            .divisor {
                background-color: #a1a1a1;
                height: 4px;
            }
        
            .flexContainer {
                text-align: center;
            }
        
            .left {
                text-align: end;  
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
            table {
           width: 100%;
           border: 1px solid #000;
           border-radius: 3px;
        }
        .equipos {
           text-align: left;
           vertical-align: top;
           border: 1px solid #000;
           
           border-collapse: collapse;
           padding: 0.3em;
           
          
        }
        .desc {
            width: 30%;
        }
        .firma {
            width: 180px;
            height: 100px;
        }
        
        th {
   background: #080845;
   color: white;
}
.flaco{
    align-self: auto;
    width: 85%;
    margin: auto;
}

        </style>
        <div class="body">
            <div class="">
                
                <div class="left">
                    <img src="https://afavor.mx/wp-content/uploads/2021/09/a-favor-logo-1.png" class="image">
                </div>
                
            </div>
            <div class="flexContainer">
                
                <h3 class="">Diagnóstico y reparación de equipos</h3>
                <h4>Departamento / Sucursal:  {{ $data['data'][0]['tipo_equipo'] }}</h4>

                
            </div>           
            <table>
                <tr>
                  <th class="equipos">Equipo</th>
                  <th class="equipos desc">Falla reportada</th>
                  <th class="equipos">Descripción</th>
                  <th class="equipos">Estatus</th>    
                </tr>            
                @foreach ($data['data'] as $equipos) 
                    <tr>
                        <td class="equipos">{{ $equipos['tipo_equipo'] }}</td>
                        <td class="equipos">{{ $equipos['observaciones'] }}</td>
                        <td class="equipos">{{ $equipos['marca'] }} / M- {{ $equipos['numero_serie'] }} / NS- {{ $equipos['modelo'] }}</td>
                        <td class="equipos">{{ $equipos['esta_detalle'] }}</td>
                    </tr>
                @endforeach
              </table>     
              
              
    
            <br>
            <div class="flaco">
                <table>
                    <tr>
                      <th class="equipos ">Envía</th>
                      <th class="equipos ">Revisa</th>
                      <th class="equipos ">Recibe</th>
        
                    </tr>
                    <tr>
                      <td class="equipos largo">
                        {{-- <img src="http://172.18.3.7/repositorio-api/public/upload/usuarios/0001.jpg" class="firma"> --}}
                        {{-- <img src="{{url('img/0001.jpg')}}" class="firma"> --}}
                        {{ $data['data'][0]['nombre_usuario'] }}
                      </td>
                      <td class="equipos largo">
                        {{-- <img src="{{url('/upload/usuarios/0001.jpg')}}" class="firma"> --}}

                      {{-- <img src="{{url('img/0001.jpg')}}" class="firma"> --}}

                      </td>
                      <td class="equipos largo">
                        {{-- <img src="{{url('img/1.png')}}" class="firma"> --}}
                        

                      </td>
                    </tr>
                   
                  </table>
            </div>
            
            
        </div>
    </body>
</html>