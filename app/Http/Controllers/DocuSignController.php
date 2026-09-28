<?php

namespace App\Http\Controllers;

use App\Models\OauthToken;
use Cache;
use Carbon\Carbon;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Client\ApiException;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\TemplateRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Session;

class DocuSignController extends Controller
{
    private $config, $args, $signer_client_id = 1000;
    // protected $docusignService;

    // public function __construct(DocuSignService $docusignService)
    // {
    //     $this->docusignService = $docusignService;
    // }

    public function docusignPage(Request $request)
    {
        if (isset($request->event)) {
            if ($request->event == 'decline') {
                echo 'Declined by user';
            } else if ($request->event == 'signing_complete') {
                echo 'Document signed successfully';
            }

        }
        echo '<br>';
        exit('docusign page');
        return view('test.docusign');
    }

    public function createEntityKycEnvelope($client, $documentPath, $recipientEmail, $recipientName)
    {
        try {
            $accountId = config('docusign.account_id');
            $envelopesApi = new EnvelopesApi($client);

            // Create a new envelope
            $envelope = new EnvelopeDefinition([
                'email_subject' => "Please sign this document",
            ]);
            $document = new Document([
                'document_base64' => base64_encode(file_get_contents($documentPath)),
                'name' => 'KYC FORM PROPERTY PURCHASE - ENTITY',
                'file_extension' => 'pdf',
                'document_id' => '1',
            ]);

            $envelope->setDocuments([$document]);

            // Add a recipient
            $signer = new Signer([
                'email' => $recipientEmail,
                'name' => $recipientName,
                'recipient_id' => '1',
                'routing_order' => '1',
            ]);
            // Define where the recipient should sign
            $signHere = new SignHere([
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '1',
                'x_position' => '10',
                'y_position' => '50',
            ]);

            // Create text tabs for user inputs
            $full_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '200', // Adjust position
                'y_position' => '320', // Adjust position
                'height' => '23',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'full_name',
                'field_id' => '11135357742',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'Khagesh',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $trading_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '200', // Adjust position
                'y_position' => '365', // Adjust position
                'height' => '23',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'trading_name',
                'field_id' => '11135357742',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $trade_license = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '200', // Adjust position
                'y_position' => '402', // Adjust position
                'height' => '23',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'trade_license_number',
                'field_id' => '11135357742',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $coi = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '270', // Adjust position
                'y_position' => '438', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'coi',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $doi = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '270', // Adjust position
                'y_position' => '481', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'doi',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $registered_address = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '250', // Adjust position
                'y_position' => '521', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'registered_address',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);

            $telephone = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '70', // Adjust position
                'y_position' => '632', // Adjust position
                'height' => '25',
                'width' => '100',
                'recipient_id' => '1',
                'tab_label' => 'telephone_number',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $email = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '192', // Adjust position
                'y_position' => '633', // Adjust position
                'height' => '25',
                'width' => '120',
                'recipient_id' => '1',
                'tab_label' => 'email',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $trade_license_issue_date = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '320', // Adjust position
                'y_position' => '635', // Adjust position
                'height' => '25',
                'width' => '120',
                'recipient_id' => '1',
                'tab_label' => 'tade_license_issue_date',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);

