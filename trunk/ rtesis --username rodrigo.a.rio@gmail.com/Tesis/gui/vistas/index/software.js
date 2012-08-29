function masAplicaciones(){
    
    var filtroTitulo = $('#filtroTitulo').val();
    var filtroCategoria = $('#filtroCategoria').val();

    if(verificarValorDefectoBool("filtroTitulo")){ filtroTitulo = ""; }

    $.ajax({
        type:"POST",
        url:"descargas/procesar",
        data:{
            masAplicaciones:"1",
            filtroTitulo: filtroTitulo,
            filtroCategoria: filtroCategoria
        },
        beforeSend: function(){
            setWaitingStatus('listadoSoftware', true);
        },
        success:function(data){
            setWaitingStatus('listadoSoftware', false);
            $("#listadoSoftwareResult").html(data);
            $("a[rel^='prettyPhoto']").prettyPhoto();
        }
    });
}

$(document).ready(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

    $("#BuscarSoftware").live('click', function(){
        masAplicaciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarSoftware').each(function(){
          this.reset();
        });
        return false;
    });
});