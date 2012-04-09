<?php

abstract class FotoIntermediary extends Intermediary{
    abstract public function guardarFotoPerfil(PersonaAbstract $oPersona);
    abstract public function insertarAsociado($oFoto, $iIdItem, $sObjetoAsociado);
}