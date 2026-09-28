<?php

namespace App\Http\Controllers\Api\V1\Deals;

use App\Http\Controllers\Api\BaseController;
use App\Models\DealInvite;
use Illuminate\Http\Request;

class DealInvitationApiController extends BaseController
{
    public function create(Request $request)
    {
        $request->validate([
            'deal_id' => 'required',
            'agent_id' => 'required',
        ]);


        $old_invite = DealInvite::where('deal_id', $request->deal_id)->where('invited_to_id', $request->agent_id)->first();

        if ($old_invite) {
            return $this->sendResponse(true, 'Deal invite already sent to agent ref['.$request->agent_id.']', $old_invite->invite_code, 200);
        }

        $dealInvite = new DealInvite();
        $dealInvite->deal_id = $request->deal_id;
        $dealInvite->invite_code = generateAlpha(15);
        $dealInvite->invited_to_id = $request->agent_id;
        $dealInvite->invited_by_id = auth()->user()->id;
        $dealInvite->save();

        event(new \App\Events\DealInviteAgent($dealInvite));
        // $environment = \App::environment();
        //     if ($environment == "production") {
        //         $message = "<b>AX Secondary CRM</b>\n\n";
        //         $message .= "\n<b> Property Details: </b> ";


        //         $agent = User::where("status", true)->get();

        //             if ($agent->telegram_chatid != null) {
        //                 $url = route('collab.deals.index',$dealInvite->invite_code); //  this will be url /admin/deals-revamped/'.$dealInvite->invite_code.'?deal_id=14#step-3
        //                 $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], $url, 'Add Deal Information', 'lead_bot');
        //             }

        //     }

        return $this->sendResponse(true, 'Deal Invite Sent', $dealInvite->invite_code, 200);
    }
}
