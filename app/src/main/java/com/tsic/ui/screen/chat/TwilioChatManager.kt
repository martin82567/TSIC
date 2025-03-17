package com.tsic.ui.screen.chat

import android.content.Context
import android.os.Handler
import android.util.Log
import com.twilio.chat.*

class TwilioChatManager(val listener: ChatListener) {
    val TAG = "TAG"
    private var chatClient: ChatClient? = null
    private var channel: Channel? = null
    private var chatTempMsgList = mutableListOf<ChatMessage>()
    private var userOffline = false
    private var isResume = false
    private var lastMessage = ""
    private val handler = Handler()
    private val notificationRunnable = Runnable {
        if (userOffline)
            listener.sendNotification(lastMessage)
    }

    private val mChatClientListener: ChatClientListener = object : ChatClientListener {
        override fun onChannelJoined(channel: Channel) {}
        override fun onChannelInvited(channel: Channel) {}
        override fun onChannelAdded(channel: Channel) {}
        override fun onChannelUpdated(channel: Channel, updateReason: Channel.UpdateReason) {}
        override fun onChannelDeleted(channel: Channel) {}
        override fun onChannelSynchronizationChange(channel: Channel) {}
        override fun onError(errorInfo: ErrorInfo) {}
        override fun onUserUpdated(user: User, updateReason: User.UpdateReason) {
            Log.d(TAG, "onUserUpdated: ")
        }

        override fun onUserSubscribed(user: User) {
            Log.d("TAG", "onUserSubscribed: ${user.identity}")
        }

        override fun onUserUnsubscribed(user: User) {
            Log.d("TAG", "onUserUnsubscribed: ${user.identity}")
        }

        override fun onClientSynchronization(synchronizationStatus: ChatClient.SynchronizationStatus) {
            if (synchronizationStatus == ChatClient.SynchronizationStatus.COMPLETED) {
                listener.onClientSynchronization()
            }
        }

        override fun onNewMessageNotification(s: String, s1: String, l: Long) {}
        override fun onAddedToChannelNotification(s: String) {}
        override fun onInvitedToChannelNotification(s: String) {}
        override fun onRemovedFromChannelNotification(s: String) {}
        override fun onNotificationSubscribed() {}
        override fun onNotificationFailed(errorInfo: ErrorInfo) {}
        override fun onConnectionStateChange(connectionState: ChatClient.ConnectionState) {}
        override fun onTokenExpired() {
            Log.d("TAG", "onTokenExpired: token expire")
            listener.onTokenExpired()
        }

        override fun onTokenAboutToExpire() {}
    }


    private val mDefaultChannelListener: ChannelListener = object : ChannelListener {
        override fun onMessageAdded(it: Message?) {
            Log.d("TAG", "onMessageAdded: ${it?.messageBody}")
            if (isResume)
                setAllMessagesConsumed()
            listener.onMessageAdded(it)
        }

        override fun onMessageUpdated(p0: Message?, p1: Message.UpdateReason?) {
            Log.d(TAG, "onMessageUpdated: ")

        }

        override fun onMessageDeleted(p0: Message?) {}

        override fun onMemberAdded(p0: Member?) {}

        override fun onMemberUpdated(p0: Member?, p1: Member.UpdateReason?) {
            Log.d(
                TAG,
                "onMemberUpdated: ${p0?.identity}--> ${p0?.lastConsumedMessageIndex} ${p1?.name}"
            )
            if (p0?.identity != chatClient?.myIdentity && p1?.name == Member.UpdateReason.LAST_CONSUMED_MESSAGE_INDEX.name) {
                userOffline = false
                handler.removeCallbacks(notificationRunnable)
                listener?.allMessageSeen()
            }
            if (p0?.identity == chatClient?.myIdentity && p1?.name == Member.UpdateReason.LAST_CONSUMPTION_TIMESTAMP.name) {
                userOffline = true
            }

        }

        override fun onMemberDeleted(p0: Member?) {}

        override fun onTypingStarted(p0: Channel?, p1: Member?) {
            Log.d(TAG, "onTypingStarted: ${p1?.identity}")
        }

        override fun onTypingEnded(p0: Channel?, p1: Member?) {
            Log.d(TAG, "onTypingEnded: ${p1?.identity}")
        }

        override fun onSynchronizationChanged(p0: Channel?) {}
    }

    fun loadChannels(channelSid: String) {
        chatClient?.channels?.getChannel(
            channelSid,
            object : CallbackListener<Channel>() {
                override fun onSuccess(channel: Channel?) {
                    if (channel != null) {
                        if (channel.status == Channel.ChannelStatus.JOINED
                        ) {
                            this@TwilioChatManager.channel = channel
                            this@TwilioChatManager.channel?.addListener(mDefaultChannelListener)
                            Log.d("TAG", "onSuccess: $channelSid ${channel?.sid}")
                            loadMessage()

                        } else {
                            Log.d("TAG", "Joining Channel:")
                            joinChannel(channel)
                        }
                    } else {
                        Log.d("TAG", "Creating Channel: ")
                        listener.createChannel()
                    }
                    listener.hideLoader()
                }

                override fun onError(errorInfo: ErrorInfo?) {
                    super.onError(errorInfo)
                    Log.d("TAG", "Error retrieving channel: " + errorInfo!!.message)
                    listener.onError(errorInfo?.message ?: "Some Error occurred")
                    listener.hideLoader()

                }
            })
        chatClient?.isReachabilityEnabled
    }

