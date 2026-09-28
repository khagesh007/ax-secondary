<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\Route;

class RouteToJs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create a js files of all the routes in the application.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routes = $this->generateRoutes();
        $this->writeJson( $routes );
        return;
    }

    public function generateRoutes()
    {
        $routes = [];
        foreach ( Route::getRoutes()->getRoutes() as $route) {
            if ( is_null( $route->getName() ))
                continue;
            if ( isset( $routes[$route->getName()] ))
                $this->comment("Overwriting duplicate named route: " . $route->getName());
            $routes[$route->getName()] = "/" . $route->uri();
        }
        return $routes;
    }

    protected function writeJson( $routes ) {
        $filename = public_path().'/assets/js/routeList.js';
        //check if file exists
//        if (file_exists($filename)) {
//
//        }

        if (!$handle = fopen($filename, 'w')) {
            $this->error( "Cannot open file: $filename" );
            return;
        }
        $data = "const routeList = " . json_encode($routes) . ";";


        if ( fwrite( $handle, $data ) === FALSE) {
            $this->error( "Cannot write to file: $filename" );
            return;
        }

        $this->info("Wrote routes to: $filename");

        fclose($handle);

    }
}
