<?php

namespace App\Services;

use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\Tabs;

class DocuSignService
{
    protected $client;
    protected $accountId;

    /**
     * DocuSignService constructor.
     * Some notes:
     * Integration key on the docusign website is client id
     */

    public function __construct()
    {
        $config = new Configuration();
        $config->setHost(config('docusign.base_uri'));
        $config->addDefaultHeader("Authorization", "Bearer " . $this->getAccessToken());

        $this->client = new ApiClient($config);
        $this->accountId = config('docusign.account_id');
    }
    private function getToken(ApiClient $apiClient): string
    {
        try {
            $privateKey = file_get_contents(storage_path(config('docusign.private_key')));
            $response = $apiClient->requestJWTUserToken(
                $ikey = config('docusign.client_id'),
                $userId = config('docusign.account_id'),
                $key = $privateKey,
                $scope = 'signature impersonation'
            );
            $token = $response[0];
            $accessToken = $token->getAccessToken();
        } catch (\Throwable $th) {
            throw $th;
        }
        return $accessToken;
    }

    private function getAccessToken()
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

        $botUrl = $url . $queryBuild;

        echo $botUrl;
        echo '<br>';
        exit('exit here');

        // Implement JWT or Authorization Code Grant to obtain the access token
        // This method should return a valid token
        $authClient = new ApiClient();
        $privateKey = file_get_contents(storage_path(config('docusign.private_key')));

        $token = (new \Firebase\JWT\JWT)->encode([
            'iss' => config('docusign.client_id'),
            'sub' => config('docusign.account_id'),
            'aud' => config('docusign.base_uri') . "/oauth/token",
            'exp' => time() + 3600,
            'scope' => 'signature',
        ], $privateKey, 'RS256');

        return $token;
        // Use the JWT to request an access token
        $response = $authClient->requestJWTUserToken(
            config('docusign.client_id'),
            config('docusign.account_id'),
            $privateKey,
            ["signature"]
        );

        return $response[0]->getAccessToken();
    }

    public function createEnvelope($recipientEmail, $recipientName, $documentPath)
    {
        $envelopesApi = new EnvelopesApi($this->client);

        // Create a new envelope
        $envelope = new EnvelopeDefinition([
            'email_subject' => "Please sign this document",
        ]);

        // Add a document
        $document = new Document([
            'document_base64' => base64_encode(file_get_contents($documentPath)),
            'name' => 'Test Document',
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
            'x_position' => '100',
            'y_position' => '150',
        ]);

        $signer->setTabs(new Tabs(['sign_here_tabs' => [$signHere]]));
        $envelope->setRecipients(['signers' => [$signer]]);

        // Send the envelope
        $envelope->setStatus('sent');
        return $envelopesApi->createEnvelope($this->accountId, $envelope);
    }
}
