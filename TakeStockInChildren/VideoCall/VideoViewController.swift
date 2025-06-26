//
//  VideoViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 28/09/20.
//  Copyright © 2020 Aquarious Technology. All rights reserved.
//

import UIKit
import ZoomVideoSDK
import CallKit
import SocketIO
import AVFoundation

protocol videoback {
    func backBVideo(name: String)
}

let appDelegate = UIApplication.shared.delegate as! AppDelegate

class VideoViewController: BaseViewController, ZoomVideoSDKDelegate {

    
    var callTimeRemainingTimer:Timer?
    var notAnsweredTimer: Timer?
    
    //var second = 0
    //var minute = 0
    //var hour = 0
    
    
    var fromWhereTag = ""
    var minute = 0
    var second = 0
    var chatCode = ""
    var uniquenameInitiate = ""
    var participantCount = 0
    
    var participantOutSecond = 0
    
    var participantNotanswerSecond = 0
    
    var receiverImgUrl = ""
    var receiverName = ""
    var strurl = ""
    
    var callReceievedOrnot = false
    
    
    @IBOutlet weak var callingUserimg: UIImageView!
    @IBOutlet weak var callingusernameLabel: UILabel!
    
    @IBOutlet weak var timedurationLabel: UILabel!
    @IBOutlet weak var connectDisconnectBtton: UIButton!
    @IBOutlet weak var statusLabel: UILabel!
    
    var remoteParticipant: ZoomVideoSDKUser?
    
    @IBOutlet weak var prviewview2: UIView!
    
    @IBOutlet weak var previewView: UIView!
    
    var senderAccessToken :String = ""
    var receiverAccessToken = ""
    var tokenurl = ""
    var roomname: String?
    
    var senderId = ""
    var senderType = ""
    var receiverId = ""
    var receiverType = ""
    
    
    var createAt: String?
    var currentTime: String?
    
    var viareceiveCreateat: String?
    
    var twilloSid: String?
    
    var delegate: videoback!
    
    var minusSecond = 4
    
    
    var socket: SocketIOClient!
    var manager: SocketManager!
    
    var isSocketConnected: Bool = false
    var isCallReceivedViaWeb: Bool = false
    var socketCallSent = false

