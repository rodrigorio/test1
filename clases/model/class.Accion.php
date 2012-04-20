<?php
/*
 * Esta clase se usa en el administrador para listar, modificar y guardar acciones del sistema.
 * (las que se asocian a los distintos perfiles luego).
 *
 * Solo se usa en el administrador,
 * a los objetos que heredan de PerfilAbstract se les asocia un array de permisos porque es mas eficiente la ejecucion
 */
class Accion{

    private $iId;
    private $sModulo;
    private $iControladorId = null;
    private $sControlador;
    private $sNombre;
    /**
     * Ojo que este NO es el id de la tabla perfiles sino el id de los grupos de perfiles asociados a las acciones
     */
    private $iGrupoPerfilId;
    private $bActivo;

    /**
     *  Se pasa un objeto stdClass y para cada atributo de este objeto se verifica que exista para la clase
     * @param stdClass $oParams
     */
    public function __construct(stdClass $oParams = null){
        $vArray = get_object_vars($oParams);
        $vThisVars = get_class_vars(__CLASS__);
        if(is_array($vArray)){
            foreach($vArray as $varName => $value){
                if(array_key_exists($varName,$vThisVars)){
                    $this->$varName = $value;
                }else{
                    throw new Exception("Unknown property $varName in "  . __CLASS__,-1);
                }
            }
        }
    }

    public function setId($iId)
    {
        $this->iId = $iId;
    }
    public function setModulo($sModulo)
    {
        $this->sModulo = $sModulo;
    }
    public function setControlador($sControlador)
    {
        $this->sControlador = $sControlador;
    }
    public function setControladorId($iControladorId)
    {
        $this->iControladorId = $iControladorId;
    }
    public function setNombre($sNombre)
    {
        $this->sNombre = $sNombre;
    }
    public function setGrupoPerfilId($iGrupoPerfilId)
    {
        $this->iGrupoPerfilId = $iGrupoPerfilId;
    }

    public function getId()
    {
        return $this->iId;
    }
    public function getModulo()
    {
        return $this->sModulo;
    }
    public function getControlador()
    {
        return $this->sControlador;
    }
    public function getControladorId()
    {
        return $this->iControladorId;
    }
    public function getNombre()
    {
        return $this->sNombre;
    }
    public function getGrupoPerfilId()
    {
        return $this->iGrupoPerfilId;
    }
    public function getNombreGrupoPerfil()
    {
        $sGrupoPerfil = "";
        switch($this->iGrupoPerfilId){
            case '1': $sGrupoPerfil = "Administrador"; break;
            case '2': $sGrupoPerfil = "Moderador"; break;
            case '3': $sGrupoPerfil = "Integrante Activo"; break;
            case '4': $sGrupoPerfil = "Integrante Inactivo"; break;
            case '5': $sGrupoPerfil = "Visitante"; break;
        }

        return $sGrupoPerfil;
    }

    public function isActivo($flag = null){
        if(null !== $flag){
            $this->bActivo = $flag ? true : false;
        }else{
            return $this->bActivo;
        }
    }
}
