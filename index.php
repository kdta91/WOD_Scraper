<?php
    function scrapeWod($url, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    function removeUtf8Bom($text) {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map(__FUNCTION__, $d);
        }
        else {
            // Return array
            return $d;
        }
    }

    header('Content-Type: application/json');

    $wodArray = array();

    for ($i=1; $i <= 15; $i++) {
        // category=1 Benchmarks
        // category=8 Qualifiers
        // category=5 Girls
        // category=7 Heroes
        $url = 'https://wodwell.com/wods/?category=7';
        $params = array(
            'nf_ajax_query' => true,
            'paged' => $i,
            'filter_referer' => false
        );
        $res = scrapeWod($url, $params);
        $res = removeUtf8Bom($res);
        $res = json_decode($res);
        $res = objectToArray($res);
        $wods = $res['data']['wods'];

        array_push($wodArray, $wods);
    }

    $jsonData = json_encode($wodArray);
    $filename = 'heroes.json';
    $action = fopen($filename, 'w+');

    fwrite($action, $jsonData);
    fclose($action);
?>