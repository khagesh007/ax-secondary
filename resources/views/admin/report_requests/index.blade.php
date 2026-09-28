@extends('admin.layouts.master')
@section('title')
    Reports Request
@endsection
@section('style')
    <!--datatable css-->
   <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>


    <!--datatable responsive css-->

@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">All Reports</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Request</a></li>
                            <li class="breadcrumb-item active">Report</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title
                        mb-0 ">Activity Report</h4>
                    </div>

                </div>
                <div class="card-body text-center">
                   <button class="btn btn-primary send-report" data-request="activity">Send Request</button>
                </div>
            </div>

        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title
                        mb-0 ">Newly Published</h4>
                    </div>

                </div>
                <div class="card-body text-center">
                   <button class="btn btn-primary send-report" data-request="newlypublished">Send Request</button>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title
                        mb-0 ">Calander Report</h4>
                    </div>

                </div>
                <div class="card-body text-center">
                   <button class="btn btn-primary send-report" data-request="calendarreport">Send Request</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title
                        mb-0 ">Leads Report</h4>
                    </div>

                </div>
                <div class="card-body text-center">
                   <button class="btn btn-primary send-report" data-request="leadreport">Send Request</button>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title
                        mb-0 ">Incomplete Leads Report</h4>
                    </div>

                </div>
                <div class="card-body text-center">
                   <button class="btn btn-primary send-report" data-request="incompleteleadreport">Send Request</button>
                </div>
            </div>
        </div>

    </div>






@endsection
@section('script')

<script>
    $(document).ready(function() {

        $('.send-report').click(function(){

            var report = $(this).data('request');
            $(this).attr('disabled', true);
            $.ajax({
            type: "POST",
            url: "{{ route('report.requests.send') }}",
            data: {
                report: report,
            },
            success: function(response) {
                $('.send-report').attr('disabled', false);
                console.log(response);
            },
            error: function(xhr, status, error) {
                $(this).attr('disabled', false);
                console.error(error);
            }
        });
        })


    });
</script>
@endsection
