<?php


$Privileges = array(
    "kJoinChannel" => 1,
    "kPublishAudioStream" => 2,
    "kPublishVideoStream" => 3,
    "kPublishDataStream" => 4,
    "kPublishAudioCdn" => 5,
    "kPublishVideoCdn" => 6,
    "kRequestPublishAudioStream" => 7,
    "kRequestPublishVideoStream" => 8,
    "kRequestPublishDataStream" => 9,
    "kInvitePublishAudioStream" => 10,
    "kInvitePublishVideoStream" => 11,
    "kInvitePublishDataStream" => 12,
    "kAdministrateChannel" => 101
);

class Message
{
    public $salt;
    public $ts;
    public $privileges;
    public function __construct()
    {
        $this->salt = rand(0, 100000);

        date_default_timezone_set("UTC");
        $date = new DateTime();
        $this->ts = $date->getTimestamp() + 24 * 3600;

        $this->privileges = array();
    }

    public function packContent()
    {
        $buffer = unpack("C*", pack("V", $this->salt));
        $buffer = array_merge($buffer, unpack("C*", pack("V", $this->ts)));
        $buffer = array_merge($buffer, unpack("C*", pack("v", sizeof($this->privileges))));
        foreach ($this->privileges as $key => $value) {
            $buffer = array_merge($buffer, unpack("C*", pack("v", $key)));
            $buffer = array_merge($buffer, unpack("C*", pack("V", $value)));
        }
        return $buffer;
    }
}

class AccessToken
{
    public $appID, $appCertificate, $channelName, $uid;
    public $message;

    public function __construct($appID, $appCertificate, $channelName, $uid)
    {
        $this->appID = $appID;
        $this->appCertificate = $appCertificate;
        $this->channelName = $channelName;

        if($uid === 0 || $uid === "0"){
            $this->uid = "";
        } else {
            $this->uid = $uid;
        }

        $this->message = new Message();
    }

    public function addPrivilege($key, $expireTimestamp)
    {
        $this->message->privileges[$key] = $expireTimestamp;
        return $this;
    }

    public function build()
    {
        $msg = $this->message->packContent();
        $val = array_merge(unpack("C*", $this->appID), unpack("C*", $this->channelName), unpack("C*", $this->uid), $msg);
        $sig = hash_hmac('sha256', implode(array_map("chr", $val)), $this->appCertificate, true);

        $crc_channel_name = crc32($this->channelName) & 0xffffffff;
        $crc_uid = crc32($this->uid) & 0xffffffff;

        $content = array_merge(unpack("C*", packString($sig)), unpack("C*", pack("V", $crc_channel_name)), unpack("C*", pack("V", $crc_uid)), unpack("C*", pack("v", count($msg))), $msg);
        $version = "006";
        $ret = $version . $this->appID . base64_encode(implode(array_map("chr", $content)));
        return $ret;
    }
}

function packString($value)
{
    return pack("v", strlen($value)) . $value;
}
