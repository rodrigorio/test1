<?php

abstract class FotoIntermediary extends Intermediary{
    abstract public function guardarFotoPerfil($oUsuario);
    abstract public function insertarAsociado($oFoto, $iIdItem, $sObjetoAsociado);
}