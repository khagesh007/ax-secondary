<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendPaginationResponse($data, $message = null, $page = 1, $perPage = 10, $title = null, $totalRecords = null)
    {

        // $page = $page ?? (method_exists($data, 'currentPage') ? $data->currentPage() : 1);
        // $perPage = $perPage ?? (method_exists($data, 'perPage') ? $data->perPage() : 10);

        $response = [
            'status' => Response::HTTP_OK,
            'title' => $title ?? 'Success!',
            'data'    => $data,
            'message' => $message ?? trans('messages.Retrieved Successfully'),
            'page' => $page,
            'itemsPerPage' => $perPage,
            'totalRecords' => $totalRecords ?? $data->total(),
        ];

        return response()->json($response, Response::HTTP_OK);
    }
    public function sendDataResponse($data, $message = null, $title = null, $status = Response::HTTP_OK)
    {
        $response = [
            'status' => $status,
            'title' => $title ?? 'Success!',
            'message' => $message ?? trans('messages.Retrieved Successfully'),
            'data'    => $data,
        ];

        return response()->json($response, $status);
    }
    public function sendException($title = null, $message = null, $error = null, $status = 500, $line = null, $file = null, $sendMessage = true)
    {
        $response = [
            'status' => $status,
            'title'    => $title ?? trans('errors.Error'),
            'message' => $message ?? trans('errors.Something Went Wrong'),
            'error' => $error,
            'line' => $line,
            'file' => $file
        ];
        // if($sendMessage)
        // {
        //     $this->sendExceptionMessage($response);
        // }

        return response()->json($response, $status);
    }
}
