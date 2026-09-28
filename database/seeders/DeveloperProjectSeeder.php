<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeveloperProject;
use Str;
use App\Models\OffplanMeta;
class DeveloperProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('developers.projects.Emaar') as $project) {
            $exist = DeveloperProject::where('project_name',$project)->where('developer_id',1)->first();
            if(!$exist ){
                $developer =  DeveloperProject::create([
                'developer_id' => 1,
                'project_name' => $project,
                'slug' => Str::slug($project),
                'description' => $project .' By '.'Emaar',
            ]);
        }


        $exits = OffplanMeta::where('value',$project)->where('key','title')->first();
            if(!$exits){
                OffplanMeta::create([
                    'developer_project_id' => $developer->id,
                    'key' => 'title',
                    'value' => $project,
                ]);
            }

        }

        foreach (config('developers.projects.Azizi') as $project) {
            $exist = DeveloperProject::where('project_name',$project)->where('developer_id',48)->first();
            if(!$exist ){
                $developer =  DeveloperProject::create([
                'developer_id' => 48,
                'project_name' => $project,
                'slug' => Str::slug($project),
                'description' => $project .' By '.'Azizi Developers',
            ]);
        }


        $exits = OffplanMeta::where('value',$project)->where('key','title')->first();
            if(!$exits){
                OffplanMeta::create([
                    'developer_project_id' => $developer->id,
                    'key' => 'title',
                    'value' => $project,
                ]);
            }

        }


    }
}
