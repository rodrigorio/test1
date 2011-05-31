<?php

/**
 * 	Action Controller Publicaciones
 */
class IndexControllerAdmin extends PageControllerAbstract
{
    /**
     *  En este caso index no adapta a otro metodo
     */
    public function index()
    {
        echo $this->getRequest()->getParam('msgInfo')."<br><br>";
        echo 'index de admin';
    }
}