<html>

<head>
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
<div class="row">
    <div class="col-12">
        <table class="table table-borderless">
            <tbody>
            <tr>
                <td>
                    <div class="d-flex justify-content-start align-items-center">
                        <img src="{{ asset("assets/images/logo/logo.png") }}" alt="QA Logo"
                             width="150px" style="padding:10px;">
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="border border-dark p-2">
                            Receipt #: PK-R001
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center">
                        <div>
                            Date: {{ $deal->created_at }}
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-center align-items-center">
            <h4>Annual Rent / Security Deposit Acknowledgment Receipt</h4>
        </div>
        <table class="table table-bordered mt-5">
            <tr>
                <td style="width: 50%"><strong>Property Ref #</strong></td>
                <td>{{ $deal->property->ref_no ?? "" }}</td>
            </tr>
            <tr>
                <td><strong>{{ $deal->deal_type_id == 1 ? "Tenant" : "Buyer" }}</strong></td>
                <td>{{ $deal->lead->first_name ?? "" }} {{ $deal->lead->last_name ?? "" }}</td>
            </tr>
            <tr>
                <td><strong>{{ $deal->deal_type_id == 1 ? "Landlord" : "Seller" }}</strong></td>
                <td>{{ $deal->contact->first_name ?? "" }} {{ $deal->contact->last_name ?? "" }}</td>
            </tr>
        </table>
        <table class="table table-bordered">
            <tbody>
            <tr>
                <td style="width: 20%">
                    Details
                </td>
                <td>
                    <div class="border-bottom border-dark">
                        Agency fee on AED {{ $deal->deal_price }} for the lease of unit# {{ $deal->property->unit_no ?? "" }}, {{ $deal->property->ref_no ?? "" }}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    Security Deposit
                </td>
                <td></td>
            </tr>

            </tbody>
        </table>

        <div class="mt-5">
            <h5 class="text-dark">{{ getApp("name") }}</h5>
            <div>Primary Agent: {{ $deal->agent->name ?? "" }}</div>
            <div>Prepared by: {{ $deal->createdBy->name ?? "" }}</div>
        </div>
    </div>
</div>
</body>
</html>
