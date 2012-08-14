<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class ObjetivoAbstract
 *
 * @author Andres
 */
abstract class ObjetivoAbstract
{
    protected $id;
    protected $sDescripcion;
    
    /**
     *  @param int $iId
     */
    public function setId($iId){
        $this->iId = (int)$iId;
    }
	/**
     *  @return int $iId
     */
    public function getAreaId(){
        return $this->iAreaId ;
    }
}

