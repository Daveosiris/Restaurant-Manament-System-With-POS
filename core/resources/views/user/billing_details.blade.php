@extends('front.layout')

@section('content')

<section class="page-title-area d-flex align-items-center" style="background-image: url('{{asset('assets/front/img/' . $bs->breadcrumb)}}');background-size:cover;">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-title-item text-center">
                    <h2 class="title">{{__('Billing Details')}}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">{{('Billing Details')}}</li>
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
                                        <h4>{{__('Edit Billing Details')}}</h4>
                                    </div>
                                    <div class="edit-info-area">
                                        <form action="{{route('billing-update')}}" method="POST" enctype="multipart/form-data" >
                                            @csrf

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Billing First Name')}}" name="billing_fname" value="{{$user->billing_fname}}">
                                                    @error('billing_fname')
                                                        <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Billing Last Name')}}" name="billing_lname" value="{{$user->billing_lname}}">
                                                    @error('billing_lname')
                                                        <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-12">
                                                    <input type="email" class="form_control" placeholder="{{__('Billing Email')}}" name="billing_email"  value="{{$user->billing_email}}">
                                                    @error('billing_email')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="input-group mb-3">
                                                        <input type="hidden" name="billing_country_code" value="">
                                                        <div class="input-group-prepend">
                                                          <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{!empty($user->billing_country_code) ? $user->billing_country_code : 'Select'}}</button>
                                                          <div class="dropdown-menu country-codes">
                                                            @foreach ($ccodes as $ccode)
                                                                <a class="dropdown-item" data-billing_country_code="{{$ccode['code']}}" href="#">{{$ccode['name']}} ({{$ccode['code']}})</a>
                                                            @endforeach
                                                          </div>
                                                        </div>
                                                        <input type="text" name="billing_number" class="form-control" placeholder="{{__('Billing Phone')}}" value="{{$user->billing_number}}">
                                                    </div>
                                                    @error('billing_country_code')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                    @error('billing_number')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                    
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Billing City')}}" name="billing_city" value="{{$user->billing_city}}">
                                                    @error('billing_city')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Billing State')}}" name="billing_state" value="{{$user->billing_state}}">
                                                    @error('billing_state')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" class="form_control" placeholder="{{__('Billing Country')}}" name="billing_country" value="{{$user->billing_country}}">
                                                    @error('billing_country')
                                                    <p class="text-danger mb-2">{{ $message }}</p>
                                                    @enderror
                                                </div>


                                                <div class="col-lg-12">
                                                    <textarea name="billing_address" class="form_control" placeholder="{{__('Billing Address')}}">{{$user->billing_address}}</textarea>
                                                    @error('billing_address')
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

