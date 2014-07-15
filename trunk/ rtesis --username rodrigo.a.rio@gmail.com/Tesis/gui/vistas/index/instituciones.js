function resetSelect(select, defaultOpt){
    if(select.length){
        select.addClass("disabled");
        select.html("");
        select.append(new Option(defaultOpt, '',true));
    }
}

function listaProvinciasByPais(idPais, idSelectProvincia, idSelectCiudad, idContenedor){
    resetSelect($('#'+idSelectCiudad), 'Elija Ciudad:');

    if(idPais == ''){
        resetSelect($('#'+idSelectProvincia), 'Elija Provincia:');
        return;
    }else{
        $('#'+idSelectProvincia).removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(lista){
            $('#'+idSelectProvincia).html("");

            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectProvincia).append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectProvincia).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectProvincia).html(new Option('No hay provincias cargadas', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia, idSelectCiudad, idContenedor){
    if(idProvincia == ''){
        resetSelect($('#'+idSelectCiudad), 'Elija Ciudad:');
        return;
    }else{
        $('#'+idSelectCiudad).removeClass("disabled");
    }

    $.ajax({
        type: "POST",
        url: "ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(lista){
            $('#'+idSelectCiudad).html("");
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectCiudad).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectCiudad).append(new Option('No hay ciudades cargadas', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
}

function masInstituciones(){

    if(!$("#contenedorMapaInstituciones").hasClass("di_no")){
        ocultarElemento($("#contenedorMapaInstituciones"));
    }
    if(!$("#listadoInstituciones").hasClass("di_bl")){
        revelarElemento($("#listadoInstituciones"));
    }

    var filtroNombre = $('#filtroNombre').val();
    var filtroTipoInstitucion = $('#filtroTipoInstitucion option:selected').val();
    var filtroPais = $('#filtroPais option:selected').val();
    var filtroProvincia = $('#filtroProvincia option:selected').val();
    var filtroCiudad = $('#filtroCiudad option:selected').val();

    if(verificarValorDefectoBool("filtroNombre")){ filtroNombre = ""; }

    $.ajax({
        type:"POST",
        url:"instituciones/procesar",
        data:{
            masInstituciones:"1",
            filtroNombre: filtroNombre,
            filtroTipoInstitucion: filtroTipoInstitucion,
            filtroPais: filtroPais,
            filtroProvincia: filtroProvincia,
            filtroCiudad: filtroCiudad
        },
        beforeSend: function(){
            setWaitingStatus('listadoInstituciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoInstituciones', false);
            $("#listadoInstitucionesResult").html(data);
        }
    });
}

function cargarMarcasMapaInstituciones()
{
    if(!$("#listadoInstituciones").hasClass("di_no")){
        ocultarElemento($("#listadoInstituciones"));
    }
    if(!$("#contenedorMapaInstituciones").hasClass("di_bl")){
        revelarElemento($("#contenedorMapaInstituciones"));
    }

    iniciarMapaMarcasMultiples('mapaInstituciones');

    var filtroNombre = $('#filtroNombre').val();
    var filtroTipoInstitucion = $('#filtroTipoInstitucion option:selected').val();
    var filtroPais = $('#filtroPais option:selected').val();
    var filtroProvincia = $('#filtroProvincia option:selected').val();
    var filtroCiudad = $('#filtroCiudad option:selected').val();

    if(verificarValorDefectoBool("filtroNombre")){ filtroNombre = ""; }

    $.ajax({
        dataType:'jsonp',
        url:"instituciones/procesar",
        data:{
            obtenerMarcas:"1",
            filtroNombre: filtroNombre,
            filtroTipoInstitucion: filtroTipoInstitucion,
            filtroPais: filtroPais,
            filtroProvincia: filtroProvincia,
            filtroCiudad: filtroCiudad
        },
        beforeSend: function(){
            setWaitingStatus('contenedorMapaInstituciones', true);
        },
        success:function(data){
            limpiarMarcas();
            if(data.marcas.length < 1){
                alert('No se encontraron instituciones. Considere modificar el filtro');
            }else{
                agregarMarcas(data.marcas);
            }
            setWaitingStatus('contenedorMapaInstituciones', false);
        }
    });
}

$(function(){

    $("a[rel^='prettyphoto']").prettyphoto();

    $("#BuscarInstituciones").live('click', function(){
        if(!$("#listadoInstituciones").hasClass("di_no")){
            masInstituciones();
        }else{
            cargarMarcasMapaInstituciones();
        }
        return false;
    });

    $(".mostrarInstitucionesFicha").live('click', function(){
        masInstituciones();
        return false;
    });

    $(".mostrarInstitucionesMapa").live('click', function(){
        cargarMarcasMapaInstituciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarInstituciones').each(function(){
          this.reset();
        });
        return false;
    });

    $("#filtroPais").change(function(){
        listaProvinciasByPais($("#filtroPais option:selected").val(), 'filtroProvincia', 'filtroCiudad', 'formFiltrarInstituciones');
    });
    $("#filtroProvincia").change(function(){
        listaCiudadesByProvincia($("#filtroProvincia option:selected").val(), 'filtroCiudad', 'formFiltrarInstituciones');
    });
});

$(window).load(function(){
    if($("#mapaInstitucion").length){
        mapaSimple("mapaInstitucion");
    }
});
