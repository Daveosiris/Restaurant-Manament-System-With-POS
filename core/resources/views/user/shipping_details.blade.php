@extends('front.layout')

@section('content')

<!--   hero area start   -->
<section class="page-title-area d-flex align-items-center" style="background-image: url('{{asset('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-title-item text-center">
                    <h2 class="title">{{__('Shipping Details')}}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">{{('Shipping Details')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!--   hero area end    -->
     <!--====== CHECKOUT PART START ======-->
     <section class="user-dashbord">
        <div class="container">
            <div class="row">
                @include('user.inc.site_bar')
                <div class="col-lg-9">
                    <div class="row mb-5">
                        <div class="col-lg-12">
                            <div class="user-profile-details">
                                <div class="account-info">
                                    <div class="title">
                                        <h4>{{__('Edit Shipping Details')}}</h4>
                                    </div>
                                    <div class="edit-info-area">
                                        <form action="{{route('user-shipping-update')}}" method="POST" enctype="multipart/form-data" >
                                            @csrf

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Shipping First Name')}}" name="shpping_fname" value="{{$user->shpping_fname}}" value="{{Request::old('fname')}}">
                                                    @error('shpping_fname')
                                                        <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Shipping Last Name')}}" name="shpping_lname" value="{{$user->shpping_lname}}" value="{{Request::old('fname')}}">
                                                    @error('shpping_lname')
                                                        <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="email" class="form_control" placeholder="{{__('Shipping Email')}}" name="shpping_email"  value="{{$user->shpping_email}}">
                                                    @error('shpping_email')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">

                                                    <div class="input-group mb-3">
                                                        <input type="hidden" name="shpping_country_code" value="{{$user->shipping_country_code}}">
                                                        <div class="input-group-prepend">
                                                          <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{!empty($user->shipping_country_code) ? $user->shipping_country_code : 'Select'}}</button>
                                                          <div class="dropdown-menu country-codes">
                                                            @foreach ($ccodes as $ccode)
                                                                <a class="dropdown-item" data-shpping_country_code="{{$ccode['code']}}" href="#">{{$ccode['name']}} ({{$ccode['code']}})</a>
                                                            @endforeach
                                                          </div>
                                                        </div>
                                                        <input type="text" name="shpping_number" class="form-control" placeholder="{{__('Billing Phone')}}" value="{{$user->shpping_number}}">
                                                    </div>
                                                    @error('shpping_country_code')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                    @error('shpping_number')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Shipping City')}}" name="shpping_city" value="{{$user->shpping_city}}">
                                                    @error('shpping_city')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Shipping State')}}" name="shpping_state" value="{{$user->shpping_state}}">
                                                    @error('shpping_state')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Shipping Country')}}" name="shpping_country" value="{{$user->shpping_country}}">
                                                    @error('shpping_country')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>


                                                <div class="col-lg-12">
                                                    <textarea name="shpping_address" class="form_control" placeholder="{{__('Shipping Address')}}">{{$user->shpping_address}}</textarea>
                                                    @error('shpping_address')
                                                    <p class="text-danger">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-button">
                                                        <button type="submit" class="btn form-btn">{{__('Submit')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

