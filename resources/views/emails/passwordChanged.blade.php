@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $data["name"] ?? "" }},</h4><br>
    <p>Thank you for registering with {{getApp('name')}}</p>

    <p>We’d like to confirm that your account was created successfully. To access the portal, please use the below login credentials.</p>

    <p><br>
    URL: {{ route('login') }}<br>
    Email: {{ $data["email"] ?? "" }}
    <br>
    @if(!empty($data["password"]))
    Password: {{ $data["password"] ?? "" }}
    @endif
                            
    <br>
    </p>

   
@endsection
