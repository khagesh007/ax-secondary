<?php

namespace App\Http\Controllers\Api\V1\Notification;

use App\Http\Controllers\Api\BaseController;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\MobileNotification;
use App\Models\Property;
use App\Models\User;
use App\Models\UserFcm;
use Illuminate\Http\Request;
use Validator;

class NotificationApiController extends BaseController
{

    /**
     *
     *
     *
     * Notification
     * @group Mobile Notification
     * @authenticated
     * @header Accept application/json
     * @bodyParam user_id requried for user id. Example: 12
     * @bodyParam title requried for title. Example: This is a test notifcation
     * @bodyParam message requried for message. Example: message
     * @bodyParam flag requried for flag.
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "multicast_id": 840949711885656629,
    "success": 1,
    "failure": 0,
    "canonical_ids": 0,
    "results": [
    {
    "message_id": "0:1722331008558326%af539ff3af539ff3"
    }
    ]
    }


     */

    public function checkNotification(Request $request)
    {
        $user_id = $request->user_id;
        $title = $request->title;
        $message = $request->message;
        $flag = $request->flag ?? 'Test';
        $model = $request->model ?? 'Property';

        $agent = User::find($user_id);

        $user_id = $agent->id;
        $fcm = UserFcm::where('user_id', $user_id)->first();
        if ($fcm) {

            $deviceToken = $fcm->fcm_token;

            $flag = 'New Property Added';
            $model = 'Property';
            if ($model == 'Property') {
                $listing = Property::where('publish_status', 'PUBLISHED')->latest()->first();
                $listingID = $listing->ref_no;
                $title = 'New Property Added';
                $message = 'New Property Added with Ref# ' . $listing->ref_no;

            } elseif ($model == 'Lead') {
                $listing = Lead::latest()->first();
                $listingID = $listing->id;
                $title = 'New Lead Added';
                $message = 'New Lead Added with id# ' . $listing->id;
            } elseif ($model == 'ListingLead') {
                $listing = ListingLead::latest()->first();
                $listingID = $listing->id;
                $title = 'New ListingLead Added';
                $message = 'New ListingLead Added with id# ' . $listing->id;
            } elseif ($model == 'Contact') {
                $listing = Contact::latest()->first();
                $listingID = $listing->id;
                $title = 'New Contact Added';
                $message = 'New Contact Added with id# ' . $listing->id;
            } else {
                $listingID = null;
                $title = 'Without Model';
                $message = 'Provide Model';
            }

            $data = [
                'title' => (string) $title,
                'message' => (string) $message,
                'type' => "Test",
                'user_id' => (string) $user_id, // Convert to string if it's not already
                'flag' => (string) $flag, // Convert to string if it's not already
                'model' => (string) $model, // Convert to string if it's not already
                'model_id' => (string) $listingID, // Convert to string if it's not already
            ];

            $notification = [
                'title' => $title,
                'body' => $message,
            ];

            $payload = [
                'message' => [
                    'token' => $deviceToken, // FCM registration token
                    'notification' => $notification, // Notification payload (title and body)
                    'data' => $data, // Custom data payload
                ],
            ];

            $function = sendNotificationToMobile($payload, $flag = "Test");
            return response()->json($function);
        }
    }

    public function _checkNotification(Request $request)
    {
        $user_id = $request->user_id;
        $title = $request->title;
        $message = $request->message;
        $flag = $request->flag ?? 'Test';
        $model = $request->model ?? 'Property';

        if ($model == 'Property') {
            $listing = Property::where('publish_status', 'PUBLISHED')->latest()->first();
            $listingID = $listing->ref_no;

        } elseif ($model == 'Lead') {
            $listing = Lead::latest()->first();
            $listingID = $listing->id;

        } elseif ($model == 'ListingLead') {
            $listing = ListingLead::latest()->first();
            $listingID = $listing->id;
        } elseif ($model == 'Contact') {
            $listing = Contact::latest()->first();
            $listingID = $listing->id;
        } else {
            $listingID = null;
        }

        $data = [
            'title' => $title,
            'message' => $message,
            'type' => "Test",
            'user_id' => $user_id,
            'flag' => $flag,
            'model' => $model,
            'model_id' => $listing->id,
        ];

        $function = sendNotificationToMobile($user_id, $title, $message, $data, $flag = "Test");
        return response()->json($function);
    }

    /**
     *
     *
     *
     * Notification
     * @group Mobile Notification
     * @authenticated
     * @header Accept application/json
     * @bodyParam token string requried for finding user. Example: 12

    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 2,
    "user_id": 1692,
    "token": "crGUegySQ4u0wjsdCH9trL:APA91bGJX9sH3KpGrqCMvuoM8cWodyYoqdZ7lNJA8XcF4WJ7F0rLam9J6iAigNx4v7korU31CJMwnSeY-4vhuK09LMDAXXRavPbRhhj5UxLR-nVEuMEy4XmRSLnlmXQHfDTydw2UTtLE",
    "title": "Test Notification",
    "body": "Test Notification Body",
    "payload": "{\"message\":{\"token\":\"crGUegySQ4u0wjsdCH9trL:APA91bGJX9sH3KpGrqCMvuoM8cWodyYoqdZ7lNJA8XcF4WJ7F0rLam9J6iAigNx4v7korU31CJMwnSeY-4vhuK09LMDAXXRavPbRhhj5UxLR-nVEuMEy4XmRSLnlmXQHfDTydw2UTtLE\",\"notification\":{\"title\":\"Test Notification\",\"body\":\"Test Notification Body\"},\"data\":{\"title\":\"Title\",\"message\":\"Message\",\"type\":\"Test\",\"user_id\":\"172\",\"flag\":\"Flag\",\"model\":\"Property\",\"model_id\":\"axc-12345\"}}}",
    "created_at": "2024-09-17T09:08:01.000000Z",
    "updated_at": "2024-09-17T09:08:01.000000Z"
    },
    {
    "id": 1,
    "user_id": 1692,
    "token": "crGUegySQ4u0wjsdCH9trL:APA91bGJX9sH3KpGrqCMvuoM8cWodyYoqdZ7lNJA8XcF4WJ7F0rLam9J6iAigNx4v7korU31CJMwnSeY-4vhuK09LMDAXXRavPbRhhj5UxLR-nVEuMEy4XmRSLnlmXQHfDTydw2UTtLE",
    "title": "Test Notification",
    "body": "Test Notification Body",
    "payload": "{\"message\":{\"token\":\"crGUegySQ4u0wjsdCH9trL:APA91bGJX9sH3KpGrqCMvuoM8cWodyYoqdZ7lNJA8XcF4WJ7F0rLam9J6iAigNx4v7korU31CJMwnSeY-4vhuK09LMDAXXRavPbRhhj5UxLR-nVEuMEy4XmRSLnlmXQHfDTydw2UTtLE\",\"notification\":{\"title\":\"Test Notification\",\"body\":\"Test Notification Body\"},\"data\":{\"title\":\"Title\",\"message\":\"Message\",\"type\":\"Test\",\"user_id\":\"172\",\"flag\":\"Flag\",\"model\":\"Property\",\"model_id\":\"axc-12345\"}}}",
    "created_at": "2024-09-17T09:00:27.000000Z",
    "updated_at": "2024-09-17T09:00:27.000000Z"
    }
    ],
    "page": 1,
    "per_page": 10
    }


     */

    public function getAllNotifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $notifications = MobileNotification::where('token', $request->token)->orderBy('id', 'desc')->get();
        return $this->sendResponse(true, 'Data found', $notifications, 200);

    }
}
