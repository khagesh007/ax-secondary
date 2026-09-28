<?php

namespace App\Http\Controllers\Api\V1\Parameters;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryApiController extends Controller
{
    public function getCountries(Request $request)
    {
        $countries = Country::select('id', 'code', 'name', 'nationality', 'phonecode')->get();
        if (!empty($countries)) {
            return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $countries]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Data Not Found']);
        }
    }
    /**
     *
     *
     * Country Nationalities.
     *
     * Get Country Nationalities.
     * @group Parameters
     * @authenticated
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
     * {
    {
    "status": true,
    "code": 200,
    "message": "2 Record(s) found",
    "data": [
    {
    "id": 1,
    "name": "Open",
    "status": 1,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-02-14T03:30:32.000000Z",
    "updated_at": "2023-02-14T03:30:32.000000Z",
    "deleted_at": null,
    "order": 0,
    "thumbnail": null,
    "image": null,
    "icon": null,
    "badge": null
    },
    {
    "id": 2,
    "name": "Closed",
    "status": 1,
    "created_by_id": null,
    "updated_by_id": null,
    "created_at": "2023-02-14T03:30:32.000000Z",
    "updated_at": "2023-02-14T03:30:32.000000Z",
    "deleted_at": null,
    "order": 0,
    "thumbnail": null,
    "image": null,
    "icon": null,
    "badge": null
    },
    ]
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="User not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Data found",
    "data": []
    }
     */

    public function getNationalities(Request $request)
    {
        $countries = Country::select('id', 'code', 'name', 'nationality', 'phonecode')->get();
        if (!empty($countries)) {
            return response()->json(['status' => '1', 'msg' => 'Data Found', 'data' => $countries]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Data Not Found']);
        }
    }

}
