$(function(){
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
		        },
		    }
		}
		$("#formCrearInstitucion").validate(validateFormInstitucion);
	
	
	//$('#formCrearInstitucion').submit(function(){return false;});
	/*$('#crearInstitucion').click(function(){
            var fields = $('#formCrearInstitucion input[type=text],#formCrearInstitucion select');
	    var error = 0;
	    fields.each(function(){
	        var value = $(this).val();
	        if( (value == "" || value == $(this).attr("title")) && $(this).hasClass("requerido") ) {
	            $(this).addClass('inst_error');
	            error++;
	        } else {
	            $(this).removeClass('inst_error');
	        }
	    });
	    if(!error) {
		    var fields =
                        "id="+$('#idInstitucion').val()+
                        "&nombre="+$('#nombre').val()+
			"&tipo="+$('#tipo').val()+
			"&email="+$('#email').val()+
			"&cargo="+$('#cargo').val()+
			"&personaJuridica="+$('#personaJuridica').val()+
			"&direccion="+$('#direccion').val()+
			"&ciudad="+$('#ciudad').val()+
			"&tel="+$('#tel').val()+
			"&web="+$('#web').val()+
			"&horarioAtencion="+$('#horarioAtencion').val()+
			"&sedes="+$('#sedes').val()+
			"&autoridades="+$('#autoridades').val()+
			"&actividadesMes="+$('#actividadesMes').val()+
			"&latitud="+$('#latitud').val()+
			"&longitud="+$('#longitud').val()+
			"&descripcion="+$('#descripcion').val();
			$.ajax({
				type:	"POST",
				url: 	"comunidad/institucion-procesar",
				data: 	fields,
				beforeSend: function() {
					$("#ajax_loading").show();
				},
				success: function(data){
					$("#ajax_loading").hide();
					//var resp = $.parseJSON(data);
                                        if(data==1){
						$("#msg_conf").addClass("correcto");
						$("#msg_conf").html("Se ha creado la institucion correctamente");
					}else{
						$("#msg_conf").addClass("error");
						$("#msg_conf").html("No se ha creado la institucion correctamente");
					}
					$("#formCrearInstitucion").hide();
					$("#map_canvas").hide();
					$("#ubicarBtn").hide();
					$("#msg_conf").show();
				}
			});
	    }else{
	    	return false;
	    }
	});*/
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
	
	$("#formCrearInstitucion").ajaxForm(optionsAjaxFormInstitucion);
});

function listaProvinciasByPais(me){
	$.ajax({
		type: "POST",
	   	url: "comunidad/provinciasByPais",
	   	data: "iPaisId="+me.value,
	   	success: function(data){
	   		var lista = $.parseJSON(data);
	   		$('#provincia').html("");
	   		if(lista.length != undefined && lista.length > 0){
	   			$('#provincia').append(new Option('Selecciones una provincia', '',true));
	   			for(var i=0;i<lista.length;i++){
	   				$('#provincia').append(new Option(lista[i].sNombre, lista[i].id));
				}
	   		}else{
	   			$('#provincia').html(new Option('Selecciones una provincia', '',true));
	   			$('#ciudad').html(new Option('Selecciones una ciudad', '',true));
	   		}
	   	}
	});
 }
function listaCiudadesByProvincia(me){
	$.ajax({
		type: "POST",
		url: "comunidad/ciudadesByProvincia",
		data: "iProvinciaId="+me.value,
		success: function(data){
			var lista = $.parseJSON(data);
			$('#ciudad').html("");
			if(lista.length != undefined && lista.length > 0){
				$('#ciudad').append(new Option('Selecciones una ciudad', '',true));
				for(var i=0;i<lista.length;i++){
					$('#ciudad').append(new Option(lista[i].sNombre, lista[i].id));
				}
			}else{
				$('#ciudad').append(new Option('Selecciones una ciudad', '',true));
			}
		}
	});
}
function searchInstitucion(){
	var nombre 	= $('#institucion_nombre').val();
	var ciudad 	= $('#ciudad').val();
	var provincia = $('#provincia').val();
	var pais 	= $('#pais').val();
	var tipoInstitucion 	= $('#tipoInstitucion').val();
	$.ajax({
		type: "POST",
		url: "comunidad/masInstituciones",
		data: "busquedaInstitucion=1&institucion_nombre="+nombre+"&ciudad="+ciudad+"&provincia="+provincia+"&pais="+pais+"&tipoInstitucion="+tipoInstitucion+"",
		success: function(data){
			$("#listadoInstituciones").html(data);
		}
	});
}

function ampliarInstitucion(id){
   window.location = "comunidad/ampliar-institucion?iInstitucionId="+id;
}
function editarInstitucion(id){
   window.location = "comunidad/editar-institucion?iInstitucionId="+id;
}

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