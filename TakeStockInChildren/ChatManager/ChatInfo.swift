//
//  ChatInfo.swift
//  TakeStockInChildren
//
//  Created by AquariousMnabook on 23/07/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import Foundation
class ChatInfo {
    static let shared = ChatInfo()
    var channelSid: String = ""
    var channelLastMessageData: String = ""
    var channelCreatedBy: String = ""
    var channelCreate: String = ""
}
