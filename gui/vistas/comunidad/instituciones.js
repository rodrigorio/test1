function listaProvinciasByPais(idPais, idSelectProvincia, idSelectCiudad, idContenedor){
    //si el valor elegido es '' entonces marco como disabled
    if(idPais == ''){
        $('#'+idSelectProvincia).addClass("disabled");
    }else{
        $('#'+idSelectProvincia).removeClass("disabled");
    }
    $('#'+idSelectCiudad).addClass("disabled");

    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus(idContenedor, true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#'+idSelectProvincia).html("");
            //dejo vacio el de ciudad si cambio de pais hasta que elija una provincia
            $('#'+idSelectCiudad).html("");
            $('#'+idSelectCiudad).html(new Option('Elija Ciudad:', '',true));
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectProvincia).append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectProvincia).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectProvincia).html(new Option('Elija Provincia:', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia, idSelectCiudad, idContenedor){
    if(idProvincia == ''){
        $('#'+idSelectCiudad).addClass("disabled");
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
        success: function(data){
            var lista = $.parseJSON(data);
            $('#'+idSelectCiudad).html("");
            if(lista.length != undefined && lista.length > 0){
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#'+idSelectCiudad).append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#'+idSelectCiudad).append(new Option('Elija Ciudad:', '',true));
            }
            setWaitingStatus(idContenedor, false);
        }
    });
}

var validateFormInstitucion = {
    errorElement: "div",
    validClass: "correcto",
    onfocusout: false,
    onkeyup: false,
    onclick: false,
    focusInvalid: false,
    focusCleanup: true,
    errorPlacement:function(error, element){
        error.appendTo(".msg_"+element.attr("id"));
    },
    highlight: function(element){},
    unhighlight: function(element){},
    rules:{
        nombre:{required:true},
        descripcion:{required:true},
        tipo:{required:true},
        cargo:{required:true},
        direccion:{required:true},
        pais:{required:true},
        provincia:{required:true},
        ciudad:{required:true},
        email:{required:true, email:true},
        telefono:{required:true},
        sitioWeb:{url:true}        
    },
    messages:{
        nombre: mensajeValidacion("requerido"),
        descripcion: mensajeValidacion("requerido"),
        tipo: mensajeValidacion("requerido"),
        cargo: mensajeValidacion("requerido"),
        direccion: mensajeValidacion("requerido"),
        pais: mensajeValidacion("requerido"),
        provincia: mensajeValidacion("requerido"),
        ciudad: mensajeValidacion("requerido"),
        email:{
            required: mensajeValidacion("requerido"),
            email: mensajeValidacion("email")
        },
        telefono: mensajeValidacion("requerido"),
        sitioWeb:mensajeValidacion("url")
    }
};

