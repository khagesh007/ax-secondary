<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Models\Lead;
use App\Models\User;
use App\Models\Property;
use  App\Events\PortalsLeadsNotification;

class BayutCallImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bayut:callLeadImport';

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
        try {
            $perPage = 25;
            $page=1;

            $currentDate = now()->format('Y-m-d');

            $currentDate = "2023-11-15";

            $headerData = array(
                'authority' => 'www.bayut.com',
                'accept' => 'application/json',
                'accept-language' => 'en',
                'access-control-allow-credentials' => 'true',
                'app-name' => 'phoenix',
                'x-xsrf-token' => 'eyJpdiI6IjhqOVh6SVwvQk5RZ2ZcLzF0K1RtVEZudz09IiwidmFsdWUiOiJhdklOUGE1WUF5eDJqMjBaK0hcL3hPS3dKUXF1a0ZcL3JrS0RcL2VMR2YyWFVYb1NmZER5QWZHYlZqUWl1TmhYTE1ZRGk1K1RUTG91NlFLNzVoM1A1cENndz09IiwibWFjIjoiY2YwMDdhZWM5MzVhMGQ2MmM4ZDE2OTU0ODRlYTU0MzhmNjdhMTU2ZTNiZjFhZmM0MTM1Mjg0M2VkODU0YTY0YSJ9',
                'x-vercel-protection-bypass' => 'rTPSuApMZPMyrst9W5mOZO85wojrWEan',
                'authorization' => 'Bearer 15699674|SBm2msQ9Qa6o0DXaiEI0ofOIjgxwZCpHAUrWMBMA',
                'cookie' => '__rtbh.lid=%7B%22eventType%22%3A%22lid%22%2C%22id%22%3A%22W77Wm88s9IfpTXRgkS65%22%7D; _fbp=fb.1.1679983787731.204545561; FPID=FPID2.2.NDV0zoYEtJick4Xag3%2BigZGo%2FeYKmWe3hLqR8EadX6w%3D.1679983787; device_id=lhrpyve9obs9m6vvi; os_name=N%2FA; os_version=N%2FA; browser_name=N%2FA; browser_version=N%2FA; __rtbh.uid=%7B%22eventType%22%3A%22uid%22%2C%22id%22%3A%221091659263.1679983787%22%7D; th_capi_em=c75c59ac4c4474939e0492f50c2b1fbc60c94a79fab597dadfe200e825dae549; __zlcmid=1FulpM4kWzOKRt2; csrftoken=tcMrIAiGUNmrzLKQgPbt52igpRlCn0WvvcjAX2TzlEGWvo9cSG1NjrhSNYsSngBx; _cc_id=e57d4441052a3d18ef7bcbc80e8910ba; _gcl_au=1.1.671814188.1696483379; fbm_104487306412437=base_domain=.bayut.com; panoramaId_expiry=1700570584862; panoramaId=0f6c3c3be7d829a6f146a368a2a316d53938a32905260b8b494ab5a33e16469a; panoramaIdType=panoIndiv; anonymous_session_id=7053182a-f2d1-490e-a672-7fd3851fcd90; PHPSESSID=rsmsjvbismqhbomkid128v4rl6; moe_uuid=49b14d76-0641-40b5-8ebe-bd0b60b3a127; settings=%7B%22area%22%3A%22SQFT%22%2C%22currency%22%3A%22AED%22%2C%22installBanner%22%3Atrue%2C%22searchHitsLayout%22%3A%22LIST%22%7D; abTests=%7B%22CompactNavBar%22%3A%22nav_bar%22%2C%22SearchHistoryDesktop%22%3A%22original%22%2C%22SearchHistoryCompact%22%3A%22filters_card%22%7D; banners=%7B%7D; userLocation=%7B%22countryCode%22%3A%22AE%22%2C%22countryName%22%3A%22United%20Arab%20Emirates%22%2C%22cityName%22%3Anull%7D; userGeoLocation=%7B%22coordinates%22%3Anull%2C%22closestLocation%22%3Anull%2C%22loading%22%3Afalse%2C%22error%22%3Anull%7D; __Host-next-auth.csrf-token=fb956486d480e0b0c08d4b1c32ab4382a48231633c6ac5bb574e8c5d395c5128%7C3da149666be181d7b1b83dd64a13e5f18ac84aee0e0d15ab7b8581c58d5eb082; __Secure-next-auth.callback-url=https%3A%2F%2Fbeta-profolio.vercel.app; _ga_KSWMYG1K3Z=GS1.1.1700455904.46.0.1700455904.0.0.0; _gid=GA1.2.138033309.1700459108; _uetsid=f63a7ea0876711ee94f6b71123e6426d; _uetvid=a5b534302aad11eea4617b3816384a05; XSRF-TOKEN=eyJpdiI6IjhqOVh6SVwvQk5RZ2ZcLzF0K1RtVEZudz09IiwidmFsdWUiOiJhdklOUGE1WUF5eDJqMjBaK0hcL3hPS3dKUXF1a0ZcL3JrS0RcL2VMR2YyWFVYb1NmZER5QWZHYlZqUWl1TmhYTE1ZRGk1K1RUTG91NlFLNzVoM1A1cENndz09IiwibWFjIjoiY2YwMDdhZWM5MzVhMGQ2MmM4ZDE2OTU0ODRlYTU0MzhmNjdhMTU2ZTNiZjFhZmM0MTM1Mjg0M2VkODU0YTY0YSJ9; byt_session=eyJpdiI6InhVMjg5VXpnYkRtS010UzBIZmlmTWc9PSIsInZhbHVlIjoiY1UxeGdBWW81SmNic0RhNlZXTHZ1VFJMeEtac01vV1l0d0VKWlpcL2NjYmpzYzlpMzFRTGdHNzlNZ1BDMmZORWQ0T0JoNVhhRXd6dUE3aExLc2ZLN2dBPT0iLCJtYWMiOiI4Y2RmMDgyM2M5ZjBlMmJiNTNmODJlNzRmMzI3Yjc1MTk0Y2M1NTY3ZTBmNDRjZmQ5N2NiNTYwYWY2ZWU1NGNhIn0%3D; AMP_TOKEN=%24NOT_FOUND; _ga=GA1.2.1091659263.1679983787; FPLC=%2Fl958irewfiKO3EkYD48lzO54eYqbHBKjO7O5YiTkKsDgTl%2BZNakvvVqtteXHubg%2F3l8hsUVbH%2BjuiL0MJyt%2F9s9D9hsCh3Cp62WoDDPc2qXQpi2H9xOeYvrCh2rGw%3D%3D; __gads=ID=a946565b1e79a3bb:T=1684328951:RT=1700459108:S=ALNI_MZt_XkQBSdHicw5tNaqRLlOx9kMlg; __gpi=UID=00000c1972f5551b:T=1684328951:RT=1700459108:S=ALNI_MbMMKGOfZxwB-ktZYH2oFuJvyYzbQ; cto_bundle=NV4ZWV82clBrd1JxUjZwY3RRVTFXMERCSHZtNCUyRjF6Y3VrdXR2cHpvb1BYUDVLbUJDQW1PR2JHekw0Wk9VME52MnRXVTNJRlFhdlM0WlJGdGMwZ1MwbHd0SUIxSm9mcFZLRHJiUGFvZmV1bW1WV0QzTG40Y2JEVWJxa05GTENhWlJ6c3Z2elRGbFR0d0hkdkxCRVd6T2V5cDQ5dHZwVHJpeGRPSVo0aUlZdzVxVSUyRlFPazlCVlQzSyUyRjRHYVd2YnFybkNnaTYlMkIxVHZBZmNLdkF4OURHJTJCZW15c3JpR3V2cHNxOEM4aFhERTY1Z2lSY3BrQW0yWHFtWXolMkZ2S2clMkIxM08wOXBqYXFWWmZ2VkdiYzZZTUN0cklVbHM3QlREVjhXQkxiNzRmRiUyQlZoSEZLUlNqMThGUUZnZkE0ZGw2VnZsOWJTZmIzeUc; fbsr_104487306412437=tqq98ZjWLKqqXC3JnpIqOJfe1LWYp7m_ieyr_lxIR9g.eyJ1c2VyX2lkIjoiMjQwNzIyODUxMjc4Mjk0OCIsImNvZGUiOiJBUUJ6RTIzcnhud2lYR3RZby1wa1lZeGlvb0wwZ3JSdWdYSXF2UWYwQXFQeVZSYjMxeE5zU1NtWk02S09rQ2drYXduNk9aMWMtZ1BwMkh3eGdUakxsejI2UXZjeU5yM1dzODlDeGpkOHZCRzdKbGZYQ3h6aE5TbVppbmQzcmYyeXh4MzFlU0xOQ1dlZDBMcS1xdzBXbDVNTm1aajJ4eGx3WmZMN08zOWVJcEVoQ3I0d0FzVEZiOU5NTzFpbUNQQkRsTFB4V2syZi02RU1qWFlmVmtNVGdMRFVnQjVJX3VWSUQ0c3piM0J1SlZhRU1tMnNSZVBiaXprM0JSVUhkaDBzSTc1VGE0c0FGVE1EQU44T1N1YkVYcTBTQm52NzdEY0JESDdfOEFQVWQyMVF3TlE2clRYTkpRWDhkZzFKNXhybWhVcnpuZ1V2M2ljYkhqM0RaWEZMd3FWMCIsIm9hdXRoX3Rva2VuIjoiRUFBQmZCOWpJUVpBVUJPMmo3bUxIVU8weEYwbnF0MERYdzNnM09kMjlLNnVON0lQWDRmUXlSVng3bEJOcnZsTXhNV3dVR2RicFh5ZlFVR3JIMFVTdjY1VlRrZm9LZ09UR1pBTlpCekdUeWlRUFpCM3pOaTNaQWt0SGZ4aG9PVEV4am0yeEU5VnBFWkF3VTlYbGx1UlpBeGh5d09KcmRvOEk0Tml6cXJkc3BKM1pCdVZaQVRxUlFtU3pVNm9QY2RqNHNIYlIzRDF5cjJ6WDM4MVpDMjJRaUQybDBaRCIsImFsZ29yaXRobSI6IkhNQUMtU0hBMjU2IiwiaXNzdWVkX2F0IjoxNzAwNDU5MTA5fQ; byt_cd=hARaQoV66tWCRywVQmyTW6dIUIZbpk5baB3y4NSSVbrxpxHKKvHr%2Fv7Kq7dLirRlDgVn5GJ1zPwSJn29G%2FOlZ9DyumAtvsx%2B%2FeuTbItAguNOMUuB2iqOrg%3D%3D; session=s%3A1e105a9a-171d-4063-96b3-55157c3d7dfd.BCZXtfgW7tJbeQD11%2BGDRd1kM7Z%2FzsaE6QHqodtJ8KI; landing_url=%2F; _ga_YTDB8TQ5Q9=GS1.1.1700459107.80.1.1700459123.44.0.0; _ga_JMZ5C490RT=GS1.1.1700459108.80.1.1700459123.0.0.0; token=15699674%7CSBm2msQ9Qa6o0DXaiEI0ofOIjgxwZCpHAUrWMBMA; __Secure-next-auth.session-token=eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIn0..7jHcnp_8IsUmCzek.BR_fsHZHQVOdr35CBbz2-RAO0wXwAR1dqWG4i8yR26hIFMZk1yoh8vs9vmGXmfYBvAV_CKCpwd45JEGoTQzerI8mjz3JniLWH5My3Z3XEkPdkX_go6pIzErSUhONpOdV_12oa0Kdiu6ZcStkriJnrB0jhfzcxH1jce5vJhnnllhZS_I3TOO1FUE946VusETsnSkMbs4G72ROYP6uq5ZXfFGufd5vVliJAAe6wkMkTayRLmvf2nR0i2Zj0ou-tnf9AacuX3wu2286gWSeGDwjc3Z_oA6VRyhkpFHLOq6xZHEqfjHXCfuyYje4LWw38mTcA6ucm0XLybwzSwS8HDbv3FIutItKaV_nfjdP1eAsP9ecApRvzU47CSebbOzGs0VcwaDaT1ksoboZw3pCpLX8WzgZZOboEuxC-L35jvWhWRJke7-0F-VNXO5CTp0uVsS7Cy1m6iKTXlmXN8MExbntiAuPtvvlhxvLTNMx4azyMAnsPsIibdr3Qzy_YDjgs3FxeyETd3E_j2YdIkplLTgrRxUam_CfJwDn8aNfs23DUJwjzxQvoCTGqS-ua5T97VtANkJd4x4Ta7bsfwXdIIX9SDg6M3_3V-HAKZ-TGOIjcNldoR5AS-OKZoFQWTxOOanT1Kah6H_eyBlRVtZ0fWQ8jbSaE9JmeobVXD6r_hPrBHHXFGSOTmeEhpGxMV7Edn5MOq1Au06eXU9gxw9pVBFQpHtunY6KhhDHPVxdxAe8kFkP0mGRJGjF61laMdQSgpeJSkn8VxEdBaHfaBWqwr80YGAxsI8UXPtb4W_yxbq4LbQgCIClUYGzf3RuS0UMoCTw4ZvUrSvq_x29zq_ttWLZoqUZ_0zbX5faOqAVqMksXjtf8pESYZdST0b41RkroPxO6XNfNQfZHXP7AZUXd405_pR5CAvDDZ8r6jgWOhqkzHsBrUErzuzlP-MBxfzljK6Fn0B8-jZ12nuRsIaZMStK1Qq8XxsWY1aVRmQDd1Un3okDePXE3qVLQeU6hcYEoz_7M2EKVDLKuFQ00AVgqb21pKZ7VKQH6HqTkAD6vq8iOEqPFoL3lV7AvUiJN5g5kzjANm3b9qUJNpzwlqka2Y6SnDXgLWIz3pcS76rNnu684UC1nPAKn04GTMDPhUF6nKsG-J9VsAQ_QngX4Fgc3Z5EYUCEif5rgxw1BXBAgHSktSgZKgbJ4Z94f6Hog_bhI9qnnTybunOd8VZH_V1gjm0HCsBd2RAOFEUzvL-WsZBj1QkdoXyhefACAA_MFM0XtIa27OLRD4S-JdJPTqkGXimNKQKygpwBs8b-Z5AqDvh7Iwbo4TILESNXkeB-E-qP8PWRT_OOlo2ccYhK3u1Wgsd_Z5r4LHYgot3EbxDAMbAMyWIUdaPtZuPGw22n2F_YYMqUapiroLgDj-YDcS1MokVUCeyGdsd4knCYG6RdlchwsQ-7DiutTBC6_vNE2okWA-hqhtvBLRQL2oN2gLfOufG_A-2KVqGI82L-aLzTsjstP39bz8kLeCRdLwMg8inDx1ynQGkxdeTPapYrk9ilAaNm7YrxmosEfQygMBmPx8qG2dwXAfM6-4npSZdABUXO8-kOXvNqYvWkF9tXSeySxz0vm9DDYlBDrXhfXJRXNpuLubIjF69bCoKKsnMfgOW1Av8CwiwdT4brlSiApcOIPVBPj90Nu60UpY7op7NAtuEunARmfMrOZ0BrY7HefYbsxuhP1Xb3IksqmXjmALCB9CVszyZqukfczOYkoqgd581PaM8lU1O8TpwFAtoar10iRxniF4o4ZJo9gaP9buOY_AmZMyjlV5tKuXYN5egR8R8wB7ZReHzoqRKwarDLnjhEHAadBf9Sq23f1Qbjn0ZEAdWvIllJN4oQFmrTk00Rf6evvnZPjOMSaR2jdcLa4DXc2j0O_ZbHBv-gYUOr8Ax6K4xTR46rFdSNkBeNT0cRR4zEjfYcwGiGLnqWyc3I6G51eNE2Bskd1nBeA5E2gYYPcL84QaPz5m2JAZ8Pj339VlVDNrcf68h94rFMl96axM_G8Idd6d9SbjLkIQVSCw32qVS5xub-VwjsIFNPVRzUkW6ZywIJxxt0ukpBFCXmoUXyfYtHM5sDqnARIaNZkoRp4_YD8UGvoUs6DaR6jC4ipPQaHGI417cxAGCgdUxpQjOwpdAE4nT3VK5UrJ_epd3_nqwRKqnrr4bSQBk3nP3dHgokkNGgghwIvmxR-9HnEhOOsXCFukKuskLghf3D5RTIZK5uOVZ_xWEn5Wh8Uwobj8_uq9ic-atEgZ_ZNQuqrkhQ7h6UqE2-nQBxov_2vFmWMJNJTUElqxUg6XQttc_0IaMt_TlBYbfri5SfVN82rctRiAJLRP7OJCLgE6BHKEUZ3gqqOApuwIfEDl4YSJbSfneUpGl7LgoY3iQyrDB7V3tjqadULar6dgUOnsOzsJdd1MQUaZxAgtXrYL_FxtkANYtAtRDQbdsjFfnrBqRE396imu5t8Casr2ulEzmLNm-OSa4hLtd0lqCrgqdEH1XbMyuYs1FDPCq0z8eTA6MMNWbDXv-3Q72SZQ7VT3H5zihApY80CJh50cYaNuLrpBR6q1mXcB-Szd4hxWZO36VnUqH3-DN6CzMyfKzEFWkz3lV2uY0hnb3UBGAYp65ZFedgNCNQ7UifcnhM_jBhMkH11_S_DgwKfM3yoa4hel62DEd5N4sqraxD72aVLkfQWzoF5kWdigsqkcShcX04CnbwZJ0PBSsejLrH_OQI2p-lZ93x7V1RzyMImBhppFcEsyS-ipb2Aao-Bq5bx4mEAh1ruLrjfJwVJV2JpC_p5XBD7jTHcWd6LEoCMv0V5KvPtPzvQhEmwylmzX36HYZl52qfSG8rKK30p3SnTJ1aNHYTiJrWNfNV36Ww1IOG0i3YdRByhoiVSiYCeLefNKA1E7QOu8PHpmFnE3EoeKov5xTTUWwQrMgYYnhBTSH0WzCTaI8odA40PvgzTEa3hj6S4bWHoLMI03vUgkeR7-HF_4pY5earJWjTnn7x6mbEOtCoLQ.yCnAe3UJdXpQIGLAFT4P8g; _gat_UA-201547-17=1'
            );
            $response = Http::withHeaders($headerData)
            ->get('https://www.bayut.com/api-v7/stats/leads/phone?page='.$page.'&perPage='.$perPage.'&start='.$currentDate.'&end='.$currentDate.'&portalIds=1&agentId=&sort=date&order=asc');

            $res = $response->json();

            $totalLeadsCount = $res['total'];
            $totalPages = round(ceil($totalLeadsCount / $perPage), 0);
            for($page; $page <= $totalPages; $page++){
                $api = Http::withHeaders($headerData)
                    ->get('https://www.bayut.com/api-v7/stats/leads/email?page='.$page.'&perPage='.$perPage.'&start='.$currentDate.'&end='.$currentDate.'&portalIds=1&agentId=&sort=date&order=asc');

                $apiResponse = $api->json();

                $bayutLeads = $apiResponse['data'];

                foreach($bayutLeads as $val)
                {
                    $reference = $val['property']['referenceID'];
                    $mobile = validatePhoneNumber($val['detail']['phone']);
                    if($reference){
                        $property = Property::where('ref_no', $reference)->first();
                        if($property){
                            $property_id = $property->id;
                        }else{
                            $property_id = null;
                        }
                    }
                    $lead = Lead::where('property_id', $property_id)->where('agent_id', $property->agent_id)->whereRaw("REPLACE(mobile, ' ', '') LIKE ?", ['%' . substr($mobile, -8) . '%'])->first();

                    if(empty($lead)){
                        $lead = new Lead();

                        $lead->mobile = $mobile;
                        $lead->first_name = $val['detail']['actor_name'];
                        $lead->lead_status_id = 1; // New Lead

                        switch ($property->for) {
                            case 'Residential for sale':
                            case 'Commercial for sale':
                                $lead_type_id = 2;
                            case 'Residential for rent':
                            case 'Commercial for rent':
                                $lead_type_id = 1;
                            default:
                                $lead_type_id = 10;
                        }

                        $lead = Lead::create([
                            'first_name' => ucfirst($val['detail']['actor_name']) ?? '',
                            'email' => $val['detail']['form_user_info']['email'] ?? '',
                            'mobile' => validatePhoneNumber(trim(str_replace("tel:", "", str_replace('+', '', $arry['mobile_matches']))) ?? ''),
                            'whatsapp' => trim($arry['mobile']) ?? '',
                            'message' => $val['emailMessage']['message'] ?? '',
                            'subject' => $val['emailMessage']['subject']?? '',
                            'for' => [$property->for],
                            'agent_id' => $property->agent_id,
                            'property_id' => $property->id,
                            'type_id' => $lead_type_id,
                            'source_id' => 4, // Bayut
                            'lead_medium_id' => 1, // Email
                            'lead_status_id' => 1, //New
                        ]);
                    }
                    echo $val['detail']['actor_name']."\n";

                    $user = User::where('id', $property->agent_id)->first();
                    event(new PortalsLeadsNotification("Lead", $lead, 'received', $user, $user, 1));

                }

            }
            return "Leads Imported Successfully";

        } catch (\Exception $exception) {
            dd($exception->getMessage());
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "External Api Controller " . $exception->getMessage());
            return $result;
        }
        return Command::SUCCESS;
    }
}

