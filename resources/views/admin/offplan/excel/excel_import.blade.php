@extends('admin.layouts.master')
@section('title')
    Developers MetaData
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }

        h2 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h5 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table tr td {
            padding: 0px !important
        }


      </style>
@endsection
@section('content')
    @if (session()->has('message'))
        <div class="alert alert-success" id="alertMessage">
            {{ session()->get('message') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Offplan </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Import</a></li>
                            <li class="breadcrumb-item active">Offplan Properties</li>
                        </ol>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <form action="{{ route('offplan.import.handle') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        {{-- <div class=" text-center">
                            <h3 class="">
                                Import Properties
                            </h3>
                            <hr>
                        </div> --}}
                        <div class="row">
                            {{-- <div class="col-md-3" id="developer_col">
                                <select class="form-control select2 inputs" name="developer_id" id="developer_id">
                                    <option selected disabled value="">Select Developer</option>
                                    @foreach ($developers ?? [] as $devloper)
                                        <option value="{{ $devloper->id }}"> {{ $devloper->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"> Please choose Developer. </div>
                            </div> --}}

                            <div class="col-md-6" id="project_col">
                                <label for="">File</label>
                                <input type="file" name="file" id="file" class="form-control">
                            </div>

                            <div class="col-md-6 text-end">
                                <button type="submit" class="btn btn-primary">Upload</button>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="overflow-x: scroll">
                        <div class="col-md-12">
                            <div id="table-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    @include('admin.partials.datatable')


    <script>
        $(document).ready(function() {
          $('#file_').on('change', function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
              var data = new Uint8Array(e.target.result);
              var workbook = XLSX.read(data, { type: 'array' });

              var sheetName = workbook.SheetNames[0];
              var sheet = workbook.Sheets[sheetName];

              var html = XLSX.utils.sheet_to_html(sheet);
              $('#table-container').html(html);
            };

            reader.readAsArrayBuffer(file);
          });
        });
      </script>
@endsection
