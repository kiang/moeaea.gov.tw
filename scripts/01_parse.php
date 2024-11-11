<?php
$targetPath = dirname(__DIR__) . '/raw/solar_tabula';
if (!file_exists($targetPath . '_csv')) {
    mkdir($targetPath . '_csv', 0777, true);
}

$files = glob($targetPath . '/*.json');
foreach ($files as $file) {
    $json = json_decode(file_get_contents($file), true);
    $lines = [];
    $header = false;
    $headerCount = 0;

    foreach ($json as $page) {
        foreach ($page['data'] as $row) {
            if (false === $header) {
                $header = [];
                foreach ($row as $k => $cell) {
                    $key = round($cell['left']);
                    $header[$key] = $k;
                }
                $lastLine = array_fill(0, count($header), ''); // Initialize empty last line
            }
            $line = array_fill(0, count($header), ''); // Initialize current line with empty values
            foreach ($row as $rowIndex => $cell) {
                if ($rowIndex === 4 || $rowIndex === 5 || $rowIndex === 6) {
                    $cell['text'] = str_replace(["\r", "\n"], ' ', trim($cell['text']));
                } else {
                    $cell['text'] = str_replace(["\r", "\n"], '', trim($cell['text']));
                }

                if (empty($cell['text'])) {
                    continue;
                }
                $currentPos = round($cell['left']);
                $nearestPos = null;
                $minDiff = PHP_FLOAT_MAX;

                // Find nearest position in header
                foreach ($header as $pos => $index) {
                    $diff = abs($pos - $currentPos);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $nearestPos = $pos;
                    }
                }

                if ($nearestPos !== null) {
                    $line[$header[$nearestPos]] = $cell['text'];
                }
            }

            // Fill empty values from last line
            foreach ($line as $k => $v) {
                if (empty($v)) {
                    $line[$k] = $lastLine[$k];
                }
            }
            if (!empty(array_filter($line))) { // Only keep non-empty lines
                $lines[] = $line;
                $lastLine = $line;
            }
        }
    }

    $outputFile = $targetPath . '_csv/' . basename($file, '.json') . '.csv';
    $fp = fopen($outputFile, 'w');
    foreach ($lines as $line) {
        fputcsv($fp, $line);
    }
    fclose($fp);
}
