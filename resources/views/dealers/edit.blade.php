@extends('backend.layouts.master')

@section('title')
    Edit Page - Dealer
@endsection

@section('admin-content')

    <!-- page title area start -->
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">Dealers</h4>
                    <ul class="breadcrumbs pull-left">
                        <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                        <li><span>Dealers</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-6 clearfix">
                @include('backend.layouts.partials.logout')
            </div>
        </div>
    </div>
    <!-- page title area end -->

    <div class="main-content-inner">
        <form action="{{ route('admin.dealers.update',$dealer->id) }}" method="POST">
            @csrf
            @method('PUT')
        <div class="row">
            <!--Left Column-->
            <div class="col-lg-9">
                <div class="row">
                    <div class="col-md-12 mt-5 mb-3">
                        <div class="card">
                            <div class="p-4">
                                <h4>Update Dealer</h4>

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



                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" value="{{ old('name', $dealer->name) }}" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="owner_name">Owner Name</label>
                                        <input type="text" class="form-control" value="{{ old('owner_name', $dealer->owner_name) }}" name="owner_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="zone">Zone</label>
                                        <input type="text" class="form-control" value="{{ old('zone', $dealer->zone) }}" name="zone">
                                    </div>

                                    <div class="form-group">
                                        <label for="dealer_code">Dealer Code</label>
                                        <input type="text" class="form-control" value="{{ old('dealer_code', $dealer->dealer_code) }}" name="dealer_code">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" value="{{old('email',$dealer->email)}}" name="email">
                                    </div>

                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="text" class="form-control" value="{{old('website',$dealer->website)}}" name="website">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Mobile</label>
                                        <input type="text" class="form-control" value="{{old('mobile',$dealer->mobile)}}" name="mobile">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Address</label>
                                        <input type="text" class="form-control" value="{{old('address', $dealer->address)}}" name="address">
                                    </div>

                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <input type="text" name="longitude" value="{{$dealer->longitude}}" id="longitude" hidden>
                                        <input type="text" name="latitude" value="{{$dealer->latitude}}"  id="latitude" hidden>
                                        <input type="text" name="district" value="{{$dealer->district}}"  id="district" hidden>
                                        <input type="text" class="form-control bksearch" value="{{$dealer->location}}"  name="location" id="location"/>
                                        <div class="bklist"></div>
                                        <div id="loading" style="display: none;">Loading...</div> <!-- Loading indicator -->
                                    </div>

                                    <div class="form-group">
                                        <div id="map" style="width: 100%; height: 400px;"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update Dealer</button>

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
                                <input type="text" class="form-control" value="{{ old('average_sales', $dealer->average_sales) }}" name="average_sales">
                            </div>
                            <div class="form-group">
                                <label for="name">Market Size</label>
                                <input type="text" class="form-control" value="{{ old('market_size', $dealer->market_size) }}" name="market_size">
                            </div>
                            <div class="form-group">
                                <label for="market-share">Market Share</label>
                                <input type="text" class="form-control" value="{{ old('market_share', $dealer->market_share) }}" id="market_share" name="market_share" readonly>
                            </div>
                            <div class="form-group">
                                <label for="name">Competitor Brand</label>
                                <input type="text" class="form-control" value="{{ old('competition_brand', $dealer->competition_brand )}}" name="competition_brand">
                            </div>
                        </div>
                        <!-- Additional content goes here -->
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

    <!-- Your existing script and styles here -->


    <script>
        document.addEventListener('DOMContentLoaded',function(){
            const averageSalesInput = document.querySelector('input[name="average_sales"]');
            const marketSizeInput = document.querySelector('input[name="market_size"]');
            const marketShareInput= document.getElementById("market_share");

            function calculateMarketShare(){
                const averageSales = parseFloat(averageSalesInput.value) || 0;
                const marketSize = parseFloat(marketSizeInput.value) || 0 ;

                if(marketSize > 0){
                    const marketShare = (averageSales / marketSize) * 100;
                    marketShareInput.value = marketShare.toFixed(2);
                    console.log(marketShare)
                }else{
                     marketShareInput.value = "0.00";
                }

            }

            averageSalesInput.addEventListener('input', calculateMarketShare);
            marketSizeInput.addEventListener('input', calculateMarketShare);

        });

        bkoigl.accessToken = "{{ env('BARIKOI_API_KEY') }}"; // required

        // Fetch dealer's coordinates from backend
        const dealerLongitude = {{ $dealer->longitude ?? 90.3938010872331 }};
        const dealerLatitude = {{ $dealer->latitude ?? 23.821600277500405 }};
        const dealerLocation = "{{ $dealer->location ?? '' }}";

        const map = new bkoigl.Map({
            container: "map",
            center: [dealerLongitude, dealerLatitude], // Set map center to dealer's coordinates
            zoom: 15,
        });
        map.addControl(new bkoigl.FullscreenControl());
        map.addControl(new bkoigl.NavigationControl());
        map.addControl(new bkoigl.ScaleControl());


        // Initialize the marker at dealer's coordinates
        let marker = new bkoigl.Marker({ draggable: true })
            .setLngLat([dealerLongitude, dealerLatitude])
            .addTo(map);

        // Populate location input field with dealer's location
        document.getElementById("location").value = dealerLocation;
        document.getElementById("longitude").value = dealerLongitude;
        document.getElementById("latitude").value = dealerLatitude;

        // Event listener for location search
        document.getElementById("location").addEventListener("input", function () {
            let query = this.value;
            let loadingIndicator = document.getElementById("loading");

            if (query.length > 2) {
                loadingIndicator.style.display = "block"; // Show loading indicator
                fetch(`/api/proxy/autocomplete?q=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        loadingIndicator.style.display = "none"; // Hide loading indicator
                        if (data.places) {
                            let suggestions = data.places;
                            let suggestionList = document.querySelector('.bklist');
                            suggestionList.innerHTML = ''; // Clear previous suggestions

                            suggestions.forEach(place => {
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
                        loadingIndicator.style.display = "none"; // Hide loading indicator on error
                        console.error('Error fetching data:', error);
                    });
            } else {
                document.querySelector('.bklist').innerHTML = ''; // Clear suggestions if query is too short
                loadingIndicator.style.display = "none"; // Hide loading indicator if no query
            }
        });

        // Event listener for marker drag end to update coordinates and address
        marker.on('dragend', function() {
            const lngLat = marker.getLngLat();
            const longitude = lngLat.lng;
            const latitude = lngLat.lat;

            fetch(`/api/proxy/reverse-geocode?longitude=${longitude}&latitude=${latitude}`)
                .then(response => response.json())
                .then(data => {
                    if (data.place && data.place.address) {
                        const locationInput = document.getElementById("location");
                        locationInput.value = data.place.address;
                        document.getElementById("longitude").value = longitude;
                        document.getElementById("latitude").value = latitude;
                    }
                })
                .catch(error => {
                    console.error('Error fetching address:', error);
                });
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
