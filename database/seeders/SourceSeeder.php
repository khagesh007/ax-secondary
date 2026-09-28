<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Source;
class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       Source::create([
        'name'=>'Agent',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Agent External',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Agent Internal',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Bayut',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Business Card',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Client referral',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Cold Call',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Company Email',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Database',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Developer',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Direct Call',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Direct Client',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Dubizzle',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Email campaign',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Event',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Existing client',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Facebook',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Friend or Relative',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Google',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Gulf News',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'Instagram',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'JustProperty',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'lookup.ae',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Landing Page',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Magazine',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Newsletter',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Newspaper advert',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Online Campaign',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Open House',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Personal Referral',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Property Finder',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Referral',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Roadshow',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Search Engine',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Signboard',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'SMS campaign',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Social media Campaign',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Twitter',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Walk-in ',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Website',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Whatsapp',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Word of Mouth',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Youtube',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Other',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Online',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Zoom Property',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'WhatsApp - PF',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'WhatsApp - Bayut',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'WhatsApp - Dubizzle',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Call - Unspecified',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Call - PF',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Call - Bayut',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'Call - Dubizzle',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

       Source::create([
        'name'=>'CRM',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);
       Source::create([
        'name'=>'AX CAPITAL',
        'status'=>1,
        'is_contact_source'=>1,
        'is_lead_source'=>1,
        'order'=>1,
       ]);

    }
}