    override func viewDidLoad() {
        
        super.viewDidLoad()
        
      //  NotificationCenter.default.addObserver(self, selector: #selector(getTimer), name: .backgroundColor, object: nil)
       // provider.reportCall(with: CommonValue.shared.uid, endedAt: nil, reason: .remoteEnded)
       // NotificationCenter.default.addObserver(self, selector: #selector(endCall), name: .callEnd, object: nil)
        
        NotificationCenter.default.addObserver(self, selector: #selector(willterminateApp), name: .terminateApp, object: nil)
        UIApplication.shared.isIdleTimerDisabled = true
        callingUserimg.layer.cornerRadius = callingUserimg.frame.size.width/2
        print("img",self.receiverImgUrl)
        print("name",self.receiverName)
        let profileImage = self.strurl.appending(receiverImgUrl)
        callingUserimg.sd_setImage(with: URL(string: profileImage), placeholderImage: UIImage(named: "defaultProfileImage"))
        callingusernameLabel.text = self.receiverName
        self.statusLabel.isHidden = false
        startPreview()
        
        if fromWhereTag == "viaChat" {
            // startPreview()
            initiateVideoChat()
            
            //  generateRoomVideoAPICall()
        }
        else {
            appDelegate.provider.reportCall(with: CommonValue.shared.uid, endedAt: nil, reason: .remoteEnded)
            self.connectRoomViareceive()
            
        }
        
        ZoomVideoSDK.shareInstance()?.delegate = self
    }
    
    //MARK:// Video Call Socket
    func videoSocket() {
        print("Socket function called")
        manager = SocketManager(socketURL: URL(string: TakeStockInChildrenConstant.SocketURL)!, config: [.log(false), .compress, .forceWebsockets(true)])
        socket = manager.defaultSocket
        socket.on(clientEvent: .connect) {data, ack in
            print("socket connected=============================================")
            self.isSocketConnected = true
            if self.fromWhereTag == "viaChat" {
                var objData = [String:String]()
                objData.updateValue(self.senderType, forKey: "type")
                objData.updateValue(self.senderId, forKey: "id")
                print("objdata",objData)
                self.socket.emit("connected",objData)
                print("After socket connect Data via chat================================",data,ack)
                self.getTimer()
            }
            else {
                var objData = [String:String]()
                objData.updateValue(CommonValue.shared.receiverType, forKey: "type")
                objData.updateValue(CommonValue.shared.receiverId, forKey: "id")
                print("objdata",objData)
                self.socket.emit("connected",objData)
                print("After socket connect Data================================",data,ack)
                self.getTimer()
            }
            if self.fromWhereTag == "viaChat" && !self.socketCallSent {
                
                var sendcallObj = [String:Any]()
                sendcallObj.updateValue(self.senderId, forKey: "sender_id")
                sendcallObj.updateValue(self.senderType, forKey: "sender_type")
                sendcallObj.updateValue(self.receiverId, forKey: "receiver_id")
                sendcallObj.updateValue(self.receiverType, forKey: "receiver_type")
                
                self.socket.emit("reqSend", sendcallObj)
                self.socketCallSent = true
            }
            else {
                print("reuest send not hit this time from via received call")
            }
        }
        
        
        socket.on("reqReceived") { data, ack in
            print("request received data=============================================",data,ack)
        }
        socket.on("setTimerVal") { data, ack in
            print("set timer val=============================================",data,ack)
            guard let objArr = data[0] as? [String: Any] else {return}
           // let timegetCheck = objArr["remainimgCallTime"] as? Int ?? 0
           //let timecheck = objArr["remainimgCallTime"] as? Double ?? 0.0
           // let timeV = objArr["remainimgCallTime"] as? String ?? ""
           // print("value",timegetCheck,timecheck,timeV)
            let timer_time = objArr["remainimgCallTime"] as? Int ?? 0
           // print("Timer time",timer_time)
            self.minute = (timer_time % 3600) / 60
            self.second = (timer_time % 3600) % 60
            print("minute and second =======",self.minute,self.second)
            if self.second == 0 {
                self.minute = self.minute - 1
                self.second = 60
                print("minute and second after set timer if second",self.minute,self.second)
            }
        
        }
        socket.on("getTimerVal") { data, ack in
            print("get timer vallll=============================================",data,ack)
            self.isCallReceivedViaWeb = true
            guard let objArr = data[0] as? [String: Any] else {return}
            print("Socket Received Data:: \(objArr)")
            print("minutes in socket",self.minute)
            let minuteSend = (self.minute * 60) + self.second
            print("overall sending second",minuteSend)
            var msgGettime = [String: Any]()
            msgGettime.updateValue(objArr["sender_id"] as? String ?? "", forKey: "sender_id")
            msgGettime.updateValue(objArr["sender_type"] as? String ?? "", forKey: "sender_type")
            msgGettime.updateValue(objArr["receiver_id"] as? String ?? "", forKey: "receiver_id")
            msgGettime.updateValue(objArr["receiver_type"] as? String ?? "", forKey: "receiver_type")
            msgGettime.updateValue(minuteSend, forKey: "remainimgCallTime")
            self.socket.emit("setTimer",msgGettime)
        }
        socket.on("endVideo") { (data, ack) in
            print("end video ========================================")
            self.disConnectVideocall()
        }
        socket.connect()
        
    }

    //MARK: --> Socket via Video call
    func sendCallViaSocket() {
        var msgObj = [String:Any]()
        msgObj.updateValue(self.senderId, forKey: "sender_id")
        msgObj.updateValue(self.senderType, forKey: "sender_type")
        msgObj.updateValue(self.receiverId, forKey: "receiver_id")
        msgObj.updateValue(self.receiverType, forKey: "receiver_type")
        
        socket.emit("reqSend", msgObj)
        print("send call via socket calll",msgObj)
    }
    
    
    //MARK: --> Call Declined
    
    func cellDenied(){
        var msgObj = [String:Any]()
        msgObj.updateValue(self.receiverType, forKey: "receiver_type")
        msgObj.updateValue(self.receiverId, forKey: "receiver_id")
        msgObj.updateValue("", forKey: "receiver_device_type")
        msgObj.updateValue("", forKey: "receiver_device_token")
        msgObj.updateValue("", forKey: "receiver_type")
        msgObj.updateValue("", forKey: "sender_id")
        
        socket.emit("callDenied", msgObj)
    }
    
    //MARK: Get Timer
    @objc func getTimer(){
        if self.isSocketConnected {
            
            var getEmitData = [String:String]()
            getEmitData.updateValue(CommonValue.shared.senderId, forKey: "sender_id")
            getEmitData.updateValue(CommonValue.shared.senderType, forKey: "sender_type")
            getEmitData.updateValue(CommonValue.shared.receiverId, forKey: "receiver_id")
            getEmitData.updateValue(CommonValue.shared.receiverType, forKey: "receiver_type")
            
            if self.fromWhereTag == "viaChat" {
                
                print("EMit not send via call send")
            }
            else {
                self.socket.emit("getTimer",getEmitData)
            }
            
            print("After gettimer emit caled====================================================",getEmitData)
            
        }
        else {
            print("Socket Not connected")
        }
    }
    
    
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(true)
        print("Socket disconnect called")
         if isSocketConnected {
            self.socket.disconnect()
         }
        UIApplication.shared.isIdleTimerDisabled = false
    }
    
    
    @objc func willterminateApp() {
        
        callTimeRemainingTimer?.invalidate()
        notAnsweredTimer?.invalidate()
        timedurationLabel.text = "00:00"
        statusLabel?.text = "Disconnecting..."
        
        
        var rsid = ""
        if fromWhereTag == "viaChat" {
            rsid = self.twilloSid ?? ""
            print("sid",rsid)
        }
        else {
            rsid = CommonValue.shared.roomSid
            print("sid",rsid)
        }
        
        
        var parameter: [String: String] = [:]
        parameter["room_sid"] = rsid
        parameter["unique_name"] = self.roomname
        if self.callReceievedOrnot {
            parameter["disconnect_type"] = "end_call"
        }
        else  {
            parameter["disconnect_type"] = "miss_call"
        }
        
        MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
            print("access called disconnect-------",json)
            let status = json["status"] as! Bool
            if status == true {
                DispatchQueue.main.async {
                    
                    ZoomVideoSDK.shareInstance()?.leaveSession(true)
                    // self.delegate?.backBVideo(name: "hello")
                    // self.navigationController?.popToRootViewController(animated: true)
                }
                
            } else {
                self.UI {
                    // Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    // })
                }
            }
        }
    }
    
    
    @objc func notanswered() {
        
        ZoomVideoSDK.shareInstance()?.leaveSession(true)
        callTimeRemainingTimer?.invalidate()
        notAnsweredTimer?.invalidate()
        timedurationLabel.text = "00:00"
        statusLabel?.text = "Disconnecting..."
        
        
        var rsid = ""
        if fromWhereTag == "viaChat" {
            rsid = self.twilloSid ?? ""
            print("sid",rsid)
        }
        else {
            rsid = CommonValue.shared.roomSid
            print("sid",rsid)
        }
        
        
        var parameter: [String: String] = [:]
        parameter["room_sid"] = rsid
        parameter["unique_name"] = rsid
        if self.callReceievedOrnot {
            parameter["disconnect_type"] = "end_call"
        }
        else  {
            parameter["disconnect_type"] = "miss_call"
        }
        
        MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
            print("access called disconnect-------",json)
            let status = json["status"] as! Bool
            if status == true {
                DispatchQueue.main.async {
                    
                    //  self.room?.disconnect()
                    // self.delegate?.backBVideo(name: "hello")
                    // self.navigationController?.popToRootViewController(animated: true)
                }
                
            } else {
                self.UI {
                    // Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    // })
                }
            }
        }
    }
    
    
    func startRemainingTimeTimer() {
        print("Remaining time timer fired")
        callTimeRemainingTimer = Timer.scheduledTimer(timeInterval: 1.0, target: self, selector: #selector(onTimerFires), userInfo: nil, repeats: true)
    }
    
    
    @objc func onTimerFires() {
      //  print("on time fire call",self.second,self.minute)
        self.second -= 1
        
        if self.minute == 0 && self.second == 0 {
            print("End")
            timedurationLabel.text = "00:00"
            self.callTimeRemainingTimer?.invalidate()
            ZoomVideoSDK.shareInstance()?.leaveSession(true)
            self.delegate?.backBVideo(name: "hello")
            self.disConnectVideocall()
            if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
                // START local User's video.
                videoHelper.stopVideo()
            }
            self.navigationController?.popViewController(animated: true)
        }
        
        if self.second == 0 {
            print("00")
            self.second = 60
            self.minute -= 1
        }
        
        let sec = String(self.second)
      //  print("countof strings",sec.count)
        
    print("minute second***************",minute,second)
        
        
        if sec.count == 1 {
            print("-")
            timedurationLabel.text = "\(minute):0\(self.second)"
        }
        else if self.minute == 1 && self.second == 60 {
            print("red blink")
            timedurationLabel.text = "\(minute):\(self.second)"
            timedurationLabel.textColor = .red
            timedurationLabel.blink()
        }
        else if self.minute == 4 && self.second == 60  {
            print("red")
            timedurationLabel.text = "\(minute):\(self.second)"
            timedurationLabel.textColor = .red
        }
        else if minute < 0 && self.second > 0 {
           timedurationLabel.text = "\(00):\(00)"
            print("-----------------")
        }
        else {
            print("minute and second",self.minute,self.second)
            if self.second == 60 {
                print("Second 60")
               timedurationLabel.text = "\(minute):00"
                print("--")
            }
            else {
                    timedurationLabel.text = "\(minute):\(self.second)"
                    print("----")
            }
        }
        
    }
    
    
    func connectRoomViareceive() {
       // self.startPreview()
        
        let timer_time = Int(CommonValue.shared.remainingTime)
        self.minute = (timer_time ?? 0 % 3600) / 60
       // self.second = (timer_time ?? 0 % 3600) % 60
        let sec = (timer_time ?? 0 % 3600) % 60
        if sec > 4 {
           self.second = sec - 4
        }
        else {
            self.second = 4 - sec
        }
        if self.second == 0 {
            self.minute = self.minute - 1
            self.second = 60
        }
        
        
        //starttimer()
        let acces_Token = CommonValue.shared.SRaccestoken
        self.roomname = CommonValue.shared.roomname
       // print("Via receive=====",acces_Token,roomname)
        
        
        let sessionContext = ZoomVideoSDKSessionContext()

        sessionContext.token = acces_Token
        sessionContext.sessionName = roomname
        sessionContext.userName = UserCredential.shared.firstname + UserCredential.shared.middlename + UserCredential.shared.lastname

        // Join the session
        if let session = ZoomVideoSDK.shareInstance()?.joinSession(sessionContext) {
            // Session joined successfully.
            print("Zoom Session",session as Any)
        }
        
        DispatchQueue.main.async {
            self.statusLabel?.text = "Connecting    ..."
        }
        
    }
    
    
    
    func initiateVideoChat() {
     
        
         var parameter = [String:String]()
  
            
        parameter["sender_id"] = self.senderId
        parameter["sender_type"] = self.senderType
        parameter["receiver_id"] = self.receiverId
        parameter["receiver_type"] = self.receiverType
         print("param----ini",parameter)
         MentorApiManager().initiateVideoCall(parameter: parameter) { (json) in
             self.stopActivityIndicator()
             print("access called initiate video chat-------",json)
             let status = json["status"] as! Bool
             
             if status == true {
                 let dicData = json["data"] as! NSDictionary
                self.uniquenameInitiate = dicData["unique_name"] as? String ?? ""
                self.chatCode = dicData["chat_code"] as? String ?? ""
                print("unique name",self.uniquenameInitiate)
                let timer_time = Int(dicData["remaining_time"] as? String ?? "")
                print("timer time",timer_time)
                self.minute = (timer_time ?? 0 % 3600) / 60
               // self.second = (timer_time ?? 0 % 3600) % 60
                let sec = (timer_time ?? 0 % 3600) % 60
                if sec > 4 {
                   self.second = sec - 4
                }
                else {
                    self.second = 4 - sec
                }
                
                if self.second == 0 {
                    self.minute = self.minute - 1
                    self.second = 60
                }
                
                if timer_time ?? 0 < 12  {
                    self.UI {
                        Common().showAlertView(title: "", msg: "Video session Expired", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
                                // START local User's video.
                                videoHelper.stopVideo()
                            }
                           self.navigationController?.popViewController(animated: true)
                        })
                    }
                }
                else {
                    self.generateRoomVideoAPICall()
                   // self.generateRoomVideoAPICall()
                }
                print("minute second",self.minute,self.second)
                
                /*
                DispatchQueue.main.async {
                    self.starttimer()
                }
                */
                /*
                DispatchQueue.main.asyncAfter(deadline: .now() + 0.5) {
                    self.generateRoomVideoAPICall()
                    // your code here
                }
                */
                
                /*
                DispatchQueue.main.async {
                    self.connectroom()
                }
                */
                
             } else {
                 self.UI {
                     Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                         if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
                             // START local User's video.
                             videoHelper.stopVideo()
                         }
                        self.navigationController?.popViewController(animated: true)
                     })
                 }
             }
         }
        
        
    }
    
    
    
    func generateRoomVideoAPICall(){
        
        
        
         var parameter = [String:String]()
     //   parameter["sender_id"] = self.senderId
      //  parameter["sender_type"] = self.senderType
     //   parameter["receiver_id"] = self.receiverId
      //  parameter["receiver_type"] = self.receiverType
        parameter["chat_code"] = self.chatCode
        parameter["unique_name"] = self.uniquenameInitiate
         print("param",parameter)
         MentorApiManager().videoroomCreate(parameter: parameter) { (json) in
             self.stopActivityIndicator()
             print("access called generate room-------",json)
             let status = json["status"] as! Bool
             
             if status == true {
                 let dicData = json["data"] as! NSDictionary
                 let sendertoken = dicData["sender_accesstoken"] as? String ?? ""
                 let receivertoken = dicData["receiver_accesstoken"] as? String ?? ""
                 let roomname = dicData["unique_name"] as? String ?? ""
                self.roomname = roomname
                self.senderAccessToken = sendertoken
                self.receiverAccessToken =  receivertoken
                 let roomSid = dicData["room_sid"] as? String ?? ""
                 let date = dicData["created_at"] as? String ?? ""
                
                let array = date.components(separatedBy: " ")
                let array2 = array[1]
                print(array2)

                let check = array2.components(separatedBy: ":")
                
                self.createAt = check[2]
                print("date----",self.createAt)
                self.twilloSid = roomSid
                 print("sendertoken",sendertoken,receivertoken,roomname,roomSid)
                 print("receivertoken",receivertoken)
                 print("roomname",roomname)
                 print("roomsid",roomSid)
                
                
                /*
                DispatchQueue.main.async {
                    self.connectroom()
                }
                */
                 DispatchQueue.main.async {
                     self.notAnsweredTimer = Timer.scheduledTimer(timeInterval: 1.0, target: self, selector: #selector(self.notansweredfireTimer), userInfo: nil, repeats: true)
                 }
              //  self.sendCallViaSocket()
                 self.videoSocket()
                 self.connectroom()
                 
             } else {
                 self.UI {
                     Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                         if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
                             // START local User's video.
                             videoHelper.stopVideo()
                         }
                        self.navigationController?.popViewController(animated: true)
                     })
                 }
             }
         }
    }
    
    
    
    func performEndCallAction(uuid: UUID) {
        /*
        let endCallAction = CXEndCallAction(call: uuid)
        _ = CXTransaction(action: endCallAction)
        */
        let callController = CXCallController()

        let endCallAction = CXEndCallAction(call: UUID())

        callController.request(
            CXTransaction(action: endCallAction),
            completion: { error in
                if let error = error {
                    print("Error CAll: \(error)")
                } else {
                    print("Success")
                }
            })

        print("call ended---------------------------------")
    }
    

    @IBAction func connect(_ sender: Any) {
     //   print("uid---",CommonValue.shared.uid)
     //   self.performEndCallAction(uuid: CommonValue.shared.uid)
        if !self.isCallReceivedViaWeb {
            var callMissedObj = [String:String]()
            callMissedObj.updateValue(self.senderId, forKey: "sender_id")
            callMissedObj.updateValue(self.senderType, forKey: "sender_type")
            callMissedObj.updateValue(self.receiverId, forKey: "receiver_id")
            callMissedObj.updateValue(self.receiverType, forKey: "receiver_type")
            callMissedObj.updateValue("true", forKey: "endBefore")
            callMissedObj.updateValue("app", forKey: "denied_by")
            callMissedObj.updateValue(self.roomname ?? "", forKey: "unique_name")
            if self.isSocketConnected {
                socket.emit("endBeforeReceived", callMissedObj)
            }
        }
        ZoomVideoSDK.shareInstance()?.leaveSession(true)
        self.prviewview2 = nil
        self.remoteParticipant = nil
        
        callTimeRemainingTimer?.invalidate()
        notAnsweredTimer?.invalidate()
        timedurationLabel.text = "00:00"
        statusLabel?.text = "Disconnecting..."
        
        var rsid = ""
        if fromWhereTag == "viaChat" {
            rsid = self.twilloSid ?? ""
            print("sid",rsid)
        }
        else {
            rsid = CommonValue.shared.roomSid
            print("sid",rsid)
        }
       
         var parameter: [String: String] = [:]
         parameter["room_sid"] = rsid
        parameter["unique_name"] = self.roomname
        if self.callReceievedOrnot {
            parameter["disconnect_type"] = "end_call"
        }
        else  {
            parameter["disconnect_type"] = "miss_call"
        }
         
         MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
             print("access called disconnect-------",json)
             let status = json["status"] as! Bool
             if status == true {
                 DispatchQueue.main.async {
                    //self.room?.disconnect()
                    self.delegate?.backBVideo(name: "hello")
                     if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
                         // START local User's video.
                         videoHelper.stopVideo()
                     }
                 }
             } else {
//                 self.UI {
//                    // Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//                    // })
//                 }
             }
             DispatchQueue.main.async {
                 self.navigationController?.popToRootViewController(animated: true)
             }
         }
    }
    
    func connectroom() {
        let sessionContext = ZoomVideoSDKSessionContext()

        sessionContext.token = senderAccessToken
        sessionContext.sessionName = roomname
        sessionContext.userName = UserCredential.shared.firstname + UserCredential.shared.middlename + UserCredential.shared.lastname

        // Join the session
        if let session = ZoomVideoSDK.shareInstance()?.joinSession(sessionContext) {
            // Session joined successfully.
            print("Zoom Session",session as Any)
        }
            DispatchQueue.main.async {
                self.statusLabel?.text = "Ringing..."
            }
        
        
    }
    
    func startPreview() {
        
        // Get local user's video canvas.
        
        if let localUserVideoCanvas = ZoomVideoSDK.shareInstance()?.getSession()?.getMySelf()?.getVideoCanvas() {
            // Pass a UIView to the canvas in order to subscribe to User's videoCanvas
            localUserVideoCanvas.subscribe(with: previewView, aspectMode: .full_Filled, andResolution: ._Auto)
        }
        
        // Get the ZoomVideoSDKVideoHelper to perform video actions.
        if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
            // START local User's video.
            videoHelper.startVideo()
        }
        
        let tap = UITapGestureRecognizer(target: self, action: #selector(VideoViewController.flipCamera))
        self.previewView.addGestureRecognizer(tap)
    }
    
    
    
    @objc func flipCamera() {
        
        // Flip camera
        ZoomVideoSDK.shareInstance()?.getVideoHelper().switchCamera()
    }
    
