<?php

/**
 * Helper para page controllers donde se suben archivos y fotos
 *
 * @author Matias Velilla
 *
 */
class UploadHelper extends FileManagerAbstract
{
    /**
     * Todas las medidas en pixeles.
     */
    const ANCHO_FOTO_GRANDE = 900; 
    const ALTO_FOTO_GRANDE = 550;
    /**
     * Thumbnail galerias, todas las fotos minimo estas dimensiones
     * Las dimensiones de los thumbnails medianos y chicos corresponde con el .css de vistas.css
     */
    const ANCHO_FOTO_MEDIANA = 170; 
    const ALTO_FOTO_MEDIANA = 120;

    const ANCHO_FOTO_CHICA = 48;
    const ALTO_FOTO_CHICA = 48;
   
    /**
     * Tamanio maximo actual para los archivos que se vayan a subir
     * (es en .kb, 5000 = 5mb aprox)
     */
    private $tamanioMaximo;
    
    /**
     * Este valor tiene que corresponder con el utilizado en el form del upload
     * es la constante que usa php para verificar q el tamanio no exceda el upload
     * se especifica en bytes
     */
    private $maxFileSize;

    /**
     * Para evitar tener que resizear imagenes ultra gigantes
     * (en pixeles)
     */
    private $anchoMaximoFoto;
    private $altoMaximoFoto;

    /**
     * Inicializa los atributos con valores por defecto
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->tamanioMaximo = 5000; //kb
        $this->maxFileSize = 5000000; //bytes

        $this->anchoMaximoFoto = 4000;
        $this->altoMaximoFoto = 4000;
    }
    
    public function setTamanioMaximoUploads($tamanio)
    {
        $this->tamanioMaximo = $tamanio;
        return $this;
    }

    public function getTamanioMaximo()
    {
        return $this->tamanioMaximo;
    }

    public function setDimensionMaximaFoto($alto, $ancho)
    {
        $this->altoMaximoFoto = $alto;
        $this->anchoMaximoFoto = $ancho;
        return $this;
    }

    public function getDimensionMaximaFoto()
    {
        return array($this->altoMaximoFoto, $this->anchoMaximoFoto);
    }

    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
        return $this;
    }

    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * Devuelve mensaje de error segun el codigo generado por PHP
     * En los comentarios se muestra la explicación original del error.
     * La función devuelve una traducción de esos errores.
     *
     * @param integer $errorCode valor de $_FILES['error'] del archivo subido.
     */
    private function traducirdErrorMessage($errorCode)
    {
        //paso el max file size a megas
        $maxFileSizeMb = number_format($this->maxFileSize / pow(1024, 2), 2, '.', '');
        $maxFileSizeIniMb = number_format(ini_get('upload_max_filesize') / pow(1024, 2), 2, '.', '');
        
        switch ($error_code) {
            case UPLOAD_ERR_OK:
                /* Value 0: 'The file uploaded with success' */
                $message = 'Archivo subido exitosamente';
            break;
            case UPLOAD_ERR_INI_SIZE:
                /* Value 1: 'The uploaded file exceeds the upload_max_filesize directive in php.ini' */
                $message = 'El tama&ntilde;o del archivo supera el m&aacute;ximo permitido por el servidor ('.$maxFileSizeIniMb.')';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                /* Value 2: 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'; */
                $message = 'El tama&ntilde;o del archivo supera el m&aacute;ximo permitido ('.$maxFileSizeMb.' MB)';
                break;
            case UPLOAD_ERR_PARTIAL:
                /* Value 3: 'The uploaded file was only partially uploaded'; */
                $message = 'El archivo fue subido s&oacute;lo parcialmente';
                break;
            case UPLOAD_ERR_NO_FILE:
                /* Value 4: 'No file was uploaded' */
                $message = 'No se subi&oacute; ning&uacute;n archivo';
                break;

            // No, no existe un código con valor 5... :P

            case UPLOAD_ERR_NO_TMP_DIR:
                /* Value 6: 'Missing a temporary folder' */
                $message = 'Se perdi&oacute; una carpeta temporal';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                /* Value 7: Failed to write file to disk */
                $message = 'Fall&oacute; la escritura del archivo en el disco';
                break;
            case UPLOAD_ERR_EXTENSION:
                /* Value 8: File upload stopped by extension */
                $message = 'Subida del archivo cancelada';
                break;
            default:
                /* 'Unknown upload error' */
                $message = 'Error desconocido al subir el archivo';
                break;
        }
        return $message;
    }

