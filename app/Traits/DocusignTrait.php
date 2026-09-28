<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DocuSignController;
use App\Models\OauthToken;
use Carbon\Carbon;
trait DocusignTrait
{

    public function refreshAccessTokenDocusign()
    {
        $from_db = OauthToken::where('application', 'docusign')->first();
      if(!$from_db){
          return;
      }
        $response = Http::withBasicAuth(env('DOCUSIGN_CLIENT_ID'),
            env('DOCUSIGN_CLIENT_SECRET'))->post('https://account-d.docusign.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' =>$from_db['refresh_token'],
        ]);
        if ($response->status() == 200) {
            $result = $response->json();
            $access_token = $result['access_token']; //its a bearer token it expires in 28800 seconds
            $refresh_token = $result['refresh_token']; //refresh token
            $expires_in = $result['expires_in'];
            $refresh_token_expires_in = '7';  // 7 days
            OauthToken::updateOrCreate(
                ['application' => 'docusign'],
                ['access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => $result['token_type'],
                'meta' => json_encode($result),
                'expires_at' => Carbon::now()->addSeconds($expires_in),
                'refresh_expires_at' => Carbon::now()->addDays($refresh_token_expires_in)
                ]);
        } else {
            echo 'Error in refreshing token';
        }

    }
    public function sendInvitationDocusign($deal,$lead,$invite)
    {
        try{
        $recipientEmail = 'khagesh.office@gmail.com';
        $recipientName = 'khagesh';
       // $documentPath = storage_path('app/public/kyc-entity.pdf');
      //  $documentPath = public_path('document-templates/LEAD_KYC_Form_For_Entity.pdf');
        $documentPath = public_path('document-templates/KYC_Form_Individual_Final.pdf');
     // $documentPath = storage_path('app/public/KYC_Form_Individual_Final.pdf');
        $envelope_id = (new DocuSignController())->createEnvelope($recipientEmail, $recipientName, $documentPath);
            return ['status'=> 1, 'envelope_id' => $envelope_id];
    }catch(Exception $e){
        \Log::info($e->getMessage());
        return ['status'=> 0, 'envelope_id' => ''];

        }
    }
}
