@extends('layouts.main')
@section('title', 'Users')
@section('content')
    <div class="main-content-inner">
        <form action="{{ route('dealers.store') }}" method="POST"> <!-- Form starts here -->
            @csrf
            <div class="row">
                <!-- Left column for form inputs -->
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-md-12 mt-5 mb-3">
                            <div class="card">
                                <div class="p-4">
                                    <h4>Create Dealer</h4>

                                    <!-- Display validation errors -->
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <!-- Display success message -->
                                    @if (session('success'))
                                        <div id="flash-message" class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <script>
                                        // Automatically hide the flash message after 5 seconds
                                        setTimeout(function() {
                                            const flashMessage = document.getElementById('flash-message');
                                            if (flashMessage) {
                                                flashMessage.style.transition = 'opacity 0.5s ease'; // Fade-out transition
                                                flashMessage.style.opacity = '0'; // Start fading

                                                setTimeout(() => flashMessage.remove(), 500); // Remove from DOM after fade-out
                                            }
                                        }, 5000); // 5-second delay
                                    </script>

                                    <!-- Form Fields Start -->
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" value="{{ old('name') }}" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="owner_name">Owner Name</label>
                                        <input type="text" class="form-control" value="{{ old('owner_name') }}" name="owner_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="zone">Zone</label>
                                        <input type="text" class="form-control" value="{{ old('zone')  }}" name="zone">
                                    </div>

                                    <div class="form-group">
                                        <label for="dealer_code">Dealer Code</label>
                                        <input type="text" class="form-control" value="{{ old('dealer_code') }}" name="dealer_code">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control"  name="email">
                                    </div>

                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="text" class="form-control" value="{{ old('website') }}" name="website">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Mobile</label>
                                        <input type="text" class="form-control" value="{{ old('mobile') }}" name="mobile">
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <input type="text" class="form-control" value="{{ old('address') }}" name="address">
                                    </div>

                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" name="longitude" id="longitude" hidden>
                                        <input type="text" name="latitude" id="latitude" hidden>
                                        <input type="text" name="district" id="district" hidden>
                                        <input type="text" class="form-control bksearch" name="location" id="location" />
                                        <div class="bklist"></div>
                                        <div id="loading" style="display: none;">Loading...</div> <!-- Loading indicator -->
                                    </div>

                                    <div class="form-group">
                                        <div id="map" style="width: 100%; height: 400px; background-color: yellow;"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Save Dealer</button>
                                    <!-- Form Fields End -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right column for additional content -->
                <div class="col-lg-3" style="height: 70vh;">
                    <div class="card mt-5 mb-3" style="height: 400px;background-color: white">
                        <div class="p-4">
                            <div class="form-group mb-4">
                                <div class="form-group">
                                    <label for="name">Average Sales</label>
                                    <input type="text" class="form-control" value="{{ old('average_sales') }}" name="average_sales">
                                </div>
                                <div class="form-group">
                                    <label for="name">Market Size</label>
                                    <input type="text" class="form-control" value="{{ old('market_size') }}" name="market_size">
                                </div>
                                <div class="form-group">
                                    <label for="market-share">Market Share</label>
                                    <input type="text" class="form-control" value="{{ old('market_share') }}" name="market_share" id="market_share" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="name">Competitor Brand</label>
                                    <input type="text" class="form-control" value="{{ old('competition_brand') }}" name="competition_brand">
                                </div>
                            </div>
                            <!-- Additional content goes here -->
                        </div>
                    </div>
                </div>
            </div>
        </form> <!-- Form ends here -->
    </div>
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const averageSalesInput = document.querySelector('input[name="average_sales"]');
            const marketSizeInput = document.querySelector('input[name="market_size"]');
            const marketShareInput = document.getElementById("market_share");

            //Calculate Market Share
            function calculateMarketShare(){
                console.log('calculate')
                const averageSales=parseFloat(averageSalesInput.value) || 0;
                const marketSize = parseFloat(marketSizeInput.value) || 0;
                if(marketSize >0){
                    const marketShare=(averageSales/marketSize) * 100;
                    marketShareInput.value=marketShare.toFixed(2);
                }else{
                    marketShareInput.value="0.00"
                }
            }
            //Attach event Listener to trigger calculation on input change

            averageSalesInput.addEventListener('input',calculateMarketShare);
            marketSizeInput.addEventListener('input', calculateMarketShare);

        });

        bkoigl.accessToken = "{{ env('BARIKOI_API_KEY') }}";

        let map, marker;

        // Use geolocation to show the current location on map load

        // Use geolocation to show the current location on map load
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                console.log("Initial location:", latitude, longitude);

                initializeMap(latitude, longitude);
                fetchLocationName(latitude, longitude);
            },
            (error) => {
                console.error("Error fetching location:", error);
                // Handle the error and provide fallback coordinates or message
                if (error.code === error.PERMISSION_DENIED) {
                    alert(error)
                    // alert("Location access denied. Please enable location services.");
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    alert("Position unavailable. Try again later.");
                } else if (error.code === error.TIMEOUT) {
                    alert("Location request timed out. Please try again.");
                }
                // Fallback coordinates if geolocation fails
                initializeMap(23.821600277500405, 90.3938010872331);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000, // Timeout after 10 seconds
                maximumAge: 0 // Do not use cached location
            }
        );

        function initializeMap(latitude, longitude) {
            map = new bkoigl.Map({
                container: "map",
                center: [longitude, latitude],
                zoom: 15,
            });

            map.addControl(new bkoigl.FullscreenControl());
            map.addControl(new bkoigl.NavigationControl());
            map.addControl(new bkoigl.ScaleControl());

            // Initialize a draggable marker
            marker = new bkoigl.Marker({ draggable: true })
                .setLngLat([longitude, latitude])
                .addTo(map);

            // Update input fields when the marker is dragged
            marker.on('dragend', () => {
                const lngLat = marker.getLngLat();
                fetchLocationName(lngLat.lat, lngLat.lng);
            });
        }

        function fetchLocationName(latitude, longitude) {
            fetch(`/api/v1/proxy/reverse-geocode?longitude=${longitude}&latitude=${latitude}`)
                .then(response => response.json())
                .then(data => {
                    if (data.place && data.place.address) {
                        document.getElementById("location").value = data.place.address;
                        document.getElementById("longitude").value = longitude;
                        document.getElementById("latitude").value = latitude;
                        document.getElementById("district").value = data.place.district;
                    }
                })
                .catch(error => {
                    console.error("Error fetching address:", error);
                });
        }

        document.getElementById("location").addEventListener("input", function () {
            let query = this.value;
            let loadingIndicator = document.getElementById("loading");

            if (query.length > 2) {
                loadingIndicator.style.display = "block";
                fetch(`/api/v1/proxy/autocomplete?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingIndicator.style.display = "none";
                        let suggestionList = document.querySelector('.bklist');
                        suggestionList.innerHTML = '';

                        if (data.places) {
                            data.places.forEach(place => {
                                let suggestionItem = document.createElement('div');
                                suggestionItem.textContent = place.address;
                                suggestionItem.className = 'suggestion-item';
                                suggestionItem.onclick = function () {
                                    marker.setLngLat([place.longitude, place.latitude]);
                                    map.flyTo({ center: [place.longitude, place.latitude], zoom: 15 });
                                    suggestionList.innerHTML = '';
                                    document.getElementById("location").value = place.address;
                                    document.getElementById("longitude").value = place.longitude;
                                    document.getElementById("latitude").value = place.latitude;
                                    document.getElementById("district").value = place.district;
                                };
                                suggestionList.appendChild(suggestionItem);
                            });
                        }
                    })
                    .catch(error => {
                        loadingIndicator.style.display = "none";
                        console.error("Error fetching data:", error);
                    });
            } else {
                document.querySelector('.bklist').innerHTML = '';
                loadingIndicator.style.display = "none";
            }
        });
    </script>


    <style>
        .suggestion-item {
            padding: 5px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #f0f0f0; /* Highlight on hover */
        }
        #loading {
            display: none; /* Initially hidden */
            font-size: 14px;
            color: #888;
            padding: 10px 0;
        }


    </style>
@endsection
