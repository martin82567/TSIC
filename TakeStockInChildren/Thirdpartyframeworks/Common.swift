//
//  Common.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 8/27/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import Foundation
import UIKit
import SystemConfiguration

class CommonValue {
    static let shared = CommonValue()
    var roomname: String = ""
    var SRaccestoken: String = ""
    var senderName: String = ""
    var roomSid: String = ""
    var remainingTime = ""
    var createAt = ""
    var senderId = ""
    var senderType = ""
    var receiverId = ""
    var receiverType = ""
    var uid = UUID()
}


class CommonFAQDATA {
    static let sharedinstance = CommonFAQDATA()
    var faqmentorurl: String = ""
    var faqmenteeurl: String = ""
}




class Common {
    
    func showAlertView(title : String, msg :String, controller:UIViewController, okClicked : @escaping ()->()){
        let alertController = UIAlertController(title: title, message: msg, preferredStyle: UIAlertController.Style.alert)
        let okAction = UIAlertAction(title: "OK", style: UIAlertAction.Style.default) { (result : UIAlertAction) -> Void in
            print("OK")
            okClicked()
        }
        alertController.addAction(okAction);
        controller.present(alertController, animated: true, completion: nil)
    }
    
    func connectedToNetwork() -> Bool {
        var zeroAddress = sockaddr_in()
        zeroAddress.sin_len = UInt8(MemoryLayout<sockaddr_in>.size)
        zeroAddress.sin_family = sa_family_t(AF_INET)
        
        guard let defaultRouteReachability = withUnsafePointer(to: &zeroAddress, {
            $0.withMemoryRebound(to: sockaddr.self, capacity: 1) {
                SCNetworkReachabilityCreateWithAddress(nil, $0)
            }
        }) else {
            return false
        }
        
        var flags: SCNetworkReachabilityFlags = []
        if !SCNetworkReachabilityGetFlags(defaultRouteReachability, &flags) {
            return false
        }
        
        let isReachable = flags.contains(.reachable)
        let needsConnection = flags.contains(.connectionRequired)
        
        return (isReachable && !needsConnection)
    }
}


