<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Deal;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\Property;
use App\Models\Source;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return abort(404);
    }

    public function contactLogin()
    {

        $user = User::find(auth()->id());

        if($user->id == 32 || $user->parent_id == 32){
            abort(403);
        }

        $email = $user->email;
        $password = $user->password;

        $url = 'https://contacts.axcrm.ae/api/secondary-login-generate-token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$email&password=$password");
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['data']['redirect_url'])) {
            $redirectUrl = $responseData['data']['redirect_url'];
            return redirect($redirectUrl);
        } else {
            return redirect('https://contacts.axcrm.ae/');
            return response()->json(['error' => 'Redirect URL not found in the response'], 400);
        }

        // return redirect('https://contacts.axcrm.ae/api/auth-login?email=' . $email . '&password=' . $password);

    }
   protected function get_login_token_from_deal(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://deals.axcrm.ae/backend/api/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('email' => 'system@axcapital.ae','password'=>'SystemUser321@#'),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: multipart/form-data',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data=json_decode($response,1);
        return $data['access_token'] ?? null;
    }
    public function dealLogin()
    {
        $token = $this->get_login_token_from_deal();

       if($token !='' && $token != null) {


           $user = User::find(auth()->id());

           if ($user->id == 32 || $user->parent_id == 32) {
               abort(403);
           }

           $email = $user->email;

           $url = 'https://deals.axcrm.ae/backend/api/secondary-login-generate-token-v2';

           $ch = curl_init($url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$email");
           curl_setopt($ch, CURLOPT_HTTPHEADER, [
               'Authorization: Bearer ' . $token,
               'Content-Type: application/x-www-form-urlencoded'
           ]);
           $response = curl_exec($ch);
           curl_close($ch);
           $responseData = json_decode($response, true);
           if (isset($responseData['data']['redirect_url'])) {
               $redirectUrl = $responseData['data']['redirect_url'];
               return redirect($redirectUrl);
           } else {
               return redirect('https://deals.axcrm.ae/');
               return response()->json(['error' => 'Redirect URL not found in the response'], 400);
           }

           // return redirect('https://contacts.axcrm.ae/api/auth-login?email=' . $email . '&password=' . $password);
       }
    }

    public function root(Request $request)
    {


        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $portalWiseCounts = Portal::where('status', true)
            ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds) {
                $query->where('is_live', '1');
                $query->whereNot('completion_status', 'Off-Plan Primary');

                if ($notAdmin) {
                    $query->whereIn('agent_id', $childrenIds);
                }
            }])
            ->get(['id', 'name', 'thumbnail']);

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["leadCount"] = Lead::where(function ($q) {
            $q->whereHas('agent', function ($q) {
                $q->where('status', 1);
            })->orWhereNull('agent_id');
        })->where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')
            ->count();
        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data['event_types'] = EventType::where('status', 1)->get();
        return view('admin.dashboard.index2')->with($data);
    }

    public function index2()
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where("status", true)->get();
        $portalWiseCounts = [];
        foreach ($portals as $portal) {
            $portalWiseCounts[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "count" => Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas("portals", fn($q) => $q->where("portal_id", $portal->id))->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->count(),
            ];
        }

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["leadCount"] = Lead::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data['event_types'] = EventType::where('status', 1)->get();
        return view('admin.dashboard.index2')->with($data);
    }

    public function rootPrimary()
    {

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $portalWiseCounts = Portal::where('status', true)
            ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds) {
                $query->where('is_live', '1');
                $query->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
                if ($notAdmin) {
                    $query->whereIn('agent_id', $childrenIds);
                }
            }])
            ->get(['id', 'name', 'thumbnail']);

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('is_live', '1')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary'])->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = "Na";
        $data["leadCount"] = Lead::where(function ($q) {
            $q->whereHas('agent', function ($q) {
                $q->where('status', 1);
            })->orWhereNull('agent_id');
        })->whereHas('property', function ($q) {
            $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        })
            ->where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')
            ->count();

        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('property', function ($q) {
            $q->whereIn('completion_status', ['Off-Plan Primary', 'Ready Primary']);
        })->count();

        $data['event_types'] = EventType::where('status', 1)->get();

        return view('admin.dashboard.primary')->with($data);
    }

    public function rootCommercial()
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $portalWiseCounts = Portal::where('status', true)
            ->withCount(['properties' => function ($query) use ($notAdmin, $childrenIds) {
                $query->where('is_live', '1');
                $query->whereIn('for', ['Commercial for sale', 'Commercial for rent']);
                if ($notAdmin) {
                    $query->whereIn('agent_id', $childrenIds);
                }
            }])
            ->get(['id', 'name', 'thumbnail']);

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereIn('for', ['Commercial for sale', 'Commercial for rent'])->where('is_live', '1')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::whereIn('for', ['Commercial for sale', 'Commercial for rent'])->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::whereIn('for', ['Commercial for sale', 'Commercial for rent'])->where('is_live', "1")->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = "Na";

        $data["leadCount"] = Lead::where(function ($q) {
            $q->whereHas('agent', function ($q) {
                $q->where('status', 1);
            })->orWhereNull('agent_id');
        })->whereHas('property', function ($q) {
            $q->whereIn('for', ['Commercial for sale', 'Commercial for rent']);
        })
            ->where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')
            ->count();

        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('property', function ($q) {
            $q->whereIn('for', ['Commercial for sale', 'Commercial for rent']);
        })->count();

        $data['event_types'] = EventType::where('status', 1)->get();

        return view('admin.dashboard.commercial')->with($data);
    }

    public function rootSecondary()
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where("status", true)->get();
        $portalWiseCounts = [];
        foreach ($portals as $portal) {
            $portalWiseCounts[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "count" => Property::whereNot('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->whereHas("portals", fn($q) => $q->where("portal_id", $portal->id))->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->count(),
            ];
        }

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereNot('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::whereNot('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::whereNot('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = "Na";

        $data["leadCount"] = Lead::whereHas('property', function ($q) {
            $q->whereNot('region_id', 2);
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->where(function ($q) {
                $q->whereHas('agent', function ($q) {
                    $q->where('status', 1);
                })->orWhereNull('agent_id');
            })
            ->where('status', '1')
            ->whereNull('converted_to_listing_lead')
            ->where('is_archive', '0')
            ->whereNotNull('agent_id')->count();

        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('property', function ($q) {
            $q->whereNot('region_id', 2);
        })->count();

        $data['event_types'] = EventType::where('status', 1)->get();
        return view('admin.dashboard.secondary')->with($data);
    }

    public function rootAbuDhabi()
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where("status", true)->get();
        $portalWiseCounts = [];
        foreach ($portals as $portal) {
            $portalWiseCounts[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "count" => Property::where('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->whereHas("portals", fn($q) => $q->where("portal_id", $portal->id))->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->count(),
            ];
        }

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::where('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForRentCount"] = Property::where('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["propertyForSaleCount"] = Property::where('region_id', 2)->whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();
        $data["contactCount"] = "Na";

        $data["leadCount"] = Lead::where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->whereHas('property', function ($q) {
            $q->where('region_id', 2);
        })
            ->count();

        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('property', function ($q) {
            $q->where('region_id', 2);
        })->count();

        $data['event_types'] = EventType::where('status', 1)->get();
        return view('admin.dashboard.abu_dhabi')->with($data);
    }

    public function rootChinese()
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $portals = Portal::where("status", true)->get();
        $portalWiseCounts = [];
        foreach ($portals as $portal) {
            $portalWiseCounts[] = [
                "id" => $portal->id,
                "name" => $portal->name,
                "thumbnail" => $portal->thumbnail,
                "count" => Property::whereNot('completion_status', 'Off-Plan Primary')->whereHas("portals", fn($q) => $q->where("portal_id", $portal->id))->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds);
                })->whereHas('agent', function ($q) {
                    $q->where('bitrix_role', 'like', '%Chinese%');
                })->count(),
            ];
        }

        $data["portalWiseCounts"] = $portalWiseCounts;
        $data["propertyCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->where('publish_status', 'PUBLISHED')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('agent', function ($q) {
            $q->where('bitrix_role', 'like', '%Chinese%');
        })->count();
        $data["propertyForRentCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('agent', function ($q) {
            $q->where('bitrix_role', 'like', '%Chinese%');
        })->count();
        $data["propertyForSaleCount"] = Property::whereNot('completion_status', 'Off-Plan Primary')->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('agent', function ($q) {
            $q->where('bitrix_role', 'like', '%Chinese%');
        })->count();

        $data["contactCount"] = Contact::where('status', 1)->when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->whereHas('agent', function ($q) {
            $q->where('bitrix_role', 'like', '%Chinese%')->whereNotNull('bitrix_role');
        })->count();

        $data["leadCount"] = Lead::where('status', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->whereHas('agent', function ($q) {
                $q->where('bitrix_role', 'like', '%Chinese%')->whereNotNull('bitrix_role');
            })
            ->count();

        $data["dealCount"] = Deal::when($notAdmin, function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })->count();

        $data['event_types'] = EventType::where('status', 1)->get();
        return view('admin.dashboard.chinese')->with($data);
    }
    //
    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
        ]);

        $user = User::find($id);
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = $avatarName;
        }

        $user->update();
        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "User Details Updated successfully!"
            // ], 200); // Status code here
            return redirect()->back();
        } else {
            Session::flash('message', 'Something went wrong!');
            Session::flash('alert-class', 'alert-danger');
            // return response()->json([
            //     'isSuccess' => true,
            //     'Message' => "Something went wrong!"
            // ], 200); // Status code here
            return redirect()->back();

        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your Current password does not matches with the password you provided. Please try again.",
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!",
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!",
                ], 200); // Status code here
            }
        }
    }

    public function telelogLogin()
    {

        $user = User::find(auth()->id());

        $email = $user->email;
        $password = $user->password;

        $url = 'https://telelog.axcrm.ae/backend/api/secondary-login-generate-token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$email&password=$password");
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['data']['redirect_url'])) {
            $redirectUrl = $responseData['data']['redirect_url'];
            return redirect($redirectUrl);
        } else {
            return response()->json(['error' => 'Redirect URL not found in the response'], 400);
        }

        // return redirect('https://contacts.axcrm.ae/api/auth-login?email=' . $email . '&password=' . $password);

    }

    // {
    //     $user = User::find(auth()->id());

    //     $email = $user->email;
    //     $password = $user->password;
    //     return redirect('https://telelog.axcrm.ae/backend/api/web/auth/login-from-secondary?email=' . $email . '&password=' . $password);
    // }

    public function agentGroupReporting(Request $request)
    {

        $group = $request->group;

        switch ($group) {
            case 'All':
                $var = $this->allUserReports();
                return view('admin.dashboard.partials._group_reporting')
                    ->with('users', $var['users'])
                    ->with('agents', $var['agents'])
                    ->with('incomplete_lead_agents', $var['incopmlete_leads_users'])
                    ->with('new_status_lead', $var['new_status_lead'])
                    ->with('sourceWithLeadsCount', $var['sourceWithLeadsCount'])
                    ->with('no_deal_agents', $var['no_deal_agents'])
                    ->with('sorted_deals_days_count', $var['sorted_deals_days_count'])
                    ->with('hours', $var['hours'])
                    ->with('group', $group);
            case 'Primary':
                $var = $this->primaryUserReports();
                return view('admin.dashboard.partials._group_reporting')
                    ->with('users', $var['users'])
                    ->with('agents', $var['agents'])
                    ->with('incomplete_lead_agents', $var['incopmlete_leads_users'])
                    ->with('new_status_lead', $var['new_status_lead'])
                    ->with('sourceWithLeadsCount', $var['sourceWithLeadsCount'])
                    ->with('hours', $var['hours'])
                    ->with('no_deal_agents', $var['no_deal_agents'])
                    ->with('sorted_deals_days_count', $var['sorted_deals_days_count'])
                    ->with('group', $group);

            case 'Secondary':
                $var = $this->secondaryUserReports();
                return view('admin.dashboard.partials._group_reporting')
                    ->with('users', $var['users'])
                    ->with('agents', $var['agents'])
                    ->with('incomplete_lead_agents', $var['incopmlete_leads_users'])
                    ->with('new_status_lead', $var['new_status_lead'])
                    ->with('sourceWithLeadsCount', $var['sourceWithLeadsCount'])
                    ->with('hours', $var['hours'])
                    ->with('no_deal_agents', $var['no_deal_agents'])
                    ->with('sorted_deals_days_count', $var['sorted_deals_days_count'])
                    ->with('group', $group);
            case 'Chinese':
                $var = $this->chineseUserReports();
                return view('admin.dashboard.partials._group_reporting')
                    ->with('users', $var['users'])
                    ->with('agents', $var['agents'])
                    ->with('incomplete_lead_agents', $var['incopmlete_leads_users'])
                    ->with('new_status_lead', $var['new_status_lead'])
                    ->with('sourceWithLeadsCount', $var['sourceWithLeadsCount'])
                    ->with('hours', $var['hours'])
                    ->with('no_deal_agents', $var['no_deal_agents'])
                    ->with('sorted_deals_days_count', $var['sorted_deals_days_count'])
                    ->with('group', $group);
            case 'Abu Dhabi':
                $var = $this->abuDhabiUserReports();
                return view('admin.dashboard.partials._group_reporting')
                    ->with('users', $var['users'])
                    ->with('agents', $var['agents'])
                    ->with('incomplete_lead_agents', $var['incopmlete_leads_users'])
                    ->with('new_status_lead', $var['new_status_lead'])
                    ->with('sourceWithLeadsCount', $var['sourceWithLeadsCount'])
                    ->with('hours', $var['hours'])
                    ->with('no_deal_agents', $var['no_deal_agents'])
                    ->with('sorted_deals_days_count', $var['sorted_deals_days_count'])
                    ->with('group', $group);
            default:
        }
    }

    private function abuDhabiUserReports()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();
        $primary_agents_message_suspension = '';
        $primary_agents_message = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name' => $agent->full_name,
                    'days' => $daysSinceLastEvent,
                    'image' => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager' => $agent->parent ? $agent->parent->full_name : 'No Manager',

                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incomplete_leads_users = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->where(function ($query) {
                            $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                        })
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();

        $new_status_lead = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();
        $hours = 400;
        $agents_source = User::where('status', true)
            ->where('region_id', 2)
            ->get();
        $agents_source_id = $agents_source->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $agents_source_id)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('is_archive', 0)
                ->whereIn('agent_id', $agents_source_id)
                ->where('created_at', '>=', now()->subHours($hours))
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();
        $no_deal_agents = User::where('status', true)
            ->where('region_id', 2)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('last_deal_date', '<', now()->subDays(60))
            ->get();

        $deals_days_count = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->whereDate('joining_date', '<=', now()->subDays(60)) // Filter for joining date more than 60 days ago
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = $deals_days_count->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $deals_days_count->sortByDesc('days_since_last_deal');

        return [
            'users' => $users,
            'agents' => $agents_data,
            'incopmlete_leads_users' => $incomplete_leads_users,
            'new_status_lead' => $new_status_lead,
            'sourceWithLeadsCount' => $sourceWithLeadsCount,
            'no_deal_agents' => $no_deal_agents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours' => $hours,
        ];
    }

    private function chineseUserReports()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();
        $primary_agents_message_suspension = '';
        $primary_agents_message = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name' => $agent->full_name,
                    'days' => $daysSinceLastEvent,
                    'image' => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager' => $agent->parent ? $agent->parent->full_name : 'No Manager',

                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incomplete_leads_users = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->where(function ($query) {
                            $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                        })
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();

        $new_status_lead = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();
        $hours = 400;
        $agents_source = User::where('status', true)
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->get();
        $agents_source_id = $agents_source->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $agents_source_id)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('is_archive', 0)
                ->whereIn('agent_id', $agents_source_id)
                ->where('created_at', '>=', now()->subHours($hours))
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();
        $no_deal_agents = User::where('status', true)
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('last_deal_date', '<', now()->subDays(60))
            ->get();
        $deals_days_count = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subDays(60)) // Filter for joining date more than 60 days ago
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = $deals_days_count->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $deals_days_count->sortByDesc('days_since_last_deal');

        return [
            'users' => $users,
            'agents' => $agents_data,
            'incopmlete_leads_users' => $incomplete_leads_users,
            'new_status_lead' => $new_status_lead,
            'sourceWithLeadsCount' => $sourceWithLeadsCount,
            'no_deal_agents' => $no_deal_agents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours' => $hours,
        ];
    }

    private function secondaryUserReports()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();
        $primary_agents_message_suspension = '';
        $primary_agents_message = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name' => $agent->full_name,
                    'days' => $daysSinceLastEvent,
                    'image' => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager' => $agent->parent ? $agent->parent->full_name : 'No Manager',

                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incomplete_leads_users = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->where(function ($query) {
                            $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                        })
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();

        $new_status_lead = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();
        $hours = 400;
        $agents_source = User::where('status', true)
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->get();
        $agents_source_id = $agents_source->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $agents_source_id)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('is_archive', 0)
                ->whereIn('agent_id', $agents_source_id)
                ->where('created_at', '>=', now()->subHours($hours))
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $no_deal_agents = User::where('status', true)
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('last_deal_date', '<', now()->subDays(60))
            ->get();

        $deals_days_count = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subDays(60)) // Filter for joining date more than 60 days ago
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = $deals_days_count->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $deals_days_count->sortByDesc('days_since_last_deal');

        return [
            'users' => $users,
            'agents' => $agents_data,
            'incopmlete_leads_users' => $incomplete_leads_users,
            'new_status_lead' => $new_status_lead,
            'sourceWithLeadsCount' => $sourceWithLeadsCount,
            'no_deal_agents' => $no_deal_agents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours' => $hours,
        ];
    }

    private function primaryUserReports()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('region_id', '!=', 2)
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();
        $primary_agents_message_suspension = '';
        $primary_agents_message = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name' => $agent->full_name,
                    'days' => $daysSinceLastEvent,
                    'image' => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager' => $agent->parent ? $agent->parent->full_name : 'No Manager',

                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incomplete_leads_users = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('status', true)->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->where(function ($query) {
                            $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                        })
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();

        $new_status_lead = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('status', true)->where('region_id', '!=', 2)
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();
        $hours = 400;
        $agents_source = User::where('status', true)
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('region_id', '!=', 2)
            ->get();
        $agents_source_id = $agents_source->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $agents_source_id)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('is_archive', 0)
                ->whereIn('agent_id', $agents_source_id)
                ->where('created_at', '>=', now()->subHours($hours))
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $no_deal_agents = User::where('status', true)
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('region_id', '!=', 2)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('last_deal_date', '<', now()->subDays(60))
            ->get();

        $deals_days_count = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subDays(60)) // Filter for joining date more than 60 days ago
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = $deals_days_count->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $deals_days_count->sortByDesc('days_since_last_deal');

        return [
            'users' => $users,
            'agents' => $agents_data,
            'incopmlete_leads_users' => $incomplete_leads_users,
            'new_status_lead' => $new_status_lead,
            'sourceWithLeadsCount' => $sourceWithLeadsCount,
            'no_deal_agents' => $no_deal_agents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours' => $hours,
        ];
    }

    private function allUserReports()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount('suspensions') // Assuming the name of the relationship is 'suspensions'
            ->orderByDesc('suspensions_count')
            ->get();
        $primary_agents_message_suspension = '';
        $primary_agents_message = '';
        foreach ($users as $agent) {
            if ($agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $agent->full_name . ": " . $agent->suspensions->count() . "\n";
            }

            $lastEventDate = $agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $agents_data[] = [
                    'name' => $agent->full_name,
                    'days' => $daysSinceLastEvent,
                    'image' => $agent->thumb_url,
                    'bitrix_role' => $agent->bitrix_role,
                    'manager' => $agent->parent ? $agent->parent->full_name : 'No Manager',
                ];
            }
        }

        usort($agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });
        $agents_data = collect($agents_data);

        $incomplete_leads_users = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->where(function ($query) {
                            $query->where('first_name', 'LIKE', '%new%')->orWhereNull('first_name');
                        })
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();

        $new_status_lead = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->withCount([
                'leads' => function ($query) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->orderBy('leads_count', 'desc')->get();
        $hours = 400;
        $agents_source = User::where('status', true)->get();
        $agents_source_id = $agents_source->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $agents_source_id)
                ->where('is_archive', 0)
                ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($agents_source_id, $hours) {
            $query
                ->where('is_archive', 0)
                ->whereIn('agent_id', $agents_source_id)
                ->where('created_at', '>=', now()->subHours($hours))
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $twoMonthsAgo = Carbon::now()->subMonths(2);
        $no_deal_agents = User::where('status', true)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('joining_date', '<', $twoMonthsAgo)
            ->whereNull('last_deal_date')
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = User::where('status', true)->where('is_report_exempt', 0)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('region_id', '!=', 2)
            ->whereDate('joining_date', '<=', now()->subDays(60)) // Filter for joining date more than 60 days ago
            ->orderBy('joining_date', 'desc')
            ->get();

        $deals_days_count = $deals_days_count->map(function ($agent) {
            $daysSinceLastDeal = now()->diffInDays($agent->last_deal_date ?? $agent->joining_date ?? $agent->created_at);
            $agent->days_since_last_deal = $daysSinceLastDeal;
            return $agent;
        });
        $sorted_deals_days_count = $deals_days_count->sortByDesc('days_since_last_deal');

        return [
            'users' => $users,
            'agents' => $agents_data,
            'incopmlete_leads_users' => $incomplete_leads_users,
            'new_status_lead' => $new_status_lead,
            'sourceWithLeadsCount' => $sourceWithLeadsCount,
            'no_deal_agents' => $no_deal_agents,
            'sorted_deals_days_count' => $sorted_deals_days_count,
            'hours' => $hours,
        ];
    }

    public function chatLogin()
    {
        $user = User::find(auth()->id());

        $email = $user->email;
        $password = $user->password;

        $url = 'https://chatapi.axcrm.ae/api/secondary-login-generate-token';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "email=$email&password=$password");
        $response = curl_exec($ch);
        curl_close($ch);
        $responseData = json_decode($response, true);
        if (isset($responseData['data']['redirect_url'])) {
            $redirectUrl = $responseData['data']['redirect_url'];
            return redirect($redirectUrl);
        } else {
            return response()->json(['error' => 'Redirect URL not found in the response'], 400);
        }


    }
}
