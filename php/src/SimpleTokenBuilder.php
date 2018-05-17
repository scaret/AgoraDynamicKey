<?php

require "AccessToken.php";

class SimpleTokenBuilder
{
    public $token;
    public function __construct($appID, $appCertificate, $channelName, $uid){
        $this->token = new AccessToken();
        $this->token->appID = $appID;
        $this->token->appCertificate = $appCertificate;
        $this->token->channelName = $channelName;
        $this->token->setUid($uid);
    }
    public static function initWithToken($token, $appCertificate, $channel, $uid){
        $this->token = AccessToken::initWithToken($token, $appCertificate, $channel, $uid);
    }
    public function initPrivilege($role){
        $p = Constants::RolePrivileges[$role];
        foreach($p as $key => $value){
            $this->setPrivilege($key, $value);
        }
    }
    public function setPrivilege($privilege, $expireTimestamp){
        $this->token->addPrivilege($privilege, $expireTimestamp);
    }
    public function removePrivilege($privilege){
        unset($this->token->message->privileges[$privilege]);
    }
    public function buildToken(){
        return $this->token->build();
    }
}


?>