            $trade_license_expiry_date = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '1',
                'x_position' => '450', // Adjust position
                'y_position' => '633', // Adjust position
                'height' => '25',
                'width' => '120',
                'recipient_id' => '1',
                'tab_label' => 'tade_license_expiry_date',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);

            $ubo_1_full_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '142', // Adjust position
                'y_position' => '123', // Adjust position
                'height' => '27',
                'width' => '145',
                'recipient_id' => '1',
                'tab_label' => 'ubo_1_full_name',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'ubo full name',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $ubo_1_percentage = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '485', // Adjust position
                'y_position' => '125', // Adjust position
                'height' => '27',
                'width' => '30',
                'recipient_id' => '1',
                'tab_label' => 'ubo_1_percentage',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $ubo_2_full_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '142', // Adjust position
                'y_position' => '174', // Adjust position
                'height' => '25',
                'width' => '145',
                'recipient_id' => '1',
                'tab_label' => 'ubo_2_full_name',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'ubo full name',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $ubo_2_percentage = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '485', // Adjust position
                'y_position' => '175', // Adjust position
                'height' => '25',
                'width' => '30',
                'recipient_id' => '1',
                'tab_label' => 'ubo_2_percentage',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => '',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $person_action_1_full_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '100', // Adjust position
                'y_position' => '291', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_action_1_full_name',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => ' 1 fullname',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $person_action_2_full_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '100', // Adjust position
                'y_position' => '328', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_action_2_full_name',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => ' 2 fullname',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $person_action_1_relationship = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '375', // Adjust position
                'y_position' => '291', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_action_1_relationship',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => ' 1 relationship',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $person_action_2_relationship = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '375', // Adjust position
                'y_position' => '328', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_action_2_relationship',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => ' 2 relationship',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $product = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '100', // Adjust position
                'y_position' => '443', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'product',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'Product',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $primary_country = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '2',
                'x_position' => '100', // Adjust position
                'y_position' => '522', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'primary_country',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'primary_country',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $individual_occupation = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '3',
                'x_position' => '75', // Adjust position
                'y_position' => '122', // Adjust position
                'height' => '25',
                'width' => '170',
                'recipient_id' => '1',
                'tab_label' => 'individual_occupation',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'individual_occupation',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $individual_employer_name = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '3',
                'x_position' => '330', // Adjust position
                'y_position' => '122', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'individual_employer_name',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'individual_employer_name',
                'locked' => 'false',
                'editable' => 'true',
            ]);

            $person_name_acting_company = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '3',
                'x_position' => '280', // Adjust position
                'y_position' => '470', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_name_acting_company',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'person_name_acting_company',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $person_name_acting_company_position = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '3',
                'x_position' => '280', // Adjust position
                'y_position' => '400', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_name_acting_company_position',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'person_name_acting_company_position',
                'locked' => 'false',
                'editable' => 'true',
            ]);
            $sign_date = new \DocuSign\eSign\Model\Text([
                'document_id' => '1',
                'page_number' => '3',
                'x_position' => '280', // Adjust position
                'y_position' => '390', // Adjust position
                'height' => '25',
                'width' => '150',
                'recipient_id' => '1',
                'tab_label' => 'person_name_acting_company_position',
                'required' => 'true',
                'font' => 'helvetica',
                'font_size' => 'size12',
                'value' => 'person_name_acting_company_position',
                'locked' => 'false',
                'editable' => 'true',
            ]);

            // Set the tabs for the signer
            $signer->setTabs(new \DocuSign\eSign\Model\Tabs([
                'sign_here_tabs' => [$signHere],
                'text_tabs' => [$full_name, $trading_name, $trade_license, $coi, $doi,
                    $registered_address, $telephone, $email, $trade_license_issue_date, $trade_license_expiry_date,
                    $ubo_1_full_name, $ubo_1_percentage, $ubo_2_full_name, $ubo_2_percentage,
                    $person_action_1_full_name, $person_action_1_relationship, $person_action_2_full_name, $person_action_2_relationship,
                    $product, $primary_country, $individual_occupation, $individual_employer_name,
                    $person_name_acting_company, $person_name_acting_company_position,
                    $sign_date,
                ],
                //'date_signed_tabs' => [$textDate]
                // 'radio_group_tabs' => $radioGroup,
            ]));
            $envelope->setRecipients(['signers' => [$signer]]);

            // Send the envelope
            $envelope->setStatus('sent');
            try {
                $results = $envelopesApi->createEnvelope($accountId, $envelope);
                // $results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);
                $envelopeId = $results->getEnvelopeId();

                return $envelopeId;
            } catch (ApiException $e) {
                if ($e->getCode() == 401) {
                    // $this->getAccessToken();
                    $this->refreshAccessToken();
                    return $this->createEntityKycEnvelope($client, $documentPath, $recipientEmail, $recipientName);
                }

            }
        } catch (Exception $e) {
            // echo 'Error: ' . $e->getMessage();
        }
    }
    public function createIndividualKycEnvelope($client, $documentPath, $recipientEmail, $recipientName)
    {
        $accountId = config('docusign.account_id');
        $envelopesApi = new EnvelopesApi($client);

        // Create a new envelope
        $envelope = new EnvelopeDefinition([
            'email_subject' => "Please sign this document",
        ]);
        $document = new Document([
            'document_base64' => base64_encode(file_get_contents($documentPath)),
            'name' => 'KYC FORM PROPERTY PURCHASE - Individual',
            'file_extension' => 'pdf',
            'document_id' => '1',
        ]);

        $envelope->setDocuments([$document]);

        // Add a recipient
        $signer = new Signer([
            'email' => $recipientEmail,
            'name' => $recipientName,
            'recipient_id' => '1',
            'routing_order' => '1',
            'anchor_x_offset' => '20',
            'anchor_y_offset' => '10',
        ]);
        // Define where the recipient should sign
        $signHere = new SignHere([
            'anchor_string' => 'Signature:',
            'anchor_units' => 'pixels',
            'anchor_y_offset' => '17',
            'anchor_x_offset' => '180',
        ]);



        // Create text tabs for user inputs
        $full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '302', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'full_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $aliases = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '330', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'aliases',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $residential_address = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '357', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'residential_address',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $email = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '55', // Adjust position
            'y_position' => '403', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'email',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $telephone = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '225', // Adjust position
            'y_position' => '403', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'telephone',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $mobile = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '393', // Adjust position
            'y_position' => '403', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'mobile',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $dob = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '55', // Adjust position
            'y_position' => '443', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'dob',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $place_of_birth = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '443', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'place_of_birth',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $nationality = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '55', // Adjust position
            'y_position' => '605', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'nationality',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $passport = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '605', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'passport_number',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $citizenship_country_of_issue = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '395', // Adjust position
            'y_position' => '604', // Adjust position
            'height' => '25',
            'width' => '130',
            'recipient_id' => '1',
            'tab_label' => 'citizenship_country_of_issue',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $citizenship_issue_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '55', // Adjust position
            'y_position' => '648', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'citizenship_issue_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $citizenship_expiry_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '220', // Adjust position
            'y_position' => '648', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'citizenship_expiry_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $citizenship_extra_field = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '395', // Adjust position
            'y_position' => '648', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'citizenship_extra_field',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $fund_source_other = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '110', // Adjust position
            'y_position' => '190', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'fund_source_other',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $fs_employment_income = new \DocuSign\eSign\Model\Checkbox([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '55',
            'y_position' => '149',
            'tab_label' => 'fs_employment_income[]',
            'value' => 'Employment Income',
            'required' => 'false'
        ]);
        $fs_sale_of_investment = new \DocuSign\eSign\Model\Checkbox([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '270',
            'y_position' => '149',
            'tab_label' => 'fs_sale_of_investment[]',
            'value' => 'Sale of Investment or Properties',
            'required' => 'false'
        ]);
        $fs_business_income = new \DocuSign\eSign\Model\Checkbox([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '55',
            'y_position' => '175',
            'tab_label' => 'fs_business_income[]',
            'value' => 'Business Income',
            'required' => 'false'
        ]);
        $fs_savings = new \DocuSign\eSign\Model\Checkbox([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '270',
            'y_position' => '175',
            'tab_label' => 'fs_savings[]',
            'value' => 'Savings',
            'required' => 'false'
        ]);
        // $fund_source_radio1 = new \DocuSign\eSign\Model\Radio([
        //     'page_number' => '3',
        //     'anchor_string' => 'Employment Income', // Use anchor string for positioning
        //     'label' => 'fund_source',
        //     'value' => 'Employment Income',
        //     'anchor_x_offset' => '-2',
        //     'anchor_y_offset' => '-2',
        //     'required' => 'true',
        // ]);

        $residency_visa_number = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '57', // Adjust position
            'y_position' => '362', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'residency_visa_number',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $emirates_id_no = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '220', // Adjust position
            'y_position' => '362', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'emirates_id_no',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $emirates_id_expiry_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '394', // Adjust position
            'y_position' => '362', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'emirates_id_expiry_date',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $employment_occupation = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '57', // Adjust position
            'y_position' => '542', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'employment_occupation',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $years_in_present_role = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '300', // Adjust position
            'y_position' => '540', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'years_in_present_role',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $employer_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '57', // Adjust position
            'y_position' => '600', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'employer_name',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $employer_address = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '300', // Adjust position
            'y_position' => '600', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'employer_address',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $wealth_source = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '325', // Adjust position
            'y_position' => '192', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'wealth_source',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $annual_income = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '135', // Adjust position
            'y_position' => '236', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'annual_income',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $purpose_of_purchase_other = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '420', // Adjust position
            'y_position' => '270', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'purpose_of_purchase_other',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $payment_other = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '420', // Adjust position
            'y_position' => '315', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'payment_other',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $pep_other = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '250', // Adjust position
            'y_position' => '380', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'pep_other',
            'required' => 'false',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => date('d/m/Y'),
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $sign_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '240', // Adjust position
            'y_position' => '601', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'sign_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => date('d/m/Y'),
            'locked' => 'false',
            'editable' => 'true',
        ]);


        // $anchorString = 'Payment Method:'; // Replace with the text on the document

        // $radio1 = new \DocuSign\eSign\Model\Radio([

        //     'page_number' => '3',
        //     'anchor_string' => $anchorString, // Use anchor string for positioning
        //     'label' => 'pep_radio',
        //     'value' => 'Yes',
        //     'anchor_x_offset' => '0',
        //     'anchor_y_offset' => '0',
        //     // 'x_position' => '200',
        //     // 'y_position' => '400',
        //     'required' => 'true'
        // ]);
        // $radio2 = new \DocuSign\eSign\Model\Radio([
        //     'page_number' => '3',
        //     'anchor_string' => $anchorString, // Use anchor string for positioning
        //     'label' => 'pep_radio',
        //     'anchor_x_offset' => '0',
        //     'anchor_y_offset' => '0',
        //     // 'x_position' => '200',
        //     // 'y_position' => '400',
        //     'value' => 'No',
        //     'required' => 'true'
        // ]);

        // $radioGroup = new \DocuSign\eSign\Model\RadioGroup([
        //     'group_name' => 'pep',
        //     'radios' => [$radio1,  $radio2]
        // ]);

        $radio1 = new \DocuSign\eSign\Model\Radio([

            'page_number' => '1',
            'anchor_string' => 'G1', // Use anchor string for positioning
            'label' => 'gender',
            'value' => 'Female',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $radio2 = new \DocuSign\eSign\Model\Radio([
            'page_number' => '1',
            'anchor_string' => 'G2', // Use anchor string for positioning
            'label' => 'gender',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-3',
            'value' => 'Male',
            'required' => 'true',
        ]);

        $gender_radio = new \DocuSign\eSign\Model\RadioGroup([
            'group_name' => 'gender',
            'radios' => [$radio1, $radio2],
        ]);
        $radio_pop_end_use = new \DocuSign\eSign\Model\Radio([

            'page_number' => '3',
            'anchor_string' => 'R1', // Use anchor string for positioning
            'value' => 'End Use',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $radio_pop_savings = new \DocuSign\eSign\Model\Radio([

            'page_number' => '3',
            'anchor_string' => 'R2', // Use anchor string for positioning
            'value' => 'Savings',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $radio_pop_other = new \DocuSign\eSign\Model\Radio([

            'page_number' => '3',
            'anchor_string' => 'R3', // Use anchor string for positioning
            'value' => 'Other',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $purpose_of_purchase = new \DocuSign\eSign\Model\RadioGroup([
            'group_name' => 'purpose_of_purchase',
            'radios' => [$radio_pop_end_use, $radio_pop_savings,$radio_pop_other],
        ]);
        $payment_method_cash = new \DocuSign\eSign\Model\Radio([

            'page_number' => '3',
            'anchor_string' => 'R4', // Use anchor string for positioning
            'value' => 'Cash',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $payment_method_cheque = new \DocuSign\eSign\Model\Radio([

            'page_number' => '3',
            'anchor_string' => 'R5', // Use anchor string for positioning
            'value' => 'Cheque',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $payment_method_bank_transfer = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'R6', // Use anchor string for positioning
            'value' => 'Bank Transfer',
            'anchor_x_offset' => '-7',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $payment_method_mortgage = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'R7', // Use anchor string for positioning
            'value' => 'Mortgage',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $payment_method_crypto = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'R8', // Use anchor string for positioning
            'value' => 'Crypto',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $payment_method_other = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'R9', // Use anchor string for positioning
            'value' => 'Other',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $method_of_payments = new \DocuSign\eSign\Model\RadioGroup([
            'group_name' => 'method_of_payments',
            'radios' => [$payment_method_cash, $payment_method_cheque, $payment_method_bank_transfer, $payment_method_mortgage, $payment_method_crypto, $payment_method_other],
        ]);
        $pep_yes = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'P1', // Use anchor string for positioning
            'value' => 'Yes',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $pep_no = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'P2', // Use anchor string for positioning
            'value' => 'No',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $pep_other = new \DocuSign\eSign\Model\Radio([
            'page_number' => '3',
            'anchor_string' => 'P3', // Use anchor string for positioning
            'value' => 'Other',
            'anchor_x_offset' => '-6',
            'anchor_y_offset' => '-2',
            'required' => 'true',
        ]);
        $pep = new \DocuSign\eSign\Model\RadioGroup([
            'group_name' => 'pep',
            'radios' => [$pep_yes, $pep_no, $pep_other],
        ]);
        $module = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '0',      // Position can be off the visible document
            'y_position' => '0',      // Position can be off the visible document
            'recipient_id' => '1',
            'tab_label' => 'module',
            'value' => 'deal_lead',      // Default value you want to set
            'locked' => 'true',              // Makes the field read-only
            'conceal_value_on_document' => 'true', // Hides the value from view
        ]);
        $document_type =  new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '0',      // Position can be off the visible document
            'y_position' => '0',      // Position can be off the visible document
            'recipient_id' => '1',
            'tab_label' => 'document_type',
            'value' => 'individual_kyc_form',      // Default value you want to set
            'locked' => 'true',              // Makes the field read-only
            'conceal_value_on_document' => 'true', // Hides the value from view
        ]);


        // Set the tabs for the signer
        $signer->setTabs(new \DocuSign\eSign\Model\Tabs([
            'sign_here_tabs' => [$signHere],
            'text_tabs' => [$full_name, $aliases, $residential_address, $dob, $email, $telephone, $mobile, $place_of_birth,
                $nationality, $citizenship_country_of_issue, $passport, $citizenship_issue_date, $citizenship_expiry_date, $citizenship_extra_field,
                $fund_source_other, $residency_visa_number, $emirates_id_no, $emirates_id_expiry_date, $employment_occupation, $years_in_present_role,
                $employer_name, $employer_address, $annual_income, $purpose_of_purchase_other, $payment_other, $pep_other,
                $wealth_source,
                $module,$document_type,
                $sign_date,
            ],
            //'date_signed_tabs' => [$dob],

            'checkbox_tabs' => [$fs_employment_income,$fs_sale_of_investment,$fs_business_income, $fs_savings],
            'radio_group_tabs' => [$gender_radio,$purpose_of_purchase,$method_of_payments,$pep],
        ]));
        $envelope->setRecipients(['signers' => [$signer]]);

        // Send the envelope
        $envelope->setStatus('sent');

        $results = $envelopesApi->createEnvelope($accountId, $envelope);
        // $results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);

        $envelopeId = $results->getEnvelopeId();
        return $envelopeId;
    }
    public function createEnvelope($recipientEmail, $recipientName, $documentPath)
    {
        $config = new Configuration();
        $config->setHost(config('docusign.base_uri'));
        $access_token = Cache::get('docusign_auth_code');
        //$config->addDefaultHeader("Authorization", "Bearer " . $this->getAccessToken());
        $config->addDefaultHeader("Authorization", "Bearer " . $access_token);
        $client = new ApiClient($config);
        $accountId = config('docusign.account_id');
        return $this->createIndividualKycEnvelope($client, $documentPath, $recipientEmail, $recipientName);
        // return $this->createEntityKycEnvelope($client, $documentPath, $recipientEmail, $recipientName);

    }
    public function getEnvelopeApi(): EnvelopesApi
    {

        $this->config = new Configuration();

        // $this->config->setHost($this->args['base_path']);
        $this->config->setHost(config('docusign.base_uri'));

        //$this->config->addDefaultHeader('Authorization', 'Bearer ' . $this->args['ds_access_token']);
        $this->config->addDefaultHeader('Authorization', 'Bearer ' . Cache::get('docusign_auth_code'));

        $this->apiClient = new ApiClient($this->config);

        return new EnvelopesApi($this->apiClient);

    }
//     public function inviteUserToSign($signerEmail, $signerName)
// {
//     // Path to your document
//     $docsFilePath = storage_path('app/public/kyc-entity.pdf');
//     $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
//     $contentBytes = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));

//     // Create the document object
//     $document = new Document([
//         'document_base64' => base64_encode($contentBytes),
//         'name' => 'Example Document',
//         'file_extension' => 'pdf',
//         'document_id' => 1
//     ]);

//     // Create the signer recipient model
//     $signer = new Signer([
//         'email' => $signerEmail,
//         'name' => $signerName,
//         'recipient_id' => '1',
//         'routing_order' => '1',
//     ]);

//     // Define the SignHere tab (where the recipient should sign)
//     $signHere = new SignHere([
//         'document_id' => '1',
//         'page_number' => '1',
//         'x_position' => '200',
//         'y_position' => '300',
//     ]);

//     // Add the tabs to the signer
//     $signer->setTabs(new Tabs([
//         'sign_here_tabs' => [$signHere]
//     ]));

//     // Create the envelope definition
//     $envelopeDefinition = new EnvelopeDefinition([
//         'email_subject' => "Please sign this document",
//         'documents' => [$document],
//         'recipients' => new Recipients(['signers' => [$signer]]),
//         'status' => "sent"  // "sent" status automatically sends the envelope
//     ]);

//     $config = new Configuration();
//     $config->setHost(config('docusign.base_uri'));

//     $apiClient = new ApiClient($config);
//     //$accountId = config('docusign.account_id');
//     // Send the envelope via DocuSign API

//     $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($apiClient);

//     $accountId = config('docusign.account_id');
//     $results = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);

//     return $results;
// }
    public function sendDocument(Request $request)
    {
        $recipientEmail = 'khagesh.office@gmail.com';
        $recipientName = 'khagesh';
        $documentPath = storage_path('app/public/kyc-entity.pdf');
        // $result = $this->inviteUserToSign($recipientEmail, $recipientName);
        $envelope_id = $this->createEnvelope($recipientEmail, $recipientName, $documentPath);

        return response()->json(['envelope_id' => $envelope_id]);
    }
    public function sendInvitationDocusign($deal, $lead, $invite)
    {

        $recipientEmail = 'khagesh.office@gmail.com';
        $recipientName = 'khagesh';
        $documentPath = storage_path('app/public/kyc-entity.pdf');
        // $result = $this->inviteUserToSign($recipientEmail, $recipientName);
        $envelope_id = $this->createEnvelope($recipientEmail, $recipientName, $documentPath);

        return response()->json(['envelope_id' => $envelope_id]);
    }

    public function callback(Request $request)
    {
        $res = $request->all();
        $code = $res['code'];
        $state_token = $res['state']; //need to verify if we send this code
        $response = Http::withBasicAuth(env('DOCUSIGN_CLIENT_ID'),
            env('DOCUSIGN_CLIENT_SECRET'))->post('https://account-d.docusign.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,

        ]);

        $result = $response->json();

        //need to put this access token on session or database
        // $access_token = $result['access_token']; //its a bearer token it expires in 28800 seconds
        // $refresh_token = $result['refresh_token']; //refresh token
        $request->session()->put('refresh_token', $result['refresh_token']);
        //  $request->session()->put('docusign_auth_code', $result['access_token']);
        // $request->cache->put('docusign_auth_code', $result['access_token'], 60);
        Cache::put('docusign_auth_code', $result['access_token']);
        Cache::put('docusign_refresh_token', $result['refresh_token']);
        return redirect()->route('sign.document');
    }
    public function refreshAccessToken()
    {
        $from_db = OauthToken::where('application', 'docusign')->first();
        if (!$from_db) {
            return;
        }
        $response = Http::withBasicAuth(env('DOCUSIGN_CLIENT_ID'),
            env('DOCUSIGN_CLIENT_SECRET'))->post('https://account-d.docusign.com/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $from_db['refresh_token'],
        ]);
        if ($response->status() == 200) {
            $result = $response->json();
            $access_token = $result['access_token']; //its a bearer token it expires in 28800 seconds
            $refresh_token = $result['refresh_token']; //refresh token
            $expires_in = $result['expires_in'];
            $refresh_token_expires_in = '7'; // 7 days
            OauthToken::updateOrCreate(
                ['application' => 'docusign'],
                ['access_token' => $result['access_token'],
                    'refresh_token' => $result['refresh_token'],
                    'token_type' => $result['token_type'],
                    'meta' => json_encode($result),
                    'expires_at' => Carbon::now()->addSeconds($expires_in),
                    'refresh_expires_at' => Carbon::now()->addDays($refresh_token_expires_in),
                ]);
        } else {
            echo 'Error in refreshing token';
        }

    }

    public function createOauthUrl()
    {
        $params = [
            'response_type' => 'code',
            'scope' => 'signature',
            'client_id' => env('DOCUSIGN_CLIENT_ID'),
            'state' => 'a39fh23hnf23', //secret token by us when callback hit then we can verify it if it is same or not
            'redirect_uri' => 'http://secondary.lan/docusign/callback',
        ];
        $queryBuild = http_build_query($params);
        $url = "https://account-d.docusign.com/oauth/auth?";
        $auth_url = $url . $queryBuild;
        echo $auth_url;
    }
    private function makeEnvelopeFileObject($args)
    {
        // Load the document
        $docsFilePath = public_path('document-templates/LEAD_KYC_Form_For_Entity.pdf');
        $arrContextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
        $contentBytes = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));

        // Create the document model
        $document = new \DocuSign\eSign\Model\Document([
            'document_base64' => base64_encode($contentBytes),
            'name' => 'KYC Document',
            'file_extension' => 'pdf',
            'document_id' => 1,
        ]);

        // Create the signer recipient model
        $signer = new \DocuSign\eSign\Model\Signer([
            'email' => 'khagesh.office@gmail.com',
            'name' => 'Khagesh',
            'recipient_id' => '1',
            'routing_order' => '1',
            'client_user_id' => $args['signer_client_id'],
            'anchor_x_offset' => '20',
            'anchor_y_offset' => '10',
        ]);

        // Create a signHere tab (field on the document)
        $signHere = new \DocuSign\eSign\Model\SignHere([
            'anchor_string' => 'Signature:',
            'anchor_units' => 'pixels',
            'anchor_y_offset' => '17',
            'anchor_x_offset' => '180',
        ]);

         // Create text tabs for user inputs
         $full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '200', // Adjust position
            'y_position' => '320', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'full_name',
            'field_id' => '11135357742',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'Khagesh',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $trading_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '200', // Adjust position
            'y_position' => '365', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'trading_name',
            'field_id' => '11135357742',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $trade_license = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '200', // Adjust position
            'y_position' => '402', // Adjust position
            'height' => '23',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'trade_license_number',
            'field_id' => '11135357742',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $coi = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '270', // Adjust position
            'y_position' => '438', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'coi',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $doi = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '270', // Adjust position
            'y_position' => '481', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'doi',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $registered_address = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '205', // Adjust position
            'y_position' => '517', // Adjust position
            'height' => '25',
            'width' => '250',
            'recipient_id' => '1',
            'tab_label' => 'registered_address',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $telephone = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '70', // Adjust position
            'y_position' => '632', // Adjust position
            'height' => '25',
            'width' => '100',
            'recipient_id' => '1',
            'tab_label' => 'telephone_number',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $email = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '192', // Adjust position
            'y_position' => '633', // Adjust position
            'height' => '25',
            'width' => '120',
            'recipient_id' => '1',
            'tab_label' => 'email',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $trade_license_issue_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '320', // Adjust position
            'y_position' => '635', // Adjust position
            'height' => '25',
            'width' => '120',
            'recipient_id' => '1',
            'tab_label' => 'tade_license_issue_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $trade_license_expiry_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '450', // Adjust position
            'y_position' => '633', // Adjust position
            'height' => '25',
            'width' => '120',
            'recipient_id' => '1',
            'tab_label' => 'tade_license_expiry_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $ubo_1_full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '142', // Adjust position
            'y_position' => '123', // Adjust position
            'height' => '27',
            'width' => '145',
            'recipient_id' => '1',
            'tab_label' => 'ubo_1_full_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'ubo full name',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $ubo_1_percentage = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '485', // Adjust position
            'y_position' => '125', // Adjust position
            'height' => '27',
            'width' => '30',
            'recipient_id' => '1',
            'tab_label' => 'ubo_1_percentage',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $ubo_2_full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '142', // Adjust position
            'y_position' => '174', // Adjust position
            'height' => '25',
            'width' => '145',
            'recipient_id' => '1',
            'tab_label' => 'ubo_2_full_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'ubo full name',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $ubo_2_percentage = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '485', // Adjust position
            'y_position' => '175', // Adjust position
            'height' => '25',
            'width' => '30',
            'recipient_id' => '1',
            'tab_label' => 'ubo_2_percentage',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => '',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $person_action_1_full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '100', // Adjust position
            'y_position' => '291', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_action_1_full_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => ' 1 fullname',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $person_action_2_full_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '100', // Adjust position
            'y_position' => '328', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_action_2_full_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => ' 2 fullname',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $person_action_1_relationship = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '375', // Adjust position
            'y_position' => '291', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_action_1_relationship',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => ' 1 relationship',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $person_action_2_relationship = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '375', // Adjust position
            'y_position' => '328', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_action_2_relationship',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => ' 2 relationship',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $product = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '100', // Adjust position
            'y_position' => '443', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'product',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'Product',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $primary_country = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '2',
            'x_position' => '100', // Adjust position
            'y_position' => '522', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'primary_country',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'primary_country',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $individual_occupation = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '75', // Adjust position
            'y_position' => '122', // Adjust position
            'height' => '25',
            'width' => '170',
            'recipient_id' => '1',
            'tab_label' => 'individual_occupation',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'individual_occupation',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $individual_employer_name = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '330', // Adjust position
            'y_position' => '122', // Adjust position
            'height' => '25',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'individual_employer_name',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'individual_employer_name',
            'locked' => 'false',
            'editable' => 'true',
        ]);

        $person_name_acting_company = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '250', // Adjust position
            'y_position' => '522', // Adjust position
            'height' => '28',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_name_acting_company',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'person name acting_company',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $person_name_acting_company_position = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '250', // Adjust position
            'y_position' => '553', // Adjust position
            'height' => '28',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'person_name_acting_company_position',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'person_name_acting_company_position',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $sign_date = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '3',
            'x_position' => '250', // Adjust position
            'y_position' => '578', // Adjust position
            'height' => '28',
            'width' => '150',
            'recipient_id' => '1',
            'tab_label' => 'sign_date',
            'required' => 'true',
            'font' => 'helvetica',
            'font_size' => 'size12',
            'value' => 'sign_date',
            'locked' => 'false',
            'editable' => 'true',
        ]);
        $module = new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '0',      // Position can be off the visible document
            'y_position' => '0',      // Position can be off the visible document
            'recipient_id' => '1',
            'tab_label' => 'module',
            'value' => 'deal_lead',      // Default value you want to set
            'locked' => 'true',              // Makes the field read-only
            'conceal_value_on_document' => 'true', // Hides the value from view
        ]);
        $document_type =  new \DocuSign\eSign\Model\Text([
            'document_id' => '1',
            'page_number' => '1',
            'x_position' => '0',      // Position can be off the visible document
            'y_position' => '0',      // Position can be off the visible document
            'recipient_id' => '1',
            'tab_label' => 'document_type',
            'value' => 'entity_kyc_form',      // Default value you want to set
            'locked' => 'true',              // Makes the field read-only
            'conceal_value_on_document' => 'true', // Hides the value from view
        ]);
        // Set the tabs for the signer
        $signer->setTabs(new \DocuSign\eSign\Model\Tabs([
            'sign_here_tabs' => [$signHere],
            'text_tabs' => [$full_name, $trading_name, $trade_license, $coi, $doi,
                $registered_address, $telephone, $email, $trade_license_issue_date, $trade_license_expiry_date,
                $ubo_1_full_name, $ubo_1_percentage, $ubo_2_full_name, $ubo_2_percentage,
                $person_action_1_full_name, $person_action_1_relationship, $person_action_2_full_name, $person_action_2_relationship,
                $product, $primary_country, $individual_occupation, $individual_employer_name,
                $person_name_acting_company, $person_name_acting_company_position,
                $sign_date,$module,$document_type
            ],
            //'date_signed_tabs' => [$textDate]
            // 'radio_group_tabs' => $radioGroup,
        ]));

        // Create the envelope definition
        $envelopeDefinition = new \DocuSign\eSign\Model\EnvelopeDefinition([
            'email_subject' => "Please fill in the information and sign this document",
            'documents' => [$document],
            'recipients' => new \DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),
            'status' => "sent",
        ]);

        return $envelopeDefinition;
    }
    private function makeEnvelopeFileObject_OLD($args)
    {

        //  $docsFilePath = file_get_contents(storage_path(config('docusign.private_key')));
        // $docsFilePath = storage_path('app/public/kyc-entity.pdf'); //public_path('doc/demo_pdf_new.pdf');
        $docsFilePath = storage_path('app/public/file-manager/pdf/lead-kyc-form-individual_1730120638.pdf'); //public_path('doc/demo_pdf_new.pdf');

        $arrContextOptions = array(

            "ssl" => array(

                "verify_peer" => false,

                "verify_peer_name" => false,

            ),

        );

        $contentBytes = file_get_contents($docsFilePath, false, stream_context_create($arrContextOptions));

        /* Create the document model */

        $document = new \DocuSign\eSign\Model\Document([

            'document_base64' => base64_encode($contentBytes),

            'name' => 'Example Document File',

            'file_extension' => 'pdf',

            'document_id' => 1,

        ]);

        /* Create the signer recipient model */

        $signer = new \DocuSign\eSign\Model\Signer([

            'email' => 'khagesh.office@gmail.com',

            'name' => 'Khagesh',
            'date' => date('d/m/Y'),

            'recipient_id' => '1',

            'routing_order' => '1',

            'client_user_id' => $args['signer_client_id'],

        ]);

        /* Create a signHere tab (field on the document) */

        $signHere = new \DocuSign\eSign\Model\SignHere([

            'anchor_string' => 'Signature:',

            'anchor_units' => 'pixels',

            'anchor_y_offset' => '17',

            'anchor_x_offset' => '180',

        ]);

        /* Create a signHere 2 tab (field on the document) */

        // $signHere2 = new \DocuSign\eSign\Model\SignHere([

        //     'anchor_string' => 'Date:',

        //     'anchor_units' => 'pixels',

        //     'anchor_y_offset' => '40',

        //     'anchor_x_offset' => '40'

        // ]);

        // $signer->settabs(new \DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere, $signHere2]]));
        $signer->settabs(new \DocuSign\eSign\Model\Tabs(['sign_here_tabs' => [$signHere]]));

        $envelopeDefinition = new \DocuSign\eSign\Model\EnvelopeDefinition([

            'email_subject' => "Please sign this document sent from the AX CAPITAL",

            'documents' => [$document],

            'recipients' => new \DocuSign\eSign\Model\Recipients(['signers' => [$signer]]),

            'status' => "sent",

        ]);

        return $envelopeDefinition;

    }
    private function getTemplateArgs()
    {

        $args = [

            'account_id' => env('DOCUSIGN_ACCOUNT_ID'),

            'base_path' => config('docusign.base_uri'),

            'ds_access_token' => Cache::get('docusign_auth_code'),

            'envelope_args' => [

                'signer_client_id' => $this->signer_client_id,

                'ds_return_url' => route('docusign'),

            ],

        ];

        return $args;

    }
    public function signDocument()
    {

        try {

            $this->args = $this->getTemplateArgs();

            $args = $this->args;

            $envelope_args = $args["envelope_args"];

            /* Create the envelope request object */

            $envelope_definition = $this->makeEnvelopeFileObject($args["envelope_args"]);

            $envelope_api = $this->getEnvelopeApi();

            $api_client = new \DocuSign\eSign\client\ApiClient($this->config);

            $envelope_api = new \DocuSign\eSign\Api\EnvelopesApi($api_client);

            $results = $envelope_api->createEnvelope($args['account_id'], $envelope_definition);

            $envelopeId = $results->getEnvelopeId();
            echo "Envelope ID: $envelopeId\n";
            $authentication_method = 'None';

            $recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest([

                'authentication_method' => $authentication_method,

                'client_user_id' => $envelope_args['signer_client_id'],

                'recipient_id' => '1',

                'return_url' => $envelope_args['ds_return_url'],

                'user_name' => 'khagesh',
                'email' => 'khagesh.office@gmail.com',

            ]);

            $results = $envelope_api->createRecipientView($args['account_id'], $envelopeId, $recipient_view_request);
            //print_r($results);
            return redirect()->to($results['url']);

        } catch (Exception $e) {

            dd($e->getMessage());

        }

    }

    public function getCompletedEnvelopeData($envelopeId = '')
    {
        $envelopeId = '3c0b8228-2ba3-4fb3-bb83-0bf5c4ae27f0';
        // Make sure to authenticate first and get your envelope API instance
        $envelopeApi = $this->getEnvelopeApi();

        try {
            $account_id = env('DOCUSIGN_ACCOUNT_ID');
            // Get the completed envelope data
            $envelope = $envelopeApi->getEnvelope($account_id, $envelopeId);

            // Get the recipients’ data (which includes the completed fields)
            $recipients = $envelopeApi->listRecipients($account_id, $envelopeId);
            foreach ($recipients->getSigners() as $signer) {

                echo '<pre>';
                print_r($signer);
                echo '</pre>';
                // $tabs = $signer->getTabs();
                // $textTabs = $tabs->getTextTabs();

                // // Process each text tab to get the filled value
                // foreach ($textTabs as $textTab) {
                //     $label = $textTab->getTabLabel();
                //     $value = $textTab->getValue();

                //     // Save or process this value as needed
                //     // e.g., FacilityData::create([...]);
                // }
            }
        } catch (\DocuSign\eSign\Client\ApiException $e) {
            echo "Error fetching envelope: ", $e->getMessage();
        }
    }

    public function getEnolopeCustomFieldData()
    {
        $url = 'restapi/v2.1/accounts/{accountId}/envelopes/{envelopeId}/custom_fields';
        $envelopeApi = $this->getEnvelopeApi();
        $account_id = env('DOCUSIGN_ACCOUNT_ID');
        $envelopeId = '3c0b8228-2ba3-4fb3-bb83-0bf5c4ae27f0';
        $customFields = $envelopeApi->listCustomFields($account_id, $envelopeId);
        echo '<pre>';
        print_r($customFields);
        echo '</pre>';

    }
    public function getEnvolopeFormData($envelopeId = '3c0b8228-2ba3-4fb3-bb83-0bf5c4ae27f0')
    {
        $envelopeApi = $this->getEnvelopeApi();
        $account_id = env('DOCUSIGN_ACCOUNT_ID');
        $formData = $envelopeApi->getFormData($account_id, $envelopeId);

        // $invite = \App\Models\DealInvite::where('docusign_envelope_id', $envelopeId)->first();

        // $invite->docusign_data = $formData;
        // $invite->save();
        echo '<pre>';
        print_r($formData);
        echo '</pre>';
        //  print_r($formData);

    }
    public function getEnvelopePdfFile($envelopeId = '0f43454a-bf51-40a8-8737-489eeb102692')
    {
        $accountId = config('docusign.account_id');
        $accessToken = Cache::get('docusign_access_token'); // Assumes access token is stored in cache
        $baseUrl = 'https://demo.docusign.net/restapi'; // Use "https://www.docusign.net/restapi" for production
        $token_from_db = OauthToken::where('application', 'docusign')->first();
        // Check that the required values are set
        $accessToken = $token_from_db->access_token;
        if (!$accountId || !$accessToken) {
            throw new \Exception("Account ID or access token is missing.");
        }
        $url = "{$baseUrl}/v2.1/accounts/{$accountId}/envelopes/{$envelopeId}/documents/combined";

        echo $url;
        exit();
        // Make the HTTP GET request to DocuSign's getDocument endpoint
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept' => 'application/pdf',
        ])->get($url);

        // Check for a successful response
        if ($response->successful()) {
            // Save the PDF file to storage
            $pdfFilePath = storage_path('app/public/docusign/' . $envelopeId . '.pdf');
            file_put_contents($pdfFilePath, $response->body());

            echo "PDF file saved to $pdfFilePath\n";
        } else {
            // Output error details
            $error = $response->json();
            echo "Error: " . $error['errorCode'] . " - " . $error['message'] . "\n";
        }
    }
