<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('entity')->nullable()->after('bitrix_role');
        });

        // $users = App\Models\User::all();
        // foreach($users as $user){
        //     $bitrixrole = $user->bitrix_role;
        //     $roles = explode(',', $bitrixrole);
        //     if (in_array('Secondary', $roles)) {
        //         $user->entity = 'Secondary';
        //     } elseif (in_array('Primary', $roles)) {
        //         $user->entity = 'Primary';
        //     } elseif (in_array('Commercial', $roles)) {
        //         $user->entity = 'Commercial';
        //     } elseif (in_array('Chinese', $roles)) {
        //         $user->entity = 'Chinese';
        //     } elseif (in_array('Abu-Dhabi Team', $roles)) {
        //         $user->entity = 'Abu-Dhabi Team';
        //     }elseif (in_array('Primary', $roles) && in_array('Secondary', $roles)) {
        //         $user->entity = 'Secondary';
        //     }
        //     $user->save();
        // }

        $primary_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('region_id', '!=', 2)
            ->get();

        foreach ($primary_agents as $agent) {
            $agent->entity = 'Primary';
            $agent->save();
        }

        $secondary = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('region_id', '!=', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($secondary as $agent) {
            $agent->entity = 'Secondary';
            $agent->save();
        }

        $chinese_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%CHINESE%')
            ->where('region_id', '!=', 2)
            ->get();

        foreach ($chinese_agents as $agent) {
            $agent->entity = 'Chinese';
            $agent->save();
        }

        $abu_dhabi_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->get();

        foreach ($abu_dhabi_agents as $agent) {
            $agent->entity = 'Abu Dhabi';
            $agent->save();
        }

        $secondary_primary = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Primary, Secondary,%')
            ->where('region_id', '!=', 2)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($secondary_primary as $agent) {
            $agent->entity = 'Secondary';
            $agent->save();
        }

        $commercial_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('is_commercial', 1)
            ->get();
        foreach ($commercial_agents as $agent) {
            $agent->entity = 'Commercial';
            $agent->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('entity');
        });
    }
};
