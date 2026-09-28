@foreach ($contacts as $user)
   <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} |  {{ $user->mobile }}</option>
@endforeach