//     public function getEnvelopePdfFile($envelopeId = '0f43454a-bf51-40a8-8737-489eeb102692')
// {
//     $envelopeApi = $this->getEnvelopeApi();
//     $account_id = config('docusign.account_id');

//     // Verify account ID and envelope ID
//     if (!$account_id) {
//         throw new \Exception("DocuSign account ID is missing or invalid.");
//     }
//     if (!$envelopeId || !is_string($envelopeId)) {
//         throw new \Exception("Invalid envelope ID provided.");
//     }

//     try {
//         // Confirm envelope status
//         $envelope = $envelopeApi->getEnvelope($account_id, $envelopeId);
//         echo "Envelope Status: " . $envelope->getStatus() . "\n";
//         // Retrieve PDF document
//         $pdf = $envelopeApi->getDocument($account_id, $envelopeId, 'combined');
//         print_r($pdf);
//         exit();
//         $pdfFilePath = storage_path('app/public/docusign/' . $envelopeId . '.pdf');

//         file_put_contents($pdfFilePath, $pdf);
//         echo "PDF file saved to $pdfFilePath\n";

//     } catch (\DocuSign\eSign\Client\ApiException $e) {
//         echo "Error: " . $e->getMessage();
//         echo "\nDetails: " . json_encode($e->getResponseBody(), JSON_PRETTY_PRINT);
//     }
// }

    public function webhook(Request $request)
    {
        $data = $request->all();
        $envelopeId = $data['data']['envelopeId'];
        $this->getEnvelopePdfFile('b363d26c-552d-467f-9ea5-c5e7be65a1aa');
        // $this->getEnvolopeFormData($envelopeId);
        \Log::info($data);
        return response()->json([
            'message' => 'Webhook received',
        ]);

        // $envelopeId = $data['envelopeId'];
        // $event = $data['event'];
        // $status = $data['status'];
        // $envelopeApi = $this->getEnvelopeApi();
        // $account_id = env('DOCUSIGN_ACCOUNT_ID');
        // $envelope = $envelopeApi->getEnvelope($account_id, $envelopeId);
        // $recipients = $envelopeApi->listRecipients($account_id, $envelopeId);
        // foreach ($recipients->getSigners() as $signer) {
        //     $tabs = $signer->getTabs();
        //     $textTabs = $tabs->getTextTabs();
        //     foreach ($textTabs as $textTab) {
        //         $label = $textTab->getTabLabel();
        //         $value = $textTab->getValue();
        //         echo "Label: $label, Value: $value\n";
        //     }
        // }
        // echo "Envelope ID: $envelopeId, Event: $event, Status: $status\n";
    }

    public function sendReminder(Request $request)
    {
        $envelopeId = '0f43454a-bf51-40a8-8737-489eeb102692';
        $envelopeApi = $this->getEnvelopeApi();
        $account_id = env('DOCUSIGN_ACCOUNT_ID');
        $reminderRequest = new \DocuSign\eSign\Model\ReminderRequest([
            'reminder_enabled' => 'true',
            'reminder_delay' => '2',
            'reminder_frequency' => '2',
        ]);
        $results = $envelopeApi->createReminder($account_id, $envelopeId, $reminderRequest);
        echo '<pre>';
        print_r($results);
        echo '</pre>';
    }

    public function sendDocumentWithTemplate()
    {
        $templateId = '12dd2023-e2ca-475a-9783-aeceeea152bd'; // The ID of the template created in DocuSign
        $accountId = config('docusign.account_id');

        $oauth_tokens = OauthToken::where('application', 'docusign')->first();
        $accessToken = $oauth_tokens->access_token;
        // Check if accountId and templateId are set
        if (!$templateId || !$accountId) {
            throw new \Exception("Template ID or Account ID is missing.");
        }

        // Check if access token is set
        // $accessToken = Cache::get('docusign_access_token');
        if (!$accessToken) {
            throw new \Exception("Access token is missing. Please check authentication.");
        }

        // Set the recipient and role
        $templateRole = new TemplateRole([
            'email' => 'khagesh.office@gmail.com',
            'name' => 'Recipient Name',
            'role_name' => 'Signer',
        ]);

        $envelopeDefinition = new EnvelopeDefinition([
            'status' => 'sent',
            'template_id' => $templateId,
            'template_roles' => [$templateRole],
        ]);

        // Initialize the DocuSign client
        $apiClient = new ApiClient();
        $apiClient->getConfig()->setHost("https://demo.docusign.net/restapi");
        $apiClient->getConfig()->setAccessToken($accessToken);

        $envelopeApi = new EnvelopesApi($apiClient);

        // Attempt to send the envelope
        try {
            $envelopeSummary = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
            return $envelopeSummary;
        } catch (\DocuSign\eSign\Client\ApiException $e) {
            // Log or output the error details for debugging
            return response()->json(['error' => $e->getMessage(), 'body' => $e->getResponseBody()], 400);
        }
    }
}
