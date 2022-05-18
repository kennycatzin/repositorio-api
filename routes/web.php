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
        $router->post('update-user/{id_usuario}', 'AuthController@updateUser');

     }); 
     $router->group(['prefix' => 'user'], function () use ($router) {
        $router->get('profile', 'UserController@profile');
        $router->get('users/{id}', 'UserController@singleUser');
        $router->get('users', 'UserController@allUsers');
        $router->post('asignar-rol-usuario', 'UserController@asignarRolUsuario');
        $router->put('eliminar-archivo-usuario', 'UserController@eliminarArchivo');
        $router->post('asignar-archivo-temporal', 'UserController@asignarArchivoUsuarioTemporal');
        $router->post('get-listado-archivos', 'UserController@getListadoArchivos');
        $router->post('get-listado-cabeceras', 'UserController@getCatSubCategorias');
        $router->post('set-archivo-estatus', 'UserController@setEstatusArchivoUsuario');
        $router->get('get-dashboard/{id_usuario}', 'UserController@totalesDashboard');
        $router->get('get-usuarios-noasignados', 'UserController@usuariosSinAsignar');
        $router->put('set-usuario-baja/{id_usuario}', 'UserController@setActivoFalsoUsuario');
        $router->get('get-repositorio-usuario/{id_usuario}', 'UserController@getCatSubArchivosUser');
        $router->get('get-data-admin/{index}', 'UserController@getUsuariosAdmin');
        $router->post('get-busqueda', 'UserController@getBusqueda');
        $router->post('guardar-usuario', 'UserController@guardarUsuarioAdmin');

        
        
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
        $router->put('baja-subcategoria/{id_subcategoria}', 'SubcategoriaController@bajaSubcategoria');
        $router->get('get-subcategoria-by-categoria/{id_categoria}', 'SubcategoriaController@getSubcategoriasByCategoria');

        
     });
     $router->group(['prefix' => 'rol'], function () use ($router) {
        $router->post('store-rol', 'RolController@storeRol');
        $router->put('update-rol/{id_rol}', 'RolController@updateRol');
        $router->get('get-roles', 'RolController@getRoles');
        $router->get('get-rol/{id_rol}', 'RolController@getRol');
        $router->post('store-conf-archivo', 'RolController@storeConfRolArchivo');
        $router->post('store-conf-rol', 'RolController@storeConfArchivoRol');        
        $router->put('eliminar-archivo-rol', 'RolController@eliminarArchivo');
        $router->get('get-roles-admin', 'RolController@getRolesAdmin');
        $router->get('get-data-admin/{id_rol}', 'RolController@getCatSubArchivosAdmin');
        $router->put('baja-rol/{id_rol}', 'RolController@bajaRol');
        $router->get('get-data-paginado/{index}', 'RolController@getRolesPaginado');
        $router->post('get-busqueda', 'RolController@busqueda');
        $router->get('get-roles-departamento/{id_departamento}', 'RolController@getRolesByDepartamento');        
     });
     $router->group(['prefix' => 'tipo'], function () use ($router) {
        $router->post('store-tipo', 'TipoController@storeTipo');
        $router->put('update-tipo/{id_tipo}', 'TipoController@updateTipo');
        $router->get('get-tipos', 'TipoController@getTipos');
        $router->get('get-tipo/{id_tipo}', 'TipoController@getTipo');
        $router->get('hola', 'TipoController@getHola');
        $router->put('baja-tipo', 'TipoController@bajaTipoDocumento');
     });
     $router->group(['prefix' => 'archivo'], function () use ($router) {
        $router->post('store-archivo', 'ArchivoController@storeArchivo');
        $router->post('update-archivo/{id_archivo}', 'ArchivoController@updateArchivo');
        $router->get('get-archivos', 'ArchivoController@getArchivos');
        $router->get('get-archivo/{id_archivo}', 'ArchivoController@getArchivo');
        $router->post('store-detalle-archivo', 'ArchivoController@guardarDetalleArchivo');
        $router->post('get-admin-configurar', 'ArchivoController@getAdminConfigurar');
        $router->put('delete-archivo', 'ArchivoController@bajaArchivo');    
        
        $router->get('get-aux-formulario-archivo', 'ArchivoController@getAuxFormularioArchivo');
        $router->post('get-busqueda-archivos', 'ArchivoController@busquedaArchivo');

        
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
         $router->put('baja-departamento/{id_departamento}', 'DepartamentoController@bajaDepartamento');
         $router->get('get-departamentos-paginado/{index}', 'DepartamentoController@getDepartamentosPaginado');
         $router->get('enviar-precio-metal', 'DepartamentoController@cambioPrecioEnviar');
         $router->post('enviar-bases-diarias', 'DepartamentoController@basesDiariasEnviar');
         $router->post('enviar-correo-general', 'DepartamentoController@correoGeneralEnviar');
         $router->post('enviar-correo-individual', 'DepartamentoController@mandarCorreoIndividual');
         $router->get('enviar-correo-pruebaaviso', 'DepartamentoController@pruebaAviso');     
         $router->get('enviar-correo-sucursales', 'DepartamentoController@pendientesSucursalesEnviar');     
      });
      $router->group(['prefix' => 'tablero'], function () use ($router) {
         $router->post('store-tablero', 'TableroController@storeTablero');
         $router->get('get-tableros', 'TableroController@getTableros');
         $router->get('get-tablero/{id_tablero}', 'TableroController@getTablero');
         $router->post('update-tablero/{id_tablero}', 'TableroController@updateTablero');
         $router->post('baja-tablero', 'TableroController@bajaAviso');
         $router->post('get-files-tablero', 'TableroController@getArchivosByEstatus');     
      });
      $router->group(['prefix' => 'marca'], function () use ($router) {
         $router->post('store-marca', 'MarcaController@storeMarca');
         $router->put('update-marca/{id_marca}', 'MarcaController@updateMarca');
         $router->get('get-marca', 'MarcaController@getMarcasAll');
         $router->get('get-marca/{id_marca}', 'MarcaController@getMarca');
         $router->post('eliminar-marca', 'MarcaController@setEliminarMarca');   
         $router->get('get-marca-paginado/{index}', 'MarcaController@getMarcasPaginado');
         $router->post('busqueda', 'MarcaController@busqueda');         
      });
      $router->group(['prefix' => 'licencia'], function () use ($router) {
         $router->post('store-item', 'LicenciaController@storeItem');
         $router->put('update-item/{id_item}', 'LicenciaController@updateItem');
         $router->get('get-item', 'LicenciaController@getAllItems');
         $router->get('get-item/{id_item}', 'LicenciaController@getItem');
         $router->put('eliminar-item', 'LicenciaController@setEliminarItem');   
         $router->get('get-item-paginado/{index}', 'LicenciaController@getItemsPaginado');
         $router->post('busqueda', 'LicenciaController@busqueda');    
         $router->get('get-estatus-licencias', 'LicenciaController@getEstatusLicencias');    

         
      });
      $router->group(['prefix' => 'tipo_equipo'], function () use ($router) {
         $router->post('store-item', 'TipoEquipoController@storeItem');
         $router->put('update-item/{id_item}', 'TipoEquipoController@updateItem');
         $router->get('get-item', 'TipoEquipoController@getAllItems');
         $router->get('get-item/{id_item}', 'TipoEquipoController@getItem');
         $router->put('eliminar-item/{id_item}', 'TipoEquipoController@setEliminarItem');   
         $router->get('get-item-paginado/{index}', 'TipoEquipoController@getItemsPaginado');
         $router->post('busqueda', 'TipoEquipoController@busqueda');     
         $router->get('get-items-admin', 'TipoEquipoController@getTipoEquipoAdmin');         
      });
      $router->group(['prefix' => 'equipo'], function () use ($router) {
         $router->post('store-item', 'EquipoController@storeItem');
         $router->put('update-item/{id_item}', 'EquipoController@updateItem');
         $router->get('get-item', 'EquipoController@getAllItems');
         $router->get('get-item/{id_item}', 'EquipoController@getItem');
         $router->put('eliminar-item/{id_item}', 'EquipoController@setEliminarItem');   
         $router->get('get-item-paginado/{index}', 'EquipoController@getItemsPaginado');
         $router->post('busqueda', 'EquipoController@busqueda');   
         $router->post('get-disponibles-admin', 'EquipoController@getListaEquiposAdmin');  
         $router->get('get-helpers', 'EquipoController@getHelpers');
         $router->get('get-licencias-disponibles/{id_licencia_o}/{id_licencia_w}', 'EquipoController@getLicenciasDisponibles');

      });
      $router->group(['prefix' => 'articulo'], function () use ($router) {
         $router->post('store-item', 'ArticuloController@storeItem');
         $router->put('update-item/{id_item}', 'ArticuloController@updateItem');
         $router->get('get-item', 'ArticuloController@getAllItems');
         $router->get('get-item/{id_item}', 'ArticuloController@getItem');
         $router->put('eliminar-item/{id_item}', 'ArticuloController@setEliminarItem');   
         $router->get('get-item-paginado/{index}', 'ArticuloController@getItemsPaginado');
         $router->post('busqueda', 'ArticuloController@busqueda');         
      });
      $router->group(['prefix' => 'sucursal'], function () use ($router) {
         $router->post('store-item', 'SucursalController@storeItem');
         $router->put('update-item/{id_item}', 'SucursalController@updateItem');
         $router->get('get-item', 'SucursalController@getAllItems');
         $router->get('get-item/{id_item}', 'SucursalController@getItem');
         $router->put('eliminar-item/{id_item}', 'SucursalController@setEliminarItem');   
         $router->get('get-item-paginado/{index}', 'SucursalController@getItemsPaginado');
         $router->post('busqueda', 'SucursalController@busqueda');    
         $router->get('get-items-admin', 'SucursalController@getSucursalesAdmin');         
      });
      $router->group(['prefix' => 'asignacion'], function () use ($router) {
         $router->get('get-asignaciones-paginado/{index}', 'AsignacionController@getAsignaciones');
         $router->post('store-configuracion', 'AsignacionController@storeConfiguracion');
         $router->post('delete-configuracion', 'AsignacionController@setAsignacionEliminar');
         $router->post('store-solicitud', 'AsignacionController@storeSolicitud');         
         $router->post('atender-detalle-solicitud', 'AsignacionController@atenderDetalleSolicitud');
         $router->post('eliminar-asignacion-configuracion', 'AsignacionController@eliminarAsignacionEstatus');         
         $router->post('get-asignacion-detalle', 'AsignacionController@getDetEquipos');         
      });
      $router->group(['prefix' => 'solicitud'], function () use ($router) {
         $router->get('get-equipos/{id_usuario}', 'SolicitudController@getEquipos');
         $router->get('get-estatus-agregar', 'SolicitudController@getListaEstatusAgregar');
         $router->post('store-solicitud', 'SolicitudController@storeSolicitud');        
         $router->get('get-solicitudes/{tipo}', 'SolicitudController@getSolicitudes');
         $router->get('imprimir/{id_solicitud}', 'SolicitudController@imprimir');

      });
      $router->group(['prefix' => 'reporte'], function () use ($router) {
         $router->get('get-documentosporrol', 'ReporteController@getDocumentosPorRol');
         $router->get('get-info-usuarios', 'ReporteController@getInfoUsuarios');

         
      });

      
 });
