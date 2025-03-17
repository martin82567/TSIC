package com.tsic.ui.screen.videocallscreen

import android.content.Context
import android.util.Log
import us.zoom.sdk.UVCCameraStatus
import us.zoom.sdk.ZoomVideoSDK
import us.zoom.sdk.ZoomVideoSDKAnnotationHelper
import us.zoom.sdk.ZoomVideoSDKAudioHelper
import us.zoom.sdk.ZoomVideoSDKAudioRawData
import us.zoom.sdk.ZoomVideoSDKCRCCallStatus
import us.zoom.sdk.ZoomVideoSDKCameraControlRequestHandler
import us.zoom.sdk.ZoomVideoSDKCameraControlRequestType
import us.zoom.sdk.ZoomVideoSDKChatHelper
import us.zoom.sdk.ZoomVideoSDKChatMessage
import us.zoom.sdk.ZoomVideoSDKChatMessageDeleteType
import us.zoom.sdk.ZoomVideoSDKChatPrivilegeType
import us.zoom.sdk.ZoomVideoSDKDelegate
import us.zoom.sdk.ZoomVideoSDKFileTransferStatus
import us.zoom.sdk.ZoomVideoSDKLiveStreamHelper
import us.zoom.sdk.ZoomVideoSDKLiveStreamStatus
import us.zoom.sdk.ZoomVideoSDKLiveTranscriptionHelper
import us.zoom.sdk.ZoomVideoSDKMultiCameraStreamStatus
import us.zoom.sdk.ZoomVideoSDKNetworkStatus
import us.zoom.sdk.ZoomVideoSDKPasswordHandler
import us.zoom.sdk.ZoomVideoSDKPhoneFailedReason
import us.zoom.sdk.ZoomVideoSDKPhoneStatus
import us.zoom.sdk.ZoomVideoSDKProxySettingHandler
import us.zoom.sdk.ZoomVideoSDKRawDataPipe
import us.zoom.sdk.ZoomVideoSDKReceiveFile
import us.zoom.sdk.ZoomVideoSDKRecordingConsentHandler
import us.zoom.sdk.ZoomVideoSDKRecordingStatus
import us.zoom.sdk.ZoomVideoSDKSSLCertificateInfo
import us.zoom.sdk.ZoomVideoSDKSendFile
import us.zoom.sdk.ZoomVideoSDKSessionContext
import us.zoom.sdk.ZoomVideoSDKSessionLeaveReason
import us.zoom.sdk.ZoomVideoSDKShareHelper
import us.zoom.sdk.ZoomVideoSDKShareStatus
import us.zoom.sdk.ZoomVideoSDKTestMicStatus
import us.zoom.sdk.ZoomVideoSDKUser
import us.zoom.sdk.ZoomVideoSDKUserHelper
import us.zoom.sdk.ZoomVideoSDKVideoCanvas
import us.zoom.sdk.ZoomVideoSDKVideoHelper
import us.zoom.sdk.ZoomVideoSDKVideoSubscribeFailReason
import us.zoom.sdk.ZoomVideoSDKVideoView


class InitVideoCallRoom(val context: Context, val roomCallback: RoomCallback) {
    private val zoomSdk = ZoomVideoSDK.getInstance()

    companion object {
        private const val TAG = "ZOOM"
    }

