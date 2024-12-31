@extends('layouts.main')
@section('title', $user->name)
@section('content')
    <!-- push external head elements to head -->
    @push('head')
        <link rel="stylesheet" href="{{ asset('plugins/select2/dist/css/select2.min.css') }}">
    @endpush


    <div class="container-fluid">
    	<div class="page-header">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="page-header-title">
                        <i class="ik ik-user-plus bg-blue"></i>
                        <div class="d-inline">
                            <h5>{{ __('Edit User')}}</h5>
                            <span>{{ __('Create new user, assign roles & permissions')}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{url('/')}}"><i class="ik ik-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ __('User')}}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <!-- clean unescaped data is to avoid potential XSS risk -->
                                {{ clean($user->name, 'titles')}}
                            </li>

                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- start message area-->
            @include('include.message')
            <!-- end message area-->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" method="POST" action="{{ url('user/update') }}" >
                        @csrf
                            <input type="hidden" name="id" value="{{$user->id}}">
                            <div class="row">
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label for="name">{{ __('Username')}}<span class="text-red">*</span></label>
                                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ clean($user->name, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="email">{{ __('Email')}}<span class="text-red">*</span></label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ clean($user->email, 'titles')}}" required>
                                        <div class="help-block with-errors"></div>

                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <!--Designation-->
                                    <div class="form-group">
                                        <label for="designation">{{ __('Designation')}}</label>
                                        <select class="select2 form-control" id="designation_id" name="designation_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach($designations as $id => $name)
                                                <option {{ $user->designation && $user->designation->id == $id ? 'selected' : '' }} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('designation_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Designation End-->
                                    <!--Department-->
                                    <div class="form-group">
                                        <label for="department">{{ __('Department')}}</label>
                                        <select class="select2 form-control" id="department_id" name="department_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach($departments as $id => $name)
                                                <option {{$user->department && $user->department->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('department_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Department End-->
                                    <!--Zone-->
                                    <div class="form-group">
                                        <label for="zone">{{ __('Zone')}}</label>
                                        <select class="select2 form-control" id="zone_id" name="zone_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach($zones as $id => $name)
                                                <option {{$user->zone && $user->zone->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('zone_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Zone End-->


                                    <div class="form-group">
                                        <label for="password">{{ __('Password')}}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"  >
                                        <div class="help-block with-errors"></div>

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password-confirm">{{ __('Confirm Password')}}</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                                        <div class="help-block with-errors"></div>
                                    </div>

                                </div>
                                <!----------------->
                                <!---------Right Side-------->
                                <div class="col-sm-6">
                                    <!--District-->
                                    <div class="form-group">
                                        <label for="district">{{ __('District')}}</label>
                                        <select class="select2 form-control" id="district_id" name="district_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach($districts as $id => $name)
                                                <option {{$user->district && $user->district->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('district_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--District End-->
                                    <!--Office-->
                                    <div class="form-group">
                                        <label for="office">{{ __('Office')}}</label>
                                        <select class="select2 form-control" id="office_id" name="office_id">
                                            <option  value="" selected disabled>Select</option>
                                            @foreach($offices as $id => $name)
                                                <option {{$user->office && $user->office->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('office_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Office End-->
                                    <!--Blood Group-->
                                    <div class="form-group">
                                        <label for="blood_group">{{ __('Blood Group')}}</label>
                                        <select class="select2 form-control" id="blood_group_id" name="blood_group_id">
                                            <option value="" selected disabled>Select</option>
                                            @foreach($blood_groups as $id => $name)
                                                <option {{$user->bloodGroup && $user->bloodGroup->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('blood_group_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Blood Group End-->
                                    <!--Brand Group-->
                                    <div class="form-group">
                                        <label for="brand">{{ __('Brand Group')}}</label>
                                        <select class="select2 form-control" id="brand_id" name="brand_id">
                                            <option  value="" selected disabled>Select</option>
                                            @foreach($brands as $id => $name)
                                                <option {{$user->brand && $user->brand->id == $id ? 'selected': ''}} value="{{ $id }}">{{ $name }}</option>
                                            @endforeach
                                        </select>

                                        <div class="help-block with-errors"></div>

                                        @error('brand_id')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--Brand Group End-->
                                    <!-- Assign role & view role permisions -->
                                    <div class="form-group">
                                        <label for="role">{{ __('Assign Role')}}<span class="text-red">*</span></label>
                                        {!! Form::select('role', $roles, $user_role->id??'' ,[ 'class'=>'form-control select2', 'placeholder' => 'Select Role','id'=> 'role', 'required'=>'required']) !!}
                                    </div>
                                    <div class="form-group">
                                        <label for="role">{{ __('Permissions')}}</label>
                                        <div id="permission" class="form-group">
                                            @foreach($user->getAllPermissions() as $key => $permission)
                                            <span class="badge badge-dark m-1">
                                                <!-- clean unescaped data is to avoid potential XSS risk -->
                                                {{ clean($permission->name, 'titles')}}
                                            </span>
                                            @endforeach
                                        </div>
                                        <input type="hidden" id="token" name="token" value="{{ csrf_token() }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary form-control-right">{{ __('Update')}}</button>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- push external js -->
    @push('script')
        <script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}"></script>
        <!--get role wise permissiom ajax script-->
        <script src="{{ asset('js/get-role.js') }}"></script>
    @endpush
@endsection
