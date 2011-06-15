<?php

/**
 * @author Matias Velilla
 */
class JsonHelper extends HelperAbstract
{

    /**
     * En jsonAjaxResponse se genera el array que luego se va a convertir en json para ser enviado.
     * Con esto se logra unificar los valores para devolver Mensaje, Resultado, etc.
     *
     * Desde un PageController el codigo se veria de la siguiente manera:
     *
     * $this->getJsonHelper()->initJsonAjaxResponse()    //se fija si existe callback y lo guarda, tmb limpia el array
     *                       ->setSuccess(true|false)    //indica si la respuesta fue una accion procesada con exito o no
     *                       ->setMessage($msg)          //agrega un mensaje de exito|error
     *                       ->setRedirect($url)         //agrega una url para que el js redireccione
     *                       ->setValor($nombre, $valor) //agrega un token al array que luego sera codificado
     *                       ->sendJsonAjaxResponse();   //automaticamente envia el json con los datos acumulados en el objeto
     * 
     * @var array
     */
    private $jsonAjaxResponse = array();

    /**
     *
     * @var string
     */
    private $jQueryCallback;

    /**
     * var boolean Guarda un flag indicando si se inicio una respuesta json para enviar por ajax.
     */
    private $jsonAjaxResponseIniciada = false;

    /**
     *
     * Encode data as JSON and set response header
     *
     * Encode the array $valueToEncode into the JSON format
     *
     */
    public static function encodeJson($valueToEncode, $options)
    {
        // Encoding
        $encodedResult = json_encode($valueToEncode);

        if(isset($options['jQueryCallback'])){
            $encodedResult = $options['jQueryCallback']."(".$encodedResult.")";
        }
        
        return $encodedResult;
    }

    /**
     * Encode JSON response and immediately send
     *
     * Si existe un callback de Jquery lo agrega en la codificacion
     *
     * @param array $data
     */
    public function sendJson($data)
    {
        $options = array();

        if(!empty($this->jQueryCallback)){
            $options['jQueryCallback'] = $this->jQueryCallback;
        }

        $data = $this->encodeJson($data, $options);

        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json', true);        
        $response->setBody($data);       
    }

    /**
     * Se fija si existe callback y lo guarda en el objeto, tmb limpia el array de respuesta
     */
    public function initJsonAjaxResponse()    
    {        
        if($this->getRequest()->has("callback")){
            $this->jQueryCallback = $this->getRequest()->getParam("callback");
        }

        $this->jsonAjaxResponse = array();
        $this->jsonAjaxResponseIniciada = true;
        return $this;
    }

    /**
     * Indica si la respuesta fue una accion procesada con exito o no
     *
     * @param boolean $result Indica si la accion procesada fue exitosa
     */
    public function setSuccess($result)    
    {
        $flag = ($result) ? "1" : "0";
        $this->jsonAjaxResponse['success'] = $flag;
        return $this;
    }

    /**
     * Agrega un mensaje de exito|error
     *
     * @param string $msg Mensaje que se agregara al response json
     */
    public function setMessage($msg)          
    {
        $this->jsonAjaxResponse['mensaje'] = $msg;
        return $this;
    }

    /**
     * Agrega una url para que el js redireccione
     *
     * @param string $url Es solo el pathInfo, el metodo automaticamente agrega el BaseUrl si es que existe.
     *                    Es decir, si el sitio esta en www.sitio.com/baseurl/ y yo quiero redireccionar a www.sitio.com/baseurl/admin
     *                    Entonces el parametro solo debe contener "/admin". Por defecto redirecciona a la raiz del sitio.
     */    
    public function setRedirect($pathInfo = "/")
    {
        $this->jsonAjaxResponse['redirect'] = $this->getRequest()->getBaseUrl().$pathInfo;
        return $this;
    }

    /**
     * Agrega un token al array que luego sera codificado
     *
     * @param string $nombre Nombre de la variable que sera codificada
     * @param string|int|date $valor Valor que sera asignado a la variable, se convierte a string
     */
    public function setValor($nombre, $valor)
    {
        $this->jsonAjaxResponse[$nombre] = $valor;
        return $this;
    }

    /**
     * Este metodo agil llama internamente a sendJson con el array de elementos acumulado para la respuesta ajax
     * @throws Exception si la respuesta no fue iniciada (no hay nada para devolver)
     */
    public function sendJsonAjaxResponse()
    {
        if(!$this->jsonAjaxResponseIniciada){
            throw new Exception("El Ajax Response no fue iniciado");
        }
        $this->sendJson($this->jsonAjaxResponse);
    }

    /**
     * Pretty-print JSON string
     *
     * Use 'indent' option to select indentation string - by default it's a tab
     *
     * @param string $json Original JSON string
     * @param array $options Encoding options
     * @return string
     */
    public static function prettyPrint($json, $options = array())
    {
        $tokens = preg_split('|([\{\}\]\[,])|', $json, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = "";
        $indent = 0;

        $ind = "\t";
        if(isset($options['indent'])) {
            $ind = $options['indent'];
        }

        foreach($tokens as $token) {
            if($token == "") continue;

            $prefix = str_repeat($ind, $indent);
            if($token == "{" || $token == "[") {
                $indent++;
                if($result != "" && $result[strlen($result)-1] == "\n") {
                    $result .= $prefix;
                }
                $result .= "$token\n";
            } else if($token == "}" || $token == "]") {
                $indent--;
                $prefix = str_repeat($ind, $indent);
                $result .= "\n$prefix$token";
            } else if($token == ",") {
                $result .= "$token\n";
            } else {
                $result .= $prefix.$token;
            }
        }
        return $result;
    }
}