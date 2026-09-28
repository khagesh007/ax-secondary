@foreach ($users as $user)
   <option value="{{ $user->id }}">{{ $user->full_name }}</option>
@endforeach
