function masPublicaciones(){

    var filtroTitulo = $('#filtroTitulo').val();
    var filtroApellidoAutor = $('#filtroApellidoAutor').val();
    var filtroFechaDesde = $('#filtroFechaDesde').val();

    if(verificarValorDefectoBool("filtroTitulo")){ filtroTitulo = ""; }
    if(verificarValorDefectoBool("filtroApellidoAutor")){ filtroApellidoAutor = ""; }
    if(verificarValorDefectoBool("filtroFechaDesde")){ filtroFechaDesde = ""; }

    $.ajax({
        type:"POST",
        url:"publicaciones/procesar",
        data:{
            masPublicaciones:"1",
            filtroTitulo: filtroTitulo,
            filtroApellidoAutor: filtroApellidoAutor,
            filtroFechaDesde: filtroFechaDesde
        },
        beforeSend: function(){
            setWaitingStatus('listadoPublicaciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoPublicaciones', false);
            $("#listadoPublicacionesResult").html(data);
            $("a[rel^='prettyphoto']").prettyphoto();
        }
    });
}

$(document).ready(function(){

    $("a[rel^='prettyphoto']").prettyphoto();

    $("#filtroFechaDesde").datepicker();

    $("#BuscarPublicaciones").live('click', function(){
        masPublicaciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarPublicaciones').each(function(){
          this.reset();
        });
        return false;
    });
});