    val roomListener = object : ZoomVideoSDKDelegate {

        override fun onSessionLeave() {
            Log.d(TAG, "onSessionLeave: ")
            roomCallback.onDisconnected()
        }

        override fun onSessionJoin() {
            Log.d(TAG, "onSessionJoin: ")
            roomCallback.onConnected(false)
        }

        override fun onSessionLeave(reason: ZoomVideoSDKSessionLeaveReason?) {
            Log.d(TAG, "onSessionLeave: $reason")
            roomCallback.onDisconnected()
        }

        override fun onError(errorCode: Int) {
            roomCallback.onConnectFailure()
            Log.e(TAG, "On Some Failure Occur: $errorCode")
        }

        override fun onUserJoin(
            userHelper: ZoomVideoSDKUserHelper?,
            userList: MutableList<ZoomVideoSDKUser>?
        ) {
            Log.d(TAG, "onUserJoin: ${userList?.map { it.userName }}")
            if ((userList?.size ?: 0) > 1) {
                roomCallback.onConnected(true)
            }
            roomCallback.onParticipantConnected()
            userList?.forEach { it -> roomCallback.onVideoTrackSubscribed(it.userID) }

            //  roomCallback.onVideoTrackSubscribed()

            /*    userList?.forEach { user ->
                    val videoView = ZoomVideoSDKVideoView(context)

                    val canvas = user.videoCanvas
                    canvas.subscribe(
                        videoView,
                        ZoomVideoSDKVideoAspect.ZoomVideoSDKVideoAspect_Original
                    )
                    roomCallback.onParticipantConnected()

                }*/

        }

        override fun onUserLeave(
            userHelper: ZoomVideoSDKUserHelper?,
            userList: MutableList<ZoomVideoSDKUser>?
        ) {

            Log.d(TAG, "onUserLeave: ${userList?.map { it.userName }}")
            roomCallback.onParticipantDisconnected()
        }

        override fun onUserVideoStatusChanged(
            videoHelper: ZoomVideoSDKVideoHelper?,
            userList: MutableList<ZoomVideoSDKUser>?
        ) {
            Log.d(TAG, "onUserVideoStatusChanged: ${userList?.map { it.userName }}")
            /*userList?.forEach {
                // Check if the current user's video is on
                val videoStatus = it.
                videoStatus.isOn
            }*/
        }

        override fun onUserAudioStatusChanged(
            audioHelper: ZoomVideoSDKAudioHelper?,
            userList: MutableList<ZoomVideoSDKUser>?
        ) {
            Log.d(TAG, "onUserAudioStatusChanged: ${userList?.map { it.userName }}")
            userList?.forEach {
                // Check the current user to see if they are muted
                val audioStatus = it.audioStatus
                audioStatus.isMuted
            }
        }

        override fun onUserShareStatusChanged(
            shareHelper: ZoomVideoSDKShareHelper?,
            userInfo: ZoomVideoSDKUser?,
            status: ZoomVideoSDKShareStatus?
        ) {
            Log.d(TAG, "onUserShareStatusChanged: ${userInfo?.userName} -  ${status?.name}")
        }

        override fun onLiveStreamStatusChanged(
            liveStreamHelper: ZoomVideoSDKLiveStreamHelper?,
            status: ZoomVideoSDKLiveStreamStatus?
        ) {
            Log.d(TAG, "onLiveStreamStatusChanged: ${status?.name}")
        }

        override fun onChatNewMessageNotify(
            chatHelper: ZoomVideoSDKChatHelper?,
            messageItem: ZoomVideoSDKChatMessage?
        ) {
            Log.d(TAG, "onChatNewMessageNotify: ${messageItem?.content}")
        }

        override fun onChatDeleteMessageNotify(
            chatHelper: ZoomVideoSDKChatHelper?,
            msgID: String?,
            deleteBy: ZoomVideoSDKChatMessageDeleteType?
        ) {
            Log.d(TAG, "onChatDeleteMessageNotify: $msgID")
        }

        override fun onChatPrivilegeChanged(
            chatHelper: ZoomVideoSDKChatHelper?,
            currentPrivilege: ZoomVideoSDKChatPrivilegeType?
        ) {
            Log.d(TAG, "onChatPrivilegeChanged: ${currentPrivilege?.name}")
        }

        override fun onUserHostChanged(
            userHelper: ZoomVideoSDKUserHelper?,
            userInfo: ZoomVideoSDKUser?
        ) {
            Log.d(TAG, "onUserHostChanged: ${userInfo?.userName}")
        }

        override fun onUserManagerChanged(user: ZoomVideoSDKUser?) {
            Log.d(TAG, "onUserManagerChanged: ${user?.userName}")
        }

        override fun onUserNameChanged(user: ZoomVideoSDKUser?) {
            Log.d(TAG, "onUserNameChanged: ${user?.userName}")
        }

        override fun onUserActiveAudioChanged(
            audioHelper: ZoomVideoSDKAudioHelper?,
            list: MutableList<ZoomVideoSDKUser>?
        ) {

            Log.d(TAG, "onUserActiveAudioChanged: ${list?.map { it.userName }}")
            for (user in list!!) {
                // Check if the current user is talking
                val audioStatus = user.audioStatus
                audioStatus.isTalking
            }
        }

        override fun onSessionNeedPassword(handler: ZoomVideoSDKPasswordHandler?) {
            Log.d(TAG, "onSessionNeedPassword: ")
        }

        override fun onSessionPasswordWrong(handler: ZoomVideoSDKPasswordHandler?) {
            Log.d(TAG, "onSessionPasswordWrong: ")
        }

        override fun onMixedAudioRawDataReceived(rawData: ZoomVideoSDKAudioRawData?) {
            Log.d(TAG, "onMixedAudioRawDataReceived: ")
        }

        override fun onOneWayAudioRawDataReceived(
            rawData: ZoomVideoSDKAudioRawData?,
            user: ZoomVideoSDKUser?
        ) {
            Log.d(TAG, "onOneWayAudioRawDataReceived: ")
        }

        override fun onShareAudioRawDataReceived(rawData: ZoomVideoSDKAudioRawData?) {
            Log.d(TAG, "onShareAudioRawDataReceived: ")
        }

        override fun onCommandReceived(sender: ZoomVideoSDKUser?, strCmd: String?) {
            Log.d(TAG, "onCommandReceived: $strCmd")

        }

        override fun onCommandChannelConnectResult(isSuccess: Boolean) {
            Log.d(TAG, "onCommandChannelConnectResult: $isSuccess")
        }

        override fun onCloudRecordingStatus(
            status: ZoomVideoSDKRecordingStatus?,
            handler: ZoomVideoSDKRecordingConsentHandler?
        ) {
            Log.d(TAG, "onCloudRecordingStatus: ${status?.name}")
        }

        override fun onHostAskUnmute() {
            Log.d(TAG, "onHostAskUnmute: ")
        }

        override fun onInviteByPhoneStatus(
            status: ZoomVideoSDKPhoneStatus?,
            reason: ZoomVideoSDKPhoneFailedReason?
        ) {
            Log.d(TAG, "onInviteByPhoneStatus: ")
        }

        override fun onMultiCameraStreamStatusChanged(
            status: ZoomVideoSDKMultiCameraStreamStatus?,
            user: ZoomVideoSDKUser?,
            videoPipe: ZoomVideoSDKRawDataPipe?
        ) {
            Log.d(TAG, "onMultiCameraStreamStatusChanged: ")
        }

        override fun onMultiCameraStreamStatusChanged(
            status: ZoomVideoSDKMultiCameraStreamStatus?,
            user: ZoomVideoSDKUser?,
            canvas: ZoomVideoSDKVideoCanvas?
        ) {
            Log.d(TAG, "onMultiCameraStreamStatusChanged: ")
        }

        override fun onLiveTranscriptionStatus(status: ZoomVideoSDKLiveTranscriptionHelper.ZoomVideoSDKLiveTranscriptionStatus?) {
            Log.d(TAG, "onLiveTranscriptionStatus: ")
        }

        override fun onOriginalLanguageMsgReceived(messageInfo: ZoomVideoSDKLiveTranscriptionHelper.ILiveTranscriptionMessageInfo?) {
            Log.d(TAG, "onOriginalLanguageMsgReceived: ")
        }

        override fun onLiveTranscriptionMsgInfoReceived(messageInfo: ZoomVideoSDKLiveTranscriptionHelper.ILiveTranscriptionMessageInfo?) {
            Log.d(TAG, "onLiveTranscriptionMsgInfoReceived: ")
        }

        override fun onLiveTranscriptionMsgError(
            spokenLanguage: ZoomVideoSDKLiveTranscriptionHelper.ILiveTranscriptionLanguage?,
            transcriptLanguage: ZoomVideoSDKLiveTranscriptionHelper.ILiveTranscriptionLanguage?
        ) {
            Log.d(TAG, "onLiveTranscriptionMsgError: ")
        }

        override fun onProxySettingNotification(handler: ZoomVideoSDKProxySettingHandler?) {
            Log.d(TAG, "onProxySettingNotification: ")
        }

        override fun onSSLCertVerifiedFailNotification(info: ZoomVideoSDKSSLCertificateInfo?) {
            Log.d(TAG, "onSSLCertVerifiedFailNotification: ")
        }

        override fun onCameraControlRequestResult(user: ZoomVideoSDKUser?, isApproved: Boolean) {
            Log.d(TAG, "onCameraControlRequestResult: ")
        }

        override fun onCameraControlRequestReceived(
            user: ZoomVideoSDKUser?,
            requestType: ZoomVideoSDKCameraControlRequestType?,
            requestHandler: ZoomVideoSDKCameraControlRequestHandler?
        ) {}

        override fun onUserVideoNetworkStatusChanged(
            status: ZoomVideoSDKNetworkStatus?,
            user: ZoomVideoSDKUser?
        ) {
            Log.d(TAG, "onUserVideoNetworkStatusChanged: ")
        }

        override fun onUserRecordingConsent(user: ZoomVideoSDKUser?) {
            Log.d(TAG, "onUserRecordingConsent: ")
        }

        override fun onCallCRCDeviceStatusChanged(status: ZoomVideoSDKCRCCallStatus?) {
            Log.d(TAG, "onCallCRCDeviceStatusChanged: ")
        }

        override fun onVideoCanvasSubscribeFail(
            fail_reason: ZoomVideoSDKVideoSubscribeFailReason?,
            pUser: ZoomVideoSDKUser?,
            view: ZoomVideoSDKVideoView?
        ) {
            Log.d(TAG, "onVideoCanvasSubscribeFail: ")
        }

        override fun onShareCanvasSubscribeFail(
            fail_reason: ZoomVideoSDKVideoSubscribeFailReason?,
            pUser: ZoomVideoSDKUser?,
            view: ZoomVideoSDKVideoView?
        ) {
            Log.d(TAG, "onShareCanvasSubscribeFail: ")
        }

        override fun onAnnotationHelperCleanUp(helper: ZoomVideoSDKAnnotationHelper?) {
            Log.d(TAG, "onAnnotationHelperCleanUp: ")
        }

        override fun onAnnotationPrivilegeChange(enable: Boolean, shareOwner: ZoomVideoSDKUser?) {
            Log.d(TAG, "onAnnotationPrivilegeChange: ")
        }

        override fun onTestMicStatusChanged(status: ZoomVideoSDKTestMicStatus?) {}

        override fun onMicSpeakerVolumeChanged(micVolume: Int, speakerVolume: Int) {}

        override fun onCalloutJoinSuccess(user: ZoomVideoSDKUser?, phoneNumber: String?) {}

        override fun onSendFileStatus(
            file: ZoomVideoSDKSendFile?,
            status: ZoomVideoSDKFileTransferStatus?
        ) {}

        override fun onReceiveFileStatus(
            file: ZoomVideoSDKReceiveFile?,
            status: ZoomVideoSDKFileTransferStatus?
        ) {}

        override fun onUVCCameraStatusChange(cameraId: String?, status: UVCCameraStatus?) {}

        override fun onVideoAlphaChannelStatusChanged(isAlphaModeOn: Boolean) {}

        override fun onSpotlightVideoChanged(
            videoHelper: ZoomVideoSDKVideoHelper?,
            userList: MutableList<ZoomVideoSDKUser>?
        ) {}

    }

