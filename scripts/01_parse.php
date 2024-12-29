<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configuration
$inputDir = __DIR__ . '/../raw/solar';
$outputDir = __DIR__ . '/../processed/solar';

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

// Define which columns should carry forward their values
$mergedColumns = ['申請年度', '項次', '業者名稱', '電廠名稱', '施工取得日期', '土地面積', '裝置容量', '縣市', '鄉鎮區', '地段'];

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
        if(empty($headerRow[0])) {
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

// Write combined CSV
if (!empty($allData)) {
    $outputFile = $outputDir . '/combined_solar.csv';
    
    // Open file for writing
    $fp = fopen($outputFile, 'w');
    
    // Write headers
    fputcsv($fp, $headers);
    
    // Write data rows
    foreach ($allData as $row) {
        fputcsv($fp, array_values($row));
    }
    
    // Close file
    fclose($fp);
    
    echo "Created combined CSV: $outputFile\n";
} else {
    echo "No data found to write\n";
}
