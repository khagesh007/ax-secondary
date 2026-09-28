{{-- @dd($data) --}}

<div class="table-responsive">
    <table id="buttons-datatables" class="table align-middle">
        <thead class="table-light">
            <tr>
                <th style="background-color: #f0e4dd!important;z-index:400"><small>Agent</small></th>
                @foreach ($data as $key => $value)
                    @foreach ($value as $key2 => $value2)
                        @if ($key2 == 'image')
                            @continue
                        @endif
                        <th style="background-color: #f0e4dd!important;z-index:400"> <small>{{ $key2 }}</small>
                        </th>
                    @endforeach
                @break
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $value)
            <tr>
                <td style="background-color: rgb(242, 240, 240)!important">
                    <div class="d-flex align-items-center" title="{{ $key }}">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{ $value['image'] ?? 'https://ui-avatars.com/api/' . $key }}" alt=""
                                class="avatar-xs rounded-circle">
                        </div>

                        <div class="flex-grow-1">{{ $key }}<p title="Created at" class="mb-0">
                            </p>
                        </div>
                    </div>
                </td>
                {{-- <td  style="background-color: rgb(242, 240, 240)!important">
                        <a href="#" class="fw-semibold">{{ $key }}</a></td> --}}

                @foreach ($value as $key2 => $value2)
                    @if ($key2 == 'image')
                        @continue
                    @endif
                    <td>{{ $value2 == 0 ? '-' : $value2 }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr>
            <th style="background-color: #f0e4dd!important;z-index:400">Agent</th>
            @foreach ($value as $key2 => $value2)
                @if ($key2 == 'image')
                    @continue
                @endif
                <th class="showCount" style="background-color: #f0e4dd!important">0</th>
            @endforeach
        </tr>
    </tfoot>

</table>
</div>
