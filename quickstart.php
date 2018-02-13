<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

define('APPLICATION_NAME', 'Google Sheets API PHP Quickstart');
define('CREDENTIALS_PATH', __DIR__ . '/token.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/sheets.googleapis.com-php-quickstart.json

// define('SCOPES', implode(' ', array(
//         Google_Service_Sheets::SPREADSHEETS_READONLY)
// ));

define('SCOPES', implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS) //просмотр и редактирование таблиц
));

/*
if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}
*/

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
    $client = new Google_Client();
    $client->setApplicationName(APPLICATION_NAME);
    $client->setScopes(SCOPES);
    $client->setAuthConfig(CLIENT_SECRET_PATH);
    $client->setAccessType('offline');

    // Load previously authorized credentials from a file.
    $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
    if (file_exists($credentialsPath)) {
        $accessToken = json_decode(file_get_contents($credentialsPath), true);
    } else {
        // Request authorization from the user.
        $authUrl = $client->createAuthUrl();
        printf("Open the following link in your browser:\n%s\n", $authUrl);
        print 'Enter verification code: ';
        //$authCode = trim(fgets(STDIN));
        $authCode = '4/5QEGiC0W4oCyukuS4rkMu03dM94tLkCPe5fn7XvpHu8';
        // Exchange authorization code for an access token.
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        // Store the credentials to disk.
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }
        file_put_contents($credentialsPath, json_encode($accessToken));
        printf("Credentials saved to %s\n", $credentialsPath);
    }
    $client->setAccessToken($accessToken);

    // Refresh the token if it's expired.
    if ($client->isAccessTokenExpired()) {

        $refresh_token = $accessToken['refresh_token'];
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $new_token = $client->getAccessToken();
        if (!isset($new_token['refresh_token'])) {
            $new_token['refresh_token'] = $refresh_token;
        }
        file_put_contents($credentialsPath, json_encode($new_token));
    }
    return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
    $homeDirectory = getenv('HOME');
    if (empty($homeDirectory)) {
        $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
    }
    return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Sheets($client);

// Prints the names and majors of students in a sample spreadsheet:
// https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit

$spreadsheetId = '19K3mGVqKXKG44apXGfgaYs6d0iCZ7iU1l529OZYHBcw';

$result = require_once 'index.php';

//$result = $result[0];

setProp($spreadsheetId, $service, $result);

//getProp($spreadsheetId, $service);

function setProp($spreadsheetId, $service, $result) {

    $range = 'Лист1!A1:N';

    foreach($result as $res) {
        $values[] = $res;
    }

    $body = new Google_Service_Sheets_ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => 'RAW'
    ];
    $result = $service->spreadsheets_values->update($spreadsheetId, $range,
        $body, $params);
    printf("%d cells updated.", $result->getUpdatedCells());

}

function getProp($spreadsheetId, $service) {

    //$range = 'ЭЭ!F24:F25';
    $range = 'Лист1!A1:N';

    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (count($values) == 0) {
        print "No data found\n";
    } else {
        //print "Hello!\n";
        $array = [];

        foreach ($values as $row) {

            $array[] = $row;
        }

        //echo json_encode($array, JSON_UNESCAPED_UNICODE);
        echo '<pre>';
        print_r($array);
        echo '</pre>';

    }
}











