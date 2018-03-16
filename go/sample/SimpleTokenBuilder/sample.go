package main

import (
	"../../src/SimpleTokenBuilder"
	"../../src/AccessToken"
    "fmt"
)

func main() {

	appID := "970CA35de60c44645bbae8a215061b33"
	appCertificate := "5CFd2fd1755d40ecb72977518be15d3b"
	channelName := "7d72365eb983485397e3e3f9d460bdda"
	uid := uint32(2882341273)
	expiredTs := uint32(24 * 3600)

	builder := SimpleTokenBuilder.CreateSimpleTokenBuilder(appID, appCertificate, channelName, uid)
	builder.InitPrivileges(SimpleTokenBuilder.Role_Attendee)
	builder.SetPrivilege(AccessToken.KJoinChannel, expiredTs)
	builder.SetPrivilege(AccessToken.KPublishAudioStream, expiredTs)
	builder.SetPrivilege(AccessToken.KPublishVideoStream, expiredTs)
	builder.SetPrivilege(AccessToken.KPublishDataStream, expiredTs)

	if result, err := builder.BuildToken(); err != nil {
		fmt.Println(err)
	} else {
		fmt.Println(result)
	}
}

