<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configuration
$inputDir = __DIR__ . '/../raw/solar';
$outputDir = __DIR__ . '/../processed/solar';
$uuidFile = __DIR__ . '/../raw/uuid.csv';

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

// Get all ODS files
$odsFiles = glob($inputDir . '/*.ods');

// Initialize array to store all data
$allData = [];

// Expected headers (modify these according to your needs)
$headers = [
    '申請年度',
    '項次',
    '業者名稱',
    '電廠名稱',
    '施工取得日期',
    '土地面積',
    '裝置容量',
    '縣市',
    '鄉鎮區',
    '地段',
    '地號'
];

// Define unique key fields
$uniqueFields = ['申請年度', '項次', '縣市', '鄉鎮區', '地段', '地號'];

// Define which columns should carry forward their values
$mergedColumns = ['申請年度', '項次', '業者名稱', '電廠名稱', '施工取得日期', '土地面積', '裝置容量', '縣市', '鄉鎮區', '地段'];

// Load existing UUID mappings
$uniqueEntries = [];
if (file_exists($uuidFile)) {
    $fp = fopen($uuidFile, 'r');
    $headers = fgetcsv($fp);
    while (($row = fgetcsv($fp)) !== false) {
        $uniqueEntries[$row[1]] = $row[0]; // key => uuid
    }
    fclose($fp);
}

foreach ($odsFiles as $odsFile) {
    $basename = basename($odsFile, '.ods');
    echo "Processing: $basename\n";

    try {
        // Load the ODS file
        $spreadsheet = IOFactory::load($odsFile);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row and column
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Get the header row to map columns
        $headerRow = $worksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, false)[0];
        $dataRowBegin = 2;
        if (empty($headerRow[0])) {
            $headerRow = $worksheet->rangeToArray('A2:' . $highestColumn . '2', null, true, false)[0];
            $dataRowBegin = 3;
        }
        $columnMap = [];

        // Create column mapping
        foreach ($headerRow as $colIndex => $header) {
            $header = trim($header);
            if (in_array($header, $headers)) {
                $columnMap[$colIndex] = $header;
            }
        }

        // Store previous row values for merged cells
        $previousValues = array_fill_keys($headers, '');

        // Read data rows
        for ($row = $dataRowBegin; $row <= $highestRow; $row++) {
            $rowData = $worksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, false)[0];

            // Create data row with mapped columns
            $dataRow = array_fill_keys($headers, '');
            $hasNonEmptyValue = false;

            // First pass: fill in non-empty values
            foreach ($columnMap as $colIndex => $header) {
                if (isset($rowData[$colIndex]) && trim($rowData[$colIndex]) !== '') {
                    $dataRow[$header] = trim($rowData[$colIndex]);
                    $hasNonEmptyValue = true;
                    // Update previous values for merged columns
                    if (in_array($header, $mergedColumns)) {
                        $previousValues[$header] = $dataRow[$header];
                    }
                }
            }

            // Skip completely empty rows
            if (!$hasNonEmptyValue) {
                continue;
            }

            // Second pass: fill in empty merged columns with previous values
            foreach ($mergedColumns as $header) {
                if (empty($dataRow[$header])) {
                    $dataRow[$header] = $previousValues[$header];
                }
            }

            // Add row to all data if it has any non-empty values
            if (array_filter($dataRow)) {
                $allData[] = $dataRow;
            }
        }

        // Free up memory
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
    } catch (Exception $e) {
        echo "Error processing $basename: " . $e->getMessage() . "\n";
    }
}



// Write original combined CSV
if (!empty($allData)) {
    $headers = array_merge(['uuid'], $headers);
    
    $outputFile = $outputDir . '/combined_solar.csv';

    // Open file for writing
    $fp = fopen($outputFile, 'w');

    // Write headers
    fputcsv($fp, $headers);

    // Process unique entries and generate UUIDs
    $newUuidMappings = [];

    foreach ($allData as $row) {
        // Create unique key
        $uniqueKey = [];
        foreach ($uniqueFields as $field) {
            $uniqueKey[] = $row[$field];
        }
        $uniqueKeyStr = implode('|', $uniqueKey);

        if (!isset($uniqueEntries[$uniqueKeyStr])) {
            // Generate UUID for new unique entry
            $uuid = uuid_create();
            $uniqueEntries[$uniqueKeyStr] = $uuid;
            $newUuidMappings[] = [$uuid, $uniqueKeyStr];
        } else {
            $uuid = $uniqueEntries[$uniqueKeyStr];
        }

        fputcsv($fp, array_merge([$uuid], $row));
    }
    // Close file
    fclose($fp);

    // Update UUID mapping file
    if (!empty($newUuidMappings)) {
        $fp = fopen($uuidFile, 'a');
        if (!file_exists($uuidFile)) {
            fputcsv($fp, ['uuid', 'key']);
        }
        foreach ($newUuidMappings as $mapping) {
            fputcsv($fp, $mapping);
        }
        fclose($fp);
        echo "Added " . count($newUuidMappings) . " new UUID mappings\n";
    }


    echo "Created combined CSV: $outputFile\n";
}
