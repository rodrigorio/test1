<?php
/**
 * Esta clase establece metodos para redireccionar desde las acciones de los pageControllers.
 *
 * NOTA: Se puede redireccionar por suspencion de sistema/modulo, porque hay un error 404,
 *       porque algún plugin detecta error, o porque no hay permiso segun perfil.
 *       Este helper es para redireccionar por OTROS motivos desde las propias acciones,
 *       por ejemplo si se guarda una entidad y luego se redirecciona a la pagina de listado con mensaje.
 *       Otro ejemplo es cuando existe permiso segun el perfil para realizar la accion pero otra condicion
 *       prohibe llevarla a cabo (como en facebook, que no te deja ver albunes de personas q no son amigos).
 *
 * Porque redireccionar con metodos de un objeto y no con un header() de php ?
 * Porque no hace falta cargar TODO de nuevo, el request esta en el loop del FrontController,
 * solo hace falta setear dispatched = false y luego modificar modulo, controlador y accion a la que tiene
 * que dirigirse luego de completada la accion actual.
 */
class RedirectorHelper {
    
}
?>
