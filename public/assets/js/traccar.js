function initTraccarMap(lat, long) {
    let title = $("#traccar_title").val();
    let zoom = 12;
    if(lat == undefined && lat != "" && long == undefined && long != "") {
        lat = 25.1860442;
        long = 55.2747167;

    }
    const myLatLng = {
        lat: lat,
        lng: long
    };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: zoom,
        center: myLatLng,
    });

    mapMarker = new google.maps.Marker({
        position: myLatLng,
        map,
        title: title,
    });

    map.addListener("click", (e) => {
        placeMarker(e.latLng);
        cityCircle.setCenter(e.latLng);

    });

    let input1 = document.getElementById('traccar_place');
    let autocomplete1 = new google.maps.places.Autocomplete(input1);
    google.maps.event.addListener(autocomplete1, 'place_changed', function () {
        let place = autocomplete1.getPlace();
        document.getElementById('location_latitude').value = place.geometry.location.lat();
        document.getElementById('location_longitude').value = place.geometry.location.lng();
       // $("#traccar_title").val(place.formatted_address);
        $("#traccar_title").val(place.name);
        initTraccarMap(place.geometry.location.lat(), place.geometry.location.lng());
    });

    const cityCircle = new google.maps.Circle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "#FF0000",
        fillOpacity: 0.35,
        map,
        center: { lat: lat, lng: long },
        radius:  1000,
    });
    const radiusInput = document.getElementById("traccar_radius");
    radiusInput.addEventListener("input", () => {
        // const radius = parseFloat(radiusInput.value);
        // if (isNaN(radius)) {
        //     alert("The radius must be a number.");
        // } else {
        //     cityCircle.setRadius(radius);
        // }
        $("#selectedRadius").html(radiusInput.value + " m");
        cityCircle.setRadius(parseInt(radiusInput.value));
    });

    const zoomInput = document.getElementById("traccar_zoom");
    zoomInput.addEventListener("change", () => {
        const zoom = parseInt(zoomInput.value);
        if (isNaN(zoom)) {
            alert("The zoom must be a number.");
        } else {

            map.setZoom(zoom);
        }

    });
    map.addListener("zoom_changed", () => {
        zoomInput.value = map.getZoom();
    });

    mapMarker.addListener("position_changed", function () {
        // Get the updated marker position
        const newPosition = mapMarker.getPosition();
        // Use Geocoder to get location name from the updated marker position
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: newPosition }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    // The location name is available in results[0].formatted_address
                    const locationName = results[0].formatted_address;
                   $("#traccar_title").val(locationName);
                }
            } else {
                console.log("Geocoder failed due to: " + status);
            }
        });
    });

}



function placeMarker(location) {
    if (mapMarker) {
        mapMarker.setPosition(location);
    } else {
        mapMarker = new google.maps.Marker({
            position: location,
            map: map,
            center: location,
        });
    }
    $("#location_latitude").val(mapMarker.getPosition().lat())
    $("#location_longitude").val(mapMarker.getPosition().lng())
}
$(document).on("input", "#location_latitude, #location_longitude", function() {
    let latitude = $("#location_latitude").val();
    let longitude = $("#location_longitude").val();
    //let path = $("#path_name").val();
    if (latitude && longitude) {
        initTraccarMap(parseFloat(latitude), parseFloat(longitude))
    }
})


const zoomInput = document.getElementById("traccar_zoom");
const buttons = document.querySelectorAll(".plus, .minus");

buttons.forEach((button) => {
    button.addEventListener("click", () => {
        const zoom = parseInt(zoomInput.value);
        zoomInput.value = (zoom + (button.classList.contains("plus") ? 1 : -1));
        const event = new Event("change");
        zoomInput.dispatchEvent(event);
    });

});
