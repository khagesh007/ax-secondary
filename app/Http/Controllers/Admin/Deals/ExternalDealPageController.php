<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Http\Controllers\Api\V1\Folders\FolderApiController;
use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Deal;
use App\Models\User;
use App\Models\DealInvite;
use App\Models\DealLead;
use App\Models\DealMedia;
use App\Models\Lead;
use App\Models\LeadMeta;
use App\Models\Medium;
use App\Models\Source;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\TelegramTrait;
use App\Traits\PDFTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExternalDealPageController extends Controller
{
    use FiltersTrait, PDFTrait, FileTrait, TelegramTrait;

    public function index($invite_code)
    {

        $invite = DealInvite::where('invite_code', $invite_code)->first();
        if ($invite) {
            $deal_type = 'SELLING';
            $entity_type = 'COMPANY';
            $leads = Lead::where('agent_id', $invite->invited_to_id)->first();
            $sources = Source::where('is_lead_source', true)->get();
            $mediums = Medium::where('is_lead_medium', true)->get();
            return view('admin.deals_revamped.invite')
                ->with('invite', $invite)
                ->with('lead', $leads)
                ->with('deal_type', $deal_type)
                ->with('entity_type', $entity_type)
                ->with('mediums', $mediums)
                ->with('sources', $sources)
                ->with('invite_code', $invite_code);
        }

    }
    public function leadExternal($invite_code)
    {

        $invite = DealInvite::where('invite_code', $invite_code)->with('media')->first();

        if ($invite) {
            $data['invite'] = $invite;
            if ($invite->form_submitted_at != null) {

                return view('admin.deals_revamped.lead_external_pdf')->with($data);
            } else {
                $deal_type = 'SELLING';
                $entity_type = 'COMPANY';

                $data['lead'] = Lead::where('id', $invite->lead_id)->with('leadMeta')->first();
                $data['lead_type'] = LeadMeta::where('lead_id', $invite->lead_id)->where('meta_key', 'type')->first();
                $data['meta'] = LeadMeta::where('lead_id', $invite->lead_id)->pluck('meta_value', 'meta_key');
                $data['countries'] = Country::where('status', '1')->get();
                return view('admin.deals_revamped.lead_external')->with($data);
            }
        }

    }

    public function storeInformation(Request $request)
    {

        try {
            DB::beginTransaction();
            $invite = DealInvite::where('invite_code', $request->invite_code)->first();
            if ($request->lead_id) {
                $lead = Lead::find($request->lead_id);
            } else {
                $mobile = preg_replace('/\D/', '', $request->mobile);
                $lastSevenDigits = substr($mobile, -7);
                $find_lead = Lead::whereRaw("SUBSTRING(REPLACE(mobile, ' ', ''), -7) = ?", [$lastSevenDigits])->first();

                if ($find_lead) {
                    $lead = $find_lead;
                } else {
                    $lead = new Lead();
                    $lead->agent_id = $invite->invited_to_id;
                    $lead->source_id = $request->source_id;
                    $lead->lead_medium_id = $request->lead_medium_id;
                    $lead->first_name = $request->first_name;
                    $lead->last_name = $request->last_name;
                    $lead->email = $request->email;
                    $lead->mobile = $request->mobile;
                    $lead->message = $request->message;
                    $lead->save();
                }
            }

            $deal_lead = new DealLead();
            $deal_lead->lead_id = $lead->id;
            $deal_lead->deal_id = $invite->deal_id;
            $deal_lead->lead_type = 'PERSONAL';
            $deal_lead->save();

            if ($request->has('passport')) {
                $name = Str::random(10);
                $path = $this->uploadDocument($request->passport, "deal/{$invite->deal_id}/$name", 'local');
                $media = LeadMedia::create([
                    "lead_id" => $lead->id,
                    "deal_id" => $invite->deal_id,
                    "type" => "Other",
                    "title" => 'Passport',
                    "description" => $request->file->getClientOriginalName() ?? null,
                    "extension" => $request->file->getClientOriginalExtension() ?? null,
                    "path" => $path,
                    "created_by_id" => auth()->id(),
                ]);

            }
            if ($request->has('visa')) {
                $name = Str::random(10);
                $path = $this->uploadDocument($request->passport, "deal/{$invite->deal_id}/$name", 'local');
                $media = LeadMedia::create([
                    "lead_id" => $lead->id,
                    "deal_id" => $invite->deal_id,
                    "type" => "Other",
                    "title" => 'Visa',
                    "description" => $request->file->getClientOriginalName() ?? null,
                    "extension" => $request->file->getClientOriginalExtension() ?? null,
                    "path" => $path,
                    "created_by_id" => auth()->id(),
                ]);
            }
            if ($request->has('emirates_id')) {
                $name = Str::random(10);
                $path = $this->uploadDocument($request->passport, "deal/{$invite->deal_id}/$name", 'local');
                $media = LeadMedia::create([
                    "lead_id" => $lead->id,
                    "deal_id" => $invite->deal_id,
                    "type" => "Other",
                    "title" => 'Emirates ID',
                    "description" => $request->file->getClientOriginalName() ?? null,
                    "extension" => $request->file->getClientOriginalExtension() ?? null,
                    "path" => $path,
                    "created_by_id" => auth()->id(),
                ]);
            }
            if ($request->has('trade_license')) {
                $name = Str::random(10);
                $path = $this->uploadDocument($request->passport, "deal/{$invite->deal_id}/$name", 'local');
                $media = LeadMedia::create([
                    "lead_id" => $lead->id,
                    "deal_id" => $invite->deal_id,
                    "type" => "Other",
                    "title" => 'Trade License',
                    "description" => $request->file->getClientOriginalName() ?? null,
                    "extension" => $request->file->getClientOriginalExtension() ?? null,
                    "path" => $path,
                    "created_by_id" => auth()->id(),
                ]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

    }
    public function saveBase64Image($base64Image)
    {
        // Remove the base64 metadata (data:image/png;base64,)
        $image_parts = explode(";base64,", $base64Image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1]; // This will return 'png', 'jpeg', etc.

        // Decode the base64 string
        $image_base64 = base64_decode($image_parts[1]);

        // Generate a unique file name
        $fileName = \Str::random(10) . '.' . $image_type;

        // Save the image to the 'public' disk (which maps to 'storage/app/public')
        \Storage::disk('s3')->put('temp/images/' . $fileName, $image_base64);

        // Return the file path or URL to the saved image
        return \Storage::disk('s3')->url('temp/images/' . $fileName);
    }
    public function updateLeadInformation(Request $request)
    { //this data is coming from the external form filled by the lead him/her self

        if ($request->is_signature == 1) {
            $sinature_file_url = $this->saveBase64Image($request->signature);
        } else {
            $sinature_file_url = '';
        }
        $nationality = Country::where('id', $request->nationality_id)->first();
        $invite = DealInvite::where('invite_code', $request->invite_code)->first();
        $lead = Lead::find($invite->lead_id);
        $fund_source = $request->fund_source_select == 'Other' ? $request->fund_source : $request->fund_source_select;
        $pdfRequest = new Request();
        $pdfRequest->replace($request->all());
        $file_name = \Str::random(10);
        $pdfRequest->merge([
            'lead_id' => $lead->id,
            'filename' => $file_name, 'type' => 'INDIVIDUAL',
            "full_legal_name" =>  $request->company_first_name ?? '',
            "trading_name" =>  $request->company_trading_name ?? '',
            "company_trading_name" =>  $request->company_trading_name ?? '',
            "company_trad_license_no" =>  $request->company_trade_license_no ?? '',
            "coi" =>  $request->company_coi ?? '',
            "doi" => $request->company_coi_date ?? '',
            'full_name' => $lead->first_name . ' ' . $lead->last_name,
            'aliases' =>  $request->aliases ?? '',
            'current_residential_address' => $lead->address,
            'trade_license_authority' => $request->trade_license_issue_authority ?? '',
            'trade_license_issue_date' =>  $request->trade_license_issue_date ?? '',
            'trade_license_expiry_date' =>  $request->trade_license_expiry_date ?? '',
            'ubo_name_1' => $lead->first_name . ' ' . $lead->last_name,
            'ubo_percentage_1' => '',
            "ubo_name_2" => "",
            "ubo_percentage_2" => "",
            "company_person1_name" => "",
            "company_person1_relationship" => "",
            "company_person2_name" => "",
            "company_person2_relationship" => "",
            "company_product" => "",
            "company_primary_countries" => "",
            "company_individual_occupation" => "",
            "name_of_employer" => "",
            "deal_with_risky_countries" => "",
            "politically_exposed" => "",
            "company_person_name" => "",
            "company_person_relationship" => "",
            "declaration_date" => "",

            'full_name' => $request->first_name . ' ' . $request->last_name,
            'aliases' => $request->aliases,
            'current_residential_address' => $request->address,
            'email' => $request->email,
            'telephone_number' => $request->phone,
            'mobile_number' => $request->mobile,
            'dob' => $request->dob,
            'pob' => $request->place_of_birth,
            'gender' => $request->gender,
            'nationality' => $nationality->name,
            'passport_number' => $request->passport_number,
            'passport_issue_country' => $request->passport_issue_country,
            'passport_issue_date' => $request->passport_issue_date,
            'passport_expiry_date' => $request->passport_expiry,
            'fund_employment_income' => $fund_source,
            'fund_sale_of_investment' => '',
            'fund_business_income' => '',
            'fund_savings' => '',
            'fund_other' => '',
            'fund_other_label' => '',
            'residency_visa_number' => '',
            'emirates_id_no' => '',
            'visa_expiry_date' => '',
            'occupation' => $request->occupation,
            'years_in_role' => $request->years_in_role,
            'employer_name' => $request->employer_name,
            'employer_address' => $request->establishment_address,
            'wealth_employment_income' => '',
            'wealth_sale_of_inv_property' => '',
            'wealth_business_income' => '',
            'wealth_savings' => '',
            'wealth_inheritance' => '',
            'wealth_other' => '',
            'wealth_label' => '',
            'annual_income' => $request->annual_income,
            'purchase_end_use' => '',
            'purchase_savings' => '',
            'purchase_other' => '',
            'purchase_other_label' => '',
            'payment_cash' => '',
            'payment_cheque' => '',
            'payment_bank_transfer' => '',
            'payment_mortgage' => '',
            'payment_crypto' => '',
            'payment_other' => '',
            'payment_other_label' => '',
            'pep' => $request->politically_exposed_person,
            'pep_other_label' => '',
            'signature' => $sinature_file_url,
            'declaration_date' => date('d/m/Y'),

        ]);

        try {
            DB::beginTransaction();


            $lead_type = LeadMeta::where('lead_id', $invite->lead_id)->where('meta_key', 'type')->pluck('meta_value')->first() ?? 'INDIVIDUAL';
            if ($invite->lead_id) {

                // $lead = Lead::where('id', $invite->lead_id)->first();
                // $mobile = preg_replace('/\D/', '', $request->mobile);
                //  $lastSevenDigits = substr($mobile, -7);
                // $find_lead = Lead::whereRaw("SUBSTRING(REPLACE(mobile, ' ', ''), -7) = ?", [$lastSevenDigits])->first();

                $lead->first_name = $request->first_name;
                $lead->last_name = $request->last_name;
                $lead->gender = $request->gender;
                $lead->dob = date('Y-m-d', strtotime($request->dob));
                // $lead->mobile = validatePhoneNumber($request->mobile);
                $lead->phone = $request->phone ?? $lead->phone;
                $lead->whatsapp = $request->whatsapp ?? $lead->whatsapp;
                $lead->address = $request->address;
                $lead->nationality_id = $request->nationality_id;
                $lead->message = $request->message;
                $lead->save();



                $leadId = $lead->id;
                LeadMeta::storeLeadMeta($leadId, "passport_number", $request->passport_number != '' ? $request->passport_number : null);
                LeadMeta::storeLeadMeta($leadId, "name_on_passport", $request->name_on_passport != '' ? $request->name_on_passport : null);
                LeadMeta::storeLeadMeta($leadId, "emirates_id", $request->emirates_id ?? null);
                LeadMeta::storeLeadMeta($leadId, "passport_issue_date", $request->passport_issue_date ?? null);
                LeadMeta::storeLeadMeta($leadId, "passport_issue_country", $request->passport_issue_country ?? null);
                LeadMeta::storeLeadMeta($leadId, "passport_expiry", $request->passport_expiry ?? null);
                LeadMeta::storeLeadMeta($leadId, "years_in_role", $request->years_in_role ?? null);
                LeadMeta::storeLeadMeta($leadId, "establishment_address", $request->establishment_address ?? null);
                LeadMeta::storeLeadMeta($leadId, "politically_exposed_person", $request->politically_exposed_person ?? false);
                LeadMeta::storeLeadMeta($leadId, "name_on_passport", $request->name_on_passport ?? null);
                LeadMeta::storeLeadMeta($leadId, "emirates_id_expiry", $request->emirates_id_expiry ?? null);
                LeadMeta::storeLeadMeta($leadId, "uae_national", $request->uae_national ?? false);
                LeadMeta::storeLeadMeta($leadId, "occupation", $request->occupation ?? null);
                LeadMeta::storeLeadMeta($leadId, "place_of_birth", $request->place_of_birth ?? null);
                LeadMeta::storeLeadMeta($leadId, "country_of_residence", $request->country_of_residence ?? null);
                LeadMeta::storeLeadMeta($leadId, "aliases", $request->aliases ?? null);
                LeadMeta::storeLeadMeta($leadId, "have_ubo", $request->have_ubo ?? null);

                LeadMeta::storeLeadMeta($leadId, "company_first_name", $request->company_first_name ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_former_name", $request->company_former_name ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_trading_name", $request->company_trading_name ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_trade_license_no", $request->company_trade_license_no ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_coi", $request->company_coi ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_coi_date", $request->company_coi_date ?? null);
                LeadMeta::storeLeadMeta($leadId, "company_principal_address", $request->company_principal_address ?? null);
                LeadMeta::storeLeadMeta($leadId, "trade_license_issue_date", $request->trade_license_issue_date ?? null);
                LeadMeta::storeLeadMeta($leadId, "trade_license_expiry_date", $request->trade_license_expiry_date ?? null);
                LeadMeta::storeLeadMeta($leadId, "trade_license_issue_authority", $request->trade_license_issue_authority ?? null);

                ($request->uae_residency != null && $request->uae_residency != '') ? LeadMeta::storeLeadMeta($leadId, "uae_residency", $request->uae_residency ?? null) : '';
                ($request->residency_visa_no != null && $request->residency_visa_no != '') ? LeadMeta::storeLeadMeta($leadId, "residency_visa_no", $request->residency_visa_no ?? null) : '';
                ($request->visa_expiry_date != null && $request->visa_expiry_date != '') ? LeadMeta::storeLeadMeta($leadId, "visa_expiry_date", $request->visa_expiry_date ?? null) : '';
                ($fund_source != null && $fund_source != '') ? LeadMeta::storeLeadMeta($leadId, "fund_source", $fund_source ?? null) : '';
                ($request->payment_method != null && $request->payment_method != '') ? LeadMeta::storeLeadMeta($leadId, "payment_method", $request->payment_method ?? null) : '';
                ($request->year_of_experience != null && $request->year_of_experience != '') ? LeadMeta::storeLeadMeta($leadId, "year_of_experience", $request->year_of_experience ?? null) : '';
                ($request->employer_address != null && $request->employer_address != '') ? LeadMeta::storeLeadMeta($leadId, "employer_address", $request->employer_address ?? null) : '';
                ($request->employer_name != null && $request->employer_name != '') ? LeadMeta::storeLeadMeta($leadId, "employer_name", $request->employer_name ?? null) : '';
                ($request->annual_income != null && $request->annual_income != '') ? LeadMeta::storeLeadMeta($leadId, "annual_income", $request->annual_income ?? null) : '';
                ($request->political != null && $request->political != '') ? LeadMeta::storeLeadMeta($leadId, "political", $request->political ?? null) : '';

                $invite->form_submitted_at = now();
                $invite->save();
                if($lead_type == 'INDIVIDUAL'){
                    $res = (new FolderApiController)->generateKycIndividualFillableV2($pdfRequest);
                    }else{
                        $res = (new FolderApiController)->generateKycEntityFillableV2($pdfRequest);
                    }




                    if (!empty($res)) {

                        if ($res['status'] == '1') {
                            $kyc_file = $res['url'];
                            $media = DealMedia::create([
                                "deal_id" => $invite->deal_id,
                                "model_type" => Lead::class,
                                "model_id" => $invite->lead_id,
                                "type" => "Other",
                                "title" => "Signed KYC",
                                "description" => null,
                                "extension" => 'pdf',
                                "thumbnail" => null,
                                "path" => $kyc_file,

                            ]);
                            if ($sinature_file_url != '') {
                            $invite->is_signed = 1;
                            $invite->form_submitted = 1;
                            }
                            $invite->deal_media_id = $media->id;
                            $invite->save();

                        }
                    } else {
                        return response()->json(['status' => 'success', 'message' => 'pdf not created']);
                    }

            }

            $result = [
                'status' => 'success',
                'message' => 'Lead Information Updated Successfully',
            ];
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::error($th);
            $result = [
                'status' => 'error',
                'message' => 'Something went wrong! Please try again later or contact support',
                'error' => $th->getMessage(),
            ];
        }

        return response()->json($result);
    }

    public function uploadSignedPdf(Request $request)
    {

        $validatedData = $request->validate([
            "invite_code" => "required|exists:deal_invites,invite_code",
            "pdf_file" => "required|mimes:pdf|max:10000",
        ]);

        $invite = DealInvite::where('invite_code', $request->invite_code)->first();
        $deal = Deal::where('id', $invite->deal_id)->first();
        $file = $request->file('pdf_file');
        if ($request->hasFile('pdf_file')) {
            $name = Str::random(10);
            $path = $this->uploadDocument($file, "deal/{$deal->id}/$name");
            $media = DealMedia::create([
                "deal_id" => $deal->id,
                "model_type" => Lead::class,
                "model_id" => $invite->lead_id,
                "type" => "Other",
                "title" => "Signed KYC",
                "description" => $file->getClientOriginalName() ?? null,
                "extension" => $file->getClientOriginalExtension() ?? null,
                "path" => $path,
                "created_by_id" => auth()->id(),
            ]);
            $invite->is_signed = 1;
            $invite->deal_media_id = $media->id;
            $invite->form_submitted = 1;
            $invite->form_submitted_at = now();
            $invite->save();
            return response()->json(['status' => 'success', 'message' => 'File uploaded successfully']);
        }
    }

    public function signDocument(Request $request){
        $validatedData = $request->validate([
            "invite_code" => "required|exists:deal_invites,invite_code",
            "signature" => "required|string",
        ]);
        if ($request->is_signature == 1) {
            $sinature_file_url = $this->saveBase64Image($request->signature);
        } else {
            $sinature_file_url = '';
        }
        $invite = DealInvite::where('invite_code', $request->invite_code)->first();
        $lead = Lead::where('id', $invite->lead_id)->first();
        $lead_meta = LeadMeta::where('lead_id', $invite->lead_id)->pluck('meta_value', 'meta_key');
        $nationality = Country::where('id', $lead->nationality_id)->first();
        if($lead_meta['type'] == 'INDIVIDUAL'){
            $lead_type = 'INDIVIDUAL';
            $document_type = 'lead-kyc-form-individual';
        }else{
            $document_type = 'lead-kyc-form-entity';
            $lead_type = 'COMPANY';
        }

        $pdfRequest = new Request();
        $pdfRequest->replace($request->all());
        $file_name = \Str::random(10);
        $pdfRequest->merge(['filename' => $file_name,
        //'type' => 'INDIVIDUAL',
            'lead_id' => $lead->id,
            "full_legal_name" =>  $lead_meta['company_first_name'] ?? '',
            "trading_name" => $lead_meta['company_trading_name'] ?? '',
            "company_trading_name" => $lead_meta['company_trading_name'] ?? '',
            "company_trad_license_no" => $lead_meta['company_trade_license_no'] ?? '',
            "coi" => $lead_meta['company_coi'] ?? '',
            "doi" => $lead_meta['company_coi_date'] ?? '',
            'full_name' => $lead->first_name . ' ' . $lead->last_name,
            'aliases' => $lead_meta['aliases'] ?? '',
            'current_residential_address' => $lead->address,
            'trade_license_authority' => $lead_meta['trade_license_issue_authority'] ?? '',
            'trade_license_issue_date' => $lead_meta['trade_license_issue_date'] ?? '',
            'trade_license_expiry_date' => $lead_meta['trade_license_expiry_date'] ?? '',
            'ubo_name_1' => $lead->first_name . ' ' . $lead->last_name,
            'ubo_percentage_1' => '',
            "ubo_name_2" => "",
            "ubo_percentage_2" => "",
            "company_person1_name" => "",
            "company_person1_relationship" => "",
            "company_person2_name" => "",
            "company_person2_relationship" => "",
            "company_product" => "",
            "company_primary_countries" => "",
            "company_individual_occupation" => "",
            "name_of_employer" => "",
            "deal_with_risky_countries" => "",
            "politically_exposed" => "",
            "company_person_name" => "",
            "company_person_relationship" => "",
            "declaration_date" => "",


            'email' => $lead->email,
            'address' => $lead->address,
            'telephone_number' => $lead->phone,
            'phone' => $lead->phone,
            'mobile_number' => $lead->mobile,
            'dob' => $lead->dob ?? '',
            'pob' => $lead_meta['place_of_birth'],
            'gender' => $lead->gender ?? '',
            'nationality' => $nationality?->name ?? '',
            'passport_number' => $lead_meta['passport_number'] ?? '',
            'passport_issue_country' => $lead_meta['>passport_issue_country'] ?? '',
            'passport_issue_date' => $lead_meta['passport_issue_date'] ?? '',
            'passport_expiry_date' => $lead_meta['passport_expiry'] ?? '',
            'fund_employment_income' => $lead_meta['fund_source'] ?? '',
            'fund_sale_of_investment' => '',
            'fund_business_income' => '',
            'fund_savings' => '',
            'fund_other' => '',
            'fund_other_label' => '',
            'residency_visa_number' => '',
            'emirates_id_no' => '',
            'visa_expiry_date' => '',
            'occupation' => $lead_meta['occupation'] ?? '',
            'years_in_role' => $lead_meta['years_in_role'] ?? '',
            'employer_name' => $lead_meta['employer_name'] ?? '',
            'employer_address' => $lead_meta['establishment_address'] ?? '',
            'wealth_employment_income' => '',
            'wealth_sale_of_inv_property' => '',
            'wealth_business_income' => '',
            'wealth_savings' => '',
            'wealth_inheritance' => '',
            'wealth_other' => '',
            'wealth_label' => '',
            'annual_income' => $lead_meta['annual_income'] ?? '',
            'purchase_end_use' => '',
            'purchase_savings' => '',
            'purchase_other' => '',
            'purchase_other_label' => '',
            'payment_cash' => '',
            'payment_cheque' => '',
            'payment_bank_transfer' => '',
            'payment_mortgage' => '',
            'payment_crypto' => '',
            'payment_other' => '',
            'payment_other_label' => '',
            'pep' => $lead_meta['politically_exposed_person'] ?? '',
            'pep_other_label' => '',
            'signature' => $sinature_file_url,
            'type' => $document_type,

        ]);


        if ($sinature_file_url != '') {

           // $res = (new FolderApiController)->generateKycIndividualFillableV2($pdfRequest);
                if($lead_meta['type'] == 'INDIVIDUAL'){
                $res = (new FolderApiController)->generateKycIndividualFillableV2($pdfRequest);
                }else{
                    $res = (new FolderApiController)->generateKycEntityFillableV2($pdfRequest);
                }

            if (!empty($res)) {

                if ($res['status'] == '1') {
                    $kyc_file = $res['url'];
                    $media = DealMedia::create([
                        "deal_id" => $invite->deal_id,
                        "model_type" => Lead::class,
                        "model_id" => $invite->lead_id,
                        "type" => "Other",
                        "title" => "Signed KYC",
                        "description" => null,
                        "extension" => 'pdf',
                        "thumbnail" => null,
                        "path" => $kyc_file,

                    ]);

                    $invite->is_signed = 1;
                    $invite->form_submitted = 1;
                    $invite->deal_media_id = $media->id;
                    $invite->save();

                    $user = User::find($invite->invited_by_id);
                    if(!empty($user) && !empty($user->telegram_chatid)){
                    $message ="Hi ".$user->name.",\n";
                    $message.= 'Buyer has submitted the KYC form. Please review and procceed with the deal.';
                    $this->sendTelegramMessageHtml($message, [$user->telegram_chatid]);
                    }

                }
                return response()->json(['status' => 'success', 'message' => 'Document signed successfully']);
            } else {
                return response()->json(['status' => 'success', 'message' => 'pdf not created']);
            }
        }
    }
    public function signDocumentOnDocusign($invite_code){
        $invite = DealInvite::where('invite_code', $invite_code)->first();
        $media = DealMedia::where('id', $invite->deal_media_id)->first();
        if($media){
            $url = $media->path;
            return redirect($url);
        }
    }
}
