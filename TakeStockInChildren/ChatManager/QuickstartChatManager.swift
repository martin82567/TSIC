//
//  QuickstartChatManager.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/3/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import Foundation
import TwilioChatClient


class TwiiloMessages {
    var sid: String
    var author: String
    var dateCreated: String
    var body: String
    var isread: Bool
    var index: Int
    init(sid: String,author: String,dateCreated: String,body: String,isread: Bool,index: Int) {
        self.sid = sid
        self.author = author
        self.dateCreated = dateCreated
        self.body = body
        self.isread = isread
        self.index = index
    }
}

protocol QuickstartChatManagerDelegate: AnyObject {
    func reloadMessages()
    func receivedNewMessage()
}

class QuickstartChatManager: NSObject, TwilioChatClientDelegate {

    // the unique name of the channel you create
    var myIdentity = ""
    var IsRefresh: Bool?
    var forChatType = ""
    var forApiChatCode = ""
    var uniqueChannelName = ""
    var friendlyChannelName = "General Channel"

    // For the quickstart, this will be the view controller
    weak var delegate: QuickstartChatManagerDelegate?
    
    // MARK: Chat variables
    var client: TwilioChatClient?
    var channel: TCHChannel?
    private(set) var messages: [TCHMessage] = [] //TCHMessage
    private var identity: String?
    private(set) var dummymessage: [TwiiloMessages] = [] //TCHMessage
    private(set) var tempMessage: [TwiiloMessages] = []
    
    func chatClient(_ client: TwilioChatClient, synchronizationStatusUpdated status: TCHClientSynchronizationStatus) {
        guard status == .completed else {
            return
        }
        checkChannelCreation { (_, channel) in
            if let channel = channel {
                print("Channel Info ========",channel.sid,channel.lastMessageDate,channel.createdBy,channel.dateCreated)
                ChatInfo.shared.channelSid = channel.sid ?? ""
                ChatInfo.shared.channelCreatedBy = channel.createdBy ?? ""
              //  ChatInfo.shared.channelLastMessageData = channel.lastMessageDate
                print(ChatInfo.shared.channelSid,ChatInfo.shared.channelCreatedBy)
                self.joinChannel(channel)
                self.getAllmessages()
                self.updateSid()
            } else {
                self.createChannel { (success, channel) in
                    if success, let channel = channel {
                        self.joinChannel(channel)
                        self.getAllmessages()
                        self.updateSid()
                    }
                }
            }
        }
    }

    // Called whenever a channel we've joined receives a new message
    func chatClient(_ client: TwilioChatClient, channel: TCHChannel,
                    messageAdded message: TCHMessage) {
        self.channel?.getUnconsumedMessagesCount(completion: { (result, number) in
            print("result Unconsumed Message -======",result.isSuccessful(),number?.intValue)
        })
        dummymessage.append(TwiiloMessages(sid: message.sid ?? "", author: message.author ?? "", dateCreated: message.dateCreated ?? "", body: message.body ?? "", isread: false, index: Int(truncating: message.index ?? 0)))
        messages.append(message)
        DispatchQueue.main.async {
            if let delegate = self.delegate {
                delegate.reloadMessages()
                if self.messages.count > 0 {
                    delegate.receivedNewMessage()
                    self.readUnreadStatus()
                }
            }
        }
    }
        
        func chatClient(_ client: TwilioChatClient, channel: TCHChannel, member: TCHMember, updated: TCHMemberUpdate) {
        let memberIdentity = member.identity
        let lastmessageIndex = member.lastConsumedMessageIndex
        //let update = updated.rawValue
        if memberIdentity != myIdentity {
            IsRefresh = true
            self.tempMessage = self.dummymessage
            self.dummymessage.removeAll()
            for item in self.tempMessage{
                self.dummymessage.append(TwiiloMessages(sid: item.sid, author: item.author, dateCreated: item.dateCreated, body: item.body, isread: true, index: item.index))
            }
            self.delegate?.reloadMessages()
        }
        print("Member Updated main callback",memberIdentity,lastmessageIndex)
    }
    
    func readUnreadStatus() {
        channel?.messages?.setAllMessagesConsumedWithCompletion({ (_, _) in
            print("Read Unread Success")
            
        })
    }
    func getAllmessages() {
        channel?.messages?.getLastWithCount(50, completion: { (result, message) in
            
            self.channel?.getUnconsumedMessagesCount(completion: { (result, number) in
                print("result Unconsumed Message -======",result.isSuccessful(),number?.intValue)
            })
            if (self.channel?.members?.membersList().count ?? 0 > 1) {
                print("Both Two are activated")
            }
            else {
   
                print("Only one Activated")
            }
            for item in message ?? [] {
                self.dummymessage.append(TwiiloMessages(sid: item.sid ?? "", author: item.author ?? "", dateCreated: item.dateCreated ?? "", body: item.body ?? "", isread: true, index: Int(truncating: item.index ?? 0)))
                self.messages.append(item)
            }
            print("Messages",self.messages)
            if (self.messages.count != 0) {
                self.delegate?.reloadMessages()
                self.delegate?.receivedNewMessage()
                self.readUnreadStatus()
            }
        })
    }
    
