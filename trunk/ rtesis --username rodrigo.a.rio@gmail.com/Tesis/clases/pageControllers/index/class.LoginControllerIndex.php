<?php
/**
 * @author Matias Velilla
 *
 * Si se hace submit del formulario se redirecciona dependiendo si existe o no el codigo de error 401
 * Si existe se redirecciona al request original (la pagina restringida que se solicitaba)
 * Si no existe se redirecciona a la url por defecto que dependera del perfil del usuario que se loguea.
 */
class LoginControllerIndex extends PageControllerAbstract
{
    public function index()
    {
        $this->mostrarFormulario();
    }

    public function mostrarFormulario()
    {
        //si esta seteado en el request el param MsgError muestro cartel con el texto.
        echo "formulario login. msg error: ".$this->getRequest()->getParam('msgError')."<br><br>";
        echo "formulario login. msg informacion: ".$this->getRequest()->getParam('msgInfo');
    }
}