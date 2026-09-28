<?php

namespace App\Console\Commands;

use App\Models\CallLog;
use App\Models\Event;
use App\Models\Kpi;
use App\Models\KpiStatistic;
use App\Models\Lead;
use App\Models\ListingLead;
use App\Models\Property;
use App\Models\BulkMessage;
use App\Models\PropertyMeta;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;

use DateTime;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ProcessAgentsKpi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:agentKpi';

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
        $kpis = Kpi::where('is_processed', '!=', 1)->get();
        foreach ($kpis as $kpi) {
            switch ($kpi->kpi) {
                case 'New Listing':
                    $this->processNewPropertyKpi($kpi);
                    break;
                case 'Total Listings':
                    $this->processTotalPropertyKpi($kpi);
                    break;
                case 'Tru Check':
                    $this->processTruCheckKpi($kpi);
                    break;
                case 'Tru Check Offplan':
                    $this->processTruCheckKpiOffPlan($kpi);
                    break;
                case 'Tru Check Ready':
                    $this->processTruCheckKpiReady($kpi);
                    break;
                case 'PF Verified Ready':
                    $this->processPFVerifiedKpiReady($kpi);
                    break;
                case 'PF Verified Offplan':
                    $this->processPFVerifiedKpiOffplan($kpi);
                    break;
                case 'PF Verified':
                    $this->processPFVerifiedKpi($kpi);
                    break;
                case 'New Lead':
                    $this->processNewLeadKpi($kpi);
                    break;
                case 'All Lead':
                    $this->processAllLeadKpi($kpi);
                    break;
                case 'Successful Leads':
                    $this->processSuccessfulLeadKpi($kpi);
                    break;
                case 'New Listing Leads':
                    $this->processNewListingLeadKpi($kpi);
                    break;
                case 'Successful Listing Leads':
                    $this->processSuccessfulListingLeadKpi($kpi);
                    break;
                case 'Total Event':
                    $this->processTotalEventKpi($kpi);
                    break;
                case 'Total Viewings':
                    $this->processViewingEventKpi($kpi);
                    break;
                case 'Total Meetings':
                    $this->processMeetingEventKpi($kpi);
                    break;
                case 'Call Logs':
                    $this->processCallLogKpi($kpi);
                    break;
                case 'Connected Call Logs':
                    $this->processConnectedCallLogKpi($kpi);
                    break;
                case 'Incoming Call Logs':
                    $this->processIncomingCallLogKpi($kpi);
                    break;
                case 'Missed Call Logs':
                    $this->processMissedCallLogKpi($kpi);
                    break;
                case 'Outgoing Call Logs':
                    $this->processOutgoingCallLogKpi($kpi);
                    break;
                case 'Outgoing Missed Call Logs':
                    $this->processOutgoingMissedCallLogKpi($kpi);
                    break;
                case 'Incoming Missed Call Logs':
                    $this->processIncomingMissedCallLogKpi($kpi);
                    break;
                case 'WhatsApp Messages':
                    $this->processWhatsappMessagesKpi($kpi);
                    break;
                case 'Successful WhatsApp Messages':
                    $this->processSuccessfulWhatsappMessagesKpi($kpi);
                    break;
                case 'WhatsApp Response':
                    $this->processWhatsappResponseTime($kpi);
                    break;
                case 'Bulk SMS':
                    $this->processBulkSms($kpi);
                    break;
                    case 'Successful Bulk SMS':
                        $this->processBulkSuccessfulSms($kpi);
                        break;

            }
        }
    }

    private function processNewPropertyKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processTotalPropertyKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processNewLeadKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $leads = Lead::where('is_archive', 0)
            ->where('status', 1)
            ->whereNull('converted_to_listing_lead')
            ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($leads as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);
        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }

        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }

    private function processAllLeadKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $leads = Lead::where('is_archive', 0)
            ->where('status', 1)
            ->whereNull('converted_to_listing_lead')
            ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($leads as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);
        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }

        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }

    private function processSuccessfulLeadKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $leads = Lead::where('is_archive', 0)

            ->where('status', 1)
            ->where('lead_status_id', 9)
            ->whereNull('converted_to_listing_lead')
            ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($leads as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);
        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }

        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }

    private function processNewListingLeadKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listingleads = ListingLead::where('status_id', 1)->where('is_archive', 0)->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listingleads as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }

    private function processSuccessfulListingLeadKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listingleads = ListingLead::where('status_id', 5)->where('is_archive', 0)->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listingleads as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }

    private function processTotalEventKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($events as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processMeetingEventKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::where('event_type_id', 2)->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($events as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processViewingEventKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $events = Event::where('event_type_id', 1)->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($events as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processTruCheckKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('publish_ststus','!=','ARCHIVED')
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'bayut_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            $property_meta += PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'dubizzle_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processTruCheckKpiReady($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            ->whereIn('completion_status', ['Ready Secondary', 'Ready Primary'])
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('publish_ststus','!=','ARCHIVED')
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        // Check trucheck for each property within the date range
        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'bayut_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            $property_meta += PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'dubizzle_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            // If both found, increment by 2, if one found, increment by 1
            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processTruCheckKpiOffPlan($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            ->whereIn('completion_status', ['Off-Plan Secondary', 'Off-Plan Primary'])
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('publish_ststus','!=','ARCHIVED')
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        // Check trucheck for each property within the date range
        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'bayut_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            $property_meta += PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'dubizzle_trucheck_status')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            // If both found, increment by 2, if one found, increment by 1
            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processPFVerifiedKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->where('publish_ststus','!=','ARCHIVED')
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        // Check trucheck for each property within the date range
        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'pf_verified')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            // If both found, increment by 2, if one found, increment by 1
            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processPFVerifiedKpiOffplan($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            ->whereIn('completion_status', ['Off-Plan Secondary', 'Off-Plan Primary'])
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->where('publish_ststus','!=','ARCHIVED')
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        // Check trucheck for each property within the date range
        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'pf_verified')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            // If both found, increment by 2, if one found, increment by 1
            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }
    private function processPFVerifiedKpiReady($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $listings = Property::where('is_archived', 0)
            ->whereIn('completion_status', ['Ready Secondary', 'Ready Primary'])
            // ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('publish_ststus','!=','ARCHIVED')
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($listings as $listing) {
            $date = (new DateTime($listing->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        // Check trucheck for each property within the date range
        foreach ($listings as $listing) {
            $property_meta = PropertyMeta::where('property_id', $listing->id)
                ->where('meta_key', 'pf_verified')
                ->whereDate('created_at', '>=', $start_date->format('Y-m-d'))
                ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
                ->count();

            // If both found, increment by 2, if one found, increment by 1
            if ($property_meta > 0) {
                $date = (new DateTime($listing->created_at))->format('Y-m-d');
                $data[$date] += min($property_meta, 2);
            }
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }



    private function processCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processConnectedCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_status', 'connected')
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processIncomingCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_type', 'INCOMING')
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processOutgoingCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_type', 'OUTGOING')
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processOutgoingMissedCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_type', 'OUTGOING')
            ->where('call_duration', 0)
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processIncomingMissedCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_type', 'INCOMING')
            ->where('call_duration', 0)
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processMissedCallLogKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $call_logs = CallLog::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('call_duration', 0)
            ->where('user_id', $kpi->agent_id)
            ->get();

        foreach ($call_logs as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processSuccessfulWhatsappMessagesKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);
        $agent_id = $kpi->agent_id;

        $response = Http::post('https://contacts.axcrm.ae/api/messages-count-by-agent-by-date-successful-only', [
            'agent_id' => $agent_id,
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'uuid' => 'e330c394-786-f74z-b4g9-hj67d54bjh64',
        ]);

        $response = $response->json();

        $data = [];

        foreach ($response as $entry) {
            $data[$entry['date']] = $entry['count'];
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processWhatsappMessagesKpi($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);
        $agent_id = $kpi->agent_id;

        $response = Http::post('https://contacts.axcrm.ae/api/messages-count-by-agent-by-date', [
            'agent_id' => $agent_id,
            'start_date' => $start_date->format('Y-m-d'),
            'end_date' => $end_date->format('Y-m-d'),
            'uuid' => 'e330c394-786-f74z-b4g9-8c4fd8g98h18',
        ]);

        $response = $response->json();

        $data = [];

        foreach ($response as $entry) {
            $data[$entry['date']] = $entry['count'];
        }
        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function _processWhatsappResponseTime($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);
        $agent_id = $kpi->agent_id;

        // $wa_average_diff = Lead::where('agent_id', $agent->id)
        //     ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
        //     ->whereNotNull('shortlinks.opted_in')
        //     ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
        //     ->value('average_diff');
        // this is the query to get the average response time for whatsapp messages for a specific agent in seconds change it so it get avearge response time for every day in the date range

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

    private function processWhatsappResponseTime($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);
        $agent_id = $kpi->agent_id;

        $data = [];
        $period = new DatePeriod($start_date, new DateInterval('P1D'), $end_date->modify('+1 day'));

        foreach ($period as $date) {
            $average_diff = Lead::where('agent_id', $agent_id)
                ->join('shortlinks', 'leads.id', '=', 'shortlinks.lead_id')
                ->whereDate('shortlinks.created_at', $date->format('Y-m-d'))
                ->whereNotNull('shortlinks.opted_in')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, shortlinks.created_at, shortlinks.opted_in)) as average_diff'))
                ->value('average_diff');

            $average_diff = $average_diff / 60;

            $data[$date->format('Y-m-d')] = round($average_diff) ?? 0;
        }

        $average_per_day = array_sum($data) / count($data);
        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();

        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();

        return $stats;
    }


    private function processBulkSms($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $bulksmses = BulkMessage::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->get();

        foreach ($bulksmses as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }
    private function processBulkSuccessfulSms($kpi)
    {
        $start_date = new DateTime($kpi->start_date);
        $end_date = new DateTime($kpi->end_date);

        $data = [];
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            $data[$current_date->format('Y-m-d')] = 0;
            $current_date->modify('+1 day');
        }

        $bulksmses = BulkMessage::whereDate('created_at', '>=', $start_date->format('Y-m-d'))
            ->whereDate('created_at', '<=', $end_date->format('Y-m-d'))
            ->where('agent_id', $kpi->agent_id)
            ->where('confirmed', 1)
            ->get();

        foreach ($bulksmses as $log) {
            $date = (new DateTime($log->created_at))->format('Y-m-d'); // Convert to DateTime object
            $data[$date]++;
        }

        $target = $kpi->target;
        $average_per_day = round($target / $kpi->duration);

        $already = KpiStatistic::where('kpi_id', $kpi->id)->first();
        if ($already) {
            $stats = $already;
        } else {
            $stats = new KpiStatistic();
        }
        $stats->kpi_id = $kpi->id;
        $stats->average_per_day = $average_per_day;
        $stats->user_id = $kpi->agent_id;
        $stats->target = $kpi->target;
        $stats->achieved = array_sum($data);
        $stats->achieved_percentage = round(($stats->achieved / $stats->target) * 100);
        $stats->data = json_encode($data);
        $stats->start_date = $kpi->start_date;
        $stats->end_date = $kpi->end_date;
        $stats->save();
        $end_date = Carbon::parse($kpi->end_date);
        if ($end_date->isFuture()) {
            $kpi->is_processed = 2; // Future date
        } else {
            $kpi->is_processed = 1; // Past date or current date
        }
        $kpi->save();
        return $stats;
    }

}
