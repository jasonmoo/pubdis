<?php
/**
* Pubdis
*
*  @requires curllib
*/
abstract class Pubdis {

    protected static
        $_pubdis_nodes = array('buff:6379'),
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

    public static function tcpPublish($id, $action, $data) {

        $domain = explode(':',self::$_pubdis_nodes[array_rand(self::$_pubdis_nodes)]);
        $host = 'tcp://'.$domain[0];
        $port = isset($domain[1]) ? $domain[1] : 6379;

        $channel = "$id/$action";
        $channel_len = mb_strlen($channel,'utf8');

        $data = json_encode($data);
        $data_len = mb_strlen($data,'utf8');

        try {
            $socket = fsockopen($host, $port, $errno, $errstr);
            if (!$socket) {
                error_log('Unable to connect to redis: '.$errno.':'.$errstr, __LINE__);
            }

            $command  = "*3\r\n\$7\r\npublish\r\n\$$channel_len\r\n$channel\r\n\$$data_len\r\n";

            stream_set_blocking($socket, 0); // set to non-blocking
            fwrite($socket, $command);
            fwrite($socket, $data);
            fwrite($socket, "\r\n");
            fclose($socket);

            unset($data);
        } catch (Exception $e) {
            error_log('Unable to connect to redis: '.$e, __LINE__);
            @fclose($socket);
        }

    }

    public static function log() {
        return self::$_log;
    }
}
class PubdisException extends Exception {}
