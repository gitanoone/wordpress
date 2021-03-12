function initMap() {
    let latar=helper.slice(0,1)
    let lat=latar.toString()
    let lngar=helper.slice(1,2)
    let lng=lngar.toString()
    let myAddressArr=helper.slice(2,3)
    let element = document.getElementById('map');
    let options = {
        zoom: 10,
        center: {lat: Number(lat), lng: Number(lng)},
    };
    let myMap = new google.maps.Map(element, options);
    let markers = [
        {
            coordinates: {lat: Number(lat), lng: Number(lng)},
            title: script_vars.title,
            info: script_vars.post.post_content,
            address: myAddressArr.toString()
        }
    ];
    for(let i = 0; i < markers.length; i++) {
        addMarker(markers[i]);
    }
    function addMarker(properties) {
        let marker = new google.maps.Marker({
            position: properties.coordinates,
            map: myMap
        });
        if(properties.title) {
            let InfoWindow = new google.maps.InfoWindow({
                content:properties.title+'<br>'
                    +properties.info+'<br>'
                    +properties.address,
            });
            marker.addListener('click', function(){
                InfoWindow.open(myMap, marker);
            });
        }
    }
}
