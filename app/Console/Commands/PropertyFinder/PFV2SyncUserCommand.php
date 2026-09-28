<?php

namespace App\Console\Commands\PropertyFinder;

use App\Models\User;
use App\Services\Portal\PropertyFinderService;
use App\Traits\PropertyFinderTrait;
use Illuminate\Console\Command;

class PFV2SyncUserCommand extends Command
{
    use PropertyFinderTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pfv2:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $data = [];
    protected $entities = ["secondary", "primary", "abu_dhabi", "commercial"];

    protected PropertyFinderService $propertyFinderService;


    private function fetchUsers()
    {
        foreach ($this->entities as $entity) {
            $this->data[$entity] = $this->propertyFinderService->searchUsers($entity);
        }
    }

    private function processUsers($entity){
        foreach ($this->data[$entity] as $item) {

        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->propertyFinderService = app(PropertyFinderService::class);
        $users = User::where("pp_enabled", true)->whereHas("roles",function ($query){
            $query->whereIn("name", ["Agent"]);
        })->get();
        foreach ($users as $user) {
            $entity = "secondary";
            if (!empty($user->entity)){
                $entity = strtolower($user->entity);
            }
            $updated = $this->propertyFinderService->updateUserPublicProfile($user->toArray(), $entity);
            if ($updated) {
                $this->info("Updated user {$user->email} for entity {$entity}");
            }else{
                $this->info("Failed user {$user->email} for entity {$entity}");
            }
        }

        return Command::SUCCESS;
    }
}
