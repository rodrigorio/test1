<?php

/**
 * Los page controllers implementan una interface en lugar de una clase abstracta por la simple razon de que 
 * las abstracciones pueden ser mucho mas especificas que generales.
 *
 * Es decir, con esto me aseguro que cumplan con el 'comando' que ejecuta el dispatcher
 * Despues con abstracciones yo puedo unificar metodos entre controladores de publicacion de Admin y de Index por ej.
 *
 *
 * En los page controllers hay metodos que facilitan el proceso de las vistas!. 
 * De lo contrario usar metodos de los controllers de modelo
 */
interface PageControllerInterface
{
    /**
     * Class constructor
     *
     * When overriding the constructor, please consider this usage as a best
     * practice and ensure that each is registered appropriately; the easiest
     * way to do so is to simply call parent::__construct($request, $invokeArgs).
     *
     *
     * @param HttpRequest $request
     * @param array $invokeArgs Any additional invocation arguments
     * @return void
     */	
    public function __construct(HttpRequest $request, Response $response, array $invokeArgs = array());
	
    /**
     * Dispatch the requested action
     *
     * @param string $action Method name of action
     * @return void
     */
    public function dispatch($action);	
	
    /**
    * Todo controller va a tener una accion index(), por ejemplo la pagina de bienvenida o listado, etc.
    * Tambien puede ser un adapter para que exista la posibilidad de marcar la funcion por defecto de un controlador.
    * Este metodo es de especial utilidad cuando se redirecciona a un modulo/controlador/ despues de alguna excepcion.
    *
    *
    * Despues de mucho pensar, la conclusion es que esta funcion siempre debe estar disponible,
    * y si es utilizada como adapter debe redireccionar a una accion que siempre este disponible.
    * EL UNICO CASO EN EL QUE INDEX() SEA DESACTIVADA ES CUANDO SE DESACTIVA TODO EL MODULO.
    * EN CUALQUIER OTRO CASO SE REDIRECCIONA A OTRO METODO DEL CONTROLADOR
    *
    * Por ejemplo si estoy en el modulo de 'registrados/' y accedo a una accion
    * que no tengo permitida o esta inhabilitada me redirecciona a registracion->index() con un parametro en
    * $request que consiste en el mensaje a mostrar en un dialog o mensaje.
    *
    * Supongamos el caso especial de un blog que la accion por defecto es mostrar el ultimo post.
    * Si index() redirecciona a ampliarPublicacion() entonces este metodo no puede estar inhabilitado.
    * Si se quiere inhabilitar la visualizacion de publicaciones, entonces se debe prohibir ampliarPublicacion()
    * y redireccionar otro metodo en index()
    *
    */
    public function index();		
}