var optionsAjaxFormInstitucion = {
    dataType: 'jsonp',
    resetForm: false,
    url:"comunidad/guardar-institucion",

    beforeSerialize: function($form, options){
        if($("#formInstitucion").valid() == true){
            $('#msg_form_institucion').hide();
            $('#msg_form_institucion').removeClass("correcto").removeClass("error");
            $('#msg_form_institucion .msg').html("");

            verificarValorDefecto("descripcion");
            verificarValorDefecto("sedes");
            verificarValorDefecto("autoridades");
            verificarValorDefecto("actividadesMes");
            
            setWaitingStatus('formInstitucion', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formInstitucion', false);

        if(data.success == undefined || data.success == 0){
            if(data.mensaje == undefined){
                $('#msg_form_institucion .msg').html(lang['error procesar']);
            }else{
                $('#msg_form_institucion .msg').html(data.mensaje);
            }
            $('#msg_form_institucion').addClass("error").fadeIn('slow');
        }else{
            if(data.mensaje == undefined){
                $('#msg_form_institucion .msg').html(lang['exito procesar']);
            }else{
                $('#msg_form_institucion .msg').html(data.mensaje);
            }
            $('#msg_form_institucion').addClass("correcto").fadeIn('slow');
        }

        if(data.agregarInstitucion != undefined){
            //limpio el form
            $('#formInstitucion').each(function(){
                this.reset();
            });
        }
    }
};

function masInstituciones(){

    var filtroNombre = $('#filtroNombre').val();
    var filtroTipoInstitucion = $('#filtroTipoInstitucion option:selected').val();
    var filtroPais = $('#filtroPais option:selected').val();
    var filtroProvincia = $('#filtroProvincia option:selected').val();
    var filtroCiudad = $('#filtroCiudad option:selected').val();

    $.ajax({
        type:"POST",
        url:"comunidad/masInstituciones",
        data:{
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

function masMisInstituciones(){
    var sOrderBy = $('#sOrderBy').val();
    var sOrder = $('#sOrder').val();

    $.ajax({
        type:"POST",
        url:"comunidad/instituciones/mas-mis-instituciones",
        data:{
            sOrderBy: sOrderBy,
            sOrder: sOrder
        },
        beforeSend: function(){
            setWaitingStatus('listadoMisInstituciones', true);
        },
        success:function(data){
            setWaitingStatus('listadoMisInstituciones', false);
            $("#listadoMisInstitucionesResult").html(data);
        }
    });
}

function borrarInstitucion(iInstitucionId){
    if(confirm("Se borrara la institucion del sistema de manera permanente, desea continuar?")){
        $.ajax({
            type:"post",
            dataType: 'jsonp',
            url:"comunidad/instituciones/procesar",
            data:{
                iInstitucionId:iInstitucionId,
                borrarInstitucion:"1"
            },
            success:function(data){
                if(data.success != undefined && data.success == 1){
                    $("."+iInstitucionId).hide("slow", function(){
                        $("."+iInstitucionId).remove();
                    });
                }

                var dialog = $("#dialog");
                if($("#dialog").length){
                    dialog.attr("title","Borrar Institución");
                }else{
                    dialog = $('<div id="dialog" title="Borrar Institución"></div>').appendTo('body');
                }
                dialog.html(data.html);

                dialog.dialog({
                    position:['center', 'center'],
                    width:400,
                    resizable:false,
                    draggable:false,
                    modal:false,
                    closeOnEscape:true,
                    buttons:{
                        "Aceptar": function() {
                            $(this).dialog( "close" );
                        }
                    }
                });
            }
        });
    }
}

$(function(){

    $("a[rel^='prettyPhoto']").prettyPhoto();

    if($("#formInstitucion").length){
        $("#formInstitucion").validate(validateFormInstitucion);
        $("#formInstitucion").ajaxForm(optionsAjaxFormInstitucion);

        $("#pais").change(function(){
            listaProvinciasByPais($("#pais option:selected").val(), 'provincia', 'ciudad', 'selectsUbicacion');
        });
        $("#provincia").change(function(){
            listaCiudadesByProvincia($("#provincia option:selected").val(), 'ciudad', 'selectsUbicacion');
        });
    }

    $("#filtroPais").change(function(){
        listaProvinciasByPais($("#filtroPais option:selected").val(), 'filtroProvincia', 'filtroCiudad', 'formFiltrarInstituciones');
    });
    $("#filtroProvincia").change(function(){
        listaCiudadesByProvincia($("#filtroProvincia option:selected").val(), 'filtroCiudad', 'formFiltrarInstituciones');
    });

    //listado instituciones comunidad
    $("#BuscarInstituciones").live('click', function(){
        masInstituciones();
        return false;
    });

    $("#limpiarFiltro").live('click',function(){
        $('#formFiltrarInstituciones').each(function(){
          this.reset();
        });
        return false;
    });
    ///////////////////////////////

    //Listado Mis Instituciones
    $(".borrarInstitucion").live('click', function(){
        var iInstitucionId = $(this).attr("rel");
        borrarInstitucion(iInstitucionId);
    });

    $(".orderLink").live('click', function(){
        $('#sOrderBy').val($(this).attr('orderBy'));
        $('#sOrder').val($(this).attr('order'));
        masMisInstituciones();
    });    
});

/*
var geocoder;
var map ;
var initialLocation;
var mdp = new google.maps.LatLng(-34.880, -58.689);
var capital = new google.maps.LatLng(-36.505, -58.6959);
var browserSupportFlag =  new Boolean();
var infowindow = new google.maps.InfoWindow();
var marker = null;
function initializeMapa() {
	geocoder = new google.maps.Geocoder();
    var myLatlng = new google.maps.LatLng(-34.397, -58.644);
    var myOptions = {
      zoom: 12,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
  
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
    google.maps.event.addListener(map, 'click', function(event) {
        var location = event.latLng;
        if(marker==null){
            marker = new google.maps.Marker({
              map: map
              ,position: location
              ,markerOptions:{
                draggable:false
                ,clickable:false
              }
            });
        }else{
            marker.setPosition(location);
        }
        $("#latitud").val(location.lat());
        $("#longitud").val(location.lng());
      });

        if(  $("#latitud").val()== "" && $("#longitud").val() == ""){
		 	// Try W3C Geolocation method (Preferred)
		    if(navigator.geolocation) {
		      browserSupportFlag = true;
		      navigator.geolocation.getCurrentPosition(function(position) {
		        initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	                $("#latitud").val(position.coords.latitude);
	                $("#longitud").val(position.coords.longitude);
		        contentString = "Localizacion encontrada usando W3C standard";
		        map.setCenter(initialLocation);
		        infowindow.setContent(contentString);
		        infowindow.setPosition(initialLocation);
		        infowindow.open(map);
		        if(marker==null){
		        	 marker = new google.maps.Marker({
		                 map: map
		                 ,position: initialLocation
		                 ,markerOptions:{
		                   draggable:false
		                   ,clickable:false
		                 }
		               });
	        	 }else{
	        		  marker.setPosition(initialLocation);
	        	 }
		      }, function() {
		        handleNoGeolocation(browserSupportFlag);
		      });
		    } else if (google.gears) {
		      // Try Google Gears Geolocation
		      browserSupportFlag = true;
		      var geo = google.gears.factory.create('beta.geolocation');
		      geo.getCurrentPosition(function(position) {
		        initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
	                $("#latitud").val(position.latitude);
	                $("#longitud").val(position.longitude);
		        contentString = "Localizacion encontrada usando Google Gears";
		        map.setCenter(initialLocation);
		        infowindow.setContent(contentString);
		        infowindow.setPosition(initialLocation);
		        infowindow.open(map);
		        if(marker==null){
		        	 marker = new google.maps.Marker({
		                 map: map
		                 ,position: initialLocation
		                 ,markerOptions:{
		                   draggable:false
		                   ,clickable:false
		                 }
		               });
	        	 }else{
	        		  marker.setPosition(initialLocation);
	        	 }
		      }, function() {
		        handleNoGeolocation(browserSupportFlag);
		      });
		    } else {
		      // Browser doesn't support Geolocation
		      browserSupportFlag = false;
		      handleNoGeolocation(browserSupportFlag);
		    }
        }else{
        	 var lonlat = new google.maps.LatLng($("#latitud").val(),$("#longitud").val());
        	 if(marker==null){
	        	 marker = new google.maps.Marker({
	                 map: map
	                 ,position: lonlat
	                 ,markerOptions:{
	                   draggable:false
	                   ,clickable:false
	                 }
	               });
        	 }else{
        		  marker.setPosition(location);
        	 }
        	 map.setCenter(lonlat);
        }
}

function handleNoGeolocation(errorFlag) {
  if (errorFlag == true) {
    initialLocation = capital;
    contentString = "Error: The Geolocation service failed.";
  } else {
    initialLocation = mdp ;
    contentString = "Error: Your browser doesn't support geolocation. Are you in Mar del Plata?";
  }
  map.setCenter(initialLocation);
  infowindow.setContent(contentString);
  infowindow.setPosition(initialLocation);
  infowindow.open(map);
}

function codeAddress() {
   var address = $("#direccion").val();
   geocoder.geocode( { 'address': address}, function(results, status) {
     if (status == google.maps.GeocoderStatus.OK) {
    	 map.setCenter(results[0].geometry.location);
    	 $("#latitud").val(results[0].geometry.location.lat());
         $("#longitud").val(results[0].geometry.location.lng());
         if(marker==null){
        	 marker = new google.maps.Marker({
        		 map: map, 
        		 position: results[0].geometry.location
        	 	});
         }else{
        	  marker.setPosition(results[0].geometry.location);
         }
     } else {
       alert("Geocode was not successful for the following reason: " + status);
     }
   });
}

initializeMapa();
*/