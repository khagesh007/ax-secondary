<?php


namespace App\Http\Tasks\Api\V1\Lead;



use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Requests\Api\V1\Lead\GetLeadRequest;
use App\Http\Resources\LeadResource;
use App\Http\Tasks\Api\BaseTask;
use App\Models\Lead;
use Illuminate\Http\Request;
use Validator;
class LeadShow extends BaseTask
{
    public function run(GetLeadRequest $request)
    {
       // $user = $this->authUser();

        $query = Lead::query();
        $query->where('id',$request->id);
        $query->where('status',1);
        $query->orderBy('id','desc');
        $query->when(!empty($request->search),function($q) use ($request){
            return $q->where('first_name','like','%'.$request->search.'%')
                ->orWhere('last_name','like','%'.$request->search.'%')
                ->orWhere('email','like','%'.$request->search.'%')
                ->orWhere('mobile','like','%'.$request->search.'%');
        });
        $query->with(['criteria','criteria.locations.parentable', 'leadAgents.agent:id,first_name,last_name,email,mobile,image',
            'leadStatus','leadStatus','leadSubStatus','leadType','leadMedium','leadSource','property']);
        $lead = $query->first();
        if($lead == null){
            return $this->sendResponse(false,'Lead not found',[],200);
        }
        $lead = new LeadResource($lead);
        return $this->sendResponse(true,'Lead details',compact('lead'),200);
    }
}
