@foreach ($groups as $group)
    <div class="row mb-3 ">
        <div class="col-6 col-md-5">
            <input type="text" name="group_name[]" id="group_name" class="form-control group_name"
                placeholder="Group Name" value="{{ $group->name }}">
        </div>
        <div class="col-6 col-md-5">
            <select name="user_id[]" id="" class="select2_ form-control languageSelect2">
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $group->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->full_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- <div class="col-2 col-md-2">
            <span class="cursur-pointer addChildGroup" data-parent_group_id="{{$group->id}}">Child Group</span>
        </div> --}}
        <div class="children">
            @foreach ($group->children as $child)
            <div class="row mb-3 mt-3 ">
                <div class="col-2">

                </div>
                <div class="col-2 col-md-2">
                    {{-- <span class="cursur-pointer removeRowAdded">Remove</span> --}}
                </div>
                <div class="col-2 col-md-2">
                    <input type="hidden" name="parent_group_id[]" value="{{$child->group_id}}" id="">
                    <input type="text" name="child_group_name[]" id="child_group_name"
                        class="form-control child_group_name" placeholder="Group Name" value="{{$child->name}}">
                </div>
                <div class="col-6 col-md-4">
                    <select name="child_user_id[]" id="" class="select2_ form-control languageSelect2">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{$child->user_id==$user->id?'selected':''}}> {{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>



            </div>
            @endforeach
        </div>
    </div>
@endforeach
