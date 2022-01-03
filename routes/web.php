<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->post('login', 'AuthController@login');
        $router->get('renovar/{id}', 'AuthController@renovarToken');
     }); 
     $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('profile', 'UserController@profile');
        $router->get('users/{id}', 'UserController@singleUser');
        $router->get('users', 'UserController@allUsers');
        $router->put('update-user/{id_usuario}', 'UserController@updateUser');
        $router->post('asignar-rol-usuario', 'UserController@asignarRolUsuario');
        $router->put('eliminar-archivo-usuario', 'UserController@eliminarArchivo');
        $router->post('asignar-archivo-temporal', 'UserController@asignarArchivoUsuarioTemporal');
        $router->post('get-listado-archivos', 'UserController@getListadoArchivos');
        $router->post('get-listado-cabeceras', 'UserController@getCatSubCategorias');
        $router->post('set-archivo-estatus', 'UserController@setEstatusArchivoUsuario');
        $router->get('get-dashboard/{id_usuario}', 'UserController@totalesDashboard');
        $router->get('get-usuarios-noasignados', 'UserController@usuariosSinAsignar');
        $router->put('set-usuario-baja/{id_usuario}', 'UserController@setActivoFalsoUsuario');

        
        
     });
     $router->group(['prefix' => 'categoria'], function () use ($router) {
        $router->post('store-categoria', 'CategoriaController@storeCategoria');
        $router->put('update-categoria/{id_categoria}', 'CategoriaController@updateCategoria');
        $router->get('get-categorias', 'CategoriaController@getCategorias');
        $router->get('get-categoria/{id_categoria}', 'CategoriaController@getCategoria');
        $router->get('get-listado-documentos', 'CategoriaController@getListadoDocumentos');
        $router->put('baja-categoria/{id_categoria}', 'CategoriaController@bajaCategoria');
        
     });
     $router->group(['prefix' => 'subcategoria'], function () use ($router) {
        $router->post('store-subcategoria', 'SubcategoriaController@storeSubcategoria');
        $router->put('update-subcategoria/{id_subcategoria}', 'SubcategoriaController@updateSubcategoria');
        $router->get('get-subcategorias', 'SubcategoriaController@getSubcategorias');
        $router->get('get-subcategoria/{id_subcategoria}', 'SubcategoriaController@getSubcategoria');
     });
     $router->group(['prefix' => 'rol'], function () use ($router) {
        $router->post('store-rol', 'RolController@storeRol');
        $router->put('update-rol/{id_rol}', 'RolController@updateRol');
        $router->get('get-roles', 'RolController@getRoles');
        $router->get('get-rol/{id_rol}', 'RolController@getRol');
        $router->post('store-conf', 'RolController@storeConfRolArchivo');
        $router->put('eliminar-archivo-rol', 'RolController@eliminarArchivo');
     });
     $router->group(['prefix' => 'tipo'], function () use ($router) {
        $router->post('store-tipo', 'TipoController@storeTipo');
        $router->put('update-tipo/{id_tipo}', 'TipoController@updateTipo');
        $router->get('get-tipos', 'TipoController@getTipos');
        $router->get('get-tipo/{id_tipo}', 'TipoController@getTipo');
        $router->get('hola', 'TipoController@getHola');
     });
     $router->group(['prefix' => 'archivo'], function () use ($router) {
        $router->post('store-archivo', 'ArchivoController@storeArchivo');
        $router->put('update-archivo/{id_archivo}', 'ArchivoController@updateArchivo');
        $router->get('get-archivos', 'ArchivoController@getArchivos');
        $router->get('get-archivo/{id_archivo}', 'ArchivoController@getArchivo');
        $router->post('store-detalle-archivo', 'ArchivoController@guardarDetalleArchivo');
        $router->post('get-admin-configurar', 'ArchivoController@getAdminConfigurar');

     });
     $router->group(['prefix' => 'estatus'], function () use ($router) {
        $router->post('store-estatus', 'EstatusController@storeEstatus');
        $router->put('update-estatus/{id_estatus}', 'EstatusController@updateEstatus');
        $router->get('get-estatus', 'EstatusController@getEstatusAll');
        $router->get('get-estatus/{id_estatus}', 'EstatusController@getEstatus');
        $router->put('update-baja/{id_estatus}', 'EstatusController@setActivoFalsoEstatus');

        
     });
     $router->group(['prefix' => 'departamento'], function () use ($router) {
      $router->post('store-departamento', 'DepartamentoController@storeDepartamento');
      $router->put('update-departamento/{id_departamento}', 'DepartamentoController@updateDepartamento');
      $router->get('get-departamentos', 'DepartamentoController@getDepartamentos');
      $router->get('get-departamento/{id_departamento}', 'DepartamentoController@getDepartamento');
   });
      $router->group(['prefix' => 'tablero'], function () use ($router) {
         $router->post('store-tablero', 'TableroController@storeTablero');
         $router->get('get-tableros', 'TableroController@getTableros');
         $router->get('get-tablero/{id_tablero}', 'TableroController@getTablero');
         $router->post('update-tablero/{id_tablero}', 'TableroController@updateTablero');

      });
 });
