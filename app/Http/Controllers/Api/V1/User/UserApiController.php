<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\UserResource;
use App\Jobs\ProcessUser;
use App\Models\CoordinateRecording;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Validator;
use Illuminate\Support\Facades\URL;

class UserApiController extends BaseController
{
    use FileTrait;

    //
    public function __construct()
    {

    }

    /**
     *
     *
     * User List
     *
     * Get a list of Users.
     * @group User
     * @authenticated
     * @queryParam get_location boolean  Get location of the user. Example: true
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "User data found",
    "data": [
    {
    "id": 1,
    "name": "Saim Aziz",
    "full_name": "Saim Aziz",
    "first_name": "Saim",
    "last_name": "Aziz",
    "position": null,
    "email": "s.mirza@axcapital.ae",
    "secondary_email": "s.mirza@axcapital.ae",
    "mobile": null,
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 1
    },
    {
    "id": 2,
    "name": "Otabek Navruzov",
    "full_name": "Otabek Navruzov",
    "first_name": "Otabek",
    "last_name": "Navruzov",
    "position": "CTO",
    "email": "cto@axcapital.ae",
    "secondary_email": "cto@axcapital.ae",
    "mobile": "+971521569330",
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 1
    },
    {
    "id": 3,
    "name": "Head Navruzov",
    "full_name": "Head Navruzov",
    "first_name": "Head",
    "last_name": "Navruzov",
    "position": "Test Account",
    "email": "head@axcapital.ae",
    "secondary_email": "head@axcapital.ae",
    "mobile": "+971234352345235",
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 0
    },
    {
    "id": 5,
    "name": "Muhammad Mohsan",
    "full_name": "Muhammad Mohsan",
    "first_name": "Muhammad",
    "last_name": "Mohsan",
    "position": "Property Consultant",
    "email": "m.mohsan@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521785458",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1215739441",
    "status": 1
    },
    {
    "id": 6,
    "name": "Syed Imran",
    "full_name": "Syed Imran",
    "first_name": "Syed",
    "last_name": "Imran",
    "position": "Property Consultant",
    "email": "s.imran@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521952454",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "892101799",
    "status": 1
    },
    {
    "id": 7,
    "name": "System Agent",
    "full_name": "System Agent",
    "first_name": "System",
    "last_name": "Agent",
    "position": "Dubai Hills Specialist",
    "email": "default@axcapital.ae",
    "secondary_email": "default@axcapital.ae",
    "mobile": "+971521943182",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 0
    },
    {
    "id": 8,
    "name": "Maksim Tuguchev",
    "full_name": "Maksim Tuguchev",
    "first_name": "Maksim",
    "last_name": "Tuguchev",
    "position": "Sales Manager",
    "email": "m.tuguchev@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971586448869",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_167928699920230320043639.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/167928699920230320043639.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1285313110",
    "status": 1
    },
    {
    "id": 9,
    "name": "Shehryar Malik",
    "full_name": "Shehryar Malik",
    "first_name": "Shehryar",
    "last_name": "Malik",
    "position": "Sales Manager",
    "email": "shehryar.malik@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521937175",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "697184586",
    "status": 1
    },
    {
    "id": 10,
    "name": "Khalid Shahzad",
    "full_name": "Khalid Shahzad",
    "first_name": "Khalid",
    "last_name": "Shahzad",
    "position": "Property Expert",
    "email": "k.shahzad@axcapital.ae",
    "secondary_email": "k.shahzad@axcapital.ae",
    "mobile": "+971521432744",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1250121066",
    "status": 1
    },
    {
    "id": 11,
    "name": "Igor Kavka",
    "full_name": "Igor Kavka",
    "first_name": "Igor",
    "last_name": "Kavka",
    "position": "Property Consultant",
    "email": "igor.kavka@axcapital.ae",
    "secondary_email": "igor.kavka@axcapital.ae",
    "mobile": "+971523971052",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "825902846",
    "status": 1
    },
    {
    "id": 12,
    "name": "Emine Sidzhimova",
    "full_name": "Emine Sidzhimova",
    "first_name": "Emine",
    "last_name": "Sidzhimova",
    "position": "Property Consultant",
    "email": "e.sidzhimova@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521864680",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1385931531",
    "status": 1
    },
    {
    "id": 13,
    "name": "Mukul Wadhwa",
    "full_name": "Mukul Wadhwa",
    "first_name": "Mukul",
    "last_name": "Wadhwa",
    "position": "Senior Property Consultant",
    "email": "m.wadhwa@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521942162",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1339154430",
    "status": 1
    },
    {
    "id": 14,
    "name": "Shermeen Baqai",
    "full_name": "Shermeen Baqai",
    "first_name": "Shermeen",
    "last_name": "Baqai",
    "position": "Property Consultant",
    "email": "s.baqai@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521804510",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1387926540",
    "status": 1
    },
    {
    "id": 15,
    "name": "Alex Lourenco",
    "full_name": "Alex Lourenco",
    "first_name": "Alex",
    "last_name": "Lourenco",
    "position": "Property Consultant",
    "email": "a.lourenco@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521349185",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1373001507",
    "status": 1
    },
    {
    "id": 16,
    "name": "Gabriela Brunariova",
    "full_name": "Gabriela Brunariova",
    "first_name": "Gabriela",
    "last_name": "Brunariova",
    "position": "Property Consultant",
    "email": "g.brunariova@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971523747265",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_167928706120230320043741.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/167928706120230320043741.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1359705122",
    "status": 1
    },
    {
    "id": 17,
    "name": "Karim Ahmed",
    "full_name": "Karim Ahmed",
    "first_name": "Karim",
    "last_name": "Ahmed",
    "position": "Team Leader",
    "email": "k.ahmed@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521816401",
    "thumbnail": "https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/user/full/2226/lGHiX7uzblkkxnat.png",
    "image": "https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/user/full/2226/lGHiX7uzblkkxnat.png",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "892101799",
    "status": 1
    },
    {
    "id": 18,
    "name": "Lizaveta Drozd",
    "full_name": "Lizaveta Drozd",
    "first_name": "Lizaveta",
    "last_name": "Drozd",
    "position": "Property Consultant",
    "email": "l.drozd@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971557649869",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5270107631",
    "status": 1
    }
    ]
    }

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No User found",
    "data": []
    }
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $users = User::all();
        if ($request->get_location) {
            foreach ($users as $user) {
                $user->locations = $user->locations->map(function ($location) {
                    $locationModel = $location['parentable_type']::find($location['parentable_id']);

                    return [
                        'location_id' => $locationModel->id,
                        'location_name' => $locationModel->name,
                        'community' => $locationModel?->community,
                        'full_path' => $locationModel?->path_name,
                    ];
                });
            }
        }

        return $this->sendResponse(true, $users->count() . ' User data found', UserResource::collection($users), 200);
    }

    public function paginateIndex(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Number of items per page, default is 10
        $usersQuery = User::query();

        if ($request->get_location) {
            $usersQuery->with(['locations' => function ($query) {
                $query->select('parentable_type', 'parentable_id'); // Optionally select only required columns
            }]);
        }
        $list_per_page =  $request->per_page?? '10';
        $user_count = $usersQuery->count();
        $users = $usersQuery->paginate($list_per_page);


        if ($request->get_location) {
            $users->getCollection()->transform(function ($user) {
                $user->locations->transform(function ($location) {
                    $locationModel = $location['parentable_type']::find($location['parentable_id']);

                    return [
                        'location_id' => $locationModel->id,
                        'location_name' => $locationModel->name,
                        'community' => $locationModel?->community,
                        'full_path' => $locationModel?->path_name,
                    ];
                });
                return $user;
            });
        }

        if($user_count > 0){
            $users->data = UserResource::collection($users);
            $users->appends($request->query())->links();
        }else{
            return $this->sendResponse(false,'No User found',[],200);
        }
        return ['status' => true, 'code' => 200, 'message' => $user_count . ' User data found', 'data' => $users];


    }

    /**
     *
     *
     * User Roles
     *
     * Get a list of User Roles.
     * @group User
     * @authenticated
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Roles found",
    "data": [
    {
    "id": 1,
    "name": "Super Admin",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:43.000000Z",
    "updated_at": "2023-02-14T03:29:43.000000Z"
    },
    {
    "id": 2,
    "name": "Head of Secondary",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:45.000000Z",
    "updated_at": "2023-02-14T03:29:45.000000Z"
    },
    {
    "id": 3,
    "name": "Sales Director",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:46.000000Z",
    "updated_at": "2023-02-14T03:29:46.000000Z"
    },
    {
    "id": 4,
    "name": "Line Manager",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z"
    },
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z"
    },
    {
    "id": 6,
    "name": "Team Leader",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-15T10:33:03.000000Z"
    },
    {
    "id": 7,
    "name": "Admin",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-15T10:35:03.000000Z",
    "updated_at": "2023-02-15T10:35:03.000000Z"
    },
    {
    "id": 8,
    "name": "Telesales",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-04-27T07:07:14.000000Z",
    "updated_at": "2023-04-27T07:07:14.000000Z"
    },
    {
    "id": 9,
    "name": "Telesales Head",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-04-27T07:07:53.000000Z",
    "updated_at": "2023-04-27T07:07:53.000000Z"
    },
    {
    "id": 10,
    "name": "Mobile App Developer",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-05-13T06:55:14.000000Z",
    "updated_at": "2023-05-13T06:55:14.000000Z"
    },
    {
    "id": 11,
    "name": "API User",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-06-20T05:49:31.000000Z",
    "updated_at": "2023-06-20T05:49:31.000000Z"
    },
    {
    "id": 12,
    "name": "Exclusive Offplan",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-08-01T10:10:23.000000Z",
    "updated_at": "2023-08-01T10:10:23.000000Z"
    }
    ]
    }
     *
     *
     */

