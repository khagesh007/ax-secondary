@extends('admin.layouts.master')
@section('title')
    User Management - View
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ URL::asset('assets/images/profile-bg.jpg') }}" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="{{ $user->thumb_url ?? URL::asset('assets/images/users/avatar-1.jpg') }}"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image  shadow"
                                alt="user-profile-image">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body shadow">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1">{{ $user->name ?? "" }}</h5>
                        <p class="text-muted mb-0">{{ collect($user?->roles)->implode("name", " / ") }} </p>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    {{-- <div class="d-flex align-items-center mb-5">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Complete Your Profile</h5>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="javascript:void(0);" class="badge bg-light text-primary fs-12"><i
                                    class="ri-edit-box-line align-bottom me-1"></i> Edit</a>
                        </div>
                    </div> --}}
                    <div class="progress animated-progress custom-progress progress-label">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 30%" aria-valuenow="30"
                            aria-valuemin="0" aria-valuemax="100">
                            <div class="label">30%</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <form class="card-body" id="social-media-form">
                    <input type="hidden" name="id" value="{{ $user->id }}">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">Portfolio</h5>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-dark text-light shadow">
                                <i class="ri-global-fill"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" name="website_link" id="website_link" placeholder="Website"
                            value="{{ $user->website_link ?? "" }}">
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-primary shadow">
                                <i class="ri-facebook-fill"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="facebook_link" name="facebook_link" placeholder="Facebook"
                            value="{{ $user->facebook_link ?? "" }}">
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-success shadow">
                                <i class="ri-instagram-fill"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="instagram_link" name="instagram_link" placeholder="Instagram"
                            value="{{ $user->instagram_link ?? "" }}">
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-danger shadow">
                                <i class="ri-youtube-fill"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="youtube_link" name="youtube_link" placeholder="Youtube"
                            value="{{ $user->youtube_link ?? "" }}">
                    </div>
                    <div class="d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-danger shadow">
                                <i class="ri-linkedin-fill"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="linkedin_link" name="linkedin_link" placeholder="LinkedIn"
                            value="{{ $user->linkedin_link ?? "" }}">
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i>
                                Personal Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i>
                                Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#telegramSettings" role="tab">
                                <i class="far fa-user"></i>
                                Telegram Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#experience" role="tab">
                                <i class="far fa-envelope"></i>
                                Experience
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#privacy" role="tab">
                                <i class="far fa-envelope"></i>
                                Privacy Policy
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form action="javascript:void(0);" id="update-form">
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="first_name" class="form-label">First Name*</label>
                                            <input type="text" class="form-control" name="first_name" id="first_name"
                                                placeholder="Enter your firstname" value="{{ $user->first_name ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="last_name" class="form-label">Last Name*</label>
                                            <input type="text" class="form-control" name="last_name" id="last_name"
                                                placeholder="Enter your lastname" value="{{ $user->last_name ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="mobile" class="form-label">Mobile*</label>
                                            <input type="text" class="form-control" name="mobile" id="mobile"
                                                placeholder="Enter your phone number" value="{{ $user->mobile ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address*</label>
                                            <input type="email" class="form-control" name="email" id="email"
                                                placeholder="Enter your email" value="{{ $user->email ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="bio" class="form-label">Licence/RERA No*</label>
                                            <input type="text" class="form-control" name="license_no" id="license_no"
                                                placeholder="Licence/RERA No" value="{{ $user->license_no ?? "" }}" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="nationality_id" class="form-label">Nationality</label>
                                            <select class="form-control select2" name="nationality_id" id="nationality_id">
                                                <option value="">Select nationality</option>
                                                @foreach($countries ?? [] as $country)
                                                    <option value="{{ $country->id }}" {{ $country->id == ($user->nationality_id ?? "") ? "selected" : "" }}>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="country_id" class="form-label">Country*</label>
                                            <select class="form-control select2" name="country_id" id="country_id" required>
                                                <option value="">Select country</option>
                                                @foreach($countries ?? [] as $country)
                                                    <option value="{{ $country->id }}" {{ $country->id == ($user->country_id ?? "") ? "selected" : "" }}>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="state_id" class="form-label">State*</label>
                                            <select class="form-control select2" name="state_id" id="state_id">
                                                <option value="">Select state</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3 pb-2">
                                            <label for="bio"
                                                class="form-label">Bio</label>
                                            <textarea class="form-control" name="bio" id="bio" placeholder="Enter your bio"
                                                rows="3">{{ $user->bio ?? "" }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form action="javascript:void(0);" id="password-form" class="needs-validation" novalidate>
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="current_password" class="form-label">Old
                                                Password*</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password"
                                                placeholder="Enter current password">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="password" class="form-label">New
                                                Password*</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                placeholder="Enter new password">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="password_confirmation" class="form-label">Confirm
                                                Password*</label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                                                placeholder="Confirm password">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Change Password</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="telegramSettings" role="tabpanel">
                            @if(!empty($user->telegram_username))
                            <p>Your telegram user name is {{$user->telegram_username}}</p>
                            <p>Telegram Notification : @if($user->telegram_chatid) <span class="badge badge-outline-success">Active</span> @else <span class="badge badge-outline-danger">Disabled</span>  <a href="javascript:void(0)" id="generateTelegramOtp">Enable Now</a> @endif</p>
                            <p id="telegramSetupInstruction" class="d-none">

                                Send this <b id="telegramOtp"></b> otp to our telegram bot <b><a href="https://t.me/real_estate01_bot" target="_blank">real_estate01_bot</a></b> to verify your account. <br>
                           <br> After sending the message to our bot. click this button. <br>
                                <button class="btn btn-primary" id="verifyTelegramOtp">Verify</button>
                            </p>
                            @else
                            <div class="alert alert-borderless shadow alert-warning" role="alert">
<strong>Attention</strong> Please Set Your telegram user name first!
</div>
                           
                            @endif
                            
                        </div>
                        <div class="tab-pane" id="experience" role="tabpanel">
                            <form>
                                <div id="newlink">
                                    <div id="1">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="jobTitle" class="form-label">Job
                                                        Title</label>
                                                    <input type="text" class="form-control" id="jobTitle"
                                                        placeholder="Job title" value="Lead Designer / Developer">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="companyName" class="form-label">Company
                                                        Name</label>
                                                    <input type="text" class="form-control" id="companyName"
                                                        placeholder="Company name" value="Themesbrand">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="experienceYear" class="form-label">Experience
                                                        Years</label>
                                                    <div class="row">
                                                        <div class="col-lg-5">
                                                            <select class="form-control" data-choices
                                                                data-choices-search-false name="experienceYear"
                                                                id="experienceYear">
                                                                <option value="">Select years</option>
                                                                <option value="Choice 1">2001</option>
                                                                <option value="Choice 2">2002</option>
                                                                <option value="Choice 3">2003</option>
                                                                <option value="Choice 4">2004</option>
                                                                <option value="Choice 5">2005</option>
                                                                <option value="Choice 6">2006</option>
                                                                <option value="Choice 7">2007</option>
                                                                <option value="Choice 8">2008</option>
                                                                <option value="Choice 9">2009</option>
                                                                <option value="Choice 10">2010</option>
                                                                <option value="Choice 11">2011</option>
                                                                <option value="Choice 12">2012</option>
                                                                <option value="Choice 13">2013</option>
                                                                <option value="Choice 14">2014</option>
                                                                <option value="Choice 15">2015</option>
                                                                <option value="Choice 16">2016</option>
                                                                <option value="Choice 17" selected>2017
                                                                </option>
                                                                <option value="Choice 18">2018</option>
                                                                <option value="Choice 19">2019</option>
                                                                <option value="Choice 20">2020</option>
                                                                <option value="Choice 21">2021</option>
                                                                <option value="Choice 22">2022</option>
                                                            </select>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-auto align-self-center">
                                                            to
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-5">
                                                            <select class="form-control" data-choices
                                                                data-choices-search-false name="choices-single-default2">
                                                                <option value="">Select years</option>
                                                                <option value="Choice 1">2001</option>
                                                                <option value="Choice 2">2002</option>
                                                                <option value="Choice 3">2003</option>
                                                                <option value="Choice 4">2004</option>
                                                                <option value="Choice 5">2005</option>
                                                                <option value="Choice 6">2006</option>
                                                                <option value="Choice 7">2007</option>
                                                                <option value="Choice 8">2008</option>
                                                                <option value="Choice 9">2009</option>
                                                                <option value="Choice 10">2010</option>
                                                                <option value="Choice 11">2011</option>
                                                                <option value="Choice 12">2012</option>
                                                                <option value="Choice 13">2013</option>
                                                                <option value="Choice 14">2014</option>
                                                                <option value="Choice 15">2015</option>
                                                                <option value="Choice 16">2016</option>
                                                                <option value="Choice 17">2017</option>
                                                                <option value="Choice 18">2018</option>
                                                                <option value="Choice 19">2019</option>
                                                                <option value="Choice 20" selected>2020
                                                                </option>
                                                                <option value="Choice 21">2021</option>
                                                                <option value="Choice 22">2022</option>
                                                            </select>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <label for="jobDescription" class="form-label">Job
                                                        Description</label>
                                                    <textarea class="form-control" id="jobDescription" rows="3"
                                                        placeholder="Enter description">You always want to make sure that your fonts work well together and try to limit the number of fonts you use to three or less. Experiment and play around with the fonts that you already have in the software you're working with reputable font websites. </textarea>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="hstack gap-2 justify-content-end">
                                                <a class="btn btn-success" href="javascript:deleteEl(1)">Delete</a>
                                            </div>
                                        </div>
                                        <!--end row-->
                                    </div>
                                </div>
                                <div id="newForm" style="display: none;">

                                </div>
                                <div class="col-lg-12">
                                    <div class="hstack gap-2">
                                        <button type="submit" class="btn btn-success">Update</button>
                                        <a href="javascript:new_link()" class="btn btn-primary">Add
                                            New</a>
                                    </div>
                                </div>
                                <!--end col-->
                            </form>
                        </div>
                        <div class="tab-pane" id="privacy" role="tabpanel">
                            <div class="mb-4 pb-2">
                                <h5 class="card-title text-decoration-underline mb-3">Security:</h5>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Two-factor Authentication</h6>
                                        <p class="text-muted">Two-factor authentication is an enhanced
                                            security meansur. Once enabled, you'll be required to give
                                            two types of identification when you log into Google
                                            Authentication and SMS are Supported.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Enable Two-facor
                                            Authentication</a>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Secondary Verification</h6>
                                        <p class="text-muted">The first factor is a password and the
                                            second commonly includes a text with a code sent to your
                                            smartphone, or biometrics using your fingerprint, face, or
                                            retina.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Set
                                            up secondary method</a>
                                    </div>
                                </div>
                                <div class="d-flex flex-column flex-sm-row mb-4 mb-sm-0 mt-2">
                                    <div class="flex-grow-1">
                                        <h6 class="fs-14 mb-1">Backup Codes</h6>
                                        <p class="text-muted mb-sm-0">A backup code is automatically
                                            generated for you when you turn on two-factor authentication
                                            through your iOS or Android Twitter app. You can also
                                            generate a backup code on twitter.com.</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-sm-3">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-primary">Generate backup
                                            codes</a>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <h5 class="card-title text-decoration-underline mb-3">Application
                                    Notifications:</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex">
                                        <div class="flex-grow-1">
                                            <label for="directMessage" class="form-check-label fs-14">Direct
                                                messages</label>
                                            <p class="text-muted">Messages from people you follow</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="directMessage" checked />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="desktopNotification">
                                                Show desktop notifications
                                            </label>
                                            <p class="text-muted">Choose the option you want as your
                                                default setting. Block a site: Next to "Not allowed to
                                                send notifications," click Add.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="desktopNotification" checked />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="emailNotification">
                                                Show email notifications
                                            </label>
                                            <p class="text-muted"> Under Settings, choose Notifications.
                                                Under Select an account, choose the account to enable
                                                notifications for. </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="emailNotification" />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="chatNotification">
                                                Show chat notifications
                                            </label>
                                            <p class="text-muted">To prevent duplicate mobile
                                                notifications from the Gmail and Chat apps, in settings,
                                                turn off Chat notifications.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="chatNotification" />
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mt-2">
                                        <div class="flex-grow-1">
                                            <label class="form-check-label fs-14" for="purchaesNotification">
                                                Show purchase notifications
                                            </label>
                                            <p class="text-muted">Get real-time purchase alerts to
                                                protect yourself from fraudulent charges.</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    id="purchaesNotification" />
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h5 class="card-title text-decoration-underline mb-3">Delete This
                                    Account:</h5>
                                <p class="text-muted">Go to the Data & Privacy section of your profile
                                    Account. Scroll to "Your data & privacy options." Delete your
                                    Profile Account. Follow the instructions to delete your account :
                                </p>
                                <div>
                                    <input type="password" class="form-control" id="passwordInput"
                                        placeholder="Enter your password" value="make@321654987" style="max-width: 265px;">
                                </div>
                                <div class="hstack gap-2 mt-3">
                                    <a href="javascript:void(0);" class="btn btn-soft-danger">Close &
                                        Delete This Account</a>
                                    <a href="javascript:void(0);" class="btn btn-light">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('assets/js/pages/profile-setting.init.js') }}"></script>
        {{-- <script src=" "></script> --}}

    <script>
        let mobileInput = initPhoneValidator();

        $("#generateTelegramOtp").click(function() {
            console.log('testsr');
            $("#telegramSetupInstruction").removeClass("d-none");
            $.ajax({
                url: "{{ route('admin.user.users.telegram.send.otp') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{$user->id}}",
                },
                success: function(response) {
                   if(response.status == 1){
                       $("#telegramOtp").html(response.otp);
                   }else{

                   }
                }
            });
        })
        $("#verifyTelegramOtp").click(function() {
           $.ajax({
                url: "{{ route('admin.user.users.telegram.verify.otp') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: "{{$user->id}}",
                },
                success: function(response) {
                   if(response.status == 1){
                      alert(response.msg);
                   }
                }
            });
        });
        $(document).on('submit', '#update-form', function (event) {
            let buttons = $(this).find("button");
            $(buttons).prop('disabled', true);
            event.preventDefault();
            $("#mobile").val(mobileInput.getNumber())
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('admin.user.users.update-profile')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $(buttons).prop('disabled', false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });

        $(document).on('submit', '#social-media-form', function (event) {
            let buttons = $(this).find("button");
            $(buttons).prop('disabled', true);
            event.preventDefault();
            $("#mobile").val(mobileInput.getNumber())
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('admin.user.users.update-social-media')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $(buttons).prop('disabled', false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
         $("#sidebarAuth").addClass('collapse show');
        $("#sidbarUsers").addClass('active');
    </script>
@endsection
