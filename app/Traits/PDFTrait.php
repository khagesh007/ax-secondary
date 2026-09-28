<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

trait PDFTrait
{
    public function isRemoteFileExists($url) {
        try{
        $headers = @get_headers($url, 1);

        // Check if the HTTP status code is 200 (OK)
        if (strpos($headers[0], '200') !== false) {
            return true;
        }
        return false;
    }catch(Exception $e){
        return false;
    }
    }
    private function uploadPdf($file, $path, $disk = "s3")
    {

        Storage::disk($disk)->put($path, $file);
        $url = Storage::disk($disk)->url($path);
        $size = Storage::disk($disk)->size($path);

        return [
            "path" => $url,
            "disk" => $disk,
            "size" => $size
        ];
    }

    public function fillAndGenerateTenancyFormDubai($data = [], $name = "tenancy-form-dubai-output", $outputPath = "file-manager/pdf")
    {
        try {

            $sampleFilePath = "app/public/file-manager/document-templates/tenancy-form-dubai.pdf";
            $filePath = storage_path($sampleFilePath);
            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("tenancy-form-dubai-sample.pdf");
            }

            $outputFilePath = storage_path("app/public/" . $outputPath . $name . ".pdf");


            $fpdi = new Fpdi();

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 11);
                $fpdi->SetTextColor(0, 0, 0);



                $propertyUsage = $data["property_usage"] ?? "";

                if ($propertyUsage == "Industrial") {
                    $fpdi->Text(71, 35,  "I"); //MOU exists
                }

                if ($propertyUsage == "Commercial") {
                    $fpdi->Text(110, 35,  "C"); //MOU exists
                }

                if ($propertyUsage == "Residential") {
                    $fpdi->Text(150, 35,  "R"); //MOU exists
                }


                $fpdi->Text(25, 41, $data["owner_name"] ?? ""); // Owner name

                $fpdi->Text(27, 49, $data["landlord_name"] ?? ""); // landlord name

                $fpdi->Text(27, 56, $data["tenant_name"] ?? ""); // Tenent name

                $fpdi->Text(27, 63, $data["tenant_email"] ?? ""); //Tenent email
                $fpdi->Text(130, 63, $data["landlord_email"] ?? ""); // Landlord email

                $fpdi->Text(27, 71, $data["tenant_phone"] ?? ""); //Tenant phone
                $fpdi->Text(130, 71, $data["landlord_phone"] ?? ""); // landlord phone

                $fpdi->Text(27, 78, $data["building_name"] ?? ""); //Building name
                $fpdi->Text(120, 78, $data["location"] ?? ""); //Location


                $fpdi->Text(31, 86, $data["property_size"] ?? ""); //Property Size
                $fpdi->Text(104, 86, $data["property_type"] ?? ""); //Property Type
                $fpdi->Text(170, 86, $data["property_no"] ?? ""); //Property Number


                $fpdi->Text(34, 93, $data["premises_no"] ?? ""); //Premises no(DEWA)
                $fpdi->Text(118, 93, $data["plot_no"] ?? ""); //Plot no

                $fpdi->Text(36, 100, $data["contract_period_to"] ?? ""); //Contract period to
                $fpdi->Text(116, 100, $data["contract_period_from"] ?? ""); //Contract period from

                $fpdi->Text(24, 107, $data["annual_rent"] ?? ""); //Annual rent
                $fpdi->Text(27, 115, $data["contract_value"] ?? ""); //Contract value

                $fpdi->Text(40, 123, $data["security_deposit_amount"] ?? ""); //Security deposit amount
                $fpdi->Text(131, 123, $data["mode_of_payment"] ?? ""); //Mode of payment