//    func roomDidConnect() {
//        print("Did connect to Room")
//        timerDisconnect  = Timer.scheduledTimer(timeInterval: 1.0, target: self, selector: #selector(fireTimer), userInfo: nil, repeats: true)
//
//
//        if let localParticipant = ZoomVideoSDK.shareInstance()?.getSession()?.getMySelf() {
//            print("Local identity \(localParticipant.getID())")
//
//        }
//        
//        self.participantCount = ZoomVideoSDK.shareInstance()?.getSession()?.getRemoteUsers()?.count ?? 0
//        
//        // Connected participants already in the room
//           print("Number of connected Participants \(self.participantCount)")
//    }
    
    
    
    @objc func fireTimer() {
        participantOutSecond += 1
        print("parrrr",participantCount,participantOutSecond)
        if participantCount == 0 && participantOutSecond == 47 {
            
            disConnectVideocall()
        }
    }
    
    
    @objc func notansweredfireTimer() {
        participantNotanswerSecond += 1
        print("Not Answered Second:\(participantNotanswerSecond)")
        if participantCount == 0 && participantNotanswerSecond == 60 {
            
            participantNotanswerSecond = 0
            self.notAnsweredTimer?.invalidate()
            disConnectVideocall()
        }
    }
    
    
    func disConnectVideocall() {
        statusLabel?.text = "Disconnecting..."
        
        var rsid = ""
        if fromWhereTag == "viaChat" {
            rsid = self.twilloSid ?? ""
            print("sid via",rsid)
        }
        else {
            rsid = CommonValue.shared.roomSid
            print("sid video",rsid)
        }
        
         var parameter: [String: String] = [:]
         parameter["room_sid"] = rsid
         parameter["unique_name"] =  self.roomname
         parameter["disconnect_type"] = "miss_call"
         MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
             print("access called disconnect function-------",json)
             let status = json["status"] as! Bool
             if status == true {
                 DispatchQueue.main.async {
                    self.cleanupRemoteParticipant()
                 }
                 
             } else {
                 self.UI {
                  //   Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                  //   })
                 }
             }
         }
        
    }
    
    func renderRemoteParticipant(participant : ZoomVideoSDKUser) {
        
        self.callReceievedOrnot = true
        self.notAnsweredTimer?.invalidate()
        statusLabel.isHidden = true
        callingUserimg.isHidden = true
        callingusernameLabel.isHidden = true
        self.prviewview2?.contentMode = .scaleAspectFit
        
        
        if CommonValue.shared.createAt == "" {
            print("via sending cell")
            let nowdate = Date()
            let formatter = DateFormatter()
            //  formatter.dateStyle = .full
            formatter.timeZone = TimeZone(abbreviation: "EST")
            formatter.setLocalizedDateFormatFromTemplate("yyyy-MM-dd HH:mm:ss")
            let datestring = formatter.string(from: nowdate)
            
            let array = datestring.components(separatedBy: ", ")
            let array2 = array[1]
            
            let check = array2.components(separatedBy: ":")
            
            self.currentTime = check[2]
            print("created at",self.createAt ?? "")
            print("current time",self.currentTime ?? "")
            
            
            let time_Cre = Int(self.createAt ?? "")  ?? 0
            let temp_Cre = String(format: "%d", time_Cre)
            let time_Crnt = Int(self.currentTime ?? "") ?? 0
            let temp_Crnt = String(format: "%d", time_Crnt)
            print("send time cret",temp_Cre)
            print("send time current",temp_Crnt)
            
            let main_Cre = Int(temp_Cre) ?? 0
            let main_Crnt = Int(temp_Crnt) ?? 0
            
            
            print("send main cret",main_Cre)
            print("send main current",main_Crnt)
            
            
            if main_Crnt > main_Cre {
                let time = main_Crnt - main_Cre
                print("send second",self.second)
                print("time",time)
                if self.second > time {
                    let minusTime = self.second - time
                    print("minus time greater",minusTime)
                    self.second = minusTime
                }
                else {
                    let minusTime = time - self.second
                    print("minus time less",minusTime)
                    self.second = minusTime
                }
                
            }
            else {
                let time = main_Cre - main_Crnt
                
                if self.second > time {
                    let minusTime = self.second - time
                    print("minus time--",minusTime)
                    self.second = minusTime
                }
                else {
                    let minusTime = time - self.second
                    print("minus time--",minusTime)
                    self.second = minusTime
                }
                
            }
            
            
            print("minute and second",self.minute,self.second)
            self.startRemainingTimeTimer()
            
            
        }
        else {
            print("via receiving call")
            let nowdate = Date()
            let formatter = DateFormatter()
            //  formatter.dateStyle = .full
            formatter.timeZone = TimeZone(abbreviation: "EST")
            formatter.setLocalizedDateFormatFromTemplate("yyyy-MM-dd HH:mm:ss")
            let datestring = formatter.string(from: nowdate)
            
            let array = datestring.components(separatedBy: ", ")
            let array2 = array[1]
            
            let check = array2.components(separatedBy: ":")
            
            self.currentTime = check[2]
            
            
            
            let array3 = CommonValue.shared.createAt.components(separatedBy: " ")
            let array4 = array3[1]
            print(array4)
            
            let checktwo = array4.components(separatedBy: ":")
            
            self.viareceiveCreateat = checktwo[2]
            
            
            print("created at",viareceiveCreateat ?? "")
            print("current time",self.currentTime ?? "")
            
            let time_Cre = Int(viareceiveCreateat ?? "") ?? 0
            let temp_Cre = String(format: "%d", time_Cre)
            let time_Crnt = Int(self.currentTime ?? "") ?? 0
            let temp_Crnt = String(format: "%d", time_Crnt)
            
            
            let main_Cre = Int(temp_Cre) ?? 0
            let main_Crnt = Int(temp_Crnt) ?? 0
            
            
            
            if main_Crnt > main_Cre {
                let time = main_Crnt - main_Cre
                
                if self.second > time {
                    let minusTime = self.second - time
                    print("minus time-",minusTime)
                    self.second = minusTime
                }
                else {
                    let minusTime = time - self.second
                    print("minus time-",minusTime)
                    self.second = minusTime
                }
                
                
            }
            else {
                let time = main_Cre - main_Crnt
                
                if self.second > time {
                    let minusTime = self.second - time
                    print("minus time--",minusTime)
                    self.second = minusTime
                }
                else {
                    let minusTime = time - self.second
                    print("minus time--",minusTime)
                    self.second = minusTime
                }
            }
            
            self.startRemainingTimeTimer()
        }
        
        if let remoteUserVideoCanvas = participant.getVideoCanvas() {
            // Pass a UIView to the canvas in order to subscribe to User's videoCanvas
            remoteUserVideoCanvas.subscribe(with: prviewview2, aspectMode: .full_Filled, andResolution: ._Auto)
        }
        self.remoteParticipant = participant
    }
    
    //Clean up participant
    func cleanupRemoteParticipant() {
        
        ZoomVideoSDK.shareInstance()?.leaveSession(true)
        self.prviewview2 = nil
        self.remoteParticipant = nil
        
        disConnectVideocall()
        if self.remoteParticipant != nil {
            CommonValue.shared.SRaccestoken = ""
            CommonValue.shared.roomname = ""
            CommonValue.shared.senderName = ""
            CommonValue.shared.roomSid = ""
            CommonValue.shared.remainingTime = ""
            CommonValue.shared.createAt = ""
            CommonValue.shared.receiverId = ""
            CommonValue.shared.receiverType = ""
            CommonValue.shared.senderId = ""
            CommonValue.shared.senderType = ""
            print("clean all value")
            self.prviewview2 = nil
            self.remoteParticipant = nil
        }
       // self.delegate?.backBVideo(name: "hello")
        // Get the ZoomVideoSDKVideoHelper to perform video actions.
        if let videoHelper = ZoomVideoSDK.shareInstance()?.getVideoHelper() {
            // START local User's video.
            videoHelper.stopVideo()
        }
        self.navigationController?.popToRootViewController(animated: true)
      //  self.navigationController?.popViewController(animated: true)
        print("clean up remote participan00t")
    }
}



