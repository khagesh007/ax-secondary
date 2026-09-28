 <style>
     .vertical-navs-step .nav .nav-link {
         padding: 0px;
         border: none;
     }

     .rmDark {
         padding: 15px;
        font-size: 13px;
         text-align: left;
/*         border-radius: 0px 10px 10px 0px;*/
     }

     .randomClass {
         border: solid 0px #f1f3f4 !important;
     }

     .nav>.nav-link {
         margin-left: 2px;
     }

     .nav-links {
         margin-left: 2px;
     }

     .arrow-navtabs .nav-item .nav-link {
         font-size: 13px;
         padding: 2px 5px;
     }

     .navLinks {
         position: relative;
         text-align: center;
         background: 0 0;
         border: 0;
         border-radius: 0.25rem;
         font-size: 13px;
         padding: 2px 5px;
         margin-left: 2px;
     }

     .pr-0 {
         padding-right: 0px;
     }

     .pl-0 {
         padding-left: 0px;
     }

     .pl-40 {
         padding-left: 40px !important;
     }
 </style>

 <div class="col-xl-12">
     <div class="card">
         <div class="card-body form-steps">
             <div class="row gy-2">
                 <div class="step-arrow-nav mb-0" id="makeItDisable">

                                     

                 <div class="col-lg-12 step-arrow-nav">
                     <ul class="nav nav-pills custom-nav nav-justified" role="tablist" aria-orientation="horizontal">

                         @foreach ($statuses ?? [] as $status)
                             <li class="nav-item checkClass" role="presentation">

                            

                                 <a class="nav-link rmActive open_sub_status click_{{$status->name}}" data-value="{{ $status->name }}"
                                     data-status_id="{{ $status->id }}"   data-status_name="{{ $status->name }}" 
                                     data-badge="{{ $status->badge ?? 'badge-soft-info' }}" data-bs-toggle="tab" 
                                     href="#arrow-profile{{ $status->id }}" role="tab" aria-selected="false" tabindex="-1" aria-selected="false" data-position="-1">
                                     <span
                                         class="rmDark d-none d-sm-block  badge  sub_{{ $status->id }} randomClass status_{{ $status->name }}">
                                         <i class="{{ $status->icon }} me-2"></i> {{ $status->name }}</span>
                                 </a>
                             </li>

                             {{-- <!-- <button class="nav-link active rmActive open_sub_status"  data-value="{{ $status->name }}"
                                data-sub_id="{{ $status->id }}" data-status_id="1"
                                data-badge="{{ $status->badge ?? 'badge-soft-info' }}" aria-selected="false">
                             <span class="step-title me-2">
                               <i class="ri-close-circle-fill step-icon me-2"></i> Unspecified </span>
                           </button>  --> --}}
                         @endforeach

                     </ul>
                     <!-- end nav -->
                 </div>
                 </div>
                 <!-- end col-->
                 <div class="col-lg-9">
                     <div class="px-lg-4">
                         <div class="tab-content">
                             <div class="tab-pane fade show active">
                                

                                 <div>
                                     <div class="row g-3">
                                         <h5 class="mt-4">Basic Information</h5>
                                         <div class="col-2 mt-2">First Name*</div>
                                         <div class="col-4 mt-2">
                                             <input type="text" class="form-control inputs" name="first_name"
                                                 id="first_name" placeholder="Enter first name" required>
                                             <div class="invalid-feedback"> Please enter first name. </div>
                                         </div>
                                         <div class="col-2 mt-2">Whatsapp</div>
                                         <div class="col-md-3 mt-2 pr-0" id="whatsappDiv1">
                                             <input type="text" class="form-control inputs" name="whatsapp"
                                                 id="whatsapp" placeholder="Enter whatsapp number">
                                             <div class="invalid-feedback"> Please enter a valid whatsapp number. </div>
                                         </div>
                                         <div class="col-1 mt-2" id="whatsappDiv2">
                                             <a href="javascript:void(0);" title="Whatsapp Now" id="whatapp-button"
                                                 class="avatar-title bg-success text-white fs-15 rounded ">
                                                 <i class="ri-whatsapp-line"></i>
                                             </a>
                                         </div>
                                         <div class="col-2 mt-2">Last Name</div>
                                         <div class="col-4 mt-2">
                                             <input type="text" class="form-control inputs" name="last_name"
                                                 id="last_name" placeholder="Enter last name">
                                             <div class="invalid-feedback"> Please enter last name. </div>
                                         </div>
                                         <div class="col-2 mt-2">Phone</div>
                                         <div class="col-4 mt-2">
                                             <input type="text" class="form-control inputs phone" name="phone"
                                                 id="phone" placeholder="Enter phone">
                                             <div class="invalid-feedback"> Please enter a valid phone number. </div>
                                         </div>
                                         <div class="col-2 mt-2">Mobile*</div>
                                         <div class="col-md-3 mt-2 pr-0" id="mobileDiv1">
                                             <input type="tel" class="form-control pl-40 inputs" name="mobile"
                                                 id="mobile" required placeholder="Enter mobile">
                                             <div class="invalid-feedback"> Please enter a valid mobile number. </div>
                                             <div class="text-danger" id="mobile-exists"></div>
                                         </div>
                                         <div class="col-1 mt-2" id="call_now_button"> @canany(['Dial my lead',
                                                 'Dial other lead'])
                                                 <a href="javascript:void(0);" title="Call Now" id="start-call-button"
                                                     class="avatar-title bg-info text-white fs-15 rounded ">
                                                     <i class="ri-phone-line"></i>
                                                 </a>
                                                 <a href="javascript:void(0);" title="Calling" id="calling-button"
                                                     class="avatar-title bg-soft-success text-success fs-15 rounded d-none">
                                                     <span class="d-flex align-items-center">
                                                         <span class="spinner-grow flex-shrink-0" role="status">
                                                             <span class="visually-hidden">Loading...</span>
                                                         </span>
                                                     </span>
                                                 </a>
                                             @endcanany
                                         </div>
                                         <div class="col-2 mt-2">Email</div>
                                         <div class="col-4 mt-2">
                                             <input type="email" class="form-control inputs" name="email"
                                                 id="email" placeholder="Enter email address">
                                             <div class="invalid-feedback"> Please enter a valid email address. </div>
                                             <div class="text-danger" id="email-exists"></div>
                                         </div>
                                         <div class="col-2 mt-2">Nationality</div>
                                         <div class="col-4 mt-2">
                                             <select class="form-control select2 inputs" name="nationality_id"
                                                 id="nationality_id">
                                                 <option selected disabled value="">Select nationality </option>
                                                 @foreach ($countries ?? [] as $country)
                                                     <option value="{{ $country->id }}">
                                                         {{ $country->nationality }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                             <div class="invalid-feedback"> Please choose nationality. </div>
                                         </div>
                                         <div class="col-2 mt-2">Gender</div>
                                         <div class="col-4 mt-2">
                                             <select class="form-control select2 inputs" name="gender"
                                                 id="gender">
                                                 <option selected disabled value="">Select gender</option>
                                                 <option value="Male">Male</option>
                                                 <option value="Female">Female</option>
                                             </select>
                                             <div class="invalid-feedback"> Please choose gender. </div>
                                         </div>
                                         <div class="col-2 mt-2">Date of birth</div>
                                         <div class="col-4 mt-2">
                                             <input type="date" class="form-control inputs"
                                                 data-provider="flatpickr" data-date-format="Y-m-d" name="dob"
                                                 id="dob" placeholder="Enter date of birth">
                                             <div class="invalid-feedback"> Please enter date of birth. </div>
                                         </div>
                                         <div class="col-2 mt-2">Address</div>
                                         <div class="col-4 mt-2">
                                             <input type="text" class="form-control inputs" name="address"
                                                 id="address" placeholder="Enter address">
                                             <div class="invalid-feedback"> Please enter address. </div>
                                         </div>
                                         {{-- <div class="col-2 mt-2">Company</div>
                                         <div class="col-4 mt-2">
                                             <input type="text" class="form-control inputs" name="company"
                                                 id="company" placeholder="Enter company">
                                             <div class="invalid-feedback"> Please enter company. </div>
                                         </div>
                                         <div class="col-2 mt-2" id="follow_up_label">Follow Up</div>
                                         <div class="col-4 mt-2">
                                             <input type="date" class="form-control inputs"
                                                 data-provider="flatpickr" data-date-format="Y-m-d"
                                                 name="follow_up_date" id="follow_up_date"
                                                 placeholder="Enter follow up date">
                                             <div class="invalid-feedback"> Please enter follow-up date. </div>
                                         </div> --}}
                                     </div>

                                 </div>
                             </div>
                             <!-- end tab pane -->
                         </div>
                         <!-- end tab content -->
                     </div>
{{-- 
                     <div class="text-center">
                        <div class="spinner-border text-success mt-5 mb-5" id="criteriaTabLoader"
                            role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div> --}}

                     <div class="px-lg-4" id="criteriaDiv">
                         <div class="tab-content">
                             <div class="">
                                 <div class="row">

                                   

                                     <h5 class="mt-2">Lead Criteria</h5>
                                     <div class="col-2 mt-2">Property</div>
                                     <div class="col-10 mt-2">
                                         <select class="form-control select2 inputs" name="property_id" id="property_id">
                                             {{-- <option value="" selected disabled>Select listing</option> --}}
                                         </select>
                                         <div class="invalid-feedback"> Please choose property. </div>
                                     </div>

                                     <div class="col-2 mt-2">Purpose*</div>
                                     <div class="col-4 mt-2">
                                         <select name='for[]' id="for" class="form-control select2 inputs"
                                             multiple required>
                                             @foreach (\App\Models\Lead::FOR_SELECT ?? [] as $key => $value)
                                                 @if (
                                                     $key == 'Unspecified' &&
                                                         auth()->user()->hasRole('Agent'))
                                                 @else
                                                     <option value="{{ $key }}">{{ $value }}</option>
                                                 @endif
                                             @endforeach
                                         </select>
                                         <div class="invalid-feedback"> Please choose lead for. </div>
                                     </div>

                                     <div class="col-2 mt-2">Furnished</div>
                                     <div class="col-4 mt-2">
                                         <select class="form-control select2 inputsCriteria inputs" id="furnished"
                                             name="furnished">
                                             <option value="" selected disabled>Select</option>
                                             <option value="FURNISHED">Furnished</option>
                                             <option value="SEMI_FURNISHED">Semi Furnished</option>
                                             <option value="UNFURNISHED">Unfurnished</option>
                                         </select>
                                     </div>

                                 </div>

                                 

                                 <div class="" id="criteriaFormDiv">


                                    <div class="row  mt-2 align-items-center">
                                        <div class="col-2 mt-2">Community</div>
                                        <div class="col-4 mt-2">
                                            <select class="select2 inputsCriteria inputs" multiple id="communities"
                                                name="communities[]"></select>
                                        </div>
                                        <div class="col-2 mt-2">Location</div>
                                        <div class="col-4 mt-2">
                                            <input type="hidden" name="locations" id="locations">
                                            <select class="select2 inputsCriteria inputs" multiple
                                                id="sub_communities" name="sub_communities[]"></select>
                                        </div>
                                      
                                    </div>
                                    
                                     {{-- <form id="criteriaForm"> --}}
                                     <div class="row">



                                     </div>
                                     <div class="row">
                                         <div class="col-2 mt-2">Beds</div>
                                         <div class="col-2 mt-2">
                                             @csrf
                                             <input type="hidden" class="inputsCriteria" name="lead_id"
                                                 id="criteria_lead_id">
                                             <input type="hidden" class="inputsCriteria" name="criteria_id"
                                                 id="criteria_id">
                                             <select class="form-control inputsCriteria select2 inputs"
                                                 name="min_bed_id" id="min_bed_id">
                                                 <option value="" selected disabled>Select</option>
                                                 @foreach ($beds as $bed)
                                                     <option value="{{ $bed->id }}">
                                                         {{ $bed->name }}</option>
                                                 @endforeach
                                             </select>
                                             <div class="invalid-feedback">
                                                 Please choose an option.
                                             </div>
                                         </div>
                                         <div class="col-2 mt-2">
                                             <select class="form-control inputsCriteria select2 inputs"
                                                 name="max_bed_id" id="max_bed_id">
                                                 <option value="" selected disabled>Select</option>
                                                 @foreach ($beds as $bed)
                                                     <option value="{{ $bed->id }}">
                                                         {{ $bed->name }}</option>
                                                 @endforeach
                                             </select>
                                             <div class="invalid-feedback">
                                                 Please choose an option.
                                             </div>
                                         </div>
                                         <div class="col-2 mt-2">Baths</div>
                                         <div class="col-2 mt-2">
                                             <select class="form-control inputsCriteria select2 inputs"
                                                 name="min_bath_id" id="min_bath_id">
                                                 <option value="" selected disabled>Select</option>
                                                 @foreach ($baths as $bath)
                                                     <option value="{{ $bath->id }}">
                                                         {{ $bath->name }}</option>
                                                 @endforeach
                                             </select>
                                             <div class="invalid-feedback">
                                                 Please choose an option.
                                             </div>
                                         </div>
                                         <div class="col-2 mt-2">
                                             <select class="form-control inputsCriteria select2 inputs"
                                                 name="max_bath_id" id="max_bath_id">
                                                 <option value="" selected disabled>Select</option>
                                                 @foreach ($baths as $bath)
                                                     <option value="{{ $bath->id }}">
                                                         {{ $bath->name }}</option>
                                                 @endforeach
                                             </select>
                                             <div class="invalid-feedback">
                                                 Please choose an option.
                                             </div>
                                         </div>
                                     </div>
                              
                                     <div class="row  mt-2 align-items-center">
                                         <div class="col-2 mt-2">Budget</div>
                                         
                                                 <div class="col-2 mt-2">
                                                    <input type="text" class="form-control inputsCriteria inputs" id="min_price" name="min_price" placeholder="Min Budget">
                                                     {{-- <select class="form-control select2 inputsCriteria inputs"
                                                         id="min_price" name="min_price">
                                                         <option value="10000">10,000</option>
                                                         <option value="20000">20,000</option>
                                                         <option value="30000">30,000</option>
                                                         <option value="40000">40,000</option>
                                                         <option value="50000">50,000</option>
                                                         <option value="60000">60,000</option>
                                                         <option value="850000">85,000</option>
                                                         <option value="110000">110,000</option>
                                                         <option value="135000">135,000</option>
                                                         <option value="160000">160,000</option>
                                                         <option value="185000">185,000</option>
                                                         <option value="210000">210,000</option>
                                                         <option value="235000">235,000</option>
                                                         <option value="260000">260,000</option>
                                                         <option value="285000">285,000</option>
                                                         <option value="310000">310,000</option>
                                                         <option value="335000">335,000</option>
                                                         <option value="360000">360,000</option>
                                                         <option value="410000">410,000</option>
                                                         <option value="460000">460,000</option>
                                                         <option value="510000">510,000</option>
                                                         <option value="560000">560,000</option>
                                                         <option value="610000">610,000</option>
                                                         <option value="660000">660,000</option>
                                                         <option value="710000">710,000</option>
                                                         <option value="760000">760,000</option>
                                                         <option value="810000">810,000</option>
                                                         <option value="860000">860,000</option>
                                                         <option value="910000">910,000</option>
                                                     </select> --}}
                                                 </div>

                                                 <div class="col-2 mt-2">
                                                    <input type="text" class="form-control inputsCriteria inputs" id="max_price" name="max_price" placeholder="Max Budget">

                                                     {{-- <select class="form-control select2 inputsCriteria inputs"
                                                         id="max_price" name="max_price">

                                                         <option value="50000">50,000</option>
                                                         <option value="60000">60,000</option>
                                                         <option value="850000">85,000</option>
                                                         <option value="110000">110,000</option>
                                                         <option value="135000">135,000</option>
                                                         <option value="160000">160,000</option>
                                                         <option value="185000">185,000</option>
                                                         <option value="210000">210,000</option>
                                                         <option value="235000">235,000</option>
                                                         <option value="260000">260,000</option>
                                                         <option value="285000">285,000</option>
                                                         <option value="310000">310,000</option>
                                                         <option value="335000">335,000</option>
                                                         <option value="360000">360,000</option>
                                                         <option value="410000">410,000</option>
                                                         <option value="460000">460,000</option>
                                                         <option value="510000">510,000</option>
                                                         <option value="560000">560,000</option>
                                                         <option value="610000">610,000</option>
                                                         <option value="660000">660,000</option>
                                                         <option value="710000">710,000</option>
                                                         <option value="760000">760,000</option>
                                                         <option value="810000">810,000</option>
                                                         <option value="860000">860,000</option>
                                                         <option value="910000">910,000</option>
                                                         <option value="1010000">1,010,000</option>
                                                     </select> --}}
                                                 </div>

                                                  <div class="col-2 mt-2">View</div>
                                                 <div class="col-4 mt-2">
                                                    <select class="form-control select2 inputs" name="view[]" id="view" multiple>
                                                    @foreach (\App\Models\Property::VIEWS ?? [] as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach

                                                </select>
                                             <div class="invalid-feedback">
                                                 Please choose an option.
                                             </div>
                                         </div>
                                        
                                             
                                     </div>
                                     <div class="row mt-2">

                                         {{-- <div class="col-12 d-flex">
                                             <span class="flex-grow-1"></span>
                                             <span class="flex-shrink-0">
                                                 <button type="submit" id="leadCirteriaSaveBtn"
                                                     class="btn btn-primary">Save</button>
                                             </span>
                                         </div> --}}
                                     </div>
                                     {{-- </form> --}}
                                 </div>
                             </div>
                         </div>
                     </div>

                 </div>
                 <!-- end col -->
                 <div class="col-lg-3">
                     <div class="row g-2">
                         <h5 class="mt-3">Actions</h5>
                         {{-- @can('Leads Assign Agent') --}}

                         <label for="zip" class="form-label">Agent</label>
                         <select name='agent_id' id="agent_id" class="form-control select2 inputs">
                             <option selected disabled value="">Select Agent</option>
                             @foreach ($agents ?? [] as $agent)
                                 <option value="{{ $agent->id }}">{{ $agent->name }}
                                 </option>
                             @endforeach
                         </select>
                         <div class="invalid-feedback"> Please choose agent. </div>
                         {{-- @endcan --}}


                         @if ($types->count() > 0)

                             <div class="col-md-12">
                                 <label for="zip" class="form-label">Lead Type*</label>
                                 <select class="form-control select2 inputs" name="type_id" id="type_id"
                                     required="true">
                                     @foreach ($types ?? [] as $type)
                                         <option value="{{ $type->id }}">{{ $type->name }}</option>
                                     @endforeach
                                 </select>
                                 <div class="invalid-feedback"> Please choose lead type. </div>
                             </div>
                         @endif

                         <div class="col-md-12">
                             <label for="zip" class="form-label">Lead Source*</label>
                             <select class="form-control select2 inputs" name="source_id" id="source_id" required>
                                 @foreach ($sources ?? [] as $src)
                                     <option value="{{ $src->id }}">{{ $src->name }}</option>
                                 @endforeach
                             </select>
                             <div class="invalid-feedback"> Please choose lead source. </div>
                         </div>
                         <div class="col-md-12">
                             <label for="zip" class="form-label">Lead Medium*</label>
                             <select class="form-control select2 inputs" name="lead_medium_id" id="lead_medium_id"
                                 required>
                                 <option value="" selected disabled>Select Medium</option>
                                 @foreach ($mediums ?? [] as $src)
                                     <option value="{{ $src->id }}">{{ $src->name }}</option>
                                 @endforeach
                             </select>
                             <div class="invalid-feedback"> Please choose lead Medium. </div>
                         </div>
                         <div class="col-md-12 d-none">
                             <label for="country" class="form-label">Sales Team Leader</label>
                             <select class="form-control select2 inputs" name="telesales_head_id"
                                 id="telesales_head_id">
                                 @foreach ($telesalesheads ?? [] as $leader)
                                     <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                                 @endforeach
                             </select>
                             <div class="invalid-feedback"> Please choose Telesales Head. </div>
                         </div>
                         <div class="col-md-12 d-none">
                             <label for="state" class="form-label">Telesales</label>
                             <select class="form-control select2 inputs" name="telesales_id" id="telesales_id">
                                 @foreach ($telesales ?? [] as $telesale)
                                     <option value="{{ $telesale->id }}">{{ $telesale->name }}</option>
                                 @endforeach
                             </select>
                             <div class="invalid-feedback"> Please choose Telesales. </div>
                         </div>
                         <div class="col-12" id="messageDiv">
                             <label for="message" class="form-label">Message From Customer</label>

                             <textarea name="message" class="form-control inputs" id="message" rows="4"></textarea>
                         </div>
                         <div class="col-12 text-end mt-3 d-none" id="">
                             <div class="d-grid gap-2">
                                 <button type="button" class="btn btn-primary  action-export">Convert to Listing Lead</button>
                             </div>

                         </div>

                         
                         <div class="col-12 text-end mt-3 d-none"id="responseDiv">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h6 class="card-title mb-3 flex-grow-1 text-start">Whatsapp Response Time</h6>
                                    <div class="mb-2">
                                        <lord-icon src="https://cdn.lordicon.com/kbtmbyzy.json" trigger="loop" colors="primary:#405189,secondary:#02a8b5" style="width:90px;height:90px"></lord-icon>
                                    </div>
                                    <h5 class="mb-1"><span id="leadResponseTime"></span></strong></h3>
                                  
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-3 d-none"id="reasonDiv">
                            <div class="card">
                                <div class="card-body text-start">
                                    <h6 class="card-title mb-3 flex-grow-1 text-start">Unsuccessful Reason</h6>
                                   
                                    <h5 class="mb-1"><span id="unsuccessfull_reason"></span></strong></h3>
                                  
                                </div>
                            </div>
                        </div>

                     </div>
                 </div>
             </div>
             <!-- end row -->
         </div>
     </div>
 </div>
