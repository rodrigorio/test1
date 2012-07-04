function listaProvinciasByPais(idPais){
    //si el valor elegido es '' entonces marco como disabled
    if(idPais == ''){
        $('#filtroProvincia').addClass("disabled");
    }else{
        $('#filtroProvincia').removeClass("disabled");
    }
    $('#filtroCiudad').addClass("disabled");

    $.ajax({
        type: "POST",
        url: "provinciasByPais",
        data: "iPaisId="+idPais,
        beforeSend: function(){
            setWaitingStatus('formFiltrarInstituciones', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#filtroProvincia').html("");
            //dejo vacio el de ciudad si cambio de pais hasta que elija una provincia
            $('#filtroCiudad').html("");
            $('#filtroCiudad').html(new Option('Elija Ciudad:', '',true));
            if(lista.length != undefined && lista.length > 0){
                $('#filtroProvincia').append(new Option('Elija Provincia:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#filtroProvincia').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#filtroProvincia').html(new Option('Elija Provincia:', '',true));
            }
            setWaitingStatus('formFiltrarInstituciones', false);
        }
    });
 }

function listaCiudadesByProvincia(idProvincia){
    if(idProvincia == ''){
        $('#filtroCiudad').addClass("disabled");
    }else{
        $('#filtroCiudad').removeClass("disabled");
    }
    $.ajax({
        type: "POST",
        url: "ciudadesByProvincia",
        data: "iProvinciaId="+idProvincia,
        beforeSend: function(){
            setWaitingStatus('formFiltrarInstituciones', true);
        },
        success: function(data){
            var lista = $.parseJSON(data);
            $('#filtroCiudad').html("");
            if(lista.length != undefined && lista.length > 0){
                $('#filtroCiudad').append(new Option('Elija Ciudad:', '',true));
                for(var i=0;i<lista.length;i++){
                    $('#filtroCiudad').append(new Option(lista[i].sNombre, lista[i].id));
                }
            }else{
                $('#filtroCiudad').append(new Option('Elija Ciudad:', '',true));
            }
            setWaitingStatus('formFiltrarInstituciones', false);
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
        tipo:{required:true},
        cargo:{required:true},
        tel:{required:true},
        pais:{required:true},
        provincia:{required:true},
        ciudad:{required:true},
        direccion:{required:true},
    },
    messages:{
        nombre: "Debe especificar un nombre",
        tipo:{
                required: mensajeValidacion("requerido")
        },
        cargo:{
                required: mensajeValidacion("requerido")
        },
        tel:{
                required: mensajeValidacion("requerido")
        },
        pais:{
                required: mensajeValidacion("requerido")
        },
        provincia:{
                required: mensajeValidacion("requerido")
        },
        ciudad:{
                required: mensajeValidacion("requerido")
        },
        direccion:{
                required: mensajeValidacion("requerido")
        },
        email:{
                required: mensajeValidacion("requerido"),
                email: mensajeValidacion("email"),
        }
    }
};

var optionsAjaxFormInstitucion = {
    dataType: 'jsonp',
    resetForm: false,
    url: 	"comunidad/institucion-procesar",

    beforeSerialize: function($form, options){
        if($("#formCrearInstitucion").valid() == true){
            $('#msg_form_institucion').hide();
            $('#msg_form_institucion').removeClass("correcto").removeClass("error");
            $('#msg_form_institucion .msg').html("");
            setWaitingStatus('formInfoBasica', true);
        }else{
            return false;
        }
    },

    success:function(data){
        setWaitingStatus('formCrearInstitucion', false);
        if(data.success == undefined || data.success == 0){
            $('#msg_form_institucion .msg').html(lang['error procesar']);
            $('#msg_form_institucion').addClass("error").fadeIn('slow');
        }else{
            $('#msg_form_institucion .msg').html(lang['exito procesar']);
            $('#msg_form_institucion').addClass("correcto").fadeIn('slow');
            $('#formCrearInstitucion input[type=text],#formCrearInstitucion select,,#formCrearInstitucion textarea').val("");
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

$(function(){

    if($("#formCrearInstitucion").length){
        $("#formCrearInstitucion").validate(validateFormInstitucion);
        $("#formCrearInstitucion").ajaxForm(optionsAjaxFormInstitucion);
    }

    $("#filtroPais").change(function(){listaProvinciasByPais($("#filtroPais option:selected").val());});
    $("#filtroProvincia").change(function(){listaCiudadesByProvincia($("#filtroProvincia option:selected").val());});

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
    
});

/*
function ampliarInstitucion(id){
   window.location = "comunidad/ampliar-institucion?iInstitucionId="+id;
}
function editarInstitucion(id){
   window.location = "comunidad/editar-institucion?iInstitucionId="+id;
}
*/

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