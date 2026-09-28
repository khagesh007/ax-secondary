<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    public function sendResponse($status,$message,$data,$code,$page = 1, $per_page = 10, $requestData = []) :JsonResponse
    {
        return response()->json([
            'status' => $status,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'page' => $page,
            'per_page' => $per_page,
            'request_data' => $requestData
        ],$code);
    }
    public function sendPaginationResponse($data, $message = null, $page = 1, $perPage = 10, $title = null, $totalRecords = null)
    {
        $response = [
            'status' => 200,
            'title' => $title ?? trans('messages.Success'),
            'data'    => $data,
            'message' => $message ?? trans('messages.Retrieved Successfully'),
            'page' => $page,
            'per_page' => $perPage,
            'total_records' => $totalRecords ?? $data->total(),
        ];

        return response()->json($response, 200);
    }
}
