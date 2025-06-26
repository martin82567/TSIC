//
//  IncomgcallViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 20/10/20.
//  Copyright © 2020 Aquarious Technology. All rights reserved.
//

import UIKit
import ZoomVideoSDK
import AVFoundation

class IncomgcallViewController: BaseViewController,videoback, ZoomVideoSDKDelegate {
    
    var roomSid: String?
    
    var remoteParticipant: ZoomVideoSDKUser?
    
   // var timer:Timer?
    
    var phonering: AVAudioPlayer?
    var participantOutincomg = 0
    
    @IBOutlet weak var name: UILabel!
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        //timer = Timer.scheduledTimer(timeInterval: 1.0, target: self,  selector: #selector(onTimerFiresincomg), userInfo: nil, repeats: true)
        
           NotificationCenter.default.addObserver(self, selector: #selector(onTimerFiresincomg), name: .disconnectcall, object: nil)
        
        self.musicPlay()
        self.name.text = CommonValue.shared.senderName
    }
    
    @objc func onTimerFiresincomg()
    {
        self.navigationController?.popViewController(animated: true)
        /*
        participantOutincomg += 1
        print("count--",participantOutincomg)
        if participantOutincomg == 48 {
            self.timer?.invalidate()
            self.navigationController?.popViewController(animated: true)
      1  }
        */
        
    }
    
    
    func musicPlay() {
        let path = Bundle.main.path(forResource: "phoneringtwo.mp3", ofType:nil)!
        let url = URL(fileURLWithPath: path)

        do {
            phonering = try AVAudioPlayer(contentsOf: url)
            phonering?.numberOfLoops = -1
            phonering?.play()
        } catch {
            // couldn't load file :(
        }

    }
    
    @IBAction func callDeclined(_ sender: Any) {
      //  self.timer?.invalidate()
        self.disconnectVideocall()
    }
    
    @IBAction func cellAccept(_ sender: Any) {
        phonering?.stop()
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    
    
    func backBVideo(name: String) {
        print("name000000000",name)
        self.navigationController?.popViewController(animated: true)
    }
    
    func connectRoomViareceive() {
        let acces_Token = CommonValue.shared.SRaccestoken
        let roomname = CommonValue.shared.roomname
        let roomSid = CommonValue.shared.roomSid
                
        let sessionContext = ZoomVideoSDKSessionContext()

        // Ensure that you do not hard code JWT or any other confidential credentials in your production app.
        sessionContext.token = acces_Token
        sessionContext.sessionName = roomSid
        sessionContext.userName = UserCredential.shared.firstname + UserCredential.shared.middlename + UserCredential.shared.lastname

        // Join the session
        if let session = ZoomVideoSDK.shareInstance()?.joinSession(sessionContext) {
            // Session joined successfully.
            print("Zoom session",session as Any)
        }
    }
   
    func disconnectVideocall() {
        
        phonering?.stop()
        connectRoomViareceive()
        ZoomVideoSDK.shareInstance()?.leaveSession(true)
        self.remoteParticipant = nil
        
        self.startActivityIndicator()
        var parameter: [String: String] = [:]
        parameter["room_sid"] = CommonValue.shared.roomSid
        parameter["unique_name"] = CommonValue.shared.roomname
        parameter["disconnect_type"] = "miss_call"
        print("roomsid____",CommonValue.shared.roomSid)
        MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
            print("access called-------",json)
            self.stopActivityIndicator()
            let status = json["status"] as! Bool
            if status == true {
                DispatchQueue.main.async {
                    CommonValue.shared.roomname = ""
                    CommonValue.shared.SRaccestoken = ""
                    CommonValue.shared.senderName = ""
                    CommonValue.shared.roomSid = ""
                    CommonValue.shared.remainingTime = ""
                    self.navigationController?.popViewController(animated: true)
                }
                
            } else {
                self.UI {
                    Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                }
            }
        }
        
    }
    
    func onUserJoin(_ helper: ZoomVideoSDKUserHelper?, users userArray: [ZoomVideoSDKUser]?) {
        self.remoteParticipant = userArray?.first(where: { $0.getID() != ZoomVideoSDK.shareInstance()?.getSession()?.getMySelf()?.getID() })
    }
    
    func onUserLeave(_ helper: ZoomVideoSDKUserHelper?, users userArray: [ZoomVideoSDKUser]?) {

        //If partcipant has left close the call
        let remoteParticipant = userArray?.first(where: { $0.getID() == self.remoteParticipant?.getID() })
        if remoteParticipant == nil {
            self.navigationController?.popViewController(animated: true)
            print("clean up remote participant")
            self.remoteParticipant = nil
        }
    }
}
