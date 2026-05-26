@extends('developer.layouts.main')

@section("styles")
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
        #info {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 100%;
            background: white;
            padding: 10px;
            max-height: 100vh;
            overflow: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            font-family: sans-serif;
            font-size: 17px;
            z-index: 999;
            color:red;
        }
    </style>
@endsection


@section("content")

    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->


        <!--<div class="d-sm-flex align-items-center justify-content-between mb-4">-->
        <!--  <h1 class="h3 mb-0 text-gray-800">Coordinate Testing</h1>-->
        <!--  <div id="error" style="color: red; margin-top: 5px;"></div>-->
        <!--  <textarea id="coordinate_series" placeholder="Paste coordinates JSON here..." class="form-control"></textarea>-->
        <!--</div>-->


        <div class="container text-center">
            <div class="row">
                <div class="col">
                    <div id="error" style="color: red; margin-top: 5px;"></div>
                    <input type="number" name="booking_id" id="booking_id" class="form-control" placeholder = "Booking Id" style="height: 62px; margin-bottom: 5px" oninput="fetchBookingCoordinate(this.value)">
                </div>
                <div class="col">
                    <textarea id="coordinate_series" placeholder="Paste coordinates JSON here..." class="form-control"></textarea>
                </div>
            </div>
        </div>




        <!-- Content Row -->
        <div class="row">

            <div class="col-xl-8 col-md-6 mb-6">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row">
                            <div id="map"></div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-6">
                {{--        <div class="card border-left-primary shadow h-100 py-2">--}}
                {{--          <div class="card-body">--}}
                <div class="row">
                    <div id="info"></div>
                </div>
                {{--            </div>--}}
                {{--          </div>--}}
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-md-6 mb-6">
        <div class="card-body">
            <div class="row">
                <div id="distance_log"></div>

            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

@endsection


@section('js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{$key}}"></script>

    <script>
        const defaultPathData = [
          { "latitude": "28.627941507590094", "longitude": "77.37457150351139" },
          { "latitude": "28.62686920552784",  "longitude": "77.37457153940233" },
          { "latitude": "28.624653112177484", "longitude": "77.37432731503304" },
          { "latitude": "28.622722872597947", "longitude": "77.37416452606953" },
          { "latitude": "28.620720667887625", "longitude": "77.37400131112923" },
          { "latitude": "28.618504401169147", "longitude": "77.37383859447345" },
          { "latitude": "28.616573938777194", "longitude": "77.37367574020726" },
          { "latitude": "28.614643398599924", "longitude": "77.37335010355946" },
          { "latitude": "28.61299902075305",  "longitude": "77.37318668440457" },
          { "latitude": "28.611569117221904", "longitude": "77.37302370461464" },
          { "latitude": "28.610639650807684", "longitude": "77.37302369673387" },
          { "latitude": "28.609424188506352", "longitude": "77.37294224198718" },
          { "latitude": "28.607994208838562", "longitude": "77.37294223733613" },
          { "latitude": "28.60670736519799",  "longitude": "77.37286069945924" },
          { "latitude": "28.60499077932515",  "longitude": "77.37277954735708" },
          { "latitude": "28.603846678208143", "longitude": "77.37277955718622" },
          { "latitude": "28.602703441179955", "longitude": "77.37269777321063" },
          { "latitude": "28.60105760497622",  "longitude": "77.3726167765434" },
          { "latitude": "28.599769819473607", "longitude": "77.37261697053128" },
          { "latitude": "28.59862516623366",  "longitude": "77.37253564761517" },
          { "latitude": "28.596623457883624", "longitude": "77.37237255700268" }
        ];

        function haversineDistance(lat1, lon1, lat2, lon2) {
          const R = 6371000;
          const toRad = angle => angle * Math.PI / 180;
          const dLat = toRad(lat2 - lat1);
          const dLon = toRad(lon2 - lon1);
          const a = Math.sin(dLat / 2) ** 2 +
                    Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                    Math.sin(dLon / 2) ** 2;
          return R * (2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)));
        }

        function initMap(pathData) {
          const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14,
            center: {
              lat: parseFloat(pathData[0].latitude),
              lng: parseFloat(pathData[0].longitude)
            }
          });

          const infoDiv = document.getElementById("info");
          infoDiv.innerHTML = '';
          let totalDistance = 0;

          const pathCoordinates = pathData.map(p => ({
            lat: parseFloat(p.latitude),
            lng: parseFloat(p.longitude)
          }));

          const route = new google.maps.Polyline({
            path: pathCoordinates,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 3
          });

          route.setMap(map);

          pathCoordinates.forEach((point, i) => {
            new google.maps.Marker({
              position: point,
              map,
              label: (i + 1).toString()
            });

            if (i > 0) {
              const prev = pathCoordinates[i - 1];
              const dist = haversineDistance(prev.lat, prev.lng, point.lat, point.lng);
              totalDistance += dist;
              infoDiv.innerHTML += `Point ${i} → ${i + 1}: ${dist.toFixed(2)} meters<br>`;
            }
          });

          infoDiv.innerHTML += `<hr><b>Total Distance:</b> ${totalDistance.toFixed(2)} meters`;
        }

        window.onload = function() {
          initMap(defaultPathData);

          const element = document.getElementById("coordinate_series");
        //   element.addEventListener("change", plotmap(this.value));
            element.addEventListener("input", function() {
              plotmap(this.value);
            });



        };


        function plotmap(coordinates) {
            const errorDiv = document.getElementById("error");
            errorDiv.textContent = ''; // Clear previous errors
                  try {
                    const parsed = JSON.parse(coordinates);

                    if (!Array.isArray(parsed) || !parsed[0]?.latitude || !parsed[0]?.longitude) {
                      throw new Error("Invalid format: Expecting an array of objects with 'latitude' and 'longitude'");
                    }

                    initMap(parsed); // Valid input — draw map
                  } catch (e) {
                    errorDiv.textContent = "Invalid JSON: " + e.message;
                  }
        }

        function fetchBookingCoordinate(booking_id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
            },
            method: 'POST',
            url: "{{ route('coordinates.fetch') }}",
            data: {booking_id: booking_id},
            success: function (data) {
                console.log(data);
                if(data.success){
                     $("#coordinate_series").val(data.coordinate.coordinates);
                     $("distance_log").text =
                    plotmap(data.coordinate.coordinates);

                    // Clear existing content first
                      const log = data.booking_distance_log;
                      $("#distance_log").empty();

                      if (log && typeof log === "object") {
                        for (const key in log) {
                          if (Object.hasOwnProperty.call(log, key)) {
                            let value = log[key];
                            if (value === undefined || value === null) {
                              value = "N/A";
                            }
                            const listItem = `
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-capitalize">${key}</span>
                                <span class="badge badge-primary badge-pill">${value}</span>
                              </li>
                            `;
                            $("#distance_log").append(listItem);
                          }
                        }
                      } else {
                        $("#distance_log").append('<li class="list-group-item text-danger">No distance log available</li>');
                      }
                }
                else{
                    const errorDiv = document.getElementById("error");
                    errorDiv.textContent = "Invalid Booking Id ";
                    console.log(data);
                }
            }
        });
    }
    </script>
@endsection
