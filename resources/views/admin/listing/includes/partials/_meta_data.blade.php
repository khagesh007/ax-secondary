@foreach ($metas as $meta )
@if ($meta->meta_key!='pf_rank_details')
<li>
    <div class="d-flex">
        <div class="flex-shrink-0 avatar-xxs text-muted">
            <i class="ri-question-answer-line"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-0">{{ removeUnderScore($meta->meta_key) }}</h6>
            @if(containsUrl($meta->meta_key))
            <a href="{{ $meta->meta_value }}" target="_blank">Open URL</a>
            @else
            <small class="text-muted">{{ $meta->meta_value }}</small>
            @endif

        </div>
    </div>
</li>
@endif
@endforeach

