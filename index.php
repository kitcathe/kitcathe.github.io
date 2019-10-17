<!DOCTYPE html>
<?php header("Access-Control-Allow-Origin"); ?>

<html>
  <head>
    <title>Cebu Map</title>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDZZOVIDAF20qESIjywEaw7TEMt66ShTPg&callback=initMap"
    async defer></script>
    <script>



      var map;
      var marker;
      var infowindow;
      var htmls = [];
      var originLat;
      var originLng;
      var origin = new Object();
      var directionsDisplay;

      function setCoords(coords) {
        origin = coords;
        originLat = coords.lat;
        originLng = coords.lng;

        //console.log("coordinates" + originLat + " " + originLng);
      } 


      function getOrigin(map, infowindow){


        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(function(position) {
              var latitude = position.coords.latitude;
              var longitude = position.coords.longitude;
              var accuracy = position.coords.accuracy;
              var coords = new google.maps.LatLng(latitude, longitude);


              var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                  };
              
                  
              setCoords(pos);

            
          },
          function error(msg) {alert('Please enable your GPS position feature.');},
          {maximumAge:10000, timeout:5000, enableHighAccuracy: true});
        } else {
            alert("Geolocation API is not supported in your browser.");
        }

      }

      // Initialize and add the map
      function initMap() {
        var cebu = {lat: 10.31672, lng: 123.89071};
        directionsDisplay = new google.maps.DirectionsRenderer();
        
        map = new google.maps.Map(
            document.getElementById('map'), {zoom: 11, center: cebu});

        directionsDisplay.setMap(map);

        var script = document.createElement('script');
              script.src = 'http://localhost/navagis/restaurants.js';
              document.getElementsByTagName('head')[0].appendChild(script);


      /*var rectangle = new google.maps.Rectangle({
        strokeColor : '#FF0000',
        strokeOpacity : 0.8,
        strokeWeight  : 2,
        fillColor     : '#FF0000',
        fillOpacity   : 0.35,
        map :map,
        bounds : {
          north: 10.318,
          south: 10.315,
          east: 123.889,
          west: 123.892
        }
      });*/

        infowindow = new google.maps.InfoWindow({
          size: new google.maps.Size(150, 50),
          content: ""
        });

        //get origin
        getOrigin(map, infowindow);

        //get places
        getPlaces();
      }


      window._callback = function(results) {
        for (var i = 0; i < results.features.length; i++) {
          var coords = results.features[i].coordinates;
          var latLng = new google.maps.LatLng(coords[0],coords[1]);//lnglat
          
          marker = new google.maps.Marker({
            position: latLng,
            map: map,
            title: results.features[i].restaurant_name,
            animation: google.maps.Animation.DROP
          });

          var restaurant_name = results.features[i].restaurant_name;
          var specialty = results.features[i].specialty;
          
          html = '<h3>'+restaurant_name+'</h3>'+
          '<p>Specialty: '+specialty+'</p>'+
          '<br><form action="javascript:getDirections()">' +
          //'<input type="hidden" SIZE=40 MAXLENGTH=40 name="oaddr" id="oaddr" value="'+ originLng+','+originLat+'" /><br>' +
          '<input type="text" SIZE=40 MAXLENGTH=40 name="oaddr" id="oaddr" value="" placeholder="Input Current Location" /><br>' +
          '<INPUT value="Get Directions" TYPE="button" onclick="getDirections()"><br>'  +
          '<input type="hidden" id="daddr" value="' + coords[0] + ',' + coords[1] +
          '"/>';

          bindInfoWindow(marker, map, infowindow, html);
          
        }
      }

  
      function bindInfoWindow(marker, map, infowindow, html){
         marker.addListener('click',function(){
        map.setCenter(marker.getPosition());
        map.setZoom(15);
        infowindow.setContent(html);
        infowindow.open(map, marker);
      });
      }

      function getDirections() {

        
        var directionsService = new google.maps.DirectionsService();

        var request = {};
        
        request.travelMode = google.maps.DirectionsTravelMode.DRIVING;
        request.avoidFerries = false;
        request.provideRouteAlternatives = true;
        
        var oaddr = document.getElementById("oaddr").value;
        var daddr = document.getElementById("daddr").value;

        request.origin = oaddr;
        //request.origin = "12.879721,121.77401700000001";
        request.destination = daddr;

        console.log(oaddr + " " +daddr);

        directionsService.route(request, function(response, status) {
          console.log("status" + status + " " + response);
          if (status == google.maps.DirectionsStatus.OK) {

            directionsDisplay.setDirections(response);
          } else alert("Directions not found:" + status);
        });
      }

  function getPlaces(){

  /*const Http = new XMLHttpRequest();
  const requestUrl = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=Museum%20of%20Contemporary%20Art%20Australia&inputtype=textquery&fields=photos,formatted_address,name,rating,opening_hours,geometry&key=AIzaSyDZZOVIDAF20qESIjywEaw7TEMt66ShTPg"
     Http.open("GET", requestUrl);
     Http.send();

     Http.onreadystatechange  = (e)=>{
      console.log("test" + Http.responseText);*/
     }

    </script>

    
    
  </body>
</html>