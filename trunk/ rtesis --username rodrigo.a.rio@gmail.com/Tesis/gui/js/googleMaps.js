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
    //si ya estan seteo el center y el marcador actual
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