    init {

        zoomSdk.addListener(roomListener)
    }

    fun connect(connectOptionsBuilder: ZoomVideoSDKSessionContext) {
        /*val tempOptions = ZoomVideoSDKSessionContext().apply {
            userName = "mentee-12"
            sessionName = "mentor-15-mentee-11-1610001228"
            token =
                "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhcHBfa2V5IjoiU3hlaU84ZkFSOEdtNzkyLWd5OFJfQSIsInJvbGVfdHlwZSI6MCwidHBjIjoibWVudG9yLTE1LW1lbnRlZS0xMS0xNjEwMDAxMjI4IiwidmVyc2lvbiI6MSwiaWF0IjoxNzEyMjE3ODIzLCJleHAiOjE3MTIyOTgwMTJ9.vQw_5UsGHfrjU097HRxY3ADfMcKDCm1aVQKNnnECykY"
        }*/
        //zoomSdk.addListener(roomListener)
        Log.d(TAG, "connect: Joining the call ${connectOptionsBuilder.sessionName}")
        Log.d(TAG, "connect: Joining the call ${connectOptionsBuilder.userName}")
        Log.d(TAG, "connect: Joining the call ${connectOptionsBuilder.token}")
        val session = zoomSdk.joinSession(connectOptionsBuilder)
        Log.d(TAG, "connect: Joined session not null - ${session != null}")
        Log.d(TAG, "connect: Joined session name - ${session?.sessionName}")
    }


}