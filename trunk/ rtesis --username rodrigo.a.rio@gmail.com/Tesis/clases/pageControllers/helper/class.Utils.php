<?php

/**
 *	Aca se ponen todas las funciones que uno crea que son utiles y se usaran en mas de un lado.
 * @author Rodrigo A. Rio
 */
class Utils {
	/**
	 * 
	 * Devuelve un fecha con el formato pasado como parametro, recibiendo una fecha con formato valido:
	 * mm/dd/yyyy
	 * mm/dd/yy
	 * yyyy/mm/dd
	 * dd-mm-yyyy
	 * yy-mm-dd
	 * yyyy-mm-dd
	 * @param unknown_type $dFecha
	 * @param unknown_type $formato
	 */
    public static function fechaFormateada($dFecha,$formato="d/m/Y H:i:s"){
        $time = strtotime($dFecha);
        return date($formato,$time);
    }

}