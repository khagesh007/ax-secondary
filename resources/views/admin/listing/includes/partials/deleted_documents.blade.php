@foreach ($data as $item)
    <div class="row">
        <div class="col-lg-4 mt-2">
            <label for="deleted_document" class="form-label">{{ $item->title }}</label>
            <br>{{ $item->password }}
        </div>
        <div class="col-lg-4 mt-2">
            <input type="file" id="deleted_document" title="Choose" name="deleted_document" class="form-control inputs"
                accept="application/pdf,image/png, image/jpeg" style="display: none;" disabled="">
            <div id="deleted_document_file">
                <a href="{{ $item->path }}" class="text-success" target="_blank">
                    <button type="button" class="btn btn-outline-primary btn-sm waves-effect waves-light"
                        title="View Document"><i class="mdi mdi-file-eye"></i></button></a>

                <a href="javascript:void(0)" type="button" class="changeDocument" data-id="{{ $item->id }}" data-title="{{ $item->title }}"> <button type="button"
                        class="btn btn-outline-success btn-sm waves-effect waves-light" title="Use This" id="">
                        <i class="mdi-eye-arrow-left-outline mdi"></i></button></a>
            </div>
        </div>
        <div class="col-lg-4 mt-2">
            <label for="deleted_document" class="form-label">{{ date('M d, Y, h:m',strtotime($item->deleted_at)) }}</label>
        </div>

    </div>
@endforeach
