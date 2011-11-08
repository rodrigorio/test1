<?php

abstract class ArchivoIntermediary extends Intermediary{
    abstract public function guardarCurriculumVitae($oUsuario);
    abstract public function insertarAsociado($oArchivo, $iIdItem, $sObjetoAsociado);
}