<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\UserThirdPartyData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateUserMetaOverview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:process-third-party-data {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected function processPfData($agentId, $start_date,$end_date,$month) : void
    {
        try {
            $pf_data = UserMeta::where('user_id', $agentId)->where('meta_key', 'like', '%pf_%')
                ->whereMonth('updated_at', date('m', strtotime($month)))
                ->pluck('meta_value', 'meta_key');
            $user_third_party_data = UserThirdPartyData::where('user_id', $agentId)
                ->where('portal', 'property_finder')
                ->where('start_date', $start_date)
                ->first();
            if ($user_third_party_data) {
                $user_third_party_data->dump_meta = json_encode($pf_data,true);
                $user_third_party_data->save();

            } else {
                $user_third_party_data = new UserThirdPartyData();
                $user_third_party_data->user_id = $agentId;
                $user_third_party_data->portal = 'property_finder';
                $user_third_party_data->dump_meta = json_encode($pf_data,true);
                $user_third_party_data->start_date = $start_date;
                $user_third_party_data->end_date = $end_date;
                $user_third_party_data->save();
            }
        }catch (\Exception $e){
           $this->info('Error on CreateUserMetaOverview - processPfData'.$e->getMessage());
        }
    }
    protected function processBayutData($agentId, $start_date,$end_date,$month) : void
    {
        try {
            $bayut_data = UserMeta::where('user_id', $agentId)
                ->where('meta_key', 'like', 'bayut_%')
                ->whereMonth('updated_at', date('m', strtotime($month)))
                ->pluck('meta_value', 'meta_key');
            $user_third_party_data = UserThirdPartyData::where('user_id', $agentId)
                ->where('portal', 'bayut')
                ->where('start_date', $start_date)
                ->first();
            if ($user_third_party_data) {
                $old_data = json_decode($user_third_party_data->dump_meta);
                if ($old_data instanceof \Illuminate\Support\Collection) {
                    $old_data = $old_data->toArray();
                }else{
                    $old_data = (array)$old_data;
                }
                if ($bayut_data instanceof \Illuminate\Support\Collection) {
                    $bayut_data = $bayut_data->toArray();
                }else{
                    $bayut_data = (array)$bayut_data;
                }
                $bayut_data = array_merge($old_data,$bayut_data);
                $user_third_party_data->dump_meta = json_encode($bayut_data,true);
                $user_third_party_data->save();

            } else {
                $user_third_party_data = new UserThirdPartyData();
                $user_third_party_data->user_id = $agentId;
                $user_third_party_data->portal = 'bayut';
                $user_third_party_data->dump_meta = json_encode($bayut_data,true);
                $user_third_party_data->start_date = $start_date;
                $user_third_party_data->end_date = $end_date;
                $user_third_party_data->save();
            }
        }catch (\Exception $e){
            $this->info('Error on CreateUserMetaOverview - processBayutData'.$e->getMessage());
        }
    }
    protected function processDubizzleData($agentId, $start_date,$end_date,$month) : void
    {
        try {
            $dubizzle_data = UserMeta::where('user_id', $agentId)
                ->where('meta_key', 'like', 'dubizzle_%')
                ->whereMonth('updated_at', date('m', strtotime($month)))
                ->pluck('meta_value', 'meta_key');
            $user_third_party_data = UserThirdPartyData::where('user_id', $agentId)
                ->where('portal', 'dubizzle')
                ->where('start_date', $start_date)
                ->first();

            if ($user_third_party_data) {

                $user_third_party_data->dump_meta = json_encode($dubizzle_data,true);
                $user_third_party_data->save();

            } else {

                $user_third_party_data = new UserThirdPartyData();
                $user_third_party_data->user_id = $agentId;
                $user_third_party_data->portal = 'dubizzle';
                $user_third_party_data->dump_meta = json_encode($dubizzle_data,true);
                $user_third_party_data->start_date = $start_date;
                $user_third_party_data->end_date = $end_date;
                $user_third_party_data->save();
            }
        }catch (\Exception $e){
            $this->info('Error on CreateUserMetaOverview - processBayutData'.$e->getMessage());
        }
    }
    public function handle()
    {
        $date = $this->argument('date');
        $month = $date ?? date('Y-m');
        $start_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->startOfMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::createFromFormat("Y-m", $month)->endOfMonth()->format('Y-m-d');

        $groups = Group::with(["user"])
            ->whereNull("parent_id")
            ->orderBy("order")
            ->get();
        foreach ($groups as $group) {
            $selectedGroup = Group::find($group->id);
            $user = $selectedGroup->user;
            $agentIds = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Agent"]))
               ->where("parent_id", $user->id)
                ->where('status', true)->pluck('id')->toArray();
            foreach ($agentIds as $agentId) {

                $this->processPfData($agentId, $start_date, $end_date, $month);
                $this->processBayutData($agentId, $start_date, $end_date, $month);
                $this->processDubizzleData($agentId, $start_date, $end_date, $month);
            }
        }

            return Command::SUCCESS;
    }

}
