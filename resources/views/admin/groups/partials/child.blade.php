<ul>
    @foreach ($users as $user)
        <li>{{ $user->name }}</li>
        @if ($user->children->isNotEmpty())
            @include('admin.groups.partials.child', ['users' => $user->children])
        @endif
    @endforeach
</ul>