    public function roles()
    {
        $roles = Role::all();
        return $this->sendResponse(true, 'Roles found', $roles, 200);
    }

    /**
     * Create a User
     *
     * Create a User.
     * @group User
     * @authenticated
     * @header Accept application/json

     * @bodyParam email email required Email of the user. Example: user@axcapital.ae
     * @bodyParam first_name string required  First Name of the User. Example: John
     * @bodyParam mobile string required  Mobile of the user. Example: 00971521234567
     * @bodyParam password string required  Password for the user. Example: Pa$$W0rD
     * @bodyParam parent_id integer  Secondary CRM id of parent. Example: 9
     * @bodyParam bitrix_parent_id integer  Bitrix id of parent. Example: 234
     * @bodyParam photo_url url  photo_url for user's Avatar (send any: photo or photo_url. If both options are provided, the photo_url will be preferred). Example: https://amazonaws.com/.../230504105943.jpg
     * @bodyParam photo image  photo for user's Avatar (send any: photo or photo_url. If both options are provided, the photo_url will be preferred. Example: 230504105943.jpg
     * @bodyParam role string  role for the user. Default role 'Agent'. Example: Agent

     * @response 200 scenario=success
     * {
     *
     * }*/
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'first_name' => 'required',
            'mobile' => 'required|unique:users,mobile',

