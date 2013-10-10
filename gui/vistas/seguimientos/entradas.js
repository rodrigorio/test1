/**
 * Objeto dedicado a manipular el calendario y los clicks en las fechas
 */
function Calendario(element){
    var self = this;
    
    this._element = element;
    this._seguimientoId = null;
    this._entradas = [];
    this._ultimaEntrada = "";
    this._entradaActual = "";
    this._firstRender = true;

    this.init = function(){
        this._seguimientoId = $("#seguimientoId").html();
        this._ultimaEntrada = $("#fechaUltimaEntrada").html();
        this._entradaActual = $("#fechaEntradaActual").html();

        if(this._ultimaEntrada != undefined && this._ultimaEntrada != ""){
            this._ultimaEntrada = new Date(this._ultimaEntrada);
        }

        //si no se esta visualizando una entrada entonces el calendario se inicializa en mes actual        
        var date = new Date();
        if(this._entradaActual != undefined && this._entradaActual != ""){
            date = new Date(this._entradaActual);
            this._entradaActual = date;
        }
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        
        this.getFechasEntradasMes(year, month);
    }

    this.bindDatePicker = function(defaultDate){
        this._element.datepicker({
            defaultDate:defaultDate,
            maxDate:new Date,
            beforeShowDay:this.mostrarEntradas,
            onChangeMonthYear:this.getFechasEntradasMes,
            onSelect:this.clickDateEvent
        });        
    }

    this.getFechasEntradasMes = function(year, month){        
        //agrego un 0 al mes si es un solo digito.
        month = ('0' + month).slice(-2);
        
        $.ajax({
            type:"get",
            dataType:'jsonp',
            async:false, 
            url:"seguimientos/entradas/procesar",
            data:{
                fechasEntradasMes:"1",
                iSeguimientoId:self._seguimientoId,
                year:year,
                month:month
            },
            beforeSend:function(){
                setWaitingStatus("calendarWrap", true, "16");
            },
            success:function(data){                
                //convierto a objeto date cada una de las fechas
                var dates = [];
                jQuery.each(data, function(){
                    dates.push(new Date(this));
                });                
                self._entradas = dates;
                
                //si no es la primera vez que se renderiza hago refresh
                if(self._firstRender){
                    var defaultDate = "01/"+month+"/"+year;
                    self.bindDatePicker(defaultDate);
                    self._firstRender = false;
                }else{
                    self._element.datepicker('refresh');
                }

                setWaitingStatus("calendarWrap", false, "16");
            }
        });       
    }

    this.mostrarEntradas = function(date){
        //puede que no haya ninguna entrada en el mes -> todas las fechas mayores a ultima entrada acceden a crear
        //si en la fecha a mostrar es entrada agrego class correspondiente
        //si la fecha a mostrar en calendario es = fecha de la entrada agrego clase para destacar
        //sino, si la fecha a mostrar es mayor a la ultima entrada y no hay entrada creada agrego una clase para bindear el "desea crear?"

        //hay entradas en el mes?
        if(self._entradas.length != 0){
            for(var i = 0; i < self._entradas.length; i++){
                //para la fecha del calendario existe entrada?
                if(+date === +self._entradas[i]){
                    //es la entrada ampliada que se esta visualizando?
                    if(self._entradaActual != undefined && self._entradaActual != "" && +self._entradaActual === +self._entradas[i]){
                        return [true, 'verEntrada entradaActual'];
                    }else{
                        return [true, 'verEntrada'];
                    }
                }
            }
        }

        //si no retorno verifico si en la fecha se puede crear una nueva entrada o no.
        if(self._ultimaEntrada != undefined && self._ultimaEntrada != ""){
            if(self._ultimaEntrada < date){
                return[true, 'crearEntrada'];
            }
        }else{
            return[true, 'crearEntrada'];
        }

        return [false, ''];
    }

    this.clickDateEvent = function(date){
        //aca ya viene configurada segun los settings del plugin en vistas.js en formato dd/mm/yyyy
        var dateObj = new Date(date.replace(/(\d{2})\/(\d{2})\/(\d{4})/, "$2/$1/$3") );
                
        var action = self.mostrarEntradas(dateObj);
        //crearEntrada || verEntrada || verEntrada entradaActual
        action = action[1];
        if(action.indexOf("entradaActual") != -1){
            return false;
        }
        if(action.indexOf("verEntrada") != -1){
            location = "seguimientos/entradas/"+self._seguimientoId+"-"+date;
        }
        if(action.indexOf("crearEntrada") != -1){
            //son 2 peticiones ajax. 1 para el dialog de aceptar (puede dar advertencia de periodo de expiracion) y otra para crear entrada y redireccionar.
            self.dialogCrearEntrada(self._seguimientoId, date);
        }

        return false;
    }

    this.dialogCrearEntrada = function(iSeguimientoId, date){
        date = date.replace(/(\d{2})\/(\d{2})\/(\d{4})/, "$3-$2-$1");

        var buttons = {
            "Confirmar": function(){
                //este es el dialog que confirma que la cuenta fue eliminada del sistema
                var buttonAceptar = { "Aceptar": function(){ $(this).dialog("close"); } }
                dialog = setWaitingStatusDialog(500, "Crear Entrada", buttonAceptar);
                $.ajax({
                    type:"post",
                    dataType:'jsonp',
                    data:{iSeguimientoId:iSeguimientoId, dFecha:date},
                    url:"seguimientos/entradas/crear",
                    success:function(data){
                        dialog.html(data.html);
                        if(data.success != undefined && data.success == 1){
                            $(".ui-dialog-buttonset .ui-button").click(function(){
                                //ampliar entrada creada para editar por primera vez.
                                location = data.redirect;
                            });
                        }
                    }
                });
            },
            "Cancelar": function() {
                $(this).dialog( "close" );
            }
        }

        //este es el dialog que pide confirmar la accion
        var dialog = setWaitingStatusDialog(500, "Crear Entrada", buttons);
        dialog.load(
            "seguimientos/entradas/crear",
            {confirmar:"1", iSeguimientoId:iSeguimientoId, dFecha:date},
            function(){}
        );
    }
}

$(document).ready(function(){

    var calendario = new Calendario($("#calendarioEntradas"));
    calendario.init();

    $(".desplegables").tooltip();
    
    $(".expand").live("click", function(){
       $(this).removeClass("expand").addClass("collapse");
    });
    $(".collapse").live("click", function(){
       $(this).removeClass("collapse").addClass("expand");
    });

    $(".desplegable").live("mouseover", function(){
        var elem = "title_"+$(this).attr("rel");
        $("#"+elem).addClass("baco2");
    });
    $(".desplegable").live("mouseout", function(){
        var elem = "title_"+$(this).attr("rel");
        $("#"+elem).removeClass("baco2");
    });
    $("#collapseAll").live("click", function(){
        $(".desplegables").hide(function(){});
        $(".desplegable").removeClass("collapse").addClass("expand");
    });

    //back to top button
    $(window).scroll(function(){
        if($(this).scrollTop()){
            $('#toTop').fadeIn();
        }else{
            $('#toTop').fadeOut();
        }
    });
    $("#toTop").click(function(){
       $("html, body").animate({scrollTop: 0}, 1000);
    });
});