<?php

require "AccessToken.php";

$Role = array(
    "kRoleAttendee" => 0,  // for communication
    "kRolePublisher" => 1, // for live broadcast
    "kRoleSubscriber" => 2,  // for live broadcast
    "kRoleAdmin" => 101
);

$attendeePrivileges = array(
    $Privileges["kJoinChannel"] => 0,
    $Privileges["kPublishAudioStream"] => 0,
    $Privileges["kPublishVideoStream"] => 0,
    $Privileges["kPublishDataStream"] => 0
);


$publisherPrivileges = array(
    $Privileges["kJoinChannel"] => 0,
    $Privileges["kPublishAudioStream"] => 0,
    $Privileges["kPublishVideoStream"] => 0,
    $Privileges["kPublishDataStream"] => 0,
    $Privileges["kPublishAudioCdn"] => 0,
    $Privileges["kPublishVideoCdn"] => 0,
    $Privileges["kInvitePublishAudioStream"] => 0,
    $Privileges["kInvitePublishVideoStream"] => 0,
    $Privileges["kInvitePublishDataStream"] => 0
);

$subscriberPrivileges = array(
    $Privileges["kJoinChannel"] => 0,
    $Privileges["kRequestPublishAudioStream"] => 0,
    $Privileges["kRequestPublishVideoStream"] => 0,
    $Privileges["kRequestPublishDataStream"] => 0
);

$adminPrivileges = array(
    $Privileges["kJoinChannel"] => 0,
    $Privileges["kPublishAudioStream"] => 0,
    $Privileges["kPublishVideoStream"] => 0,
    $Privileges["kPublishDataStream"] => 0,
    $Privileges["kAdministrateChannel"] => 0
);

$RolePrivileges = array(
	$Role["kRoleAttendee"] => $attendeePrivileges,
    $Role["kRolePublisher"] => $publisherPrivileges,
    $Role["kRoleSubscriber"] => $subscriberPrivileges,
    $Role["kRoleAdmin"] => $adminPrivileges
);



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
        $p = $RolePrivileges[$role];
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