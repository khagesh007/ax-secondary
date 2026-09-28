<?php

namespace App\Jobs;

use App\Exports\DealCommissionExportExcel;
use App\Http\Controllers\Api\V1\Deals\DealApiController;
use App\Mail\DealCommissionMail;
use App\Models\Deal;
use App\Models\DealSplit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class DealReportSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $deal = Deal::where('id',30)->with('agent', 'property', 'contact', 'createdBy','leads')->first();





        $deal_splits = DealSplit::where('deal_id',$deal->id)->with('commissions')->get();

        $deal_users = (new DealApiController())->getCommissionUsersProcess($deal->id);

        $deal_user = collect($deal_users);
        //get unique users
        $deal_users = $deal_user->unique('id');
        //keep only id, name,email, role_list
        $deal_users = $deal_users->map(function ($item, $key) {
            return collect($item)->only(['id', 'name','email', 'role_list']);
        });

        $agent_commission = 0;
        $primary_agent = 0;
        $data['company_share'] = '';
        $data['company_amount'] = '';
        $company_amount = 0;
        $excel_header = [];
        $excel_data = [];
        foreach ($deal_splits as $key => $deal_split) {
            $split_amount = $deal_split->split_amount;
            $commissions = $deal_split->commissions;
            $total_split_commission = 0;
            foreach ($commissions as $key => $commission) {
                $total_split_commission += $commission->commission;
//             if($deal->agent_id == $commission->user_id){
//                 $data['primary_agent'] = $commission->user->name;
//                 $data['primary_agent_amount'] = $commission->commission;
//                 $data['primary_agent_share'] = $commission->commission_value;
//             }
                //$deal_users


                foreach ($deal_users as $key => $deal_user) {
                    if($deal_user['id'] == $commission->user_id){
                        $deal_users[$key]['amount'] = $commission->commission;
                        $deal_users[$key]['share'] = $commission->commission_value;
                        $deal_users[$key]['role'] = $deal_user['role_list'][0];
                        $excel_data[$deal_user['role_list'][0].' Share'] = $commission->commission_value;
                        $excel_data[$deal_user['role_list'][0].' Amount (AED)'] = $commission->commission;
                        $excel_header[] = $deal_user['role_list'][0].' Share';
                        $excel_header[] = $deal_user['role_list'][0].' Amount (AED)';
                        $excel_data[] = $commission->commission_value;
                        $excel_data[] = $commission->commission;
                    }
                }

            }


            $company_amount+= (float)$split_amount - (float)$total_split_commission;
        }

        $data['deal'] = $deal;
        $data['users'] = $deal_users;
        $data['company_amount'] = $company_amount;
        $excel_data = [$excel_header,$excel_data];
        $file_path = "exports/deal_".$deal->id."_report.xlsx";
        Excel::store(new DealCommissionExportExcel($excel_data), $file_path, 'public');

        Mail::to('khagesh.office@gmail.com')->send(new DealCommissionMail($data, $file_path,auth()->user()));

    }
}
