<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use App\Imports\PhoneNumberImport;
use App\Imports\PhoneNumberImportHeader;
use App\Jobs\ProcessBulkSms;
use App\Models\BulkMessage;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\User;
use App\Traits\FiltersTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BulkSMSController extends Controller
{
    use FiltersTrait;

    public function index(Request $request)
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $users = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Sales Director', 'Team Leader', 'Line Manager']);
        })->whereHas('children')
            ->where('status', 1)
            ->get();

        $agents = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Agent']);
        })
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('id', $childrenIds);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })
            ->where('status', 1)
            ->get();
        return view('admin.bulk_sms.index')
            ->with('agents', $agents)
            ->with('users', $users);
    }

    public function datatable(Request $request)
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = BulkMessage::select('bulk_messages.*');

        if ($request->agent_id) {
            $this->isArray("agent_id") ? $query->whereIn("id", \request("agent_id")) : $query->where("id", \request("agent_id"));
        }

        return DataTables::of($query->with(['agent', 'sender'])
                ->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->where(function ($q) use ($childrenIds) {
                        $q->whereIn('agent_id', $childrenIds);
                    });
                })
                ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                    $q->where('region_id', 2);

                })
                ->when($request->status, function ($q) use ($request) {
                    $q->where('status', $request->status);
                })
                ->when($request->confirmation, function ($q) use ($request) {
                    $q->where('confirmed', $request->confirmation);
                })
                ->when($request->batch_no, function ($q) use ($request) {
                    $q->where('batch_no', 'like', '%' . $request->batch_no . '%');
                })
        )->make(true);
    }

    public function getContacts(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);
        $user = User::find($request->agent_id);

        $contacts = Contact::whereIn('agent_id', $request->agent_id)
            ->where('status', 1)
            ->get();

        return view('admin.bulk_sms.includes._contacts')
            ->with('contacts', $contacts);
    }

    public function save(Request $request)
    {
        dd($request->all());
        $request->validate([
            'input' => 'nullable|string',
            'message' => 'required|string',
        ]);

        if ($request->has('file')) {
            $result = $this->fileImported($request);
        } else {
            $result = $this->manualEnteredNumber($request);
        }
        return response()->json($result);

    }

    private function fileImported($request)
    {

        if ($request->has('file')) {
            $file = $request->file('file');
            $import = new PhoneNumberImport();
            Excel::import($import, $file);
            $phoneNumberIndex = $request->input('phone_number_index');
            $specificValue = $import->getColumnValues($phoneNumberIndex);
            if ($specificValue === null) {
                return response()->json(['error' => 'Invalid index or no value found'], 400);
            }

        }

        // make a string of all the numbers
        $numbers_string = implode(',', $specificValue->toArray());
        $numbers = explode(',', str_replace(' ', '', $numbers_string));

        $batch_no = 'BN-' . auth()->id() . '-' . generateAlpha(3) . generateAlpha(3);
        $count = 0;

        $campaign = new Campaign();
        $campaign->batch_no = $batch_no;
        $campaign->name = $request->campaign_name;
        $campaign->status = 2;
        $campaign->module = 'Contact';
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        $campaign->created_by = auth()->id();
        $campaign->save();

        foreach ($numbers as $number) {
            $original = $number;
            if (empty($number) || !is_numeric($number)) {
                continue;
            }

            if (substr($number, 0, 1) == '0') {
                $number = substr($number, 1);
            }

            // if (strlen($number) < 9) {
            //     $number = '971' . $number;
            // }

            if (substr($number, 0, 1) == '+') {
                $number = substr($number, 1);
            }

            $formatted_number = validatePhoneNumber($number);

            if (substr($formatted_number, 0, 7) == '971971') {
                $formatted_number = substr($formatted_number, 3);
            }

            // dd(strlen($number),$number,$formatted_number);
            if (format_and_validate_number_check($formatted_number) == 'FALSE') {
                continue;
            }

            $formatted_number = str_replace('+', '', $formatted_number);

            if (substr($formatted_number, 0, 3) != '971') {
                // info('Invalid Number: ' . $formatted_number);
                continue;
            }

            $bulkMessage = new BulkMessage();
            $bulkMessage->campaign_id = $campaign->id;

            $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
            $short_url = urlShortner(route('public.smslinkClicked', $slug));
            // $extra_content = "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
            // $extra_content .= urlShortner(route('public.smslinkClicked', $short_url));

            if ($request->send_checking_link) {
                $message = $request->message . ' ' . $short_url;
            } else {
                $message = $request->message . ' ' . $request->custom_link;
            }

            $bulkMessage->agent_id = auth()->id();
            $bulkMessage->batch_no = $batch_no;
            $bulkMessage->short_url = $slug;
            $bulkMessage->contact_id = null;
            $bulkMessage->custom_link = $request->custom_link;
            $bulkMessage->full_name = 'Bulk SMS';
            $bulkMessage->mobile = $formatted_number;
            $bulkMessage->message = $message;
            $bulkMessage->type = 'sms';
            $bulkMessage->status = 2;
            $bulkMessage->created_by_id = auth()->id();
            $bulkMessage->save();
            $count++;
            // info($short_url);
        }

        // dispatch(new ProcessBulkSms());
        $result = array('status' => '1', 'msg' => 'Bulk SMS Sent to ' . $count . '.', 'data' => []);
        return $result;

    }

    private function manualEnteredNumber($request)
    {

        $numbers = explode(',', str_replace(' ', '', $request->input));
        $batch_no = 'BN-' . auth()->id() . '-' . generateAlpha(3) . generateAlpha(3);
        $count = 0;

        $campaign = new Campaign();
        $campaign->batch_no = $batch_no;
        $campaign->name = $request->campaign_name;
        $campaign->status = 2;
        $campaign->module = 'Contact';
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        $campaign->created_by = auth()->id();
        $campaign->save();

        foreach ($numbers as $number) {
            $original = $number;
            if (empty($number) || !is_numeric($number)) {
                continue;
            }

            if (substr($number, 0, 1) == '0') {
                $number = substr($number, 1);
            }

            // if (strlen($number) < 9) {
            //     $number = '971' . $number;
            // }

            if (substr($number, 0, 1) == '+') {
                $number = substr($number, 1);
            }

            $formatted_number = validatePhoneNumber($number);

            if (substr($formatted_number, 0, 7) == '971971') {
                $formatted_number = substr($formatted_number, 3);
            }

            // dd(strlen($number),$number,$formatted_number);
            if (format_and_validate_number_check($formatted_number) == 'FALSE') {
                continue;
            }

            $formatted_number = str_replace('+', '', $formatted_number);
            $bulkMessage = new BulkMessage();
            $bulkMessage->campaign_id = $campaign->id;

            $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
            $short_url = urlShortner(route('public.smslinkClicked', $slug));
            // $extra_content = "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
            // $extra_content .= urlShortner(route('public.smslinkClicked', $short_url));
            if ($request->send_checking_link) {
                $message = $request->message . ' ' . $short_url;
            } else {
                $message = $request->message . ' ' . $request->custom_link;
            }

            $bulkMessage->agent_id = auth()->id();
            $bulkMessage->batch_no = $batch_no;
            $bulkMessage->short_url = $slug;
            $bulkMessage->contact_id = null;
            $bulkMessage->custom_link = $request->custom_link;
            $bulkMessage->full_name = 'Bulk SMS';
            $bulkMessage->mobile = $formatted_number;
            $bulkMessage->message = $message;
            $bulkMessage->type = 'sms';
            $bulkMessage->status = 2;
            $bulkMessage->created_by_id = auth()->id();
            $bulkMessage->save();
            $count++;
            info($short_url);
        }

        // dispatch(new ProcessBulkSms());
        $result = array('status' => '1', 'msg' => 'Bulk SMS Sent to ' . $count . '.', 'data' => []);
        return $result;

    }

    public function sendMsg(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $q = Contact::query();
        $contacts = $this->applyContactFilters($q, $request)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhereHas('contactAgents', function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    });
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi"), function ($q) {
                $q->where('region_id', 2);
            })->get();
        $batch_no = 'BN-' . auth()->id() . '-' . date('YmdHis');

        $campaign = new Campaign();
        $campaign->batch_no = $batch_no;
        $campaign->name = $request->campaign_name;
        $campaign->status = 2;
        $campaign->module = 'Contact';
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        $campaign->created_by = auth()->id();
        $campaign->save();

        foreach ($contacts as $contact) {
            if ($contact->status == 1 && $contact->mobile != null && $contact->mobile != '') {

                $contactName = $contact->first_name . ' ' . $contact->last_name;
                $requestMessage = $request->message;

                $pattern = '/\{\{\s*name\s*\}\}/i';
                $requestMessage = preg_replace($pattern, $contactName, $requestMessage);

                $formatted_number = validatePhoneNumber($contact->mobile);
                if (format_and_validate_number_check($formatted_number) == 'FALSE') {
                    continue;
                }

                if (checkBlocklist($formatted_number, 'Contact', $request)) {
                    continue;
                }
                $formatted_number = str_replace('+', '', $formatted_number);

                $bulkMessage = new BulkMessage();
                $bulkMessage->campaign_id = $campaign->id;
                $extra_content = ' ';
                $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
                $short_url = urlShortner(route('public.smslinkClicked', $slug));

                // $extra_content .= "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
                $extra_content .= $short_url;

                if ($request->send_checking_link) {
                    $message = $requestMessage . ' ' . $extra_content;
                } else {
                    $message = $requestMessage . ' ' . $request->custom_link;
                }
                $bulkMessage->agent_id = $contact->agent_id ?? null;
                $bulkMessage->contact_id = $contact->id;
                $bulkMessage->custom_link = $request->custom_link;
                $bulkMessage->short_url = $slug;
                $bulkMessage->full_name = $contact->first_name . ' ' . $contact->last_name;
                $bulkMessage->mobile = $formatted_number;
                $bulkMessage->batch_no = $batch_no;
                $bulkMessage->message = $message;
                $bulkMessage->type = 'sms';
                $bulkMessage->status = 2;
                $bulkMessage->created_by_id = auth()->id();
                $bulkMessage->save();
            }
        }

        dispatch(new ProcessBulkSms());
        $result = array('status' => '1', 'msg' => 'Bulk SMS Sent.', 'data' => []);
        return response()->json($result);

    }

    public function sendMsgListingLead(Request $request)
    {
        if (auth()->user()->parent_id == 32 || auth()->user()->id == 32) {
            abort(403);
        }

        $q = ListingLead::query();
        $contacts = $this->applyLeadPoolFilters($q, $request)->get();

        $batch_no = 'BN-' . auth()->id() . '-' . date('YmdHis');
        $campaign = new Campaign();
        $campaign->batch_no = $batch_no;
        $campaign->name = $request->campaign_name;
        $campaign->status = 2;
        $campaign->module = 'ListingLead';
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        $campaign->created_by = auth()->id();
        $campaign->save();

        foreach ($contacts as $contact) {
            $contactName = $contact->first_name . ' ' . $contact->last_name;
            $requestMessage = $request->message;

            // Pattern to match {{ name }} or {{name}}
            $pattern = '/\{\{\s*name\s*\}\}/i';
            $requestMessage = preg_replace($pattern, $contactName, $requestMessage);

            if ($contact->mobile != null && $contact->mobile != '') {

                $formatted_number = validatePhoneNumber($contact->mobile);
                if (format_and_validate_number_check($formatted_number) == 'FALSE') {
                    continue;
                }

                if (checkBlocklist($formatted_number, 'ListigLead', $request)) {
                    continue;
                }
                $formatted_number = str_replace('+', '', $formatted_number);

                $bulkMessage = new BulkMessage();
                $bulkMessage->campaign_id = $campaign->id;
                $extra_content = ' ';
                $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
                $short_url = urlShortner(route('public.smslinkClicked', $slug));

                // $extra_content .= "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
                $extra_content .= $short_url;

                if ($request->send_checking_link) {
                    $message = $requestMessage . ' ' . $extra_content;
                } else {
                    $message = $requestMessage . ' ' . $request->custom_link;
                }
                $bulkMessage->agent_id = $contact->agent_id ?? null;
                $bulkMessage->contact_id = $contact->id;
                $bulkMessage->custom_link = $request->custom_link;
                $bulkMessage->short_url = $slug;
                $bulkMessage->full_name = $contact->first_name . ' ' . $contact->last_name;
                $bulkMessage->mobile = $formatted_number;
                $bulkMessage->batch_no = $batch_no;
                $bulkMessage->message = $message;
                $bulkMessage->type = 'sms';
                $bulkMessage->status = 2;
                $bulkMessage->created_by_id = auth()->id();
                $bulkMessage->save();
            }
        }

        dispatch(new ProcessBulkSms());
        $result = array('status' => '1', 'msg' => 'Bulk SMS Sent.', 'data' => []);
        return response()->json($result);

    }
    public function sendMsgLead(Request $request)
    {
        if (auth()->user()->parent_id == 32 || auth()->user()->id == 32) {
            abort(403);
        }

        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if (auth()->user()->hasRole(['Telesales'])) {
            $query = Lead::query()->where('telesales_id', auth()->id())->whereNull('agent_id');

        } else if (auth()->user()->hasRole(['Telesales Head'])) {
            $query = Lead::query()->whereNull('agent_id');
        } else {
            $query = Lead::query();
        }
        if (!$request->inactive_agents) {
            $query = $query->where(function ($q) {
                $q->whereNull('agent_id')->orWhereHas('agent', function ($q) {
                    $q->where('status', 1);
                });
            });
        }

        $contacts = $this->applyLeadFilters($query, $request)
            ->when($notAdmin && (!auth()->user()->hasPermissionTo("Lead Pool List")), function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->when($notAdmin && auth()->user()->hasPermissionTo("Lead Pool List") && $request->all_listings == "false", function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds)
                        ->whereNull('converted_to_listing_lead');
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where('region_id', 2);
            })
            ->when($request->all_listings == "false", function ($q) {
                $q->whereNotNull('agent_id');
            })->get();

        $batch_no = 'BN-' . auth()->id() . '-' . date('YmdHis');
        $campaign = new Campaign();
        $campaign->batch_no = $batch_no;
        $campaign->name = $request->campaign_name;
        $campaign->status = 2;
        $campaign->module = 'Lead';
        $campaign->custom_link = $request->custom_link;
        $campaign->message = $request->message;
        $campaign->created_by = auth()->id();
        $campaign->save();

        foreach ($contacts as $contact) {
            $contactName = $contact->first_name . ' ' . $contact->last_name;
            $requestMessage = $request->message;

            // Pattern to match {{ name }} or {{name}}
            $pattern = '/\{\{\s*name\s*\}\}/i';
            $requestMessage = preg_replace($pattern, $contactName, $requestMessage);

            if ($contact->mobile != null && $contact->mobile != '') {

                $formatted_number = validatePhoneNumber($contact->mobile);
                if (format_and_validate_number_check($formatted_number) == 'FALSE') {
                    continue;
                }

                if (checkBlocklist($formatted_number, 'ListigLead', $request)) {
                    continue;
                }
                $formatted_number = str_replace('+', '', $formatted_number);

                $bulkMessage = new BulkMessage();
                $bulkMessage->campaign_id = $campaign->id;
                $extra_content = ' ';
                $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
                $short_url = urlShortner(route('public.smslinkClicked', $slug));

                // $extra_content .= "\n\nClick the link below to choose your preferred communication method for our agent to contact you: ";
                $extra_content .= $short_url;

                if ($request->send_checking_link) {
                    $message = $requestMessage . ' ' . $extra_content;
                } else {
                    $message = $requestMessage . ' ' . $request->custom_link;
                }

                $bulkMessage->agent_id = $contact->agent_id ?? null;
                $bulkMessage->contact_id = $contact->id;
                $bulkMessage->custom_link = $request->custom_link;
                $bulkMessage->short_url = $slug;
                $bulkMessage->full_name = $contact->first_name . ' ' . $contact->last_name;
                $bulkMessage->mobile = $formatted_number;
                $bulkMessage->batch_no = $batch_no;
                $bulkMessage->message = $message;
                $bulkMessage->type = 'sms';
                $bulkMessage->status = 2;
                $bulkMessage->created_by_id = auth()->id();
                $bulkMessage->save();
            }
        }

        dispatch(new ProcessBulkSms());
        $result = array('status' => '1', 'msg' => 'Bulk SMS Sent.', 'data' => []);
        return response()->json($result);

    }

    public function excelImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        $file = $request->file('file');
        $import = new PhoneNumberImportHeader();

        Excel::import($import, $file);

        $headers = $import->headers;
        return view('admin.bulk_sms.includes._headers')
            ->with('headers', $headers);
    }

}