    func updateSid() {
        
        var param = [String:String]()
        param["chat_type"] = self.forChatType
        param["chat_code"] = self.forApiChatCode
        param["channel_sid"] = channel?.sid ?? ""
        print("Param for channel Update",param)
        MentorApiManager().channelSidUpdate(parameter: param) { (json) in
            print("Channel SID Update--",json)
            }
        }
    
    
    func chatClientTokenWillExpire(_ client: TwilioChatClient) {
        print("Chat Client Token will expire.")
        // the chat token is about to expire, so refresh it
        refreshAccessToken()
    }
    
    private func refreshAccessToken() {
        guard let identity = identity else {
            return
        }
        let urlString = //"\(TOKEN_URL)?identity=\(identity)"

        TokenUtils.retrieveToken(url: "") { (token, _, error) in
            guard let token = token else {
               print("Error retrieving token: \(error.debugDescription)")
               return
           }
            self.client?.updateToken(token, completion: { (result) in
                if (result.isSuccessful()) {
                    print("Access token refreshed")
                } else {
                    print("Unable to refresh access token")
                }
            })
        }
    }

    func sendMessage(_ messageText: String,
                     completion: @escaping (TCHResult, TCHMessage?) -> Void) {
        if let messages = self.channel?.messages {
            let messageOptions = TCHMessageOptions().withBody(messageText)
            messages.sendMessage(with: messageOptions, completion: { (result, message) in
                completion(result, message)
            })
        }
    }

    func login(_ identity: String,param: [String:String],chatBy: String, completion: @escaping (Bool) -> Void) {
        // Fetch Access Token from the server and initialize Chat Client - this assumes you are
        // calling a Twilio function, as described in the Quickstart docs
       // let urlString = "https://mentorappdev.tsic.org:3700/token" //"https://mentorappdev.tsic.org/api/chat/get_access_token"//"\(TOKEN_URL)?identity=\(identity)"
        
        var token: String?
        self.identity = identity
        MentorApiManager().retrieveTokenChat(parameter: param, chatBy: chatBy) { (json) in
           // self.stopActivityIndicator()
            print("chat called-------",json)
          //  let status = json["status"] as! Bool
          //  if status == true {
              //  let dicData = json["data"] as! NSDictionary
            token = json["token"] as? String ?? ""
            UserCredential.shared.Identity = json["identity"] as? String ?? ""
            //token ?? ""
                TwilioChatClient.chatClient(withToken: token ?? "", properties: nil,
                                            delegate: self) { (result, chatClient) in
                    self.client = chatClient
                    print("client==== and resuly",result,self.client?.channelsList())
                    completion(result.isSuccessful())
                }
        }
    }

    func shutdown() {
        if let client = client {
            client.delegate = nil
            client.shutdown()
            self.client = nil
        }
    }
    
    func chatClient(_ client: TwilioChatClient, typingEndedOn channel: TCHChannel, member: TCHMember) {
        print("Typing Ended")
    }
    
    func chatClient(_ client: TwilioChatClient, typingStartedOn channel: TCHChannel, member: TCHMember) {
        print("Typing Started")
    }
    
    
    private func createChannel(_ completion: @escaping (Bool, TCHChannel?) -> Void) {
        guard let client = client, let channelsList = client.channelsList() else {
            return
        }
        // Create the channel if it hasn't been created yet
        let options: [String: Any] = [
            TCHChannelOptionUniqueName: uniqueChannelName,
            TCHChannelOptionFriendlyName: friendlyChannelName,
            TCHChannelOptionType: TCHChannelType.public.rawValue
            ]
        channelsList.createChannel(options: options, completion: { channelResult, channel in
            if channelResult.isSuccessful() {
                print("Channel created.")
            } else {
                print("Channel NOT created.")
            }
            completion(channelResult.isSuccessful(), channel)
        })
    }

    private func checkChannelCreation(_ completion: @escaping(TCHResult?, TCHChannel?) -> Void) {
        guard let client = client, let channelsList = client.channelsList() else {
            return
        }
        channelsList.channel(withSidOrUniqueName: uniqueChannelName, completion: { (result, channel) in
            completion(result, channel)
        })
    }

    private func joinChannel(_ channel: TCHChannel) {
        self.channel = channel
        if channel.status == .joined {
            print("Current user already exists in channel")
        } else {
            channel.join(completion: { result in
                print("Result of channel join: \(result.resultText ?? "No Result")")
            })
        }
    }
    
}
