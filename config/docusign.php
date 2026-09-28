<?php

return [
    'base_uri' => env('DOCUSIGN_BASE_URI', 'https://demo.docusign.net/restapi'),
    'client_id' => env('DOCUSIGN_CLIENT_ID'),
    'account_id' => env('DOCUSIGN_ACCOUNT_ID'),
    'private_key' => env('DOCUSIGN_PRIVATE_KEY_PATH'),
    'integration_key' => env('DOCUSIGN_INTEGRATION_KEY'),
    'user_guid' => env('DOCUSIGN_USER_GUID'),
];
