function mapaSimple(id){

    //latitud y longitud se imprime en el rel del div. practicidad para mapas simples
    var rel = $("#"+id).attr("rel").split('_');
    var latitud = rel[0];
    var longitud = rel[1];
    
    var latlng = new google.maps.LatLng(latitud, longitud);

    var myOptions = {
        zoom: 8,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById(id), myOptions);

    var marker = new google.maps.Marker({
      position: latlng
    });
    
    marker.setMap(map);
    
}

/**
 * Para usar dentro de los <form> trabaja en conjunto de 2 inputs hidden: 'latitud' y 'longitud'
 * Si los campos tienen valor se supone un formulario de edicion en el que ya hay una ubicacion cargada
 */
function mapaSeleccionCoordenadas(id)
{
    var latitudElement = $("#latitud");
    var longitudElement = $("#longitud");

    var marker = null;
    var latlng = null;
    var zoom = 8;

    //si ya estan seteo el center y el marcador actual
    if(latitudElement.val() != "" && longitudElement.val() != ""){
        latlng = new google.maps.LatLng(latitudElement.val(), longitudElement.val());
        marker = new google.maps.Marker({ position: latlng });
    }else{
        //sudamerica
        latlng = new google.maps.LatLng("-35.317366", "-56.702636");
        zoom = 3;
    }

    var myOptions = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map = new google.maps.Map(document.getElementById(id), myOptions);

    if(marker != null){
        marker.setMap(map);
    }

    //agrego el evento al mapa que extrae las coordenadas
    google.maps.event.addListener(map, 'click', function(event){

        //borro la marca anterior si existia
        if(marker != null){ marker.setMap(null); }

        //agrego la nueva marca
        marker = new google.maps.Marker({
            position:event.latLng,
            map:map
        });

        //centro el mapa en la nueva marca
        map.setCenter(event.latLng);

        //copio las coordenadas en los inputs del form
        latitudElement.val(event.latLng.lat());
        longitudElement.val(event.latLng.lng());
    });
}