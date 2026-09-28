@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>Deal commission overview</p>
   <br>
    <p>
        <strong>Responsible:</strong> {{ $deal->agent?->first_name ?? "" }} {{ $deal->agent?->last_name ?? "" }}<br>
        <strong>Deal Name:</strong>
        @foreach($deal->leads ?? [] as $lead)
            {{$lead->first_name ?? ""}} {{$lead->last_name ?? ""}},
        @endforeach
        <br>
        <strong>Income:</strong> {{ number_format($deal->net_income) ?? "" }}<br>
        <strong>Start Date:</strong> {{date('d/m/Y',strtotime($deal->created_at)) ?? "" }}<br>
        <strong>Projects:</strong> {{$deal->property?->ref_no}}<br>
        <strong>Unit:</strong> {{$deal->property?->unit_no}}<br>
    </p>
<table class="content" border="1">
    <tr>
        <td>Cashback (Total in AED)</td>
        <td>-</td>
    </tr>
    @php $i = 1; @endphp
    @foreach($data['users'] as $user)
        @if($user['role'] == 'Agent')
            <tr>
                <td>Agent {{$i}} Share(%)</td>
                <td>{{$user['share']}}</td>
            </tr>
            <tr>
                <td>Agent {{$i}} commission (AED)</td>
                <td>{{number_format($user['amount'])}}</td>
            </tr>
            @php $i++; @endphp
        @endif
    @endforeach

    <tr>
        <td>Company Share (%)</td>
        <td>-</td>
    </tr>
    <tr>
        <td>Company Share (AED)</td>
        <td>{{number_format($data['company_amount'])}}</td>
    </tr>
    @foreach($data['users'] as $user)
        @if($user['role'] == 'Line Manager')
            <tr>
                <td>Manager Share (%)</td>
                <td>{{$user['share']}}</td>
            </tr>
            <tr>
                <td>Manager Commission (AED)</td>
                <td>{{number_format($user['amount'])}}</td>
            </tr>
        @endif
    @endforeach
    @foreach($data['users'] as $user)
        @if($user['role'] == 'Team Leader')
            <tr>
                <td>Team Leader Share (%)</td>
                <td>{{$user['share']}}</td>
            </tr>
            <tr>
                <td>Team Leader Commission (AED)</td>
                <td>{{number_format($user['amount'])}}</td>
            </tr>
        @endif
    @endforeach
    @foreach($data['users'] as $user)
        @if($user['role'] == 'Head of Secondary')
            <tr>
                <td>Head of Secondary Share (%)</td>
                <td>{{$user['share']}}</td>
            </tr>
            <tr>
                <td>Head of Secondary Commission (AED)</td>
                <td>{{number_format($user['amount'])}}</td>
            </tr>
        @endif
    @endforeach
    @foreach($data['users'] as $user)
        @if($user['role'] == 'Sales Director')
            <tr>
                <td>Sales Director Share (%)</td>
                <td>{{$user['share']}}</td>
            </tr>
            <tr>
                <td>Sales Director Commission (AED)</td>
                <td>{{number_format($user['amount'])}}</td>
            </tr>
        @endif
    @endforeach

    <tr>
        <td>NET commission</td>
        <td>{{number_format($data['deal']['net_commission'])}}</td>
    </tr>
    <tr>
        <td>Total Commission (AED)</td>
        <td>{{number_format($data['deal']['gross_commission'])}}</td>
    </tr>
    <tr>
        <td>VAT (5%)</td>
        <td>500</td>
    </tr>
{{--    <tr>--}}
{{--        <td>Second Agent</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Agent2 Commission (AED)</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Manager</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Team Leader</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Head of Secondary</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Head of Off-Plan</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <td>Sales Director</td>--}}
{{--        <td>500</td>--}}
{{--    </tr>--}}
    <tr>
        <td>Deal Type</td>
        <td>{{$data['deal']['type']}}</td>
    </tr>
    <tr>
        <td>Comments</td>
        <td>-</td>
    </tr>
</table>
@endsection