            'parent_id' => 'nullable|exists:users,id',
            'bitrix_parent_id' => 'nullable',
            'old_user_id' => 'nullable|exists:users,id',
            'slug' => 'nullable|unique:users,slug',
            'title' => 'nullable',
            'last_name' => 'nullable',
            'position' => 'nullable',
            'call_tracking_user_id' => 'nullable',
            'bitrix_id' => 'nullable',
            'website_link' => 'nullable',
            'facebook_link' => 'nullable',
            'instagram_link' => 'nullable',
            'linkedin_link' => 'nullable',
            'youtube_link' => 'nullable',
            'bio' => 'nullable',
            'license_no' => 'nullable',
            'pp_name' => 'nullable',
            'pp_license_no' => 'nullable',
            'secondary_email' => 'nullable|email',
            'telegram_username' => 'nullable',
            'telegram_chatid' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo_url' => 'nullable|url',
            'role' => 'nullable',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->name = $request->first_name . " " . $request->last_name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            if ($request->mobile) {
                $user->mobile = validatePhoneNumber($request->mobile);
            }
            $user->secondary_email = $request->secondary_email;
            $user->telegram_username = $request->telegram_username;
            $user->telegram_chatid = $request->telegram_chatid;
            $user->parent_id = $request->parent_id;

            if ($request->parent_bitrix_id) {
                $find_parent = User::where('bitrix_id', $request->parent_bitrix_id)->first();
                if ($find_parent) {
                    $user->parent_id = $find_parent->id;
                    $user->zone_id = $find_parent->zone_id;
                }
            }

            $user->old_user_id = $request->old_user_id;
            $user->slug = $request->slug;
            $user->title = $request->title;
            $user->position = $request->position;
            $user->call_tracking_user_id = $request->call_tracking_user_id;
            $user->bitrix_id = $request->bitrix_id;
            $user->website_link = $request->website_link;
            $user->facebook_link = $request->facebook_link;
            $user->instagram_link = $request->instagram_link;
            $user->linkedin_link = $request->linkedin_link;
            $user->youtube_link = $request->youtube_link;
            $user->bio = $request->bio;
            $user->license_no = $request->license_no;
            $user->pp_name = $request->pp_name;
            $user->pp_license_no = $request->pp_license_no;
            $user->status = 1;

            if ($request->hasFile("photo")) {
                $images = $this->uploadImageAndThumb($request->file("photo"), "avatar");
                $user->image = $images["image"] ?? null;
                $user->thumbnail = $images["thumbnail"] ?? null;
                $user->avatar = $images["thumbnail"] ?? null;
            }
            if ($request->photo_url) {
                $media = $this->uploadImageAndThumbWithWatermarkApi(file: $request->photo_url, path: "listings");

            }

            if (!empty($media) && !isset($media['error'])) {
                $user->image = $media["image"] ?? null;
                $user->thumbnail = $media["thumbnail"] ?? null;
                $user->avatar = $media["thumbnail"] ?? null;
            }

            $user->save();
            event(new Registered($user));
            if (env('APP_ENV') == 'production') {
                event(new UserCreated($user));
            }

