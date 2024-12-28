<?php
require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;

$baseDir = dirname(__DIR__) . '/processed';

$fh = fopen($baseDir . '/solar/combined_solar.csv', 'r');
$headers = fgetcsv($fh);
$baseDays = strtotime('2023-11-14');
$timeTarget = strtotime('2020-08-01');
$cities = ['C' => '基隆市', 'A' => '臺北市', 'F' => '新北市', 'H' => '桃園市', 'G' => '宜蘭縣', 'O' => '新竹市', 'J' => '新竹縣', 'K' => '苗栗縣', 'B' => '臺中市', 'N' => '彰化縣', 'M' => '南投縣', 'U' => '花蓮縣', 'P' => '雲林縣', 'I' => '嘉義市', 'Q' => '嘉義縣', 'D' => '臺南市', 'E' => '高雄市', 'V' => '臺東縣', 'T' => '屏東縣', 'W' => '金門縣', 'Z' => '連江縣', 'X' => '澎湖縣',];
$towns = array(
    'C' =>
    array(
        'C02' => '七堵區',
        'C05' => '中山區',
        'C01' => '中正區',
        'C04' => '仁愛區',
        'C06' => '安樂區',
        'C07' => '信義區',
        'C03' => '暖暖區',
    ),
    'A' =>
    array(
        'A15' => '士林區',
        'A09' => '大同區',
        'A02' => '大安區',
        'A10' => '中山區',
        'A03' => '中正區',
        'A14' => '內湖區',
        'A11' => '文山區',
        'A16' => '北投區',
        'A01' => '松山區',
        'A17' => '信義區',
        'A13' => '南港區',
        'A05' => '萬華區',
    ),
    'F' =>
    array(
        'F32' => '八里區',
        'F30' => '三芝區',
        'F05' => '三重區',
        'F15' => '三峽區',
        'F19' => '土城區',
        'F18' => '中和區',
        'F03' => '五股區',
        'F22' => '平溪區',
        'F33' => '永和區',
        'F31' => '石門區',
        'F08' => '石碇區',
        'F28' => '汐止區',
        'F10' => '坪林區',
        'F02' => '林口區',
        'F14' => '板橋區',
        'F25' => '金山區',
        'F06' => '泰山區',
        'F11' => '烏來區',
        'F24' => '貢寮區',
        'F27' => '淡水區',
        'F09' => '深坑區',
        'F07' => '新店區',
        'F01' => '新莊區',
        'F21' => '瑞芳區',
        'F26' => '萬里區',
        'F17' => '樹林區',
        'F23' => '雙溪區',
        'F04' => '蘆洲區',
        'F16' => '鶯歌區',
    ),
    'H' =>
    array(
        'H08' => '八德區',
        'H06' => '大園區',
        'H02' => '大溪區',
        'H03' => '中壢區',
        'H10' => '平鎮區',
        'H01' => '桃園區',
        'H13' => '復興區',
        'H11' => '新屋區',
        'H04' => '楊梅區',
        'H09' => '龍潭區',
        'H07' => '龜山區',
        'H05' => '蘆竹區',
        'H12' => '觀音區',
    ),
    'G' =>
    array(
        'G10' => '三星鄉',
        'G11' => '大同鄉',
        'G07' => '五結鄉',
        'G08' => '冬山鄉',
        'G04' => '壯圍鄉',
        'G01' => '宜蘭市',
        'G12' => '南澳鄉',
        'G05' => '員山鄉',
        'G02' => '頭城鎮',
        'G03' => '礁溪鄉',
        'G06' => '羅東鎮',
        'G09' => '蘇澳鎮',
    ),
    'O' =>
    array(
        'O01' => '新竹市',
    ),
    'J' =>
    array(
        'J15' => '五峰鄉',
        'J12' => '北埔鄉',
        'J14' => '尖石鄉',
        'J05' => '竹北市',
        'J02' => '竹東鎮',
        'J13' => '峨眉鄉',
        'J06' => '湖口鄉',
        'J04' => '新埔鎮',
        'J09' => '新豐鄉',
        'J08' => '橫山鄉',
        'J03' => '關西鎮',
        'J11' => '寶山鄉',
        'J10' => '芎林鄉',
    ),
    'K' =>
    array(
        'K06' => '三義鄉',
        'K13' => '三灣鄉',
        'K15' => '大湖鄉',
        'K04' => '公館鄉',
        'K09' => '竹南鎮',
        'K07' => '西湖鄉',
        'K16' => '卓蘭鎮',
        'K14' => '南庄鄉',
        'K12' => '後龍鎮',
        'K01' => '苗栗市',
        'K02' => '苑裡鎮',
        'K18' => '泰安鄉',
        'K03' => '通霄鎮',
        'K11' => '造橋鄉',
        'K17' => '獅潭鄉',
        'K05' => '銅鑼鄉',
        'K10' => '頭份市',
        'K08' => '頭屋鄉',
    ),
    'B' =>
    array(
        'B11' => '大甲區',
        'B22' => '大安區',
        'B24' => '大肚區',
        'B28' => '大里區',
        'B18' => '大雅區',
        'B01' => '中區',
        'B27' => '太平區',
        'B08' => '北屯區',
        'B05' => '北區',
        'B21' => '外埔區',
        'B20' => '石岡區',
        'B15' => '后里區',
        'B06' => '西屯區',
        'B04' => '西區',
        'B13' => '沙鹿區',
        'B29' => '和平區',
        'B02' => '東區',
        'B10' => '東勢區',
        'B07' => '南屯區',
        'B03' => '南區',
        'B23' => '烏日區',
        'B16' => '神岡區',
        'B14' => '梧棲區',
        'B12' => '清水區',
        'B19' => '新社區',
        'B17' => '潭子區',
        'B25' => '龍井區',
        'B09' => '豐原區',
        'B26' => '霧峰區',
    ),
    'N' =>
    array(
        'N20' => '二水鄉',
        'N08' => '二林鎮',
        'N15' => '大村鄉',
        'N24' => '大城鄉',
        'N04' => '北斗鎮',
        'N18' => '永靖鄉',
        'N07' => '田中鎮',
        'N21' => '田尾鄉',
        'N25' => '竹塘鄉',
        'N10' => '伸港鄉',
        'N12' => '秀水鄉',
        'N03' => '和美鎮',
        'N19' => '社頭鄉',
        'N23' => '芳苑鄉',
        'N13' => '花壇鄉',
        'N14' => '芬園鄉',
        'N05' => '員林市',
        'N17' => '埔心鄉',
        'N16' => '埔鹽鄉',
        'N22' => '埤頭鄉',
        'N02' => '鹿港鎮',
        'N26' => '溪州鄉',
        'N06' => '溪湖鎮',
        'N01' => '彰化市',
        'N11' => '福興鄉',
        'N09' => '線西鄉',
    ),
    'M' =>
    array(
        'M08' => '中寮鄉',
        'M13' => '仁愛鄉',
        'M11' => '水里鄉',
        'M06' => '名間鄉',
        'M04' => '竹山鎮',
        'M12' => '信義鄉',
        'M01' => '南投市',
        'M02' => '埔里鎮',
        'M03' => '草屯鎮',
        'M10' => '國姓鄉',
        'M09' => '魚池鄉',
        'M07' => '鹿谷鄉',
        'M05' => '集集鎮',
    ),
    'U' =>
    array(
        'U03' => '玉里鎮',
        'U02' => '光復鄉',
        'U05' => '吉安鄉',
        'U11' => '秀林鄉',
        'U13' => '卓溪鄉',
        'U01' => '花蓮市',
        'U10' => '富里鄉',
        'U04' => '新城鄉',
        'U09' => '瑞穗鄉',
        'U12' => '萬榮鄉',
        'U06' => '壽豐鄉',
        'U07' => '鳳林鎮',
        'U08' => '豐濱鄉',
    ),
    'P' =>
    array(
        'P11' => '二崙鄉',
        'P19' => '口湖鄉',
        'P05' => '土庫鎮',
        'P08' => '大埤鄉',
        'P17' => '元長鄉',
        'P01' => '斗六市',
        'P02' => '斗南鎮',
        'P20' => '水林鄉',
        'P06' => '北港鎮',
        'P07' => '古坑鄉',
        'P16' => '臺西鄉',
        'P18' => '四湖鄉',
        'P04' => '西螺鎮',
        'P14' => '東勢鄉',
        'P10' => '林內鄉',
        'P03' => '虎尾鎮',
        'P12' => '崙背鄉',
        'P13' => '麥寮鄉',
        'P15' => '褒忠鄉',
        'P09' => '莿桐鄉',
    ),
    'I' =>
    array(
        'I01' => '嘉義市',
    ),
    'Q' =>
    array(
        'Q04' => '大林鎮',
        'Q18' => '大埔鄉',
        'Q14' => '中埔鄉',
        'Q08' => '六腳鄉',
        'Q12' => '太保市',
        'Q13' => '水上鄉',
        'Q03' => '布袋鎮',
        'Q05' => '民雄鄉',
        'Q02' => '朴子市',
        'Q15' => '竹崎鄉',
        'Q09' => '東石鄉',
        'Q20' => '阿里山鄉',
        'Q16' => '梅山鄉',
        'Q11' => '鹿草鄉',
        'Q17' => '番路鄉',
        'Q07' => '新港鄉',
        'Q06' => '溪口鄉',
        'Q10' => '義竹鄉',
    ),
    'D' =>
    array(
        'D22' => '七股區',
        'D16' => '下營區',
        'D19' => '大內區',
        'D30' => '山上區',
        'D08' => '中西區',
        'D32' => '仁德區',
        'D17' => '六甲區',
        'D24' => '北門區',
        'D04' => '北區',
        'D31' => '左鎮區',
        'D39' => '永康區',
        'D36' => '玉井區',
        'D12' => '白河區',
        'D07' => '安平區',
        'D29' => '安定區',
        'D06' => '安南區',
        'D21' => '西港區',
        'D20' => '佳里區',
        'D18' => '官田區',
        'D14' => '東山區',
        'D01' => '東區',
        'D38' => '南化區',
        'D02' => '南區',
        'D13' => '後壁區',
        'D11' => '柳營區',
        'D23' => '將軍區',
        'D15' => '麻豆區',
        'D27' => '善化區',
        'D26' => '新化區',
        'D28' => '新市區',
        'D09' => '新營區',
        'D37' => '楠西區',
        'D25' => '學甲區',
        'D35' => '龍崎區',
        'D33' => '歸仁區',
        'D34' => '關廟區',
        'D10' => '鹽水區',
    ),
    'E' =>
    array(
        'E05' => '三民區',
        'E16' => '大社區',
        'E14' => '大寮區',
        'E15' => '大樹區',
        'E11' => '小港區',
        'E17' => '仁武區',
        'E35' => '內門區',
        'E32' => '六龜區',
        'E03' => '左營區',
        'E27' => '永安區',
        'E22' => '田寮區',
        'E33' => '甲仙區',
        'E34' => '杉林區',
        'E38' => '那瑪夏區',
        'E19' => '岡山區',
        'E13' => '林園區',
        'E23' => '阿蓮區',
        'E07' => '前金區',
        'E09' => '前鎮區',
        'E31' => '美濃區',
        'E26' => '茄萣區',
        'E36' => '茂林區',
        'E08' => '苓雅區',
        'E37' => '桃源區',
        'E29' => '梓官區',
        'E18' => '鳥松區',
        'E25' => '湖內區',
        'E06' => '新興區',
        'E04' => '楠梓區',
        'E24' => '路竹區',
        'E02' => '鼓山區',
        'E30' => '旗山區',
        'E10' => '旗津區',
        'E12' => '鳳山區',
        'E20' => '橋頭區',
        'E21' => '燕巢區',
        'E28' => '彌陀區',
        'E01' => '鹽埕區',
    ),
    'V' =>
    array(
        'V05' => '大武鄉',
        'V06' => '太麻里鄉',
        'V01' => '臺東市',
        'V02' => '成功鎮',
        'V10' => '池上鄉',
        'V04' => '卑南鄉',
        'V12' => '延平鄉',
        'V07' => '東河鄉',
        'V15' => '金峰鄉',
        'V08' => '長濱鄉',
        'V13' => '海端鄉',
        'V09' => '鹿野鄉',
        'V14' => '達仁鄉',
        'V11' => '綠島鄉',
        'V03' => '關山鎮',
        'V16' => '蘭嶼鄉',
    ),
    'T' =>
    array(
        'T08' => '九如鄉',
        'T26' => '三地門鄉',
        'T13' => '內埔鄉',
        'T14' => '竹田鄉',
        'T33' => '牡丹鄉',
        'T23' => '車城鄉',
        'T09' => '里港鄉',
        'T21' => '佳冬鄉',
        'T30' => '來義鄉',
        'T25' => '枋山鄉',
        'T16' => '枋寮鄉',
        'T03' => '東港鎮',
        'T19' => '林邊鄉',
        'T06' => '長治鄉',
        'T20' => '南州鄉',
        'T01' => '屏東市',
        'T04' => '恆春鎮',
        'T31' => '春日鄉',
        'T18' => '崁頂鄉',
        'T29' => '泰武鄉',
        'T22' => '琉球鄉',
        'T11' => '高樹鄉',
        'T15' => '新埤鄉',
        'T17' => '新園鄉',
        'T32' => '獅子鄉',
        'T05' => '萬丹鄉',
        'T12' => '萬巒鄉',
        'T24' => '滿州鄉',
        'T28' => '瑪家鄉',
        'T02' => '潮州鎮',
        'T27' => '霧臺鄉',
        'T07' => '麟洛鄉',
        'T10' => '鹽埔鄉',
    ),
    'W' =>
    array(
        'W02' => '金沙鎮',
        'W03' => '金城鎮',
        'W01' => '金湖鎮',
        'W04' => '金寧鄉',
        'W05' => '烈嶼鄉',
        'W06' => '烏坵鄉',
    ),
    'Z' =>
    array(
        'Z02' => '北竿鄉',
        'Z04' => '東引鄉',
        'Z01' => '南竿鄉',
        'Z03' => '莒光鄉',
    ),
    'X' =>
    array(
        'X06' => '七美鄉',
        'X03' => '白沙鄉',
        'X04' => '西嶼鄉',
        'X01' => '馬公市',
        'X05' => '望安鄉',
        'X02' => '湖西鄉',
    ),
);
$features = [];
$sections = [];
$sectionMappings = [
    'Q04大功一段' => '大工一段',
    'D09茄苳腳段' => '茄苳脚段',
    'Q14下六段公館小段' => '下六段司公舘小段',
    'Q03菜舖廍段蔡舖廍小段' => '菜舖廍段菜舖廍小段',
    'Q11鹿草斷鹿東小段' => '鹿草段鹿東小段',
    'B11大甲區幼獅段' => '幼獅段',
    'N09線西鄉西興段' => '西興段',
    'T11柬振新段' => '東振新段',
    'D23山子腳段' => '山子脚段',
    'Q09栗仔崙段中洲小段' => '栗子崙段中洲小段',
    'D25宅仔港段' => '宅子港段',
    'D22下山寮段' => '下山子寮段',
    'T16大響營一小段' => '大響營段一小段',
];
$cityMappings = [
    '屏東鎮' => '屏東縣',
    '桃園縣' => '桃園市',
    '嘉義鄉' => '嘉義縣',
];
$townMappings = [
    '布袋鄉' => '布袋鎮',
    '林邊鎮' => '林邊鄉',
    '台西鄉' => '臺西鄉',
    '潮洲鎮' => '潮州鎮',
];
$townSectionMapping = [
    '鹽水區嘉芳段' => '新營區',
    '獅子鄉海口段' => '車城鄉',
    '學甲區客子寮段' => '麻豆區',
    '山上區大社段' => '新市區',
    '新市區牛稠埔段' => '山上區',
    '桃園區華亞段' => '龜山區',
];
$pointsCsvFile = $baseDir . '/solar_points.csv';
$pointsPool = [];
if (file_exists($pointsCsvFile)) {
    $pFh = fopen($pointsCsvFile, 'r');
    $pHeaders = fgetcsv($pFh);
    while ($row = fgetcsv($pFh)) {
        $data = array_combine($pHeaders, $row);
        $pointKey = $data['縣市'] . $data['鄉鎮區'] . $data['地段'] . $data['地號'];
        $pointsPool[$pointKey] = [
            'Longitude' => $data['Longitude'],
            'Latitude' => $data['Latitude'],
        ];
    }
    fclose($pFh);
}
$browser = new HttpBrowser(HttpClient::create());
$oFh = fopen($pointsCsvFile, 'w');
$firstLine = false;
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
    if (isset($cityMappings[$data['縣市']])) {
        $data['縣市'] = $cityMappings[$data['縣市']];
    }
    if (isset($townMappings[$data['鄉鎮區']])) {
        $data['鄉鎮區'] = $townMappings[$data['鄉鎮區']];
    }
    $townSectionMappingKey = $data['鄉鎮區'] . $data['地段'];
    if (isset($townSectionMapping[$townSectionMappingKey])) {
        $data['鄉鎮區'] = $townSectionMapping[$townSectionMappingKey];
    }
    if (false === strpos($data['地段'], '段')) {
        $data['地段'] .= '段';
    }
    if (false !== strpos($data['地段'], "\n")) {
        $data['地段'] = trim(substr($data['地段'], 0, strpos($data['地段'], "\n")));
    }
    if (false !== strpos($data['地號'], "\n")) {
        $data['地號'] = trim(substr($data['地號'], 0, strpos($data['地號'], "\n")));
    }
    $cityCode = array_search($data['縣市'], $cities);
    $townCode = array_search($data['鄉鎮區'], $towns[$cityCode]);
    $data['地段'] = Normalizer::normalize($data['地段'], Normalizer::FORM_C);
    $mappingKey = $townCode . $data['地段'];
    if (isset($sectionMappings[$mappingKey])) {
        $data['地段'] = $sectionMappings[$mappingKey];
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


    $json = json_decode(file_get_contents($geojsonFile), true);
    $pointKey = $data['縣市'] . $data['鄉鎮區'] . $data['地段'] . $data['地號'];
    if (!empty($json['features'])) {
        $feature = $json['features'][0];
        $p = $feature['properties'];
        $feature['properties'] = $data;
        if (!isset($features[$data['縣市']])) {
            $features[$data['縣市']] = [];
        }
        $features[$data['縣市']][] = $feature;
        $data['Longitude'] = $p['xcenter'];
        $data['Latitude'] = $p['ycenter'];
    } elseif (isset($pointsPool[$pointKey])) {
        $data['Longitude'] = $pointsPool[$pointKey]['Longitude'];
        $data['Latitude'] = $pointsPool[$pointKey]['Latitude'];
    } else {
        $moiFile = dirname(__DIR__) . '/raw/moi/' . $townCode . '.json';
        if (!file_exists($moiFile)) {
            $moiJson = [];
            $items = json_decode(file_get_contents("https://easymap.land.moi.gov.tw/W10Web/City_json_getSectionList?cityCode={$cityCode}&townCode=" . substr($townCode, 1)), true);
            foreach ($items as $item) {
                $moiJson[$item['name']] = $item;
            }
            file_put_contents($moiFile, json_encode($moiJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
        if (!isset($sections[$townCode])) {
            $sections[$townCode] = json_decode(file_get_contents($moiFile), true);
        }
        $section = $sections[$townCode][$data['地段']];

        $browser->request('GET', 'https://easymap.land.moi.gov.tw/Z10Web/layout/setToken.jsp');
        $tokenPage = $browser->getResponse()->getContent();
        $tokenParts = explode('"', $tokenPage);

        $result = exec("curl 'https://easymap.land.moi.gov.tw/W10Web/Land_json_locate' \
  -H 'Accept: application/json, text/javascript, */*; q=0.01' \
  -H 'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' \
  -H 'Cache-Control: no-cache' \
  -H 'Connection: keep-alive' \
  -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
  -H 'Origin: https://easymap.land.moi.gov.tw' \
  -H 'Pragma: no-cache' \
  -H 'Referer: https://easymap.land.moi.gov.tw/W10Web/Normal' \
  -H 'Sec-Fetch-Dest: empty' \
  -H 'Sec-Fetch-Mode: cors' \
  -H 'Sec-Fetch-Site: same-origin' \
  -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36' \
  -H 'X-Requested-With: XMLHttpRequest' \
  -H 'sec-ch-ua: \"Google Chrome\";v=\"131\", \"Chromium\";v=\"131\", \"Not_A Brand\";v=\"24\"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: \"Linux\"' \
  --data-raw 'sectNo={$section['id']}&office={$section['officeCode']}&landNo={$data['地號']}&struts.token.name={$tokenParts[9]}&token={$tokenParts[11]}'");

        // $browser->request('GET', "https://easymap.land.moi.gov.tw/Z10Web/Land_json_locate?sectNo={$section['id']}&office={$section['officeCode']}&landNo={$data['地號']}&struts.token.name={$tokenParts[9]}&token={$tokenParts[11]}");
        // $result = $browser->getResponse()->getContent();

        while (false !== strpos($result, '系統檢測您的連線不正常')) {
            echo $result;
            sleep(3); //被阻擋，所以暫停3秒
            $browser->request('GET', 'https://easymap.land.moi.gov.tw/Z10Web/layout/setToken.jsp');
            $tokenPage = $browser->getResponse()->getContent();
            $tokenParts = explode('"', $tokenPage);
            $result = exec("curl 'https://easymap.land.moi.gov.tw/W10Web/Land_json_locate' \
            -H 'Accept: application/json, text/javascript, */*; q=0.01' \
            -H 'Accept-Language: zh-TW,zh;q=0.9,en-US;q=0.8,en;q=0.7' \
            -H 'Cache-Control: no-cache' \
            -H 'Connection: keep-alive' \
            -H 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8' \
            -H 'Origin: https://easymap.land.moi.gov.tw' \
            -H 'Pragma: no-cache' \
            -H 'Referer: https://easymap.land.moi.gov.tw/W10Web/Normal' \
            -H 'Sec-Fetch-Dest: empty' \
            -H 'Sec-Fetch-Mode: cors' \
            -H 'Sec-Fetch-Site: same-origin' \
            -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36' \
            -H 'X-Requested-With: XMLHttpRequest' \
            -H 'sec-ch-ua: \"Google Chrome\";v=\"131\", \"Chromium\";v=\"131\", \"Not_A Brand\";v=\"24\"' \
            -H 'sec-ch-ua-mobile: ?0' \
            -H 'sec-ch-ua-platform: \"Linux\"' \
            --data-raw 'sectNo={$section['id']}&office={$section['officeCode']}&landNo={$data['地號']}&struts.token.name={$tokenParts[9]}&token={$tokenParts[11]}'");
        }

        if (false === strpos($result, '地號查詢無資料')) {
            $json = json_decode($result, true);
            $data['Longitude'] = $json['X'];
            $data['Latitude'] = $json['Y'];
        }
    }

    if (!empty($data['Longitude'])) {
        if (false === $firstLine) {
            fputcsv($oFh, array_keys($data));
            $firstLine = true;
        }
        fputcsv($oFh, $data);
    }
}

foreach ($features as $city => $c) {
    file_put_contents($baseDir . '/solar/' . $city . '.json', json_encode([
        'type' => 'FeatureCollection',
        'features' => $c,
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
