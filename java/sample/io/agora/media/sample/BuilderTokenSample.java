package io.agora.media.sample;

import io.agora.media.AccessToken;
import io.agora.media.SimpleTokenBuilder;

import java.util.TreeMap;

public class BuilderTokenSample {
    static String appId = "970CA35de60c44645bbae8a215061b33";
    static String appCertificate = "5CFd2fd1755d40ecb72977518be15d3b";
    static String channelName = "7d72365eb983485397e3e3f9d460bdda";
    static String uid = "2882341273";
    static int expireTimestamp = 0;

    public static void main(String[] args) throws Exception {
        SimpleTokenBuilder token = new SimpleTokenBuilder(appId, appCertificate, channelName, uid);
        token.initPrivileges(SimpleTokenBuilder.Role.Role_Attendee);
        token.setPrivilege(AccessToken.Privileges.kJoinChannel, expireTimestamp);
        token.setPrivilege(AccessToken.Privileges.kPublishAudioStream, expireTimestamp);
        token.setPrivilege(AccessToken.Privileges.kPublishVideoStream, expireTimestamp);
        token.setPrivilege(AccessToken.Privileges.kPublishDataStream, expireTimestamp);
        TreeMap<Short, Integer> test = token.mTokenCreator.message.messages;
        System.out.println("");

        String result = token.buildToken();
        System.out.println(result);
    }
}
