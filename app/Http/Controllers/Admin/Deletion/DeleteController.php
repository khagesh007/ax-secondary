<?php

namespace App\Http\Controllers\Admin\Deletion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

class DeleteController extends Controller
{
    public function delete_general(Request $request)
    {

        $id = $request->input('id');
        $model = $request->input('model');
        try
        {
            $count_relation_data = [];
            $data = $model::find($id);
            $relationships = $data->relationships;

            // $count_relations = $data->with($relationships)->count();
            if ($relationships) {
                foreach ($relationships as $key => $relation) {
                    if ($data->$relation()->count() == 0) {
                        continue;
                    }
                    $count_relation_data[$key] = $data->$relation()->count();
                }

                if (count($count_relation_data) > 0) {
                    return Response::json(['error_related_data' => $count_relation_data, 201]);

                }
            }
            $data->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == "23000") { //23000 is sql code for integrity constraint violation
                // return error to user here
                $message = 'Cannot Delete! Relational Data found';
                return Response::json(['error' => $message, 201]);
            }
        } catch (Exception $e) {
            $message = 'Cannot Delete! An Error occured';
            return Response::json(['error' => $message, 201]);
        }
        $message = 'Deleted Successfully';
        return Response::json(['success' => $message, 200]);
    }

    public function delete_listing_hard(Request $request)
    {
        // dd($request->all());
        // if (\Auth::user()->hasPermissionTo('Delete Rent listing'))
        // {
        $model = $request->model;
        request()->validate(['data_id' => 'required']);
        $data_id = $request->get('data_id');
        $data = $model::where('id', $data_id)->first();
        $relationships = $data->relationships;

        if ($relationships) {
            foreach ($relationships as $key => $relation) {
                if ($relation == 'images') {
                    if ($model == 'App\Models\Property') {
                        $property = Property::find($data_id);
                        $media = PropertyMedia::where('property_id', $property->id)->get();
                            if ($media) {
                                // if (env('APP_ENV') == "production") {
                                //     $this->removePhotoFromBucket($request->media_id);
                                // }
                                $media->delete();
                            }
                    }
                } else {
                    foreach($data->$relation() as $relation_data)
                    {
                        $relation_data->detach();
                    }

                }

            }
        }
        $data->delete();
        return Response::json(['message' => 'Data deleted successfully'], 200);

        // }
        // else
        // {
        //     abort(403, 'User does not have the right permissions.');
        // }

    }

    public function deleteGeneral(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'model' => 'required|string',
            'key' => 'required|string',
        ]);

        try {
            $model = $request->input('model');
            $key = $request->input('key');

            if ($key != 'image' or $key != 'thumbnail') {
                $result = array('status' => '0', 'msg' => 'Unauthorised access');
            }

            $source = $model::find($request->id);
            $source->$key = null;
            if ($source->save()) {
                $result = array('status' => '1', 'msg' => $request->key . ' Deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
            return json_encode($result);
        } catch (\Throwable $th) {
            throw $th;
        }

    }

}
