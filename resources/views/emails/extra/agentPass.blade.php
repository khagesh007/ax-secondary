@extends("emails.layouts.layout")
@section("body")
   
    <h4>Dear {{ $data['name'] ?? "" }},</h4>

    Welcome to AX Secondary CRM. We are excited to have you as a new member of our team. Your account has been created and you can now log in using the following credentials:
    <br><br>
    URL: <a href="{{route('login')}}?email={{$data['email']}}">{{route('root')}}</a><br>
    Email: {{ $data['email'] ?? "" }}<br>
    Password: {{$data['password'] ?? ''}}<br><br>
    
    In case you need to reset your password, you can follow the link : <a href="{{route('forget.password')}}">Reset link</a>
    <br><br>
    We are confident that you will find the platform useful and we look forward to your contributions. If you have any questions or concerns, please don't hesitate to reach out.
    <br><br>
    Best regards, 
@endsection
