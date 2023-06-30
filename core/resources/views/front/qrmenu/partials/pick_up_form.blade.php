
    <div class="row">
       <div class="col-12">
          <div class="form billing-info">
             <div class="shop-title-box">
                <h3>{{__('Information')}}</h3>
             </div>
             <div class="row">
                <div class="col-md-4">
                   <div class="field-label">{{__('Name')}} *</div>
                   <div class="field-input">
                        @php
                            $bname = '';
                            if(empty(old())) {
                                if (Auth::check()) {
                                    $bname = Auth::user()->billing_fname;
                                }
                            } else {
                                $bname = old('billing_fname');
                            }
                        @endphp
                      <input type="text" name="billing_fname" value="{{$bname}}">
                      @error('billing_fname')
                      <p class="text-danger">{{convertUtf8($message)}}</p>
                      @enderror
                   </div>
                </div>
                <div class="col-md-4">
                   <div class="field-label">{{__('Contact Email')}} *</div>
                   <div class="field-input">
                        @php
                            $bmail = '';
                            if(empty(old())) {
                                if (Auth::check()) {
                                    $bmail = Auth::user()->billing_email;
                                }
                            } else {
                                $bmail = old('billing_email');
                            }
                        @endphp
                      <input type="text" name="billing_email" value="{{$bmail}}">
                      @error('billing_email')
                      <p class="text-danger">{{convertUtf8($message)}}</p>
                      @enderror
                   </div>
                </div>

                <div class="col-md-4">
                   <div class="field-label">{{__('Phone')}} *</div>

                   @php
                        $bnumber = '';
                        if(empty(old())) {
                            if (Auth::check()) {
                                $bnumber = Auth::user()->billing_number;
                            }
                        } else {
                            $bnumber = old('billing_number');
                        }

                        $bccode = '';
                        if(empty(old())) {
                            if (Auth::check()) {
                                $bccode = Auth::user()->billing_country_code;
                            }
                        } else {
                            $bccode = old('billing_country_code');
                        }
                    @endphp
                   <div class="input-group mb-3">
                        <input type="hidden" name="billing_country_code" value="{{$bccode}}">
                        <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{!empty($bccode) ? $bccode : 'Select'}}</button>
                        <div class="dropdown-menu country-codes">
                            @foreach ($ccodes as $ccode)
                                <a class="dropdown-item" data-billing_country_code="{{$ccode['code']}}" href="#">{{$ccode['name']}} ({{$ccode['code']}})</a>
                            @endforeach
                        </div>
                        </div>
                        <input type="text" name="billing_number" class="form-control" value="{{$bnumber}}">
                    </div>
                    @error('billing_country_code')
                    <p class="text-danger mb-2">{{ $message }}</p>
                    @enderror
                    @error('billing_number')
                    <p class="text-danger mb-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="col-md-6">
                   <div class="field-label">{{__('Pick up Date')}} *</div>
                   <div class="field-input">
                      <input type="text" class="datepicker" name="pick_up_date" value="{{old('pick_up_date')}}" autocomplete="off">
                      @error('pick_up_date')
                      <p class="text-danger">{{convertUtf8($message)}}</p>
                      @enderror
                   </div>
                </div>
                <div class="col-md-6">
                   <div class="field-label">{{__('Pick up Time')}} *</div>
                   <div class="field-input">
                      <input type="text" class="timepicker" name="pick_up_time" value="{{old('pick_up_time')}}" autocomplete="off">
                      @error('pick_up_time')
                      <p class="text-danger">{{convertUtf8($message)}}</p>
                      @enderror
                   </div>
                </div>
             </div>
          </div>
       </div>

    </div>
