<?php

/**
 *	Aca se ponen todas las funciones que uno crea que son utiles y se usaran en mas de un lado.
 * @author Rodrigo A. Rio
 */
class Utils{
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
    
    /**
     * 
     * Devuelve una fecha en formato valido para sql yyyy-mm-dd HH:MM:SS
     * @param String $dFecha con formato:
     * dd-mm-yyyy o dd/mm/yyyy
     * @param boolean $time
     */
    public static function fechaAFormatoSQL($dFecha,$time = FALSE){
    	$vFecha  = preg_split("/ /",$dFecha);
		$vFechaD = preg_split("/(\/)|(-)/", $vFecha[0]);
		$fechaForm = $vFechaD[2]."-".$vFechaD[1]."-".$vFechaD[0];
		if($time){
			$sTime	= $vFecha[1];
			$fechaForm.= " ".$sTime;			
		}	
    	return $fechaForm;
    }

    /**
     * Devuelve la diferencia en dias entre fecha desde y fecha hasta.
     *
     * los parametros tienen que ser en formato '2009-12-20 20:12:10'
     *
     * el string para formatear en ese tipo desde php es "Y-m-d H:i:s"
     */
    public static function dateDiffDays($dFechaDesde, $dFechaHasta)
    {
        $dFechaDesde = strtotime($dFechaDesde);
        $dFechaHasta = strtotime($dFechaHasta);

        $seconds_diff = $dFechaHasta - $dFechaDesde;

        return floor($seconds_diff/3600/24);
    }

    /**
     * Genera un password aleatorio
     *
     */
    public static function generarPassword($longitud = 8)
    {
       $conjunto_caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890*.+=-_#&";
       $str = "";
       for($i=0; $i<$longitud; $i++){
          $str .= $conjunto_caracteres{rand() % strlen( $conjunto_caracteres)};
       }
       return $str;
    }
}