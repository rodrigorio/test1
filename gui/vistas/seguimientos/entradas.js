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
            beforeShowDay:self.mostrarEntradas,
            onChangeMonthYear:self.getFechasEntradasMes
        });
        
        this.addEventClick();
    }

    this.getFechasEntradasMes = function(year, month){        
        //agrego un 0 al mes si es un solo digito.
        month = ('0' + month).slice(-2);
        
        $.ajax({
            type:"get",
            dataType:'jsonp',
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
        //si en la fecha a mostrar es entrada agrego 'azul'
            //si la fecha a mostrar en calendario es = fecha de la entrada agrego clase para destacar
        //sino, si la fecha a mostrar es mayor a la ultima entrada y no hay entrada creada agrego una clase para bindear el "desea crear?"

/*
        if(self._ultimaEntrada != undefined && self._ultimaEntrada != ""){
            if(self._ultimaEntrada < date){
                return[true, 'fowe_bo'];
            }else{

            }
        }

        if(self._entradas.length == 0){
        }
            
        jQuery.each(self._entradas, function(){
            if(date.getTime() == this.getTime()){
                return [true, 'fowe_bo'];
            }
        });
*/

        return [false, ''];

    }

    this.addEventClick = function(){
        //agrega eventos on click custom
    }
}

$(document).ready(function(){

    var calendario = new Calendario($("#calendarioEntradas"));
    calendario.init();

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