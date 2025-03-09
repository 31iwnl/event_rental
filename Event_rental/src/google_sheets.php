<?php
require_once __DIR__.'/../vendor/autoload.php';
$client = new Google\Client();
class GoogleSheetsHelper {
    private $service;
    const SHEET_ID = '1EzLUCZKYyB8G1GR7gJuzGWP80-Qct_PdjSaxvoENcTA';

    public function __construct() {
        $client = new Google\Client();
        $client->setAuthConfig(__DIR__.'/../credentials.json');
        $client->addScope(Google\Service\Sheets::SPREADSHEETS);

        $httpClient = new \GuzzleHttp\Client([
            'verify' => false,
        ]);
        $client->setHttpClient($httpClient);

        $this->service = new Google\Service\Sheets($client);
    }


    public function getSheetData($sheetName) {
        try {
            $response = $this->service->spreadsheets_values->get(self::SHEET_ID, $sheetName);
            $values = $response->getValues();
    
    
            return $this->formatData($values);
        } catch (Exception $e) {
            error_log("Google Sheets Error: ".$e->getMessage());
            return [];
        }
    }
    

    private function formatData($values) {
        if (empty($values)) return [];
    
        $headers = array_shift($values);
        $result = [];
    
        foreach ($values as $rowIndex => $row) {
            if (count($headers) !== count($row)) {
                error_log("Ошибка в строке $rowIndex: заголовков (".count($headers)."), значений (".count($row).")");
                continue; 
            }
    
            try {
                $result[] = array_combine($headers, $row);
            } catch (ValueError $e) {
                error_log("Ошибка в строке $rowIndex: " . $e->getMessage());
            }
        }
    
        return $result;
    }
    

    public function getCategories() {
        $spreadsheet = $this->service->spreadsheets->get(self::SHEET_ID);
        return array_map(function($sheet, $index) {
            return [
                'id' => $index + 1,
                'name' => $sheet->getProperties()->title,
                'image' => "category".($index+1).".jpg"
            ];
        }, $spreadsheet->getSheets(), array_keys($spreadsheet->getSheets()));
    }
}
?>
