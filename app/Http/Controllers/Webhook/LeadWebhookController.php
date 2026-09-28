<?php

namespace App\Http\Controllers\Webhook;

use App\Events\PortalsLeadsNotification;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\TempDump;
use App\Models\User;
use App\Traits\TelegramTrait;
use Crypt;
use Illuminate\Http\Request;
use Log;

class LeadWebhookController extends Controller
{
    use TelegramTrait;

    public function handle(Request $request)
    {
        // dd($request->all());
        $dump = new TempDump();
        $dump->data = json_encode($request->all());
        $dump->name = 'MARKETING_LEAD';
        $dump->status = '1';
        $dump->save();
        info('Webhook received', ['data' => $request->all()]);
        // return response()->json(['message' => 'Invalid key'], 401);

        if ($request->key == '35ezjjulUhM8Ff39IB08Qzo31hs3p6zI') {
            $data = $request->all();
            $body = $request->Body;
            $whatsapp = $request->From;
            $profile_name = $request->ProfileName;
            $message = "";
            preg_match('/sec-(\d+)/i', $body, $matches);

            if (isset($matches[1])) {
                $number = $matches[1];
                $agent_id = $number;
            } else {
                $agent_id = null;
            }

            preg_match('/whatsapp:\+(\d+)/', $whatsapp, $matches);
            if (isset($matches[1])) {
                $phoneNumber = '+' . $matches[1];
                $whatsapp = $phoneNumber;
            }

            try {
                // $user_by_profile_name = User::where(DB::raw("CONCAT(first_name, ' ', last_name)"), '=', $profile_name)->first();
                // if ($user_by_profile_name) {
                //     $agent_id = $user_by_profile_name->id;
                // } else {
                //     $agent_id = null;
                // }

                $agent = User::where('id', $agent_id)->where('status', true)->first();
                if ($agent) {
                    $notification = false;
                    $agent_id = $agent->id;
                } else {
                    $notification = true;
                    $agent_id = null;
                }

                if (strpos($profile_name, ' ') !== false) {
                    $profile_name = explode(' ', $profile_name);
                    $first_name = $profile_name[0];
                    $last_name = $profile_name[1];
                } else {
                    $first_name = $profile_name;
                    $last_name = null;
                }
                // dd($is_archive);
                $is_archive = endsWithNumberInBrackets($body);
                if ($is_archive == true) {
                    $lead = new Lead();

                    if ($is_archive == false) {
                        $lead->is_archive = 1;
                    } else {
                        $lead->is_archive = 0;
                        $notification = false;
                    }
                    $lead->first_name = $first_name;
                    $lead->last_name = $last_name;
                    $lead->mobile = $phoneNumber;
                    $lead->whatsapp = $phoneNumber;
                    $lead->agent_id = $agent_id;
                    $lead->message = $body;
                    $lead->lead_status_id = 1;
                    $lead->source_id = 14;
                    $lead->lead_medium_id = 4;
                    $lead->utm_content = json_encode($data);
                    $lead->save();
                    $obj = new LeadNote();
                    $obj->lead_id = $lead->id;
                    $obj->note = "A lead has been recorded through a marketing campaign created by Tosin.";
                    $obj->save();

                    info('Webhook received and Processed', ['lead' => $lead]);

                    $environment = env('APP_ENV');
                    // $environment = 'production';
                    if ($environment == "production" && $notification == true) {
                        $message = "<b>AX Secondary CRM</b>\nA lead has been recorded through a marketing campaign created by Tosin.l Kindly assign it to an agent.\n";
                        $message .= "\n<b> Lead Details: </b> ";
                        $message .= "\n<b> ID: </b> " . $lead->id ?? '';
                        $message .= "\n<b> Source: </b> " . $lead->leadSource->name ?? '';
                        $message .= "\n<b> Medium: </b> " . $lead->leadMedium->name ?? '';
                        $message .= "\n<b> Message: </b> " . $lead->message ?? '';

                        $message .= "\n\n<b> Lead Details: </b> ";
                        ($lead->first_name != " " ? $message .= "\n<b> Name: </b> " . $lead?->first_name . ' ' . $lead?->last_name : null);
                        ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                        ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);

                        $agents = User::whereHas("roles", function ($q) {
                            $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
                        })->where("status", true)->get();
                        foreach ($agents as $agent) {
                            if ($agent->telegram_chatid != null && $lead->agent_id == null) {

                                $url = route('api.thirdparty.leadAssignment', [Crypt::encryptString($lead->id), Crypt::encryptString($agent->id)]);
                                Log::channel('urlLogs')->info('URL lead: Manager:' . $agent->full_name, ['id' => $lead->id, 'data' => $url]);
                                info($url);
                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Assign Lead', 'lead_bot');
                                telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);

                            }
                        }
                    }

                    if ($environment == "production" && $notification == false && $lead->agent_id != null) {
                        $agent = User::whereHas("roles", function ($q) {
                            $q->whereIn("name", ["Agent"]);
                        })->where('id', $lead->agent_id)->where("status", true)->first();
                        if ($agent) {
                            $message = "<b>AX Secondary CRM</b>\n\nA new <b>Lead</b> [" . $lead?->id . "] has been recorded.\n";
                            $message .= "\n<b> Lead Details: </b> ";
                            $message .= "\n<b> ID: </b> " . $lead->id ?? '';
                            $message .= "\n<b> Source: </b> " . $lead->leadSource->name ?? '';
                            $message .= "\n<b> Medium: </b> " . $lead->leadMedium->name ?? '';
                            $message .= "\n<b> Message: </b> " . $lead->message ?? '';

                            $message .= "\n\n<b> Lead Details: </b> ";
                            ($lead->first_name != " " ? $message .= "\n<b> Name: </b> " . $lead?->first_name . ' ' . $lead?->last_name : null);
                            ($lead->mobile ? $message .= "\n<b> Mobile: </b> " . $lead?->mobile : null);
                            ($lead->email ? $message .= "\n<b> Email: </b> " . $lead?->email : null);
                            if ($agent->telegram_chatid != null) {
                                $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
                            }
                            telegramNotification('Lead', $lead->id, $agent->telegram_chatid, $agent->id, $message);

                        }

                    }

                    if ($lead->agent_id) {
                        $user = User::where('id', $lead->agent_id)->first();
                        if ($user) {
                            event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));
                        }
                    }
                    return response()->json(['message' => 'Webhook received and being processed'], 200);
                } else {
                    info('Webhook received and being processed but Lead not Created');
                    return response()->json(['message' => 'Webhook received and being processed but Lead not Created'], 200);
                }

            } catch (\Throwable $th) {
                return response()->json(['message' => 'Webhook received but failed to process', 'error' => $th->getMessage()], 500);
            }
        } else {
            return response()->json(['message' => 'Invalid key'], 401);
        }

    }

}
