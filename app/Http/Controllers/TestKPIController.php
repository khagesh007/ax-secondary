<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyCycle;
use App\Traits\CallTrait;
use App\Traits\TelegramTrait;
use App\Traits\FileTrait;
use App\Traits\PDFTrait;
use DateTime;
use Illuminate\Support\Facades\DB;
use App\Traits\PusherTrait;
use App\Traits\DocusignTrait;
use App\Traits\PropertyFinderTrait;
class TestKPIController extends Controller
{
    use CallTrait, PusherTrait, TelegramTrait, PDFTrait, FileTrait,DocusignTrait,PropertyFinderTrait;

    protected $counter = 0;

    public function index()
    {
        $start_date = new DateTime('2025-05-01');
        $end_date = new DateTime('2025-05-31');

        $dailyData = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $dailyData[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $latestCycles = PropertyCycle::select('property_id', DB::raw('MAX(created_at) as max_created_at'))
            ->groupBy('property_id');

        $listings = Property::select([
            'properties.id',
            'properties.ref_no',
            'properties.published_date',
            'properties.publish_status',
            'properties.created_at',
            'properties.created_by_id',
            'pc.status',
            'pc.created_at as cycle_created_at',
        ])
            ->join('property_cycles as pc', 'properties.id', '=', 'pc.property_id')
            ->joinSub($latestCycles, 'latest', function ($join) {
                $join->on('pc.property_id', '=', 'latest.property_id')
                    ->on('pc.created_at', '=', 'latest.max_created_at');
            })
            ->where('properties.agent_id', 1554)
            ->where('pc.status', 'PUBLISHED')
            ->whereDate('pc.created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('pc.created_at', '<=', $end_date->format('Y-m-d'))
            ->where('properties.ref_no', 'NOT LIKE', 'axc-%-r%')
            ->where('properties.is_live', true)
            ->whereIn('properties.publish_status', ['PUBLISHED', 'PENDING'])
            ->orderByDesc('pc.created_at')
            ->distinct()
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->cycle_created_at))->format('Y-m-d'); // Convert to DateTime object
            $dailyData[$date]++;
        }


//        var_dump(count($listings));die();

        echo "<pre>";
        print_r($dailyData);die();

        $target = preg_replace('/[^0-9]/', '', 5);


        dd($listings);
    }
}
