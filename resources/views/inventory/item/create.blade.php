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
    <style>

        /* Container style */
        .input-images-wrapper {
            position: relative;
            width: 100%;
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background-color: #f9f9f9;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        /* Hover effect */
        .input-images-wrapper:hover {
            border-color: #007bff;
        }

        /* Text inside the div */
        .input-images-wrapper span {
            color: #666;
            font-size: 16px;
        }

        /* Hidden file input */
        .input-images {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Preview image style */
        .preview-container {
            margin-top: 10px;
            text-align: center;
        }

        .preview-container img {
            max-width: 100%;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
<script>
    document.querySelector('#item_image').addEventListener('change', function (e) {
        const previewContainer = document.getElementById('preview-container');
        const file = e.target.files[0];

        // Clear any existing preview
        previewContainer.innerHTML = '';

        if (file) {
            const reader = new FileReader();

            reader.onload = function (event) {
                const imgElement = document.createElement('img');
                imgElement.src = event.target.result;
                previewContainer.appendChild(imgElement);
            };

            reader.readAsDataURL(file);
        }
    });

</script>
@endsection
