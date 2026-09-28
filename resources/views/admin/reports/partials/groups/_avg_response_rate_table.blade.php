<div class="col-lg-12">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Whatsapp Response Time</h5>
        </div>
        <div class="card-body">
            <table id="whatsAppResponseTable" class="table table-bordered dt-responsive nowrap table-striped align-middle"
                style="width:100%">
                <thead>
                    <tr>

                        <th>Name</th>
                        <th>Time</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($datasets as $key => $time)
                        <tr>
                            <td><span class="">{{ $key }}</span></td>
                            <td>{!! getSecondsTohumanReadableReportsTimeBase($time, false) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
