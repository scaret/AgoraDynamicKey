<?php

class DynamicKey5
{

    const version = "005";
    const NO_UPLOAD = "0";
    const AUDIO_VIDEO_UPLOAD = "3";

    // InChannelPermissionKey
    const ALLOW_UPLOAD_IN_CHANNEL = 1;

    // Service Type
    const MEDIA_CHANNEL_SERVICE = 1;
    const RECORDING_SERVICE = 2;
    const PUBLIC_SHARING_SERVICE = 3;
    const IN_CHANNEL_PERMISSION = 4;
    public static function generateRecordingKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return DynamicKey5::generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, DynamicKey5::RECORDING_SERVICE, array());
    }

    public static function generateMediaChannelKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs)
    {
        return DynamicKey5::generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, DynamicKey5::MEDIA_CHANNEL_SERVICE, array());
    }

    public static function generateInChannelPermissionKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $permission)
    {
        $extra[DynamicKey5::ALLOW_UPLOAD_IN_CHANNEL] = $permission;
        return DynamicKey5::generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, DynamicKey5::IN_CHANNEL_PERMISSION, $extra);
    }

    public static function generateDynamicKey($appID, $appCertificate, $channelName, $ts, $randomInt, $uid, $expiredTs, $serviceType, $extra)
    {
        $signature = DynamicKey5::generateSignature($serviceType, $appID, $appCertificate, $channelName, $uid, $ts, $randomInt, $expiredTs, $extra);
        $content = DynamicKey5::packContent($serviceType, $signature, hex2bin($appID), $ts, $randomInt, $expiredTs, $extra);
        // echo bin2hex($content);
        return DynamicKey5::version . base64_encode($content);
    }

    public static function generateSignature($serviceType, $appID, $appCertificate, $channelName, $uid, $ts, $salt, $expiredTs, $extra)
    {
        $rawAppID = hex2bin($appID);
        $rawAppCertificate = hex2bin($appCertificate);

        $buffer = pack("S", $serviceType);
        $buffer .= pack("S", strlen($rawAppID)) . $rawAppID;
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("S", strlen($channelName)) . $channelName;
        $buffer .= pack("I", $uid);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= pack("S", strlen($value)) . $value;
        }

        return strtoupper(hash_hmac('sha1', $buffer, $rawAppCertificate));
    }

    public static function packString($value)
    {
        return pack("S", strlen($value)) . $value;
    }

    public static function packContent($serviceType, $signature, $appID, $ts, $salt, $expiredTs, $extra)
    {
        $buffer = pack("S", $serviceType);
        $buffer .= DynamicKey5::packString($signature);
        $buffer .= DynamicKey5::packString($appID);
        $buffer .= pack("I", $ts);
        $buffer .= pack("I", $salt);
        $buffer .= pack("I", $expiredTs);

        $buffer .= pack("S", count($extra));
        foreach ($extra as $key => $value) {
            $buffer .= pack("S", $key);
            $buffer .= DynamicKey5::packString($value);
        }

        return $buffer;
    }
}