    /**
     *  Se fija la variable $_FILES y comprueba que no se hayan producido errores durante el upload.
     * 
     *  @param string $inputFileName El nombre del input en el formulario
     */
    public function verificarUpload($inputFileName = 'archivo')
    {        
        if(!empty($_FILES)){
            $file_error = intval($_FILES[$inputFileName]['error']);
            $file_size = $_FILES[$inputFileName]['size'];
            $file_type = $_FILES[$inputFileName]['type'];
            $file_tmp_name = $_FILES[$inputFileName]['tmp_name'];
            $file_name = $_FILES[$inputFileName]['name'];
        }else{
            //directamente hubo error en el submit del formulario
            throw new Exception("Hubo un error al procesar el envío. Intentelo nuevamente.");
            return;
        }

        if ($file_error != UPLOAD_ERR_OK){
            throw new Exception($this->traducirdErrorMessage($file_error));
            return;
        }
        
        //no dio error de PHP me fijo si cumple con el tipo mime permitido
        if(!array_key_exists($file_type, $this->getTiposValidos())){
            $mensaje = "El tipo del archivo no es v&aacute;lido.<br>
                        S&oacute;lo se permiten archivos: ".$this->getStringTiposValidos()."<br>
                        Su archivo es de tipo ".str_replace("image/", "", $file_type).".";

            throw new Exception($mensaje);
            return;
        }

        //no hubo error
        return true;
    }
    
