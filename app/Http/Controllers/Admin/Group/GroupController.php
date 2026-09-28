<?php

namespace App\Http\Controllers\Admin\Group;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    use FileTrait;
    public function index()
    {

        // $users = User::whereHas('roles', function ($query) {
        //     $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager', 'Head of Secondary']);
        // })->where('status', 1)->get();

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager']);
        })
            ->whereHas('children')
            ->where('status', 1)
            ->get();

        $ben_thomos = User::where('id', 65)->get();


        $users = $users->merge($ben_thomos);

        $groups = Group::whereNull('parent_id')->get();

        $data_filtered = Group::with(['parent', 'user'])->whereNull('parent_id')->get();

        foreach ($data_filtered as $row) {
            $user_id = $row->user->id ?? null;
            $childCount = childcount($user_id) ?? 0;
            $row->child_count = $childCount;
        }

        return view('admin.groups.index')
            ->with('users', $users)
            ->with('groups', $groups)
            ->with('data_filtered', $data_filtered);
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);

        $groups = Group::whereNull('parent_id')->get();

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Team Leader', 'Line Manager', 'Sales Director']);
        })->where(function ($query) {
            $query->whereNull('group_id')->orWhere('group_id', request()->id);
        })->where('status', 1)->get();

        $ben_thomos = User::where('id', 65)->get();
        $users = $users->merge($ben_thomos);

        // dd($group->icon);
        return view('admin.groups.edit')
            ->with('users', $users)
            ->with('groups', $groups)
            ->with('group', $group);
    }

    public function getGroups()
    {
        $groups = Group::where('group_id', null)->get();
        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager', 'Head of Secondary']);
        })->where('status', 1)->get();
        return view('admin.groups.partials._groups')->with('groups', $groups)->with('users', $users);
    }

    public function getGroupUsers(Request $request)
    {

        $user = User::find($request->user_id);

        $notAdmin = $user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($user->id);
        }
        $query = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader", "Agent"]));
        $agents = $query->whereIn("parent_id", $childrenIds);

        $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();
        return view('admin.groups.partials._users')->with('users', $agents);
    }

    // public function userGroupsSave(Request $request)
    // {
    //     // dd($request->all());
    //     foreach ($request->user_id as $user_id) {

    //         $group_user = new GroupUser();
    //         $group_user->group_id = $request->group_id;
    //         $group_user->user_id = $user_id;
    //         $group_user->save();
    //     }
    //     return response()->json(['status' => 'success', 'message' => 'Group Saved Successfully']);

    // }

    public function datatable(Request $request)
    {

        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name',
            3 => 'thumbnail',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = Group::count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $data_filtered = Group::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%')->with(['parent', 'user']);
            });

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = Group::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with(['parent', 'user']);
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();

            foreach ($data as $row) {
                $user_id = $row->user->id; // Assuming this is how you get the user_id
                $childCount = childcount($user_id); // Call your childcount function here
                $row->child_count = $childCount; // Add child count to the row
            }

        }

        // in datafilered add number of children of each group user_id

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        );

        echo json_encode($json_data);
    }

    public function store(Request $request)
    {

        $request->validate([
            'group_name' => 'required',
            'user_id' => 'required',
            'group_color' => 'required',
        ]);

        $findUser = User::find($request->user_id);
        if ($findUser->group_id) {
            $find_group = Group::find($findUser->group_id);
            $find_group->name = $request->group_name;
            $find_group->parent_id = null;
            $find_group->save();
            return redirect()->back();
        }

        if ($request->parent_id) {
            $childCount = Group::where('parent_id', $request->parent_id)->count();
            $group = new Group();
            $group->name = $request->group_name . '-' . $this->getAlphabetByNumber($childCount + 1);
            $group->parent_id = $request->parent_id;
            $group->color = $request->group_color;

            $group->save();

            User::where("zone_id", $group->id)->update([
                "zone_id" => null,
            ]);

            $agent = User::find($request->user_id);
            $agent->group_id = $group->id;
            $agent->zone_id = $group->id;
            $agent->save();

            User::where("parent_id", $agent->id)->whereNull("group_id")->update([
                "zone_id" => $group->id,
            ]);
        } else {
            $group = new Group();
            $group->name = $request->group_name;
            $group->color = $request->group_color;
            $group->save();

            User::where("zone_id", $group->id)->update([
                "zone_id" => null,
            ]);

            $user = User::find($request->user_id);
            $user->group_id = $group->id;
            $user->zone_id = $group->id;
            $user->save();

            User::where("parent_id", $user->id)->whereNull("group_id")->update([
                "zone_id" => $group->id,
            ]);

            $notAdmin = $user->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIdsByID($user->id);
            }

            $query = User::whereHas("roles", fn($q) => $q->whereNotIn("name", ["Agent"]));
            $agents = $query->whereIn("parent_id", $childrenIds);
            $agents = $query->where('status', 1)->orderBy('first_name', 'asc')->get();

            foreach ($agents as $key => $agent) {
                $agent = User::find($agent->id);
                if ($agent->group_id) {
                    $find_group = Group::find($agent->group_id);
                    $find_group->parent_id = null;
                    $find_group->save();
                    continue;
                    // $agent->group_id = null;
                    // $agent->save();
                }
                $group_user = new Group();
                $group_user->name = $request->group_name . '-' . $this->getAlphabetByNumber($key + 1);
                $group_user->parent_id = $group->id;
                $group_user->color = $request->group_color;
                $group_user->save();

                User::where("zone_id", $group_user->id)->update([
                    "zone_id" => null,
                ]);

                $agent->group_id = $group_user->id;
                $agent->zone_id = $group_user->id;
                $agent->save();

                User::where("parent_id", $agent->id)->whereNull("group_id")->update([
                    "zone_id" => $group_user->id,
                ]);

                // $group_users = User::where('parent_id', $agent->id)->where('status', 1)->get();
                // foreach ($group_users as $group_user) {
                //     $group_user->group_id = $group_user->id;
                //     $group_user->save();
                // }

            }
        }
        return redirect()->back();

        $result = array('status' => '1', 'message' => 'Group Saved');
        return response()->json($result, 200);
    }

    public function update(Request $request, $id)
    {

//         dd($request->all());
        $request->validate([
            'group_name' => 'required',
            'user_id' => 'required',
            'group_color' => 'nullable',
            'group_order' => 'nullable',
            'group_badge' => 'nullable',

        ]);

        $group = Group::find($id);
        $group->name = $request->group_name;
        $group->color = $request->group_color;

        $group->order = $request->group_order;
        $group->badge = $request->group_badge;
        $group->icon = $request->group_icon;

        if ($request->has('group_image')) {
            $image = $this->uploadImage($request->file("group_image"));
            $group->image = $image;
        }

        if ($request->has('group_thumbnail')) {
            $image = $this->uploadImage($request->file("group_thumbnail"));
            $group->thumbnail = $image;
        }

        if ($request->parent_id) {
            $group->parent_id = $request->parent_id;
        } else {
            $group->parent_id = null;
        }

        $group->save();
        User::where("zone_id", $group->id)->update([
            "zone_id" => null,
        ]);
        $users = User::where('group_id', $group->id)->get();
        foreach ($users as $user) {
            $user->group_id = null;
            $user->zone_id = null;
            $user->save();
        }
        $user = User::find($request->user_id);
        $user->group_id = $group->id;
        $user->zone_id = $group->id;
        $user->save();

        User::where("parent_id", $user->id)->whereNull("group_id")->update([
            "zone_id" => $group->id,
        ]);

        return redirect()->back();
    }

    public function getAlphabetByNumber($number)
    {
        if ($number < 1 || $number > 26) {
            return null; // Handle out-of-range numbers as needed.
        }

        $alphabetLetter = chr(64 + $number);

        return $alphabetLetter;
    }

    public function makeTree(Request $request)
    {

        $users = User::where('status', 1)->whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager', 'Head of Secondary', 'Agent', 'CEO']);
        })->get();

        return view('admin.groups.tree')
            ->with('users', $users);
    }

    public function deleteGroup(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $group = Group::find(request()->id);
        $agent = User::where('group_id', $group->id)->first();
        $agent->group_id = null;
        $agent->zone_id = null;
        $agent->save();

        User::where("parent_id", $agent->id)->whereNull("group_id")->update([
            "zone_id" => $group->id,
        ]);
        $group->delete();

        return response()->json(['status' => '1', 'message' => 'Selected Group Deleted']);
    }

    public function deleteGroupAll(Request $request)
    {
        // delete all groups
        $groups = Group::all();
        foreach ($groups as $group) {
            $agent = User::where('group_id', $group->id)->first();
            $agent->group_id = null;
            $agent->zone_id = null;
            $agent->save();

            User::where("parent_id", $agent->id)->whereNull("group_id")->update([
                "zone_id" => $group->id,
            ]);

            $group->delete();
        }
        return response()->json(['status' => '1', 'message' => 'Selected Group Deleted']);

    }

}