// MARK:- RemoteParticipantDelegate
extension VideoViewController {

    func onUserJoin(_ helper: ZoomVideoSDKUserHelper?, users userArray: [ZoomVideoSDKUser]?) {
        self.participantCount = ZoomVideoSDK.shareInstance()?.getSession()?.getRemoteUsers()?.count ?? 0
        if let remoteParticipant = ZoomVideoSDK.shareInstance()?.getSession()?.getRemoteUsers()?.first, self.remoteParticipant == nil {
            renderRemoteParticipant(participant: remoteParticipant)
        }
    }
    
    func onUserLeave(_ helper: ZoomVideoSDKUserHelper?, users userArray: [ZoomVideoSDKUser]?) {
        self.participantCount = ZoomVideoSDK.shareInstance()?.getSession()?.getRemoteUsers()?.count ?? 0
        self.cleanupRemoteParticipant()
        self.callTimeRemainingTimer?.invalidate()
        self.notAnsweredTimer?.invalidate()
    }
}

extension UILabel {
    func blink() {
        self.alpha = 0.0;
        UIView.animate(withDuration: 0.5, //Time duration you want,
            delay: 0.0,
            options: [.curveEaseInOut, .autoreverse, .repeat],
            animations: { [weak self] in self?.alpha = 1.0 },
            completion: { [weak self] _ in self?.alpha = 0.0 })
    }
}


