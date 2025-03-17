package com.tsic.ui.screen.videocallscreen


interface RoomCallback {
    fun onConnected(isRemoteParticipantPresent: Boolean)

    fun onReconnected()
    fun onReconnecting()
    fun onConnectFailure()
    fun onDisconnected()
    fun onParticipantConnected()
    fun onParticipantDisconnected()
    fun onVideoTrackSubscribed(userId:String)
}