                $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 5, 20, 5, 'PNG');
            }

            $fpdi->Output($outputFilePath, 'F');
            return $outputFilePath;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function fillAndGenerateContactBetweenBuyerAndBrokerFormB($data = [], $name = "contract-between-buyer-and-broker-form-b-output", $outputPath = "file-manager/pdf")
    {

        try {

            $sampleFilePath = "app/public/file-manager/document-templates/contract-between-buyer-and-broker-form-b.pdf";
            $filePath = storage_path($sampleFilePath);
            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("contract-between-buyer-and-broker-form-b-sample.pdf");
            }

            $outputFilePath = storage_path("app/public/" . $outputPath . $name . ".pdf");

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 9);
                $fpdi->SetTextColor(0, 0, 0);

                if ($i == 1) {
                    $fpdi->Text(40, 83, $data["party_agent_office_name"] ?? ""); // Office name

                    $fpdi->Text(45, 90, $data["party_agent_license_authority"] ?? ""); // License Authority

                    $fpdi->Text(30, 97, $data["party_agent_orn"] ?? ""); //ORN
                    $fpdi->Text(135, 97, $data["party_agent_license_number"] ?? ""); // License number

                    $fpdi->Text(30, 104, $data["party_agent_fax"] ?? ""); //FAX
                    $fpdi->Text(123, 104, $data["party_agent_phone"] ?? ""); // Phone number

                    $fpdi->Text(35, 111, $data["party_agent_address"] ?? ""); // Address

                    $fpdi->Text(30, 118, $data["party_agent_email"] ?? ""); // Email

                    $fpdi->Text(40, 125, $data["party_agent_name"] ?? ""); // Agent name

                    $fpdi->Text(30, 132, $data["party_agent_brn"] ?? ""); //BRN
                    $fpdi->Text(125, 132, $data["party_agent_mobile"] ?? ""); // Mobile number

                    $fpdi->Text(42, 149.5, $data["party_buyer_name"] ?? ""); // Buyer name

                    $fpdi->Text(45, 156.5, $data["party_buyer_id_card_number"] ?? ""); //ID card number
                    $fpdi->Text(126, 156.5, $data["party_buyer_nationality"] ?? ""); // Nationality

                    $fpdi->Text(40, 163.5, $data["party_buyer_passport_no"] ?? ""); //Passport number
                    $fpdi->Text(130, 163.5, $data["party_buyer_passport_expiry_date"] ?? ""); // Expiry date

                    $fpdi->Text(35, 170.5, $data["party_buyer_mobile"] ?? ""); //Mobile number
                    $fpdi->Text(125, 170.5, $data["party_buyer_po_box"] ?? ""); // PO Box

                    $fpdi->Text(31, 177.5, $data["party_buyer_phone"] ?? ""); //Phone
                    $fpdi->Text(120, 177.5, $data["party_buyer_fax"] ?? ""); // FAX

                    $fpdi->Text(34, 184.5, $data["party_buyer_address"] ?? ""); // Address

                    $fpdi->Text(30, 191.5, $data["party_buyer_email"] ?? ""); // Email

                    $fpdi->Text(40, 215.5, $data["property_type_of_area"] ?? ""); // Type of Area
                    $fpdi->Text(125, 215.5, $data["property_location"] ?? ""); //Location

                    $fpdi->Text(45, 222.5, $data["property_type_of_property"] ?? ""); // Type of Property

                    $fpdi->Text(42, 230, $data["property_buyer_budget"] ?? ""); //Buyer’s Budget

                    $fpdi->Text(30, 237, $data["property_area"] ?? ""); // Area

                    $fpdi->Text(45, 244, $data["property_tenanted_property"] ?? ""); //Tenanted Property
                    $fpdi->Text(130, 244, $data["property_present_use"] ?? ""); // Present use

                    $fpdi->Text(50, 251, $data["property_special_requirements"] ?? ""); //Special Requirements
                }

                if ($i == 2) {
                    $fpdi->Text(40, 75, $data["property_financial_ready_cash"] ?? ""); // Ready cash
                    $fpdi->Text(130, 75, $data["property_financial_finance_amout"] ?? ""); // Finance Amout

                    $fpdi->Text(45, 82, $data["property_financial_approval_date"] ?? ""); //Date of Approval
                    $fpdi->Text(120, 82, $data["property_financial_bank"] ?? ""); // Bank

                    $fpdi->Text(35, 99.5, $data["commission_start_date"] ?? ""); //Start date
                    $fpdi->Text(126, 99.5, $data["commission_end_date"] ?? ""); //End date

                    $fpdi->Text(50, 106.5, $data["commission_broker_commission"] ?? ""); //Broker's commission
                    $fpdi->Text(135, 106.5, $data["commission_activity_reporting"] ?? ""); //Activity reporting

                    $fpdi->Text(30, 132, $data["signature_party_name"] ?? ""); // Party name

                    $fpdi->Text(28, 139, $data["signature_title"] ?? ""); // Title


                    $fpdi->Text(30, 146, $data["signature_date"] ?? ""); // Date


                    $fpdi->Text(35, 200, $data["signature_buyer_name_english"] ?? ""); // Buyer name in English
                    $fpdi->Text(95, 200, $data["signature_buyer_name_arabic"] ?? ""); // Buyer name in Arabic


                    $fpdi->Text(30, 238, $data["signature_legal_representative_english"] ?? ""); // Legal representative name in English
                    $fpdi->Text(65, 238, $data["signature_legal_representative_arabic"] ?? ""); // Legal representative in Arabic
                    $fpdi->Text(120, 238, $data["signature_legal_attorney_number"] ?? ""); // Legal attorney number

                }
                // $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 5, 20, 5, 'PNG');
            }

            $fpdi->Output($outputFilePath, 'F');
            return $outputFilePath;
        } catch (Exception $ex) {
            return null;
        }
    }


    public function fillAndGenerateA2AAgreementForm($data = [], $name = "a2a-agreement-form-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/a2a-agreement-form.pdf";
            $filePath = storage_path($sampleFilePath);
            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("a2a-agreement-form-sample.pdf");
            }

            $outputFilePath = storage_path("app/public/" . $outputPath . $name . ".pdf");

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);



                $fpdi->Text(12, 45, $data["party_seller_established_name"] ?? ""); // Name of the establisment

                $fpdi->Text(12, 57, $data["party_seller_address"] ?? ""); // Address


                $fpdi->Text(15, 69, $data["party_seller_telephone"] ?? ""); // Phone number
                $fpdi->Text(68, 69, $data["party_seller_fax"] ?? ""); //FAX

                $fpdi->Text(19, 73, $data["party_seller_po_box"] ?? ""); // PO Box
                $fpdi->Text(68, 73, $data["party_seller_email"] ?? ""); //Email

                $fpdi->Text(19, 78, $data["party_seller_orn"] ?? ""); // ORN
                $fpdi->Text(68, 78, $data["party_seller_ded_license"] ?? ""); //DED License


                $fpdi->Text(19, 88, $data["party_seller_agent_name"] ?? ""); // Agent Name

                $fpdi->Text(17, 92, $data["party_seller_agent_brn_no"] ?? ""); //BRN No
                $fpdi->Text(67, 92, $data["party_seller_agent_date_issued"] ?? ""); //Date issued

                $fpdi->Text(19, 96, $data["party_seller_agent_mobile"] ?? ""); //Mobile

                $fpdi->Text(17, 101, $data["party_seller_agent_email"] ?? ""); //Email

                $fpdi->Text(43, 105, $data["party_seller_agent_str_no"] ?? ""); //Seller's agent form A str no




                $fpdi->Text(105, 45, $data["party_buyer_established_name"] ?? ""); // Name of the establisment

                $fpdi->Text(105, 57, $data["party_buyer_address"] ?? ""); // Address


                $fpdi->Text(15 + 94, 69, $data["party_buyer_telephone"] ?? ""); // Phone number
                $fpdi->Text(68 + 93, 69, $data["party_buyer_fax"] ?? ""); //FAX

                $fpdi->Text(19 + 95, 73, $data["party_buyer_po_box"] ?? ""); // PO Box
                $fpdi->Text(68 + 93, 73, $data["party_buyer_email"] ?? ""); //Email

                $fpdi->Text(19 + 93, 78, $data["party_buyer_orn"] ?? ""); // ORN
                $fpdi->Text(68 + 93, 78, $data["party_buyer_ded_license"] ?? ""); //DED License


                $fpdi->Text(19 + 93, 88, $data["party_buyer_agent_name"] ?? ""); // Agent Name

                $fpdi->Text(17 + 95, 92, $data["party_buyer_agent_brn_no"] ?? ""); //BRN No
                $fpdi->Text(67 + 94, 92, $data["party_buyer_agent_date_issued"] ?? ""); //Date issued

                $fpdi->Text(19 + 95, 96, $data["party_buyer_agent_mobile"] ?? ""); //Mobile

                $fpdi->Text(17 + 95, 101, $data["party_buyer_agent_email"] ?? ""); //Email

                $fpdi->Text(43 + 95, 105, $data["party_buyer_agent_str_no"] ?? ""); //buyer's agent form A str no


                //PROPERTY
                $fpdi->Text(30, 143, $data["property_address"] ?? ""); //Propety adress

                $fpdi->Text(30, 151, $data["property_master_developer"] ?? ""); //Master developer
                $fpdi->Text(33, 156, $data["property_master_project_name"] ?? ""); //Master project name

                $fpdi->Text(39, 160, $data["property_listing_agent_to_complete"] ?? ""); //Listing agent to complete

                $fpdi->Text(27, 164, $data["property_building_name"] ?? ""); //Building name

                $fpdi->Text(24, 168, $data["property_listet_price"] ?? ""); //Listed price

                $fpdi->Text(24, 173, $data["property_description"] ?? ""); //Description


                $propertyMOUExists = $data["property_mou_axists"] ?? "";

                if ($propertyMOUExists == 1) {
                    $fpdi->Text(61.5, 181,  "Y"); //MOU exists
                }

                if ($propertyMOUExists == 0) {
                    $fpdi->Text(79, 181,  "N"); //MOU exists
                }

                $propertyTenanted = $data["property_tenanted"] ?? "";

                if ($propertyTenanted == 1) {
                    $fpdi->Text(61.5, 185,  "Y"); //Tenanted
                }

                if ($propertyTenanted == 0) {
                    $fpdi->Text(79, 185,  "N"); //Tenanted
                }

                $fpdi->Text(30, 189, $data["property_maintenace_fee"] ?? ""); //Maintenance fee

                $fpdi->Text(30, 194, $data["property_additional_notes"] ?? ""); //Additional notes

                //COMMISSION
                $fpdi->Text(125, 160, $data["commission_seller_agent"] ?? ""); //Sellers agent
                $fpdi->Text(165, 160, $data["commission_buyer_agent"] ?? ""); //Buyers agent

                $fpdi->Text(141, 168, $data["commission_buyer_name"] ?? ""); //Buyer name


                $comissionTransferFeePaidBY = $data["commission_transfer_fee_paid_by"] ?? "";
                if ($comissionTransferFeePaidBY == "Seller") {
                    $fpdi->Text(143, 173, "Y");
                }
                if ($comissionTransferFeePaidBY == "Buyer") {
                    $fpdi->Text(160, 173, "Y");
                }
                if ($comissionTransferFeePaidBY == "Neg") {
                    $fpdi->Text(178, 173, "Y");
                }

                $comissionPreFinanceApproved = $data["commission_pre_finance_approved"] ?? "";
                if ($comissionPreFinanceApproved == 1) {
                    $fpdi->Text(160, 181, "Y");
                }
                if ($comissionPreFinanceApproved == 0) {
                    $fpdi->Text(177, 181, "N");
                }

                $comissionBuyerContactedToAgent = $data["commission_buyer_contacted_to_agent"] ?? "";
                if ($comissionBuyerContactedToAgent == 1) {
                    $fpdi->Text(160, 185, "Y");
                }
                if ($comissionBuyerContactedToAgent == 0) {
                    $fpdi->Text(177, 185, "N");
                }

                // $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 5, 20, 5, 'PNG');
            }

            $fpdi->Output($outputFilePath, 'F');
            return $outputFilePath;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function fillAndGenerateMarketingFormALeasingFillable($data = [], $name = "marketing-form-a-leasing-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/marketing-form-a-leasing-fillable.pdf";
            $filePath = storage_path($sampleFilePath);
            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("marketing-form-a-leasing-fillable-sample.pdf");
            }

            $outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                $propertyFor = $data["property_for"] ?? "";

                if ($propertyFor == "Residential") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 143.2, 43.1, 10, 10, 'PNG');
                }
                if ($propertyFor == "Commercial") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 174.4, 43.1, 10, 10, 'PNG');
                }

                $fpdi->Text(40, 60, $data["lsting_consultant"] ?? ""); // lsting_consultant
                $fpdi->Text(140, 60, $data["property_reference_no"] ?? ""); // property_reference_no

                $fpdi->Text(39, 77, $data["landlord_name"] ?? ""); //landlord_name

                $fpdi->Text(39, 84.6, $data["passport"] ?? ""); // passport_no
                $fpdi->Text(130, 84.6, $data["emirates_id_no"] ?? ""); //emirates_id_no

                $passportExpiry = $data["passport_expiry"] ?? "";
                if (!empty($passportExpiry)) {
                    $fpdi->Text(31, 93, date('d', strtotime($passportExpiry))); // expiry_date day
                    $fpdi->Text(40, 93, date('m', strtotime($passportExpiry))); // expiry_date month
                    $fpdi->Text(51, 93, date('Y', strtotime($passportExpiry))); // expiry_date year
                }

                $propertyType = $data["category"] ?? "";

                if ($propertyType == "Villa") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 26.3, 108.8, 10, 10, 'PNG');
                }

                if ($propertyType == "Apartment") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 66.7, 108.8, 10, 10, 'PNG');
                }


                $propertyFurnished = $data["property_furnished"] ?? "";


                if ($propertyFurnished == "FURNISHED") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 105, 108.8, 10, 10, 'PNG');
                }

                if ($propertyFurnished == "UNFURNISHED") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 147, 108.8, 10, 10, 'PNG');
                }
                if ($propertyFurnished == "SEMI_FURNISHED") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 193.7, 108.8, 10, 10, 'PNG');
                }

                $propertyStatus = $data["property_status"] ?? "";


                if ($propertyStatus == "Vacant") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 26.4, 117.4, 10, 10, 'PNG');
                }

                if ($propertyStatus == "Tenanted") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 75, 117.4, 10, 10, 'PNG');
                }

                $fpdi->Text(122, 123.7, $data["availability_date"] ?? "");

                $fpdi->Text(35.5, 132, $data["building_name"] ?? "");
                $fpdi->Text(113, 132, $data["unit_no"] ?? "");

                $fpdi->Text(39, 140.5, $data["street"] ?? "");
                $fpdi->Text(118, 140.5, $data["community"] ?? "");

                $fpdi->Text(30, 149, $data["build_up_area"] ?? "");
                $fpdi->Text(123, 149, $data["plot_size"] ?? "");

                $fpdi->Text(29, 157.3, $data["bedrooms"] ?? "");
                $fpdi->Text(74, 157.3, $data["bathrooms"] ?? "");
                $fpdi->Text(112, 157.3, $data["parking"] ?? "");

                $fpdi->Text(36, 166, $data["rental_amount"] ?? "");
                $fpdi->Text(32, 175, $data["commission"] ?? "");

                $landlordAgreedToAppointAs = $data["landlord_agreed_to_appoint_as"] ?? "";
                if ($landlordAgreedToAppointAs == "Exclusive") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 13.1, 201.5, 10, 10, 'PNG');
                }
                if ($landlordAgreedToAppointAs == "Non-Exclusive") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 41.6, 201.5, 10, 10, 'PNG');
                }

                $advertisePeriodOfMonth = $data["advertise_period_of_month"] ?? "";
                if ($advertisePeriodOfMonth == "1") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 13.1, 209.5, 10, 10, 'PNG');
                }
                if ($advertisePeriodOfMonth == "2") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 41.6, 209.5, 10, 10, 'PNG');
                }
                if ($advertisePeriodOfMonth == "3") {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 68.4, 209.4, 10, 10, 'PNG');
                }

                $advertiseFrom = $data["advertise_from"] ?? "";
                if (!empty($advertiseFrom)) {
                    $fpdi->Text(113, 215.4, date('d', strtotime($advertiseFrom))); // day
                    $fpdi->Text(125, 215.4, date('m', strtotime($advertiseFrom))); // Month
                    $fpdi->Text(135, 215.4, date('Y', strtotime($advertiseFrom))); // year
                }

                $advertiseTo = $data["advertise_to"] ?? "";
                if (!empty($advertiseTo)) {
                    $fpdi->Text(153, 215.4, date('d', strtotime($advertiseTo))); // day
                    $fpdi->Text(162, 215.4, date('m', strtotime($advertiseTo))); // Month
                    $fpdi->Text(174, 215.4, date('Y', strtotime($advertiseTo))); // year
                }
                // $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 5, 20, 5, 'PNG');
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            return null;
        }
    }
    public function fillAndGenerateKycEntityFillable($data = [], $name = "KYC_Form_For_Entity-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
             //check if signture is empty
        if(empty($data['signature'])) {
            //check if storage has the signture
             if(Storage::exists($data['signature'])) {
                 $data['signature'] = storage_path($data['signature']);
             }else {
                 $data['signature'] = '';
             }
         }

            $sampleFilePath = "app/public/file-manager/document-templates/KYC_Form_For_Entity.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/LEAD_KYC_Form_For_Entity.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(70, 120, $data['full_legal_name']); //Full legal name
                    $fpdi->Text(70, 135,  $data['company_trading_name']); //Trading name
                    $fpdi->Text(70, 148, $data['company_trad_license_no']); //Trading number
                    $fpdi->Text(100, 162, $data['coi']); //Country of incorporation
                    $fpdi->Text(93, 176, $data['doi']); //Date of incorporation
                    $fpdi->Text(70, 188, $data['address']); //Registered address
                    $fpdi->Text(85, 201, $data['trade_license_authority']); //Trade license issuing authority
                    //contact details
                    $fpdi->Text(28, 230, $data['phone']); //Telephone number
                    $fpdi->Text(72, 230, $data['email']); //Email id
                    $fpdi->Text(114, 230, $data['trade_license_issue_date']); //Trade license issue date:
                    $fpdi->Text(160, 230, $data['trade_license_expiry_date']); //Trade license expiry date
                    //ubo details
                   // $fpdi->Text(160, 330, "khagesh"); //Trade license expiry date

                }else if($i == 2) {
                    $fpdi->Text(50, 51, $data['ubo_name_1']); //Full name
                    $fpdi->Text(172, 51, $data['ubo_percentage_1']); //Percentage owed
                    $fpdi->Text(50, 69, $data['ubo_name_2']); //Full name
                    $fpdi->Text(172, 69, $data['ubo_percentage_2']); //Percentage owed

                    $fpdi->Text(33, 109, $data['company_person1_name']); //Full name
                    $fpdi->Text(135, 109, $data['company_person1_relationship']); //Relationship

                    $fpdi->Text(33, 122, $data['company_person2_name']); //Full name
                    $fpdi->Text(135, 122, $data['company_person2_relationship']); //Relationship

                    $fpdi->Text(35, 163, $data['company_product']); //Business details. (products/ services)
                    $fpdi->Text(35, 191, $data['company_primary_countries']); //Which are the primary countries

                }else if($i == 3) {
                    //page 3

                    $fpdi->Text(27, 50, $data['company_individual_occupation']); //individual’s occupation
                    $fpdi->Text(112, 50, $data['name_of_employer']); //f Establishment or Employer
                    //countries
                    if($data['deal_with_risky_countries'] == '1') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 127.65, 70.8, 9, 9, 'PNG'); //if yes
                    }else {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 127.65, 75.31, 9, 9, 'PNG'); //if No
                    }
                    //PEP
                    if($data['politically_exposed'] == '1') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 138.2, 109, 9, 9, 'PNG'); //if Yes
                    }else {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 138.2, 113.6, 9, 9, 'PNG'); //if No
                    }
                    if($data['signature'] != '') {
                        if($data['signature'] !='' && $this->isRemoteFileExists($data['signature'])) {
                        $fpdi->Image($data['signature'], 90, 173, 35, 13, 'PNG'); //signature file
                        }
                    }
                    $fpdi->Text(90, 190, $data['company_person_name']); //person acting on behalf of company
                    $fpdi->Text(90, 201, $data['company_person_relationship']); //relationship with the company
                    $fpdi->Text(90, 210, $data['declaration_date']); //date
                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }

    public function fillAndGenerateKycIndividualFillable($data = [], $name = "Form_A_contract_seller_broker-fillable-output", $outputPath = "file-manager/pdf")
    {


        //check if signture is empty
        if(empty($data['signature'])) {
           //check if storage has the signture
            if(Storage::exists($data['signature'])) {
                $data['signature'] = storage_path($data['signature']);
            }else {
                $data['signature'] = '';
            }
        }

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/KYC_Form_Individual.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/KYC_Form_Individual.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(80, 113, $data['full_name']); //Full name
                    $fpdi->Text(80, 123,  $data['aliases']); //Aliases name
                    $fpdi->Text(80, 133, $data['current_residential_address']); //Current Residential Address

                    $fpdi->Text(21, 148, $data['email']); //Email
                    $fpdi->Text(80, 148, $data['telephone_number']); //TelePhone number
                    $fpdi->Text(140, 148, $data['mobile_number']); //Mobile number
                    $fpdi->Text(82, 162, $data['pob']); //Country of incorporation
                    $fpdi->Text(21, 162, $data['dob']); //Date of incorporation
                    if($data['gender'] == 'Female') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 137.3, 154.9, 9, 9, 'PNG'); //if gender is female
                    }else {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 162.4, 154.9, 9, 9, 'PNG'); //if gender is male
                    }
                    //CITIZENSHIP
                    $fpdi->Text(20, 220, $data['nationality']); //Nationality
                    $fpdi->Text(80, 220, $data['passport_number']); //Passport No.:
                    $fpdi->Text(141, 220, $data['passport_issue_country']); //Trade license issuing authority
                    $fpdi->Text(20, 235, $data['passport_issue_date']); //issue Date
                    $fpdi->Text(80, 235, $data['passport_expiry_date']); //expiry Date
                }else if($i == 2) {
                    if($data['fund_employment_income'] == 'true') {
//                        exit($data['fund_employment_income']);
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16.53, 51, 9, 9, 'PNG'); //fund employment income
                    }
                    if($data['fund_business_income'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16.53, 60.21, 9, 9, 'PNG'); //Business income
                    }
                    if($data['fund_other'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16.53, 69.51, 9, 9, 'PNG'); //fund_other
                        $fpdi->Text(40, 74, "Investment"); //fund other label
                    }
                    if($data['fund_sale_of_investment'] == 'true') {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 93, 51, 9, 9, 'PNG'); //fund_sale_of_investment
                        }
                    if($data['fund_savings'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 93.6, 60.2, 9, 9, 'PNG'); //if gender is male
                    }





                    $fpdi->Text(21, 133, $data['residency_visa_number']); //Full name
                    $fpdi->Text(80, 133, $data['emirates_id_no']); //Emirates
                    if($data['visa_expiry_date'] != ''){
                        $fpdi->Text(21, 133, $data['visa_expiry_date']); //Expiry Date
                    }

                    $fpdi->Text(21, 199, $data['occupation']); //Occupation
                    $fpdi->Text(107, 199, $data['years_in_role']); //Years in Present Rol
                    $fpdi->Text(21, 219, $data['employer_name']); //employer_name
                    $fpdi->Text(107, 219, $data['employer_address']); //Years in Present Rol

                }else if($i == 3) {
                    //page 3
                    //SOURCE OF WEALTH
                    if($data['wealth_employment_income'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16, 51.58, 9, 9, 'PNG'); //Employment Income
                    }
                    if($data['wealth_sale_of_inv_property'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 91.46, 51.58, 9, 9, 'PNG'); //Sale of Investment or Propertie
                    }
                    if($data['wealth_business_income'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16, 60.71, 9, 9, 'PNG'); //Business Income
                    }
                    if($data['wealth_savings'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 91.2, 60.71, 9, 9, 'PNG'); //Savings
                    }
                    if($data['wealth_inheritance'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16, 70, 9, 9, 'PNG'); //Inheritance
                    }
                    if($data['wealth_other'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 90.6, 70, 9, 9, 'PNG'); //Other

                        $fpdi->Text(113, 75, $data['wealth_label']); //other wealth label
                    }
                    $fpdi->Text(49, 90, $data['annual_income']); //Annual Income
                    if($data['purchase_end_use'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 63.5, 93.9, 9, 9, 'PNG'); //End Use
                    }
                    if($data['purchase_savings'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 96.5, 93.9, 9, 9, 'PNG'); //Savings
                    }
                    if($data['purchase_other'] == 'true') {
                    $fpdi->Image(public_path("assets/icons/dot.png"), 126.7, 93.9, 9, 9, 'PNG'); //Other
                        $fpdi->Text(149, 99, $data['purchase_other_label']); //Annual Income
                        }


                    if($data['payment_cash'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 63.5, 103.2, 9, 9, 'PNG'); //cash
                    }
                    if($data['payment_cheque'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 96, 103.2, 9, 9, 'PNG'); //cheque
                    }
                    if($data['payment_bank_transfer'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 125.6, 103.2, 9, 9, 'PNG'); //Bank Transfer
                    }

                    if($data['payment_mortgage'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 63.2, 112.5, 9, 9, 'PNG'); //Mortgage
                    }
                    if($data['payment_crypto'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 96.2, 112.5, 9, 9, 'PNG'); //Crypto
                    }
                    if($data['payment_other'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 125.6, 112.5, 9, 9, 'PNG'); //Other
                        $fpdi->Text(149, 117, $data['payment_other_label']); //Other
                    }


                    //pep
                    if($data['pep'] == 'Yes') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 16, 135, 9, 9, 'PNG'); //PEP YES
                    }
                    if($data['pep'] == 'No') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 41.4, 135, 9, 9, 'PNG'); //PEP NO
                    }
                    if($data['pep'] == 'Other') {
                        $fpdi->Image(public_path("assets/icons/dot.png"), 67.8, 135, 9, 9, 'PNG'); //OTHER
                        $fpdi->Text(90, 140, $data['pep_other_label']); //PEP Other
                    }


                    if($data['signature'] != '') {
                        if($data['signature'] !='' && $this->isRemoteFileExists($data['signature'])) {
                        $fpdi->Image($data['signature'], 90, 193, 35, 15, 'PNG'); //signature file
                        $fpdi->Text(90, 218, date('d/m/Y')); //signature date
                        }
                    }

                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath,'public');
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
 public function writeFormAContractSellerBroker($data = [], $name = "Form_A_contract_seller_broker-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/Form_A_contract_seller_broker.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/Form_A_contract_seller_broker.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(88, 55.6, $data['contract_number']); //Contract number
                    $fpdi->Text(40, 84, $data['agent_office_name']); //Office name
                    $fpdi->Text(135, 55.6, $data['agent_office_name_ar']); //Contract number arabic
                    $fpdi->Text(45, 91, $data['agent_license_authority']); //License authority
                    $fpdi->Text(135, 91, $data['agent_license_authority_ar']); //License authority
                    $fpdi->Text(30, 97, $data['agent_orn']); //ORN name
                    $fpdi->Text(135, 97, $data['agent_license_number']); //License number
                    $fpdi->Text(30, 104, $data['agent_fax']); //Fax
                    $fpdi->Text(121, 104, $data['agent_phone']); //PHONE NUMBER
                    $fpdi->Text(35, 111, $data['agent_address']); //Address
                    $fpdi->Text(128, 111, $data['agent_address_ar']); //العنوان
                    $fpdi->Text(35, 119, $data['agent_email']); //email
                    $fpdi->Text(40, 125, $data['agent_name']); //agent name
                    $fpdi->Text(128, 125, $data['agent_name_ar']); //اسم الوسيط
                    $fpdi->Text(32, 132, $data['agent_brn']); //BRN
                    $fpdi->Text(122, 132, $data['agent_mobile']); //MOBILE

                    //THE PROPERTY OWNER’S DETAILS
                    $fpdi->Text(43, 150, $data['owner_name']); //Name of owner
                    $fpdi->Text(125, 150, $data['owner_name_ar']); //Name of owner right
                    $fpdi->Text(43, 157, $data['owner_id_card_number']); //ID CARD NUMBER
                    $fpdi->Text(127, 157, $data['owner_nationality']); //Nationality
                    $fpdi->Text(40, 164, $data['owner_passport_number']); //PASSPORT NUMBER
                    $fpdi->Text(35, 171, $data['owner_mobile']); //OWNER MOBILE
                    $fpdi->Text(35, 178, $data['owner_phone']); //OWNER PHONE
                    $fpdi->Text(35, 185, $data['owner_address']); //ADDRESS
                    $fpdi->Text(124, 185, $data['owner_address_ar']); //العنوان
                    $fpdi->Text(35, 192, $data['owner_email']); //OWNER EMAIL
                    $fpdi->Text(128, 164, $data['owner_passport_expiry_date']); //passport Expiry date
                    $fpdi->Text(125, 171, $data['owner_po_box']); //PO BOX
                    $fpdi->Text(124, 178, $data['owner_fax']); //OWNER FAX



                }else if($i == 2) {


                    $fpdi->Text(88, 55.6, $data['contract_number']); //Contract number
                    $fpdi->Text(42, 82, $data['property_status']); //PROPERTY STATUS
                    $fpdi->Text(40, 89, $data['type_of_area']); //Type of Area
                    $fpdi->Text(36, 96, $data['property_location']); //Location
                    $fpdi->Text(44, 103, $data['property_type']); //Type of Property
                    $fpdi->Text(32, 110, $data['property_area']); //Area
                    $fpdi->Text(40, 118, $data['property_present_use']); //present use
                    $fpdi->Text(55, 124, $data['property_age']); //Approx Age
                    $fpdi->Text(50, 131, $data['car_parks']); //no. of car parks
                    $fpdi->Text(50, 138, $data['bathrooms']); //no of bathrooms
                    $fpdi->Text(55, 145, $data['unit_numbers']); //unit no.
                    $fpdi->Text(40, 152, $data['no_of_floors']); //no. of floor
                    $fpdi->Text(35, 159, $data['facilities']); //facilities
                    $fpdi->Text(42, 166, $data['extra_facilities']); //extra facilities
                    $fpdi->Text(51, 173, $data['additional_information']); //additional information

                    $fpdi->Text(130, 82, $data['property_plot_number']); //plot number
                    $fpdi->Text(136, 89, $data['title_deed_number']); //Title Deed Number
                    $fpdi->Text(135, 96, $data['property_number']); //property number
                    $fpdi->Text(130, 103, $data['project_name']); //Project Name
                    $fpdi->Text(140, 110, $data['owner_association_no']); //Owner’s Association No
                    $fpdi->Text(140, 117, $data['community_number']); //Community number
                    $fpdi->Text(140, 131, $data['bedrooms']); //no. of BEDROOMS
                    $fpdi->Text(140, 138,  $data['kitchens']); //no. of kitchen
                    $fpdi->Text(140, 145, $data['floor_no']); //floor no.

                    $fpdi->Text(135, 159, $data['facilities_ar']); //الخدمات
                    $fpdi->Text(135, 166, $data['extra_facilities_ar']); //
                    $fpdi->Text(135, 173, $data['additional_information_ar']); //

                    //PROPERTY FINANCIAL
                    $fpdi->Text(40, 191, $data['listed_price']); //LISTED PRICE
                    $fpdi->Text(132, 191, $data['original_price']); //Original PRICE
                    $fpdi->Text(40, 198, $data['paid_amount']); //Paid amount
                    $fpdi->Text(134, 198, $data['balance_amount']); //Balance amount
                    $fpdi->Text(43, 205, $data['service_charge']); //service charge
                    $fpdi->Text(45, 212, $data['mortgage_status']); //Mortgage status
                    $fpdi->Text(30, 219, $data['property_bank']); //Bank
                    $fpdi->Text(135, 219, $data['mortgage_amount']); //Mortgage amount
                    $fpdi->Text(49, 226, $data['pre_closure_charge']); //pre-closure charges
                    $fpdi->Text(128, 226, $data['pre_closure_charge_ar']); //pre-closure charges arabic
                    $fpdi->Text(48, 233, $data['payment_schedule']); //Payment schedule
                    $fpdi->Text(128, 233, $data['payment_schedule_ar']); //Payment schedule arabic

                    $fpdi->Text(70, 247, $data['payment_date1']); //date
                    $fpdi->Text(70, 254, $data['payment_date2']); //date
                    $fpdi->Text(125, 247, $data['amount_aed1']); //amount
                    $fpdi->Text(125, 254, $data['amount_aed2']); //amount


                }else if($i == 3) {
                    //page 3
                    $fpdi->Text(88, 55.6, $data['contract_number']); //Contract number
                    $fpdi->Text(50, 75, $data['is_rented']); //is property rented
                    $fpdi->Text(150, 75, $data['is_rented_ar']); //هل العقار مؤجر؟

                    $fpdi->Text(50, 93, $data['contract_start_date']); //contract start date
                    $fpdi->Text(57, 100, $data['commission_amount']); //commission amount
                    $fpdi->Text(41, 107, $data['contract_type']); //contract type

                    $fpdi->Text(138, 93, "01/04/2024"); //contract end date
                    $fpdi->Text(132, 100, $data['commission_amount_ar']); //commission amount
                    $fpdi->Text(135, 107, $data['activity_reporting']); //activity reporting type
                    //FIRST PARTY
                    $fpdi->Text(32, 132, $data['broker_name']); //name
                    $fpdi->Text(135, 132, $data['broker_name_ar']); //name
                    $fpdi->Text(32, 139, $data['broker_title']); //title
                    $fpdi->Text(135, 139, $data['broker_title_ar']); //title arabic
                    $fpdi->Text(32, 146, $data['broker_date']); //date

                    $fpdi->Text(22, 198, $data['owner_name_en_1']); //owner name 1
                    $fpdi->Text(22, 205, $data['owner_name_en_2']); //owner name 2
                    $fpdi->Text(22, 212, $data['owner_name_en_3']); //owner name 3

                    $fpdi->Text(90, 198, $data['owner_name_ar_1']); //owner name 1
                    $fpdi->Text(90, 205, $data['owner_name_ar_2']); //owner name 2
                    $fpdi->Text(90, 211, $data['owner_name_ar_3']); //owner name 3

                    $fpdi->Text(22, 238, $data['legal_rep_name_en_1']); //Legal Representative
                    $fpdi->Text(22, 245, $data['legal_rep_name_en_2']); //Legal Representative
                    $fpdi->Text(22, 252, $data['legal_rep_name_en_3']); //Legal Representative

                    $fpdi->Text(70, 238, $data['legal_rep_name_ar_1']); //Legal Representative arabic
                    $fpdi->Text(70, 245, $data['legal_rep_name_ar_2']); //Legal Representative arabic
                    $fpdi->Text(70, 252, $data['legal_rep_name_ar_3']); //Legal Representative arabic

                    $fpdi->Text(117, 238, $data['attorney_number1']); //attorney number
                    $fpdi->Text(117, 245, $data['attorney_number2']); //attorney number
                    $fpdi->Text(117, 252, $data['attorney_number3']); //attorney number
                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
    public function writeKeyHandoverForm($data = [], $name = "Key-handover-form-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/Key_handover_form.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/Key_Handover_Form.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(30, 78, date('d/m/Y',strtotime($data['date']))); //Date
                    $fpdi->Text(95, 78, $data['time']); //Time
                    $fpdi->Text(50, 96, $data['property_details']); //Property details
                    $fpdi->Text(40, 113, $data['main_door']); //Main door
                    $fpdi->Text(122, 113, $data['parking']); //Parking
                    $fpdi->Text(49, 122, $data['main_door_keys']); //Main door keys
                    $fpdi->Text(130, 122, $data['access_card']); //access card
                    $fpdi->Text(62, 131,  $data['main_door_access_card']); //Main Door Access Card
                    $fpdi->Text(130, 131, $data['transporter']); //Transporter
                    $fpdi->Text(70, 139, $data['main_door_remote_control']); //Main Door Remote Control
                    $fpdi->Text(137, 139, $data['remote_control']); //Main Door Remote Control
                    $fpdi->Text(34, 149, $data['others']); //Main Door Remote Control
                    $long_text = $data['long_text'];
                    $broken_text = wordwrap($long_text, 100, "||");
                    $exploded = explode("||", $broken_text);
                    if(count($exploded) > 1) {
                        foreach ($exploded as $key => $value) {
                            $fpdi->Text(20, 162 + ($key * 7.5), $value);
                        }
                    }
                    $fpdi->Text(23, 196, $data['user_name']); //first person name
                    $fpdi->Text(21, 207, $data['user_relative_name']); //first person name



                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
    public function writeLeasingLetterIntentPayable($data = [], $name = "Leasing-Letter-of-Intent-Payables-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/Leasing_Letter_of_Intent_Payables.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/Leasing_Letter_of_Intent_Payables.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(31, 86, date('d/m/Y',strtotime($data['date']))); //Date
                    $fpdi->Text(45, 110, $data['client_name']); //Property details
                    $fpdi->Text(39, 134, $data['property']); //Property details
                    $fpdi->Text(55, 145, date('d/m/Y',strtotime($data['leasing_start_date']))); //Property details



                    $fpdi->Text(43, 167, $data['annual_rent']); //Annual Rent
                    $fpdi->Text(43, 178, $data['annual_rent_payable_to']); //Payable to

                    $fpdi->Text(52, 199, $data['security_deposit']); //Security Deposit
                    $fpdi->Text(50, 210, $data['security_deposit_payable_to']); //Paybal to

                    $fpdi->Text(48, 233, $data['agency_fee']); //agency fee
                    $fpdi->Text(48, 243, $data['agency_fee_payable_to']); //Paybal to


                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
    public function writeLeasingReservationForm($data = [], $name = "Leasing-Reservation-Form-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/Leasing_Reservation_Form.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/Leasing_Reservation_Form.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);
                $fpdi->SetFont('Arial', '', 11);
                // $fpdi->SetFont("helvetica", "", 11);
                $fpdi->SetTextColor(0, 0, 0);
                if($i == 1) {

                    if($data['residential'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 34, 4, 4, 'PNG'); // residential
                    }
                    if($data['commercial'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 39, 4, 4, 'PNG'); //commercial
                    }
                    if($data['individual'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 136, 48, 4, 4, 'PNG'); //contract type Individual
                    }
                    if($data['corporate'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 158, 48, 4, 4, 'PNG'); //contract type Corporate
                    }
                    if($data['government'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 179, 48, 4, 4, 'PNG'); //contract type Government
                    }

//                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 63, 64, 4, 4, 'PNG'); //adults
//
//                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 88, 64, 4, 4, 'PNG'); //children

                    $fpdi->Text(49, 40, $data['property_name']); //name of property
                    $fpdi->Text(49, 50, $data['tenant']); //buyer/tenant
                    $fpdi->SetFont('Arial', '', 9);
                    $fpdi->Text(144, 37.5, $data['unit']); //unit#
                    $fpdi->Text(185, 37.5, $data['unit_types']); //Unit Type#
                    $fpdi->Text(144, 42.5, $data['unit_ref']); //Unit Ref#
                    $fpdi->Text(185, 42.5, $data['unit_area']); //Unit AREA

                    $fpdi->SetFont('Arial', '', 11);
                    $fpdi->Text(117, 66.5, $data['children_age']); //children age

                    $fpdi->Text(63, 66.5, $data['adults']); //adults
                    $fpdi->Text(88, 66.5, $data['children']); //children

                    $fpdi->Text(170, 66.5, $data['marital_status']); //marital status
                    $fpdi->Text(49, 59, $data['occupants_name']); //Name of occupant
                    $fpdi->Text(154, 59, $data['po_box']); //PO box
                    $fpdi->Text(49, 76, $data['tenant_passport']); //Tenant passport
                    $fpdi->Text(154, 76, $data['visa_type']); //Type of visa

                    $fpdi->Text(49, 85, $data['dob']); //Date of birth
                    $fpdi->Text(154, 85, $data['visa_no']); //visa number

                    $fpdi->Text(49, 92.5, $data['profession']); //Profession
                    $fpdi->Text(154, 92.5, $data['nationality']); //nationality

                    $fpdi->Text(49, 100.5, $data['office_number']); //Office Number
                    $fpdi->Text(110, 100.5, $data['mobile']); //Mobile
                    $fpdi->SetFont('Arial', '', 9);
                    $fpdi->Text(170, 100.5, $data['fax']); //fax
                    $fpdi->SetFont('Arial', '', 11);

                    $fpdi->Text(49, 110, $data['email']); //email
                    $fpdi->Text(49, 118.5, $data['company_name']); //name company
                    $fpdi->Text(49, 128, $data['landlord_name']); //name of landlord

                    $fpdi->Text(49, 138, date('d/m/Y',strtotime($data['contract_start_date']))); //Contract start date
                    $fpdi->Text(154, 138, $data['lease_term']); //Lease Term

                    $fpdi->Text(57, 147.5, $data['annual_rent']); //Annual rent
                    $fpdi->Text(154, 147.5,  $data['cheques']); //no. of cheques

                    $fpdi->Text(57, 157.5, $data['security_deposit']); //security dep
                    $fpdi->Text(167, 157.5, $data['agency_fee']); //agency fee

                }

                //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
    public function writeSalesReservationForm($data = [], $name = "KYC-Compliance-Declaration-Form-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/KYC_Compliance_Declaration_Form.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/KYC_Compliance_Declaration_Form.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);
                $fpdi->SetFont('Arial', '', 11);
                // $fpdi->SetFont("helvetica", "", 11);
                $fpdi->SetTextColor(0, 0, 0);
                if($i == 1) {

                    if($data['residential'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 34, 4, 4, 'PNG'); // residential
                    }
                    if($data['commercial'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 118, 39, 4, 4, 'PNG'); //commercial
                    }
                    if($data['individual'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 136, 48, 4, 4, 'PNG'); //contract type Individual
                    }
                    if($data['corporate'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 158, 48, 4, 4, 'PNG'); //contract type Corporate
                    }
                    if($data['government'] == 'true') {
                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 179, 48, 4, 4, 'PNG'); //contract type Government
                    }

//                        $fpdi->Image(public_path("assets/icons/check-mark.png"), 63, 64, 4, 4, 'PNG'); //adults
//
//                    $fpdi->Image(public_path("assets/icons/check-mark.png"), 88, 64, 4, 4, 'PNG'); //children

                    $fpdi->Text(49, 40, $data['property_name']); //name of property
                    $fpdi->Text(49, 50, $data['tenant']); //buyer
                    $fpdi->SetFont('Arial', '', 9);

                    $fpdi->Text(144, 37.5, $data['unit']); //unit#
                    $fpdi->Text(185, 37.5, $data['unit_types']); //Unit Type#
                    $fpdi->Text(144, 42.5, $data['unit_ref']); //Unit Ref#
                    $fpdi->Text(185, 42.5, $data['unit_area']); //Unit AREA

                    $fpdi->SetFont('Arial', '', 11);
                    $fpdi->Text(117, 66.5, $data['children_age']); //children age
                    $fpdi->Text(63, 66.5, $data['adults']); //adults
                    $fpdi->Text(88, 66.5, $data['children']); //children

                    $fpdi->Text(170, 66.5, $data['marital_status']); //marital status
                    $fpdi->Text(49, 59, $data['deal_type']); //Deal type
                    $fpdi->Text(154, 59, $data['po_box']); //PO box
                    $fpdi->Text(49, 76, $data['tenant_passport']); //Tenant passport
                    $fpdi->Text(154, 76, $data['visa_type']); //Type of visa

                    $fpdi->Text(49, 85, "01/01/2004"); //Date of birth
                    $fpdi->Text(154, 85, "visa7878"); //visa number

                    $fpdi->Text(49, 92.5, "Software Engineer"); //Profession
                    $fpdi->Text(154, 92.5, "Indian"); //visa number

                    $fpdi->Text(49, 100.5, "077654981"); //Office Number
                    $fpdi->Text(110, 100.5, "87374889"); //Mobile
                    $fpdi->Text(170, 100.5, "SDAF8787"); //fax

                    $fpdi->Text(49, 110, "test@email.com"); //email
                    $fpdi->Text(49, 118.5, "Facebook Inc"); //name company
                    $fpdi->Text(49, 128, "Joe Tribe"); //name of landlord

                    $fpdi->Text(49, 138, "01/01/2024"); //Contract start date
                    $fpdi->Text(154, 138, "2yrs"); //Lease Term

                    $fpdi->Text(57, 147.5, "1,20,000"); //Annual rent
                    $fpdi->Text(154, 147.5, "6"); //Lease Term

                    $fpdi->Text(57, 157.5, "1,20,000"); //security dep
                    $fpdi->Text(166, 157.5, "6000"); //agency fee

                }

                //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }
    public function writeKycComplianceDForm($data = [], $name = "KYC-Compliance-Declaration-Form-fillable-output", $outputPath = "file-manager/pdf")
    {

        $yes_no = explode(",", $data['yes_no']);
        $explanation = explode(",", $data['explanation']);

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/KYC_Compliance_Declaration_Form.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/KYC_Compliance_Declaration_Form.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);
                $fpdi->SetFont('Arial', '', 11);
                // $fpdi->SetFont("helvetica", "", 11);
                $fpdi->SetTextColor(0, 0, 0);
                if($i == 1) {




                    $fpdi->Text(21, 77, $data['we']); //we
                    $fpdi->Text(140, 77, $data['trade_license']); //trade license no.
                    $fpdi->Text(52, 84, $data['reg_no']); //REG NO.


                    $fpdi->Text(21, 156.5, $data['company_name']); //COMPANY NAME
                    $fpdi->Text(142, 156.5, $data['company_trade_license']); //agency fee

                    //share holders and passports
                    $fpdi->Text(24, 171, $data['share_holder1']); //share holder name 1
                    $fpdi->Text(142, 171, $data['share_holder_passport_no1']); //passport number 1
                    $fpdi->Text(24, 186, $data['share_holder2']); //share holder name 2
                    $fpdi->Text(142, 186, $data['share_holder_passport_no2']); //passport number 2
                    $fpdi->Text(24, 201, $data['share_holder3']); //share holder name 3
                    $fpdi->Text(142, 201, $data['share_holder_passport_no2']); //passport number 3
                    //table form
                    $fpdi->Text(123, 240, $yes_no[0]); //Does the firm have
                    $fpdi->Text(123, 255, $yes_no[1]); //Are the procedures
                    $fpdi->SetFont('Arial', '', 9);
                    $long_text = $explanation[0];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 233 + ($key * 4.5), $value);
                    }



                    $long_text = $explanation[1];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 250 + ($key * 4.5), $value);
                    }

                }else  if($i == 2) {
                    $fpdi->Text(124, 38, $yes_no[2]); //Do you have procedures
                    $fpdi->Text(124, 54, $yes_no[3]); //Do you have procedures in place to identify Politically
                    $fpdi->Text(124, 70, $yes_no[4]); //record retention procedures
                    $fpdi->Text(124, 86, $yes_no[5]); //Has your company or any of the shareholders
                    $fpdi->Text(124, 104, $yes_no[6]); //Dose your company conduct Client Risk Asse
                    $fpdi->Text(124, 120, $yes_no[7]); //Does the firm have procedures to establish a
                    $fpdi->Text(124, 136, $yes_no[8]); //m have a process to review and, where
                    $fpdi->Text(124, 154, $yes_no[9]); //systems & procedures in place to screen
                    $fpdi->Text(124, 170, $yes_no[10]); //Do you have a designated and Independent
                    $fpdi->Text(124, 186, $yes_no[11]); //Do you have a regular AML/CFT/Sanctio
                    $fpdi->Text(124, 204, $yes_no[12]); //internal audit function


                    $fpdi->SetFont('Arial', '', 9);
                    $long_text = $explanation[2];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 32 + ($key * 4.5), $value);
                    }
                    //Do you have procedures in place to identify Politically
                    $long_text = $explanation[3];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 49 + ($key * 4.5), $value);
                    }
                    //have record retention procedures in place
                    $long_text = $explanation[4];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 66 + ($key * 4.5), $value);
                    }
                    //Has your company or any of the
                    $long_text = $explanation[5];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 82 + ($key * 4.5), $value);
                    }
                    //Dose your company conduct Client Risk
                    $long_text = $explanation[6];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 98 + ($key * 4.5), $value);
                    }
                    //Does the firm have procedures to establish
                    $long_text = $explanation[7];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 114 + ($key * 4.5), $value);
                    }

                    //Have a process to review and, where
                    //appropriate, update client
                    $long_text = $explanation[8];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 131 + ($key * 4.5), $value);
                    }
                    //procedures in place to screen new
                    //clients as per the regulatory requirement
                    $long_text = $explanation[9];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 148 + ($key * 4.5), $value);
                    }

                    //designated and independed money laundering
                    $long_text = $explanation[10];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 165 + ($key * 4.5), $value);
                    }
                    //AML/CFT/Sanctions training program
                    $long_text = $explanation[11];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 181 + ($key * 4.5), $value);
                    }
                    //internal audit function
                    $long_text = $explanation[12];
                    $broken_text = wordwrap($long_text, 40, "||");
                    $exploded = explode("||", $broken_text);
                    foreach ($exploded as $key => $value) {
                        $fpdi->Text(140, 197 + ($key * 4.5), $value);
                    }
                }


                //  $fpdi->Image("https://secondary.axcrm.ae/assets/images/logo/ax-short-logo.png", 80, 20, 20, 5, 'PNG');
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }

    public function writeSalesLetterIntentPurchase($data = [], $name = "Sales-Letter-Of-Intent-Purchase-fillable-output", $outputPath = "file-manager/pdf")
    {

        try {
            $sampleFilePath = "app/public/file-manager/document-templates/Sales_Letter_Of_Intent_Purchase.pdf";
            $filePath = storage_path($sampleFilePath);

            if (!Storage::exists($sampleFilePath)) {
                $filePath = public_path("document-templates/Sales_Letter_Of_Intent_Purchase.pdf");
            }
            $outputPath = "file-manager/pdf";
            $outputFilePath = $outputPath . "/" . $name . "_".time().".pdf";
            //$outputFilePath = $outputPath . "/" . $name . ".pdf";

            $fpdi = new FPDI;

            $count = $fpdi->setSourceFile($filePath);

            for ($i = 1; $i <= $count; $i++) {

                $template = $fpdi->importPage($i);
                $size = $fpdi->getTemplateSize($template);
                $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
                $fpdi->useTemplate($template);

                $fpdi->SetFont("helvetica", "", 10);
                $fpdi->SetTextColor(0, 0, 0);

                if($i == 1) {

                    $fpdi->Text(32, 86, date('d/m/Y',strtotime($data['date']))); //Date
                    $fpdi->Text(42, 106, $data['property']); //Property
                    $fpdi->Text(42, 119, $data['subject']); //Subject
                    $fpdi->Text(44, 132, $data['purchaser']); //Purchaser



                    $fpdi->Text(35, 166, $data['offer']); //Offer
                    $fpdi->Text(66, 178, $data['agency_commission']); //Agency commission

                    $fpdi->Text(63, 192, $data['security_deposit']); //Security Deposit
                    $fpdi->Text(135, 192, $data['held_by']); //held by

                    $fpdi->Text(25, 240, $data['property_consultant']); //WITHOUT liability


                }
            }

            $pdfFile = $fpdi->Output("", 'S');


            return $this->uploadPdf($pdfFile, $outputFilePath);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit();
            return null;
        }
    }

    private function prepareMarketingFormData($property)
    {
        return [
            "type" => "marketing-form-a-leasing-fillable",
            "file_id" => null,
            "filename" => null,
            "property_id" => $property->id,
            "property_for" => (strpos($property->for, "Residential") !== false) ? "Residential" : "Commercial",
            "lsting_consultant" => $property->agent->name ?? null,
            "property_reference_no" => $property->ref_no,
            "landlord_name" => ($property->owner->first_name ?? "") . " " . ($property->owner->last_name ?? ""),
            "passport" => $property->owner->passport ?? null,
            "emirates_id_no" => null,
            "passport_expiry" => $property->owner->passport_expiry ?? null,
            "category" => $property->category,
            "property_furnished" => $property->furnished,
            "property_status" => $property->propertyStatus->title ?? null,
            "availability_date" => $property->availability_date,
            "building_name" => null,
            "unit_no" => $property->unit_no,
            "street" => $property->street,
            "community" => $property->community->name ?? null,
            "build_up_area" => $property->build_up_area,
            "plot_size" => $property->plot_size,
            "bedrooms" => $property->bed->name ?? null,
            "bathrooms" => $property->bath->name ?? null,
            "parking" => $property->parking,
            "rental_amount" => $property->price->price ?? null,
            "commission" => $property->commission ?? null,
            "landlord_agreed_to_appoint_as" => $property->exclusive ? "Exclusive" : "Non-Exclusive",
            "advertise_period_of_month" => null,
            "advertise_from" => $property->exclusive_start,
            "advertise_to" => $property->exclusive_end,
        ];
    }
}
