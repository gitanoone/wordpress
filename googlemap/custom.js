function initMap() {
    var latar=helper.slice(0,1),lngar=helper.slice(1,2);
    var lat=latar.toString(),lng=lngar.toString();
    var element = document.getElementById('map');
    geo = new google.maps.Geocoder();
    var infoWindow = new google.maps.InfoWindow();
    var options = {
        zoom: 7,
        center: {lat: Number(lat), lng: Number(lng)},
    };
    var myMap = new google.maps.Map(element, options);
    var marker, i;
           for (i = 0; i < meta_value.length; i++) {
		let infoWindodContent = script_vars[i] + '<br>' + results[0].formatted_address
               geo.geocode({'address' : meta_value[i]}, function (results,status) {
                   if(status === google.maps.GeocoderStatus.OK) {
                           marker = new google.maps.Marker({
                               map: myMap,
                               content: results[0].formatted_address + script_vars,
                               position: results[0].geometry.location,
                           })
                           google.maps.event.addListener(marker, 'click', (function (marker) {
                    return function () {
                            infoWindow.setContent(infoWindodContent);
                            infoWindow.open(myMap, marker);
                        }
                })(marker));
            }
        })
    }
}