extension Date {
    /// Returns the amount of years from another date
    func years(from date: Date) -> Int {
        return Calendar.current.dateComponents([.year], from: date, to: self).year ?? 0
    }
    /// Returns the amount of months from another date
    func months(from date: Date) -> Int {
        return Calendar.current.dateComponents([.month], from: date, to: self).month ?? 0
    }
    /// Returns the amount of weeks from another date
    func weeks(from date: Date) -> Int {
        return Calendar.current.dateComponents([.weekOfMonth], from: date, to: self).weekOfMonth ?? 0
    }
    /// Returns the amount of days from another date
    func days(from date: Date) -> Int {
        return Calendar.current.dateComponents([.day], from: date, to: self).day ?? 0
    }
    /// Returns the amount of hours from another date
    func hours(from date: Date) -> Int {
        return Calendar.current.dateComponents([.hour], from: date, to: self).hour ?? 0
    }
    /// Returns the amount of minutes from another date
    func minutes(from date: Date) -> Int {
        return Calendar.current.dateComponents([.minute], from: date, to: self).minute ?? 0
    }
    /// Returns the amount of seconds from another date
    func seconds(from date: Date) -> Int {
        return Calendar.current.dateComponents([.second], from: date, to: self).second ?? 0
    }
    /// Returns the a custom time interval description from another date
    func offset(from date: Date) -> String {
        if years(from: date)   > 0 { return "\(years(from: date))y"   }
        if months(from: date)  > 0 { return "\(months(from: date))M"  }
        if weeks(from: date)   > 0 { return "\(weeks(from: date))w"   }
        if days(from: date)    > 0 { return "\(days(from: date))d"    }
        if hours(from: date)   > 0 { return "\(hours(from: date))h"   }
        if minutes(from: date) > 0 { return "\(minutes(from: date))m" }
        if seconds(from: date) > 0 { return "\(seconds(from: date))s" }
        return ""
    }
}
extension String {
    func videoDate(byFormat : String = "MM-dd-yyyy HH:mm:ss" , byZone : String = "UTC") -> Date {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = byFormat
        if byZone != "" {
            dateFormatter.timeZone = TimeZone(abbreviation: byZone) // "UTC"
        }
        let outdate = dateFormatter.date(from: self) ?? Date()
        return outdate
    }
}
