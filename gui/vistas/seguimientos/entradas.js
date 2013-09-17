/*
var eventDates = [{date: new Date(2010, 6-1, 4), events: 1},
                  {date: new Date(2010, 6-1, 18), events: 2},
                  {date: new Date(2010, 7-1, 16), events: 4},
                  {date: new Date(2010, 8-1, 6), events: 8}];

function showEventDates(date){
    for (var i = 0; i < eventDates.length; i++){
        if(date.getTime() == eventDates[i].date.getTime()){
            return [true, eventDates[i].events > 4 ? 'busyDay' : 'eventDay'];
        }
    }
    return [false, ''];
}

//array dias permitidos para crear -> click, popup seguro?, crea por ajax y redirecciona a url de editar.
//array ver entradas. click redirecciona ver.
//todos los demas dias devuelve false.
//agrega clases para uno o para otro, se necesita un selector que se fija si el <a> de la fecha tiene clase ver o crear.

*/

$(document).ready(function(){    
    $("#calendarioEntradas").datepicker({
        maxDate:new Date
    });   
});