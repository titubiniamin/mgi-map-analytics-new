@extends('backend.layouts.master')

@section('title')
    Dealers Page - Dealer
@endsection

@section('admin-content')
    <!-- Page title area start -->
    <div class="page-title-area">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="breadcrumbs-area clearfix">
                    <h4 class="page-title pull-left">Dealers</h4>
                    <ul class="breadcrumbs pull-left">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.dealers.index') }}">Dealers</a></li>
                        <li><span>Excel Import</span></li>
                    </ul>
                </div>
            </div>
            <div class="col-sm-6 clearfix">
                @include('backend.layouts.partials.logout')
            </div>
        </div>
    </div>
    <!-- Page title area end -->

    <div class="main-content-inner">
        <!-- Existing success message display -->
        @if (session('error'))
            <div class="alert alert-danger">
                {!! session('error') !!}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <form action="{{ route('admin.dealers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-12 mt-5 mb-3">
                            <div class="card">
                                <div class="p-4">
                                    <h4>Import Dealers</h4>

                                    <!-- Existing error handling and success message code here -->

                                    <!-- Sample File Download Button -->
                                    <div class="mb-3">
                                        <a href="{{ route('admin.dealers.sample-excel') }}" class="btn btn-secondary">Download Sample Excel</a>
                                    </div>

                                    <!-- File Upload Field -->
                                    <div class="form-group">
                                        <label for="file">Upload Dealer Data (Excel)</label>
                                        <input type="file" class="form-control" name="file" accept=".xls,.xlsx" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Import Dealers</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
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

@endsection