            $notAdmin = ["Sales Director", "Line Manager", "Team Leader", "Agent"];
            $role = in_array($request->input('role'), $notAdmin) ? $request->input('role') : 'Agent';
            $user->assignRole($role);
            return $this->sendResponse(true, 'User created successfully', new UserResource($user), 200);

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in create function in UserController.php " . $th->getMessage());
            return $this->sendResponse(false, 'Something Went Wrong', null, 400);
        }

    }
    /**
     * Create a User by Bitrix Id
     *
     * Create a User by Bitrix Id.
     * @group User
     * @authenticated
     * @header Accept application/json

     * @bodyParam bitrix_id int required Bitrix Id of the user. Example: 1234
     * @bodyParam manager_bitrix_id string required Bitrix Id of the manager. Example: 1234
     * @bodyParam password required string Password for the user in plain text. Example: Password@321

     * @response 200 scenario=success
     * {
     *
     * }*/
    public function createWebhook(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'bitrix_id' => 'required',
            'password' => 'required',
            'manager_bitrix_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {

            ProcessUser::dispatch($request->all());
            return $this->sendResponse(true, 'User created successfully', [], 200);

        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in create function in UserController.php " . $th->getMessage());
            return $this->sendResponse(false, 'Something Went Wrong', $th->getMessage(), 400);
        }

    }
    /**
     * Show a User
     *
     * Show a User.
     * @group User
     * @authenticated
     * @header Accept application/json
     * @urlParam id integer required  id of the User. Example: 1
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "User Found",
    "data": {
    "status": true,
    "code": 200,
    "message": "User data found",
    "data": {
    "id": 12,
    "name": "Emine Sidzhimova",
    "full_name": "Emine Sidzhimova",
    "first_name": "Emine",
    "last_name": "Sidzhimova",
    "position": "Property Consultant",
    "email": "e.sidzhimova@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521864680",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1385931531",
    "status": 1
    }
    }
    }

     *
     * @response 200 scenario="not found"
     * {
    "status": false,
    "code": 404,
    "message": "User not found",
    "data": null
    }
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->sendResponse(false, 'User not found', null, 404);
        }

        return $this->sendResponse(true, 'User data found', new UserResource($user), 200);
    }

    /**
     * Deactivate User
     *
     * Change the user status to false and throw out to login page.
     * @group User
     * @authenticated
     * @header Accept application/json

     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @authenticated
     * @response 200 scenario="Successful Logout"
     * {
    "status": true,
    "code": 200,
    "message": "Deactivated successfully",
    "data": null
    }
     * */

    /**
     *
     *
     * Deactivate User
     * @group User
     * @header Accept application/json
     * @bodyParam agent_email required_without:bitrix_id   The email of the User is required without Bitrix ID. Example: abc@axcapital.ae
     * @bodyParam bitrix_id required_without:agent_email The Bitrix ID of the User is required without email. Example: 1
     * @response 200 scenario=success
    {
    "status":true,
    "code":200,
    "message":"Deactivated successfully",
    "data":null
    }
     *
     *
     * @response 422 scenario="Validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Please pass valid email or bitrix id of the user",
    "data": {
    "email": [
    "The agent_email field is required when bitrix_id is not present."
    ],
    "bitrix_id": [
    "The bitrix_id field is required when agent_email is not present."
    ]
    }
    }
     */

    public function disableUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'agent_email' => 'required_without:bitrix_id|exists:users,email',
                'bitrix_id' => 'required_without:agent_email|exists:users,bitrix_id',
            ]);
            // Deactivate use by email or bitrix_id if provided
            if ($validator->passes()) {
                // Validation passed
                $email = $request->input('agent_email');
                $bitrixId = $request->input('bitrix_id');
                $query = User::query();
                // Deactivate the user based on the provided email or bitrix_id
                if (!empty($email)) {
                    $query->where('email', $email);
                }

                if (!empty($bitrixId)) {
                    $query->orWhere('bitrix_id', $bitrixId);
                }

                // Deactivate the user based on the provided email or bitrix_id
                $user = $query->first();

                if ($user) {
                    if ($user->status == false) {
                        return $this->sendResponse(true, 'User is already deactivated', null, 200);
                    }
                    $user->status = false;
                    $user->password = bcrypt(rand(100000, 999999));
                    $user->updated_by_id = auth()->id();
                    $user->updated_at = now();
                    $user->save();
                    if (env('APP_ENV') == 'production') {
                        event(new UserUpdated($user));
                    }
                    return $this->sendResponse(true, 'Deactivated successfully', null, 200);
                    // Additional deactivation logic if needed
                }
                return $this->sendResponse(false, 'Unable to deactivate user', null, 500);

            } else {
                // Validation failed
                return $this->sendResponse(false, 'Please pass valid email or bitrix id of the user', $validator->errors(), 422);
            }

        } catch (\Exception $e) {
            Log::channel('discord')->error("Error in disableUser function in UserController.php " . $e->getMessage());

            return $this->sendResponse(false, 'Unable to deactivate user', null, 500);
        }
    }

    /**
     *
     *
     * Assign Manager
     * @header Accept application/json

     * @group User
     * @bodyParam agent_email required_without:agent_bitrix_id The email of the Agent is required without his Bitrix ID. Example: abc@axcapital.ae
     * @bodyParam agent_bitrix_id required_without:agent_email The Bitrix ID of the Agent is required without email. Example: 1
     * @bodyParam manager_email required_without:manager_bitrix_id The email of the Manager is required without his Bitrix ID. Example: abc@axcapital.ae
     * @bodyParam manager_bitrix_id required_without:manager_email The Bitrix ID of the Manager is required without email. Example: 1
     *
     * @response 200 scenario=success
    {
    "status": true,
    "code": 200,
    "message": "Manager assigned to agent Successfully.",
    "data": null
    }
     *
     *
     * @response 422 scenario="Validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Please pass valid email or bitrix id of the user.",
    "data": {
    "agent_email": [
    "The agent email field is required when agent bitrix id is not present."
    ],
    "agent_bitrix_id": [
    "The agent bitrix id field is required when agent email is not present."
    ],
    "manager_email": [
    "The manager email field is required when manager bitrix id is not present."
    ],
    "manager_bitrix_id": [
    "The manager bitrix id field is required when manager email is not present."
    ]
    }
    }
     */
    public function updateManager(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'agent_email' => 'required_without:agent_bitrix_id|exists:users,email',
                'agent_bitrix_id' => 'required_without:agent_email|exists:users,bitrix_id',

                'manager_email' => 'required_without:manager_bitrix_id|exists:users,email',
                'manager_bitrix_id' => 'required_without:manager_email|exists:users,bitrix_id',
            ]);

            if ($validator->passes()) {

                $agent_email = $request->input('agent_email');
                $agent_bitrixId = $request->input('agent_bitrix_id');
                $query = User::query();
                if (!empty($agent_email)) {
                    $query->where('email', $agent_email);
                }
                if (!empty($agent_bitrixId)) {
                    $query->orWhere('bitrix_id', $agent_bitrixId);
                }
                $agent = $query->first();

                $manager_email = $request->input('manager_email');
                $manager_bitrixId = $request->input('manager_bitrix_id');
                $query = User::query();
                if (!empty($manager_email)) {
                    $query->where('email', $manager_email);
                }
                if (!empty($manager_bitrixId)) {
                    $query->orWhere('bitrix_id', $manager_bitrixId);
                }
                $manager = $query->first();

                if ($agent && $manager) {
                    // check manager role is manager
                    if ($manager->hasRole(["Sales Director", "Team Leader", "Line Manager", 'Head of Secondary', 'CEO'])) {
                        $agent->syncRoles([]);
                        $role = Role::where('name', 'Agent')->first();
                        $agent->assignRole($role);

                    } elseif ($manager->hasRole(["Primary Sales Director", "Primary Sales Manager"])) {
                        $agent->syncRoles([]);
                        $role = Role::where('name', 'Primary Agent')->first();
                        $agent->assignRole($role);

                    } elseif ($manager->hasRole(["Admin"])) {
                        $agent->syncRoles([]);
                        $role = Role::where('name', 'Admin')->first();
                        $agent->assignRole($role);
                    }
                    $agent->updated_by_id = auth()->id();
                    $agent->updated_at = now();
                    $agent->parent_id = $manager->id;
                    $agent->zone_id = $manager->zone_id;
                    $agent->save();

                    if (env('APP_ENV') == 'production') {
                        event(new UserUpdated($agent));
                    }
                    return $this->sendResponse(true, 'Manager assigned to agent Successfully.', null, 200);

                }
                return $this->sendResponse(false, 'Unable to assign manager.', null, 500);
            } else {
                // Validation failed
                return $this->sendResponse(false, 'Please pass valid email or bitrix id of the user and manager.', $validator->errors(), 422);
            }

        } catch (\Exception $e) {
            // create log or error

            Log::channel('discord')->error("Error in updateManager function in UserController.php " . $e->getMessage());

            return $this->sendResponse(false, 'Unable to assign Manager.', null, 500);
        }
    }

    /**
     * Update a User
     *
     * Update a User.
     * @group User
     * @authenticated
     * @header Accept application/json

     * @urlParam email  Email of the User. Example: abc@axcapital.ae
     * @bodyParam email email required Email of the User. Example: abc@axcapital.ae
     * @bodyParam photo_url url  photo_url for user's Avatar (send any: photo or photo_url. If both options are provided, the photo_url will be preferred). Example: https://amazonaws.com/.../230504105943.jpg
     * @bodyParam photo image  photo for user's Avatar (send any: photo or photo_url. If both options are provided, the photo_url will be preferred. Example: 230504105943.jpg
     * @response 200 scenario=success
     * {
    "status": true,
    "code": 200,
    "message": "User Updated  Successfully.",
    "data": {
    "id": 255,
    "parent_id": null,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": null,
    "title": null,
    "name": "Fraz Shabbir",
    "first_name": "Fraz",
    "last_name": "Shabbir",
    "position": null,
    "email": "fraz@axcap.ae",
    "secondary_email": null,
    "email_verified_at": null,
    "mobile": "0000000",
    "telegram_username": null,
    "telegram_chatid": "00000000",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": null,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": null,
    "pp_enabled": null,
    "pp_name": null,
    "pp_photo": null,
    "pp_license_no": null,
    "country_id": null,
    "state_id": null,
    "nationality_id": null,
    "community_id": null,
    "thumbnail": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/thumb_168310038820230503115308.png",
    "image": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/168310038820230503115308.png",
    "avatar": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/thumb_168310038820230503115308.png",
    "color_code": null,
    "status": 1,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-05-03T07:41:17.000000Z",
    "updated_at": "2023-05-03T08:01:13.000000Z",
    "deleted_at": null,
    "full_name": "Fraz Shabbir",
    "thumb_url": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/thumb_168310038820230503115308.png",
    "image_url": "https://s3.eu-west-1.amazonaws.com/secondary.crm/images/avatar/168310038820230503115308.png",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 255,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ]
    }
    }
     *
     * @response 200 scenario="not found"
     * {
    "status": false,
    "code": 404,
    "message": "User not found",
    "data": null
    }
     *
     * @response 422 scenario="validation error"
     * {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "email": [
    "The email has already been taken."
    ]
    }

    }
     *
     * @response 403 scenario="Not Allowed"
     * {
    "status": true,
    "code": 403,
    "message": "Permission Denied For This User.",
    "data": null
     *}
     */

    public function editUser(Request $request, $email)
    {
        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->sendResponse(false, 'User not found.', null, 404);
        }
        if ($user && !$user->hasRole(['Sales Director', 'Line Manager', 'Team Leader', 'Agent'])) {
            return $this->sendResponse(true, 'Permission Denied For This User.', null, 403);

        }

        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:users,id',
            'old_user_id' => 'nullable|exists:users,id',
            'slug' => 'nullable|unique:users,slug',
            'title' => 'nullable',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'position' => 'nullable',
            'call_tracking_user_id' => 'nullable',
            'bitrix_id' => 'nullable',
            'website_link' => 'nullable',
            'facebook_link' => 'nullable',
            'instagram_link' => 'nullable',
            'linkedin_link' => 'nullable',
            'youtube_link' => 'nullable',
            'bio' => 'nullable',
            'license_no' => 'nullable',
            'pp_name' => 'nullable',
            'status' => 'nullable|number',
            'pp_license_no' => 'nullable',
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
            'password' => 'nullable',
            'mobile' => 'required|unique:users,mobile,' . $user->id . ',id',
            'secondary_email' => 'nullable|email',
            'telegram_username' => 'nullable',
            'telegram_chatid' => 'nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'photo_url' => 'nullable|url',

        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $notAdmin = ["Sales Director", "Line Manager", "Team Leader", "Agent"];
            if ($user && $user->hasRole(['Sales Director', 'Line Manager', 'Team Leader', 'Agent'])) {
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->name = $request->first_name . " " . $request->last_name;
                $user->email = $request->email;

                if ($request->mobile) {
                    $user->mobile = validatePhoneNumber($request->mobile);
                }

                $user->secondary_email = $request->secondary_email;
                $user->telegram_username = $request->telegram_username;
                $user->telegram_chatid = $request->telegram_chatid;
                $user->parent_id = $request->parent_id;
                $user->old_user_id = $request->old_user_id;
                $user->slug = $request->slug;
                $user->title = $request->title;
                $user->position = $request->position;
                $user->call_tracking_user_id = $request->call_tracking_user_id;
                $user->bitrix_id = $request->bitrix_id;
                $user->website_link = $request->website_link;
                $user->facebook_link = $request->facebook_link;
                $user->instagram_link = $request->instagram_link;
                $user->linkedin_link = $request->linkedin_link;
                $user->youtube_link = $request->youtube_link;
                $user->bio = $request->bio;
                $user->license_no = $request->license_no;
                $user->pp_name = $request->pp_name;
                $user->pp_license_no = $request->pp_license_no;
                // dd($request->status);
                if (isset($request->status)) {
                    $user->status = (int) $request->status;
                }
                if ($request->password) {
                    $user->password = bcrypt($request->password);
                }

                if ($request->hasFile("photo")) {
                    $images = $this->uploadImageAndThumb($request->file("photo"), "avatar");
                    $user->image = $images["image"] ?? null;
                    $user->thumbnail = $images["thumbnail"] ?? null;
                    $user->avatar = $images["thumbnail"] ?? null;
                }
                if ($request->photo_url) {
                    $media = $this->uploadImageAndThumbWithWatermarkApi(file: $request->photo_url, path: "listings");

                }

                if (!empty($media) && !isset($media['error'])) {
                    $user->image = $media["image"] ?? null;
                    $user->thumbnail = $media["thumbnail"] ?? null;
                    $user->avatar = $media["thumbnail"] ?? null;
                }

                $user->save();
                if (env('APP_ENV') == 'production') {
                    event(new UserUpdated($user));
                }

                return $this->sendResponse(true, 'User Updated  Successfully.', $user, 200);
            } else {
                return $this->sendResponse(true, 'Permission Denied For This User.', null, 403);
            }
        } catch (\Throwable $th) {
            Log::channel('discord')->error("Error in disableUser function in UserController.php " . $e->getMessage());
            return $this->sendResponse(false, 'Something Went Wrong.', null, 200);

        }

    }

    /**
     * Upload Traccar Coordinates
     *
     * Upload Traccar Coordinates.
     * @group Traccar
     * @authenticated
     * @header Accept application/json

     * @bodyParam user_id integer required  id of the User. Example: 12
     * @bodyParam coordinate_recording file required  coordinate_recording of the User.
     * @bodyParam traccar_restricted_coordinate_id integer required  traccar_restricted_coordinate_id of the User. Example: 12
     * @response 200 scenario=success

    {
    "status": true,
    "code": 200,
    "message": "Coordinate Recording Uploaded Successfully.",
    "data": null
    }
     *
     * @response 422 scenario="validation error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "user_id": [
    "The user id field is required."
    ],
    "coordinate_recording": [
    "The coordinate recording field is required."
    ],
    "traccar_restricted_coordinate_id": [
    "The traccar restricted coordinate id field is required."
    ]
    }
    }

     */

    public function uploadCoordinates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'coordinate_recording' => 'required',
            'traccar_restricted_coordinate_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $user = User::find($request->user_id);

        $path = $this->uploadCallRecording($request->file("coordinate_recording"), "coordinate_recording");

        $rec = CoordinateRecording::create([
            'user_id' => $request->user_id,
            'traccar_restricted_coordinate_id' => $request->traccar_restricted_coordinate_id,
            'path' => $path,
        ]);

        Log::channel('mobileApi')->info('uploadCoordinates: Traccar Recording of ID ' . $user->id, ['Recording' => $rec]);

        return $this->sendResponse(true, 'Coordinate Recording Uploaded Successfully.', null, 200);

    }
    /**
     *
     *
     * User Agents with Children
     *
     * Get a list of Agents with children.
     * @group User
     * @authenticated
     * @queryParam get_location boolean  Get location of the user. Example: true
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "User data found",
    "data": [
    {
    "id": 1,
    "name": "Saim Aziz",
    "full_name": "Saim Aziz",
    "first_name": "Saim",
    "last_name": "Aziz",
    "position": null,
    "email": "s.mirza@axcapital.ae",
    "secondary_email": "s.mirza@axcapital.ae",
    "mobile": null,
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 1
    },
    {
    "id": 2,
    "name": "Otabek Navruzov",
    "full_name": "Otabek Navruzov",
    "first_name": "Otabek",
    "last_name": "Navruzov",
    "position": "CTO",
    "email": "cto@axcapital.ae",
    "secondary_email": "cto@axcapital.ae",
    "mobile": "+971521569330",
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 1
    },
    {
    "id": 3,
    "name": "Head Navruzov",
    "full_name": "Head Navruzov",
    "first_name": "Head",
    "last_name": "Navruzov",
    "position": "Test Account",
    "email": "head@axcapital.ae",
    "secondary_email": "head@axcapital.ae",
    "mobile": "+971234352345235",
    "thumbnail": null,
    "image": null,
    "avatar": "avatar-1.jpg",
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 0
    },
    {
    "id": 5,
    "name": "Muhammad Mohsan",
    "full_name": "Muhammad Mohsan",
    "first_name": "Muhammad",
    "last_name": "Mohsan",
    "position": "Property Consultant",
    "email": "m.mohsan@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521785458",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1215739441",
    "status": 1
    },
    {
    "id": 6,
    "name": "Syed Imran",
    "full_name": "Syed Imran",
    "first_name": "Syed",
    "last_name": "Imran",
    "position": "Property Consultant",
    "email": "s.imran@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521952454",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "892101799",
    "status": 1
    },
    {
    "id": 7,
    "name": "System Agent",
    "full_name": "System Agent",
    "first_name": "System",
    "last_name": "Agent",
    "position": "Dubai Hills Specialist",
    "email": "default@axcapital.ae",
    "secondary_email": "default@axcapital.ae",
    "mobile": "+971521943182",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": null,
    "status": 0
    },
    {
    "id": 8,
    "name": "Maksim Tuguchev",
    "full_name": "Maksim Tuguchev",
    "first_name": "Maksim",
    "last_name": "Tuguchev",
    "position": "Sales Manager",
    "email": "m.tuguchev@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971586448869",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_167928699920230320043639.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/167928699920230320043639.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1285313110",
    "status": 1
    },
    {
    "id": 9,
    "name": "Shehryar Malik",
    "full_name": "Shehryar Malik",
    "first_name": "Shehryar",
    "last_name": "Malik",
    "position": "Sales Manager",
    "email": "shehryar.malik@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521937175",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "697184586",
    "status": 1
    },
    {
    "id": 10,
    "name": "Khalid Shahzad",
    "full_name": "Khalid Shahzad",
    "first_name": "Khalid",
    "last_name": "Shahzad",
    "position": "Property Expert",
    "email": "k.shahzad@axcapital.ae",
    "secondary_email": "k.shahzad@axcapital.ae",
    "mobile": "+971521432744",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1250121066",
    "status": 1
    },
    {
    "id": 11,
    "name": "Igor Kavka",
    "full_name": "Igor Kavka",
    "first_name": "Igor",
    "last_name": "Kavka",
    "position": "Property Consultant",
    "email": "igor.kavka@axcapital.ae",
    "secondary_email": "igor.kavka@axcapital.ae",
    "mobile": "+971523971052",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "825902846",
    "status": 1
    },
    {
    "id": 12,
    "name": "Emine Sidzhimova",
    "full_name": "Emine Sidzhimova",
    "first_name": "Emine",
    "last_name": "Sidzhimova",
    "position": "Property Consultant",
    "email": "e.sidzhimova@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521864680",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1385931531",
    "status": 1
    },
    {
    "id": 13,
    "name": "Mukul Wadhwa",
    "full_name": "Mukul Wadhwa",
    "first_name": "Mukul",
    "last_name": "Wadhwa",
    "position": "Senior Property Consultant",
    "email": "m.wadhwa@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521942162",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1339154430",
    "status": 1
    },
    {
    "id": 14,
    "name": "Shermeen Baqai",
    "full_name": "Shermeen Baqai",
    "first_name": "Shermeen",
    "last_name": "Baqai",
    "position": "Property Consultant",
    "email": "s.baqai@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521804510",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1387926540",
    "status": 1
    },
    {
    "id": 15,
    "name": "Alex Lourenco",
    "full_name": "Alex Lourenco",
    "first_name": "Alex",
    "last_name": "Lourenco",
    "position": "Property Consultant",
    "email": "a.lourenco@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521349185",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1373001507",
    "status": 1
    },
    {
    "id": 16,
    "name": "Gabriela Brunariova",
    "full_name": "Gabriela Brunariova",
    "first_name": "Gabriela",
    "last_name": "Brunariova",
    "position": "Property Consultant",
    "email": "g.brunariova@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971523747265",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_167928706120230320043741.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/167928706120230320043741.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "1359705122",
    "status": 1
    },
    {
    "id": 17,
    "name": "Karim Ahmed",
    "full_name": "Karim Ahmed",
    "first_name": "Karim",
    "last_name": "Ahmed",
    "position": "Team Leader",
    "email": "k.ahmed@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971521816401",
    "thumbnail": "https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/user/full/2226/lGHiX7uzblkkxnat.png",
    "image": "https://s3-ap-southeast-1.amazonaws.com/mycrm-pro-accounts-v2/user/full/2226/lGHiX7uzblkkxnat.png",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "892101799",
    "status": 1
    },
    {
    "id": 18,
    "name": "Lizaveta Drozd",
    "full_name": "Lizaveta Drozd",
    "first_name": "Lizaveta",
    "last_name": "Drozd",
    "position": "Property Consultant",
    "email": "l.drozd@axcapital.ae",
    "secondary_email": null,
    "mobile": "+971557649869",
    "thumbnail": null,
    "image": null,
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5270107631",
    "status": 1
    }
    ]
    }

     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No User found",
    "data": []
    }
     */
    public function agents(Request $request)
    {

        $admin = auth()->user()->hasRole(["Super Admin", "Admin", "Head of Secondary"]);
        $childrenIds = [];
        if (!$admin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data = User::where('status', true)->whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent", "Team Leader", "Head of Secondary", "Sales Director", "Line Manager"]);
        })->when(!$admin, fn($q) => $q->whereIn("id", $childrenIds))->get();

        return $this->sendResponse(true, $data->count() . ' User data found', UserResource::collection($data), 200);
    }

    public function getUserThroughBitrixId($id)
    {
        $user = User::with('roles')->where('bitrix_id', $id)->first();

        if (!$user) {
            return $this->sendResponse(false, 'User not found', null, 404);
        }

        return $this->sendResponse(true, 'User data found', new UserResource($user), 200);
    }
    public function  getUserThroughId($id) 
    {
        $user = User::with('roles')->find($id);

        if (!$user) {
            return $this->sendResponse(false, 'User not found', null, 404);
        }

        return $this->sendResponse(true, 'User data found', new UserResource($user), 200);    
    }
}