    fun createChannel(chatCode: String) {
        chatClient!!.channels.createChannel(chatCode,
            Channel.ChannelType.PUBLIC, object : CallbackListener<Channel>() {
                override fun onSuccess(channel: Channel) {
                    Log.d("TAG", "Joining Channel: ")
                    listener.getChannelSid(channel.sid)
                    joinChannel(channel)
                }

                override fun onError(errorInfo: ErrorInfo) {
                    Log.e("TAG", "Error creating channel: " + errorInfo.message)
                    listener.onError(errorInfo?.message ?: "Some Error occurred")
                    listener.hideLoader()

                }
            })
    }

    private fun joinChannel(channel: Channel) {
        Log.d("TAG", "Joining Channel: ${channel.uniqueName}")
        if (channel.status == Channel.ChannelStatus.JOINED) {
            this@TwilioChatManager.channel = channel
            Log.d("TAG", "Already joined default channel")
            this@TwilioChatManager.channel?.addListener(mDefaultChannelListener)
            return
        }

        channel.join(object : StatusListener() {
            override fun onSuccess() {
                this@TwilioChatManager.channel = channel
                Log.d("TAG", "Joined default channel")
                this@TwilioChatManager.channel?.addListener(mDefaultChannelListener)
                loadMessage()
            }

            override fun onError(errorInfo: ErrorInfo?) {
                super.onError(errorInfo)
                Log.e("TAG", "Error joining channel: ${errorInfo!!.message}")
                listener.onError(errorInfo?.message ?: "Some Error occurred")
                listener.hideLoader()

            }
        })
    }

    fun loadMessage() {
        this@TwilioChatManager.channel?.getMessagesCount(object : CallbackListener<Long>() {
            override fun onSuccess(p1: Long?) {
                this@TwilioChatManager.channel?.messages?.getLastMessages(p1?.toInt() ?: 0,
                    object : CallbackListener<List<Message>>() {
                        override fun onSuccess(p0: List<Message>?) {
                            Log.d("TAG", "onSuccess: total ${p0?.size}")
                            var totalUnseenMsg = 0
                            var totalMsg = p0?.size ?: 0

                            if (channel?.members?.membersList?.size ?: 0 > 1) {
                                if (chatClient?.myIdentity != channel?.members?.membersList?.get(0)?.identity)
                                    channel?.members?.membersList?.get(0)?.lastConsumedMessageIndex?.let {
                                        totalUnseenMsg = p1?.minus(it)?.toInt() ?: 0
                                        totalUnseenMsg--
                                    }
                                else
                                    channel?.members?.membersList?.get(1)?.lastConsumedMessageIndex?.let {
                                        totalUnseenMsg = p1?.minus(it)?.toInt() ?: 0
                                        totalUnseenMsg--
                                    }
                            } else
                                totalUnseenMsg = totalMsg
                            setAllMessagesConsumed()
                            Log.d(TAG, "onSuccess: total unread $totalUnseenMsg")
                            p0?.map {
                                chatTempMsgList.clear()
                                chatTempMsgList.add(
                                    ChatMessage(
                                        it.sid,
                                        it.messageBody,
                                        it.author,
                                        it.dateCreated,
                                        totalMsg-- > totalUnseenMsg && channel?.members?.membersList?.size ?: 0 > 1
                                    )
                                )
                                listener.onLoadMessage(chatTempMsgList)
                            }

                        }

                        override fun onError(errorInfo: ErrorInfo?) {
                            super.onError(errorInfo)
                            Log.d("TAG", "onError: ${errorInfo?.message}")
                            listener.onError(errorInfo?.message ?: "Some Error occurred")
                            listener.hideLoader()
                        }
                    })
            }

        })
    }

    fun build(context: Context, token: String?) {
        val props = ChatClient.Properties.Builder()
            .setRegion("us1")
            .createProperties()
        ChatClient.create(
            context,
            token!!,
            props,
            object : CallbackListener<ChatClient?>() {
                override fun onSuccess(chatClient: ChatClient?) {
                    Log.d("TAG", "onSuccess: ${chatClient?.myIdentity}")
                    this@TwilioChatManager.chatClient = chatClient
                    this@TwilioChatManager.chatClient?.addListener(mChatClientListener)
                }

                override fun onError(errorInfo: ErrorInfo) {
                    Log.d("TAG", "onError: ${errorInfo.message}")
                    listener.onError(errorInfo.message ?: "Some Error occurred")
                    listener.hideLoader()

                }
            }
        )

    }

    fun sendChatMessage(messageBody: String?) {
        if (channel != null) {
            val options = Message.options().withBody(messageBody)
            //Log.d("TAG", "Message created")
            channel!!.messages.sendMessage(options, object : CallbackListener<Message?>() {
                override fun onSuccess(message: Message?) {
                    Log.d("TAG", "onSuccess: ${message?.messageBody}")
                    sendNotification(message?.messageBody)
                    listener.onMessageSend()
                }

                override fun onError(errorInfo: ErrorInfo?) {
                    super.onError(errorInfo)
                    listener.onError(errorInfo?.message ?: "Some Error occurred")
                }
            })
        }
    }


    fun setAllMessagesConsumed() {
        this@TwilioChatManager.channel?.messages?.setAllMessagesConsumedWithResult(object :
            CallbackListener<Long>() {
            override fun onSuccess(p0: Long?) {
                //Log.d("TAG", "onSuccess: read $p0")
            }
        })
    }

    fun sendNotification(messageBody: String?) {
        lastMessage = messageBody.toString()
        handler.postDelayed(notificationRunnable, 1200)
    }

    fun onPause() {
        isResume = false
    }

    fun onResume() {
        isResume = true
        setAllMessagesConsumed()
    }

    fun destroy() {
        handler.removeCallbacks(notificationRunnable)
        chatClient?.shutdown()
    }

}