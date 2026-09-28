<?php

namespace App\Http\Controllers\Api\V1\Group;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GroupController extends BaseController
{
    public function getGroups(Request $request)
    {
        if (!$request->secret) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        if ($request->secret != env('AXDEAL_SECRET_KEY')) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }
        try {
            $groups = Group::with('user')->select(['id','name', 'parent_id'])->get();
            return $this->sendResponse(true, 'Group List', $groups, 200);
        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', ['error' => $exception->getMessage(),
                'line' => $exception->getLine(),
                'file' => $exception->getFile()
            ], 500);
        }
    }
    public function updateUser(Request $request, $id)
    {
        Log::channel('bitrix_alert')->info("secondary User updateUser for {$id}");

        if (!$request->secret || $request->secret !== env('AXDEAL_SECRET_KEY')) {
            return $this->sendResponse(false, 'You are not authorized', [], 403);
        }

        try {
            $user = User::find($id);

            if (!$user) {
                return $this->sendResponse(false, 'User not found', [], 404);
            }
            Log::channel('bitrix_alert')->info("secondary User updateUser for {$user->id}");

            $is_manager = $user->children()->exists() ? 1 : 0;

            $roles = $user->getRoleNames();
            $roles_v2 = $roles->toArray();

            $client = new \GuzzleHttp\Client();
            $url = "https://deals.axcrm.ae/backend/api/webhook/secondary/user/sync";

            $data = [
                'name' => $user->first_name . ' ' . $user->last_name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'id' => $user->id,
                'email' => $user->email,
                'secondary_email' => $user->secondary_email,
                'position' => $user->position,
                'password' => $user->password,
                'mobile' => $user->mobile,
                'thumbnail' => $user->thumbnail,
                'image' => $user->image,
                'avatar' => $user->avatar,
                'telegram_username' => $user->telegram_username,
                'telegram_chatid' => $user->telegram_chatid,
                'status' => $user->status,
                'entity_id' => 1,
                'parent_id' => $user->parent_id,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'updated_by_id' => $user->updated_by_id,
                'created_by_id' => $user->created_by_id,
                'deleted_at' => $user->deleted_at,
                'bitrix_role' => $user->bitrix_role,
                'bitrix_id' => $user->bitrix_id,
                'roles' => $roles->implode(', '),
                'roles_v2' => $roles_v2,
                'is_manager' => $is_manager,
                'zone_id' => $user->zone_id,
            ];

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => env('AXDEAL_SECRET_KEY'),
                ],
                'form_params' => $data,
            ]);

            return $this->sendResponse(true, 'User synced successfully', $user, 200);

        } catch (\Exception $e) {
            Log::channel('bitrix_alert')->error(json_encode([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], JSON_PRETTY_PRINT));
            return $this->sendResponse(false, 'Something went wrong.', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

}
