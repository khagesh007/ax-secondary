<?php

namespace App\Console\Commands;

use App\Models\CommissionSlab;
use App\Models\CommissionSlabLimit;
use App\Models\CommissionSlabUser;
use App\Models\Deal;
use App\Models\DealSplitCommission;
use App\Models\User;

use Illuminate\Console\Command;

class commissionCalculations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:commission {deal_id}';

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
    public function handle()
    {
        $dealId = $this->argument('deal_id');
        $deal = Deal::find($dealId);
        $netCommission = 100000;
        $agent = User::with('parent')->find($deal->agent_id);
        $commissions = [];
        $parentHierarchy = getUserParentOnly($deal->agent_id);

        $type = "Direct";
        $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($agent){
            $q->where('user_id', $agent->id);
        })->where('commission_category', $type)->first();

        if(!$agentCommission)
        {
            $role = $agent->roles->first();
            $agentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($role){
                $q->where('role_id', $role->id);
            })->where('commission_category', $type)->first();
            // Calculating Commission Here
            if ($agentCommission->commission_type === 'Percentage') {
                $commissionVal = $netCommission * ($agentCommission->commission_value / 100); // Convert percentage to decimal
            } elseif ($agentCommission->commission_type === 'Fixed') {
                $commissionVal = $agentCommission->commission_value; // Use the fixed value
            } else {
                // Handle other types of commission structures if needed
                $commissionVal = 0; // Default to zero if the type is not recognized
            }

            $remainingAmount = $netCommission - $commissionVal;
            foreach($parentHierarchy as $parent){
                $parentUser = User::find($parent);
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($parentHierarchy){
                    $q->where('user_id', $parentHierarchy);
                })->where('commission_category', 'Indirect')->first();

                if(empty($parentCommission)){

                    $parentRole = $parentUser->roles->first();

                    if($parentRole){

                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($parentRole){
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if($parentCommission){

                            if($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission')
                            {

                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";

                            }
                            elseif($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount')
                            {

                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";

                            }
                            elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission')
                            {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                            }
                            elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount')
                            {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                            }
                            else{
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name] = $parentCommissionVal;
                            }
                        }else{
                             $commissions[$parentRole->name] = 0;
                        }

                    }else{
                        $commissions[$parentRole->name] = 0;
                    }
                }else{

                    if($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission')
                    {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";

                    }
                    elseif($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount')
                    {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";

                    }
                    elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission')
                    {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                    }
                    elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount')
                    {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                    }
                    else{
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name] = $parentCommissionVal;
                    }
                }
            }

        }else{
            // Calculating Commission Here

            if ($agentCommission->commission_type === 'Percentage') {
                $commissionVal = $netCommission * ($agentCommission->commission_value / 100); // Convert percentage to decimal
            } elseif ($agentCommission->commission_type === 'Fixed') {
                $commissionVal = $agentCommission->commission_value; // Use the fixed value
            } else {
                // Handle other types of commission structures if needed
                $commissionVal = 0; // Default to zero if the type is not recognized
            }
            $remainingAmount = $netCommission - $commissionVal;
            foreach($parentHierarchy as $parent){
                $parentUser = User::with("roles:id,name")->find($parent);
                $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($parent){
                    $q->where('user_id', $parent);
                })->where('commission_category', 'Indirect')->first();

                if(empty($parentCommission)){

                    $parentRole = $parentUser->roles->first();
                    // dd($parentRole->name);
                    if($parentRole){

                        $parentCommission = CommissionSlab::whereHas('commissionSlabUsers', function($q) use($parentRole){
                            $q->where('role_id', $parentRole->id);
                        })->where('commission_category', 'Indirect')->first();

                        if($parentCommission){

                            if($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission')
                            {
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal

                                $remainingAmount = $remainingAmount - $parentCommissionVal;
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";

                            }
                            elseif($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount')
                            {

                                $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                //dd($parentRole->name);
                                $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                                $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";

                            }
                            elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission')
                            {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                            }
                            elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount')
                            {

                                $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                                $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                            }
                            else{
                                $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                                $commissions[$parentRole->name] = $parentCommissionVal;
                            }
                        }else{
                             $commissions[$parentRole->name] = 0;
                        }

                    }else{
                        $commissions[$parentRole->name] = 0;
                    }
                }else{

                    if($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Net Commission')
                    {

                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name]['commission'] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";

                    }
                    elseif($parentCommission->commission_type === 'Percentage' && $parentCommission->calculation_from === 'Remaining Amount')
                    {

                        $parentCommissionVal = $remainingAmount * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name] = $parentCommissionVal;
                        $commissions[$parentRole->name]['calculation_from'] = "Remaining Amount";

                    }
                    elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Net Commission')
                    {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;
                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                    }
                    elseif($parentCommission->commission_type === 'Fixed' && $parentCommission->calculation_from === 'Remaining Amount')
                    {

                        $commissions[$parentRole->name]['commission'] = $parentCommission->commission_value;

                        $commissions[$parentRole->name]['calculation_from'] = "Net Commission";
                    }
                    else{
                        $parentCommissionVal = $netCommission * ($parentCommission->commission_value / 100); // Convert percentage to decimal
                        $commissions[$parentRole->name] = $parentCommissionVal;
                    }
                }
            }

        }
        $commissions['agent_commissoin'] = $commissionVal;
        $commissions['deal_id'] = $deal->id;

        $dealSplitCommission = [
            'deal_id' => $deal->id,
            'user_id' => $deal->agent_id,
            'commission_value' => $commissionVal,
            'commission_type' => $agentCommission->commission_type,
            'commission' => $agentCommission->commission_value,
            'created_by_id' => $deal->created_by_id,
        ];
        DealSplitCommission::updateOrCreate(['deal_id' => $deal->id, 'user_id' => $deal->agent_id], $dealSplitCommission);
      //
        //  dd($commissions);
        return Command::SUCCESS;
    }
}
