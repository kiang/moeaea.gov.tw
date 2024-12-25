<?php
$baseDir = dirname(__DIR__) . '/processed';

$fh = fopen($baseDir . '/solar/combined_solar.csv', 'r');
$headers = fgetcsv($fh);
$baseDays = strtotime('2023-11-14');
$timeTarget = strtotime('2020-08-01');
$cases = [];
while ($row = fgetcsv($fh)) {
    $data = array_combine($headers, $row);
    if (false === strpos($data['施工取得日期'], '/')) {
        // 45266 = 2023/12/6
        // 45244 = 2023/11/14
        $diffDays = $data['施工取得日期'] - 45244;
        $theTime = strtotime("+{$diffDays} days", $baseDays);
    } else {
        $parts = explode('/', $data['施工取得日期']);
        $parts[0] = intval($parts[0]) + 1911;
        $theTime = strtotime("{$parts[0]}-{$parts[1]}-{$parts[2]}");
    }
    $landPos = strrpos($data['土地面積'], '.');
    if (false !== $landPos) {
        $data['土地面積'] = preg_replace('/[^0-9]+/', '', substr($data['土地面積'], 0, $landPos));
    }
    if (empty($data['縣市'])) {
        continue;
    }
    $data['縣市'] = str_replace('台', '臺', $data['縣市']);
    if(false === strpos($data['地段'], '段')) {
        $data['地段'] .= '段';
    }

    $cityPath = $baseDir . '/twland/' . $data['縣市'];
    if (!file_exists($cityPath)) {
        mkdir($cityPath, 0777, true);
    }
    $landNo = "{$data['縣市']}{$data['鄉鎮區']}{$data['地段']}{$data['地號']}";
    $geojsonFile = $cityPath . '/' . $landNo . '.json';
    if (!file_exists($geojsonFile)) {
        file_put_contents($geojsonFile, file_get_contents('https://twland.ronny.tw/index/search?lands[]=' . urlencode($landNo . '號')));
    }
}
