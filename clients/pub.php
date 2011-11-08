<?php
/**
* Pubdis
*
*  @requires curllib
*/
abstract class Pubdis {

    protected static
        $_pubdis_nodes = array('127.0.0.1:8000'),
        $_logging = true;

    private static
        $_log = array();

    public static function publish($id, $action, $data) {

        $domain = self::$_pubdis_nodes[array_rand(self::$_pubdis_nodes)];
        $path = "/$id/$action";
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => $domain.$path,
            CURLOPT_TIMEOUT_MS => 500,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_POST => 3,
            CURLOPT_POSTFIELDS => 'data='.urlencode(json_encode($data))
        ));

        $r = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            throw new PubdisException('Curl Error: '.curl_error($ch), curl_errno($ch));
        }

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_status !== 201) {
            throw new PubdisException("HTTP status code returned: $http_status", $http_status);
        }

        curl_close($ch);
        self::$_log[] = $info;
    }

    public static function log() {
        return self::$_log;
    }
}
class PubdisException extends Exception {}