    /**
     * Similar a verificarUpload() pero agrega el analisis de las dimensiones de la foto.
     *
     * @param string $inputFileName El nombre del input en el formulario
     */
    public function verificarUploadFoto($inputFileName = 'archivo')
    {
        try{
            if($this->verificarUpload($inputFileName)){

                //verifico ancho y alto maximo y despues que tenga el minimo necesario para que se puedan generar los thumbnails
                $tmpName = $_FILES[$inputFileName]['tmp_name'];
                list($ancho, $alto, $mimeType, $attr) = getimagesize($tmpName);

                if($ancho > $this->anchoMaximoFoto){
                    $mensaje = "El ancho de la imagen es mayor al m&aacute;ximo
                                permitido (".$this->anchoMaximoFoto." pixels).<br>
                                Su archivo tiene $ancho pixels.";
                    throw new Exception($mensaje);
                    return;
                }

                if($alto > $this->altoMaximoFoto){
                    $mensaje = "El alto de la imagen es mayor al m&aacute;ximo
                                permitido (".$this->altoMaximoFoto." pixels).<br>
                                Su archivo tiene $alto pixels.";
                    throw new Exception($mensaje);
                    return;
                }

                if($ancho < self::ANCHO_FOTO_MEDIANA){
                    $mensaje = "El ancho de la imagen es menor al m&iacute;nimo
                                permitido (".self::ANCHO_FOTO_MEDIANA." pixels).<br>
                                Su archivo tiene $ancho pixels.";
                    throw new Exception($mensaje);
                    return;
                }

                if($alto < self::ALTO_FOTO_MEDIANA){
                    $mensaje = "El alto de la imagen es menor al m&iacute;nimo
                                permitido (".self::ALTO_FOTO_MEDIANA." pixels).<br>
                                Su archivo tiene $alto pixels.";
                    throw new Exception($mensaje);
                    return;
                }

                return true;
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    public function generarArchivoSistema($idItem = "", $extra = "", $inputFileName = 'archivo')
    {
        try{
            $fileName = $_FILES[$inputFileName]["name"];
            $fileType = $_FILES[$inputFileName]['type'];
            $fileSize = $_FILES[$inputFileName]['size'];
            $nombreArchivo = $this->generarNombreArchivo($fileName, $idItem, $extra);
            $rutaDestino = $this->getDirectorioUploadArchivos(true).$nombreArchivo;
            
            move_uploaded_file($_FILES[$inputFileName]["tmp_name"], $rutaDestino);            

            return array($fileName, $fileType, $fileSize, $nombreArchivo);
        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Toma el archivo temporal obtenio desde el formulario y genera las fotos
     * que van a ser utilizadas en el sistema.
     *
     * El archivo temporal enviado desde el formulario debe estar previamente verificado
     *
     * @return array Con cada uno de los nombres generados para cada uno de los tamanios de fotos
     */
    public function generarFotosSistema($idItem = "", $inputFileName = 'archivo')
    {
        try{
            //Genero los nombres para los (por ahora) tres tamanios de fotos utilizados (big, medium, small)
            $fileName = $_FILES[$inputFileName]["name"];

            $aNombreArchivos = array();
            $aNombreArchivos['nombreFotoGrande'] = $this->generarNombreArchivo($fileName, $idItem, "big");
            $aNombreArchivos['nombreFotoMediana'] = $this->generarNombreArchivo($fileName, $idItem, "medium");
            $aNombreArchivos['nombreFotoChica'] = $this->generarNombreArchivo($fileName, $idItem, "small");

            //genera las fotos y las guarda en el servidor.
            $this->generarFotoRedimensionada(self::ANCHO_FOTO_GRANDE, self::ALTO_FOTO_GRANDE, $this->getDirectorioUploadFotos(true).$aNombreArchivos['nombreFotoGrande'], true, $inputFileName);
            $this->generarFotoRedimensionada(self::ANCHO_FOTO_MEDIANA, self::ALTO_FOTO_MEDIANA, $this->getDirectorioUploadFotos(true).$aNombreArchivos['nombreFotoMediana'], false, $inputFileName);
            $this->generarFotoRedimensionada(self::ANCHO_FOTO_CHICA, self::ALTO_FOTO_CHICA, $this->getDirectorioUploadFotos(true).$aNombreArchivos['nombreFotoChica'], false, $inputFileName);

            return $aNombreArchivos;

        }catch(Exception $e){
            throw new Exception($e->getMessage(), 0);
        }
    }

    /**
     * Generar una foto con las dimensiones transformadas según los valores de referencia.
     */
    private function generarFotoRedimensionada($anchoMaximo, $altoMaximo, $rutaDestino, $isBigSize = false, $inputFileName = 'archivo'){

        //Obtener información de la imagen original:
        $tmpName = $_FILES[$inputFileName]['tmp_name'];
        $mimeType = $_FILES[$inputFileName]['type'];
                
        list($anchoActual, $altoActual, $iMimeType, $attr) = getimagesize($tmpName);

        //la foto de tamaño grande solo se redimensiona si se pasa de las dimensiones maximas,
        //es decir: NO SE AGRANDA, SOLO SE REDUCE
        //Si es la foto grande y si es menor que el tamaño maximo no redimensiono y copio de una
        if($isBigSize && $anchoActual < $anchoMaximo && $altoActual < $altoMaximo){
            copy($tmpName, $rutaDestino);
            return;
        }

        list($anchoRedimension, $altoRedimension) = $this->recalcularDimensionesFoto($anchoActual, $altoActual, $anchoMaximo, $altoMaximo);

        //Solo si la imagen cambio de dimensiones, sino copio el archivo de una.
        if($anchoActual != $anchoRedimension || $altoActual != $altoRedimension){
            
            //Crear con las dimensiones calculadas:
            $newImg = imagecreatetruecolor($anchoRedimension, $altoRedimension);
                        
            switch($mimeType){
                case 'image/gif':   $imagenOrigen = imagecreatefromgif($tmpName); break;
                case 'image/pjpeg': $imagenOrigen = imagecreatefromjpeg($tmpName); break;
                case 'image/jpeg':  $imagenOrigen = imagecreatefromjpeg($tmpName); break;
                case 'image/png':   $imagenOrigen = imagecreatefrompng($tmpName); break;
            }

            imagecopyresampled($newImg, $imagenOrigen, 0, 0, 0, 0, $anchoRedimension, $altoRedimension, imagesx($imagenOrigen), imagesy($imagenOrigen));

            //Graba con el nombre de destino:
            switch($mimeType){
                case 'image/gif':   imagegif($newImg, $rutaDestino); break;
                case 'image/pjpeg': imagejpeg($newImg, $rutaDestino); break;
                case 'image/jpeg':  imagejpeg($newImg, $rutaDestino); break;
                case 'image/png':   imagepng($newImg, $rutaDestino); break;
            }
        }else{
            copy($tmpName, $rutaDestino);
        }
    }

    /**
     *	Recalcula ancho y alto desde un ancho y alto maximo permitidos para una foto
     *	REDIMENSIONA HASTA QUE LLEGA A ALGUNO DE LOS MÁXIMOS
     *	(del lado contrario puede permanecer mas grande que el máximo).
     *	Esto sirve para que no haya espacios sin completar en un thumbnail
     *
     *  Por ahora lo dejamos aca como una funcion privada
     *  Si en el futuro hay mas de un algoritmo hay que hacer un strategy
     */
    private function recalcularDimensionesFoto($ancho, $alto, $anchoMaximo, $altoMaximo){
        if ($ancho > $anchoMaximo) {
            $anchoSalida = $anchoMaximo;
            $sigue = true;
            while($sigue){
                $proporcion = round(($anchoSalida * 100) / $ancho);
                $porcentajeLadoContrario = round(($alto * $proporcion) / 100);
                if ( $porcentajeLadoContrario >= $altoMaximo ){
                    $ancho = $anchoSalida;
                    $alto = $porcentajeLadoContrario;
                    $sigue = false;
                }else{
                    $anchoSalida++;
                }
            }
        }else{
            $altoSalida = $altoMaximo;
            $sigue = true;
            while($sigue){
                $proporcion = round(($altoSalida * 100) / $alto);
                $porcentajeLadoContrario = round(($ancho * $proporcion) / 100);
                if ( $porcentajeLadoContrario >= $anchoMaximo ){
                    $alto = $altoSalida;
                    $ancho = $porcentajeLadoContrario;
                    $sigue = false;
                }else{
                    $altoSalida++;
                }
            }
        }
        return array($ancho, $alto);
    }
}