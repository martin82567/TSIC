//
//  ChatVC.swift
//  Zowod
//
//  Created by Aquarious Technology on 22/02/19.
//  Copyright © 2019 Aquarious. All rights reserved.
//

import UIKit
import SocketIO
import IQKeyboardManagerSwift
import ReverseExtension
import Alamofire
protocol datapass {
    func passdata(tag: String)
}
class ChatVC: BaseViewController, UITextFieldDelegate, UITableViewDelegate, UITableViewDataSource, UITextViewDelegate {
    
    @IBOutlet weak var videoBtn: UIButton!
    @IBOutlet var safearacolor: UIView!
    @IBOutlet weak var headerview: UIView!
    @IBOutlet weak var backgroundImage: UIImageView!
    @IBOutlet weak var tblChatMessage: UITableView!
    @IBOutlet weak var scrollView1: UIScrollView!
    @IBOutlet weak var vwChatTextContainer: UIView!
    @IBOutlet weak var constraintVwChatHeight: NSLayoutConstraint!
    @IBOutlet weak var constraintViewChatBottom: NSLayoutConstraint!
    
    @IBOutlet weak var btnChat: UIButton!
    @IBOutlet weak var txtViewChat: UITextView!
    
    
    
    var chatUserName: String!
    var loginUserProPicURL: String!
    var otherUserProPicURL: String!
    var seller_ID: String!
    var buyer_ID: String!
    var product_ID: String!
    var product_Name: String!
    var Log_User_ID: String!
    var sender_userid: String!
    var sender_Name = ""
    var fromWhereMessageSend: String!
    var isSocketConnected: Bool = false
    var maxHeightChat = 140
    var minHeightChat = 60
    var arrChatMessages = [[String:Any]]()
    var tempChatMessages = [[String:Any]]()
    var tempArray = [[String:Any]]()
    var tempanotherArray = [[String:Any]]()
    
    var copyArray = [[String:Any]]()
    var socket: SocketIOClient!
    var manager: SocketManager!
    var firebaseKeyReceiver: String!
    var strIamfrom = ""
    
    var receiverTypeCheck = ""
    
    var totalChat: Int?
    
    var isSend: Bool = false
    var isFromSocket: Bool = false
    
    var isrefresh: Bool = false
    
    var LoginID = ""
    var chatCode = ""
    var deviceType = ""
    var timezone = ""

    var menteeIdForChat = ""
   var receiver_id = ""
    var chatBy = ""
    var strUrl = ""
   var senderIDForStaff = ""
   var senderIDForMentor = ""
    var loginMenteeButMentorDicData = NSDictionary()
   // var loginMentorButMenteeDicData = [NSDictionary]()
    var sendMessaage: Bool = false
    
    var pageList : Int = 0
    var isLoadMoreList : Bool = false
    //var takevalue : Int = 10
    
    var temploginMode: String?
    
    var delegate: datapass!
    var valueMode: String?
    
    var receivername: String?
    var receiverimageUrl: String?
    
    @IBOutlet weak var imgViewProfile: UIImageView!
    @IBOutlet weak var lblUserName: UILabel!
    
    // MARK:: UIView
    override func viewDidLoad() {
        super.viewDidLoad()
        print("strim---",self.strIamfrom)
        
      //  self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodeChange()
        tblChatMessage.re.dataSource = self
        tblChatMessage.re.delegate = self
        
        
        
        tblChatMessage.estimatedRowHeight = 75
        tblChatMessage.rowHeight = UITableView.automaticDimension
        /*
        tblChatMessage.re.scrollViewDidReachTop = { scrollView in
            print("scrollViewDidReachTop")
        }
        tblChatMessage.re.scrollViewDidReachBottom = { scrollView in
            print("scrollViewDidReachBottom")
        }
        */
        sendMessaage =  false
        
        NotificationCenter.default.addObserver(self, selector: #selector(roomExitFromChatNotification(_:)), name:  Notification.Name("UserRoomExitFromChat"), object: nil)
    }
    
    
    @IBAction func videoControllerPush(_ sender: Any) {
        
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("login mode",loginMode)
        var sendertype: String = ""
        if loginMode == "Mentor"{
            sendertype = "mentor"
        }
        else if loginMode == "Mentee" {
            sendertype = "mentee"
        }
        
        
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        print("send id",self.LoginID)
        newViewController.senderId = self.LoginID
        newViewController.senderType = sendertype
        newViewController.receiverId = senderIDForStaff
        print("receive id",senderIDForStaff)
        newViewController.receiverType = self.receiverTypeCheck
        newViewController.fromWhereTag = "viaChat"
        newViewController.receiverName = self.receivername ?? ""
        newViewController.receiverImgUrl =  self.receiverimageUrl ?? ""
        newViewController.strurl = strUrl
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    
    func darkmodeChange() {
        
        if self.valueMode == "dark" {
            backgroundImage.image = UIImage(named: "BG4")
            headerview.backgroundColor = UIColor(hex: "#0E0F27")
            safearacolor.backgroundColor = UIColor(hex: "#0E0F27")
        }
        else if self.valueMode == "light" {
            backgroundImage.image = UIImage(named: "BackgroundImage")
            //headerview.backgroundColor = UIColor(hex: "#A7AE3B")
            safearacolor.backgroundColor = UIColor(hex: "#B1B82C")
        }
        
    }
    
    @objc func roomExitFromChatNotification(_ notification:Notification) {
        // Do something now //DisConnect Socket
        if isSocketConnected {
           // self.disconnectSocket()
        }
    }
    
    //MARK:: Function AS a MENTEE OR MENTOR
    func loginUserTypeCheck() {
        
        self.temploginMode = UserDefaults.standard.value(forKey: "loginMode") as? String ?? ""
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            guard let mentorData = UserDefaults.standard.value(forKey: "mentorUserDetails") as? NSDictionary else {
                return
            }
            if self.strIamfrom == "staff" {
                  chatBy = "staff"
                videoBtn.isHidden = true
            } else {
                   chatBy = "mentor"
                videoBtn.isHidden = false
            }
            print("striamfrom",self.strIamfrom)
            strUrl = TakeStockInChildrenConstant.MenteeImageBaseURL
            
            LoginID = "\(mentorData["id"] as! Int)" //"\(loginMenteeButMentorDicData["id"] as! Int)"//
            senderIDForStaff  = "\(loginMenteeButMentorDicData["id"] as! Int)"
            sender_Name = self.totalName(dicData: mentorData)
            print("data for receiver---",loginMenteeButMentorDicData)
            self.receivername = loginMenteeButMentorDicData["firstname"] as? String ?? ""
            print("data id",loginMenteeButMentorDicData["id"] as! Int)
        
            getChatMessages(id: loginMenteeButMentorDicData["id"] as! Int)
            loadMentorData()
        } else {
            if self.strIamfrom == "staff" {
                videoBtn.isHidden = true
                print("staff chat",self.strIamfrom)
                menteeStaffMethod()
            } else {
                videoBtn.isHidden = true
                print("mentee data load",self.strIamfrom)
                self.menteeDataLoad()
                //getMentorDetails()
                //menteeDataLoad()
            }
        }
    }
    
    func menteeStaffMethod() {
        chatBy = "menteestaff"
        guard let menteeData = UserDefaults.standard.value(forKey: "userDetails") as? NSDictionary else {
            return
        }
        
        print(menteeData)
        
        strUrl = TakeStockInChildrenConstant.MenteeImageBaseURL
        
        
        LoginID = "\(menteeData["id"] as! Int)" //"\(loginMenteeButMentorDicData["id"] as! Int)"//
        senderIDForStaff  = "\(loginMenteeButMentorDicData["id"] as! Int)"
        sender_Name = self.totalName(dicData: menteeData)
        getChatMessages(id: loginMenteeButMentorDicData["id"] as! Int)
        loadMentorData()
    }
    
    func menteeDataLoad() {
        let menteeData = UserDefaults.standard.value(forKey: "userDetails") as! NSDictionary
        print("MenteeData:: ",menteeData)
        strUrl = TakeStockInChildrenConstant.MentorImageBaseURL
        
        senderIDForStaff  = senderIDForMentor
        
        chatBy = "mentee"
        loadMentorData()
        LoginID = "\(menteeData["id"] as! Int)"
        sender_Name = self.totalName(dicData: menteeData)
        print("name----",sender_Name)
        getChatMessagesMenteeWithMentor(id: senderIDForMentor)
       // getChatMessages(id: menteeData["id"] as! Int)
    }
    
    func loadMentorData() {
        
        if let imageProfile = loginMenteeButMentorDicData["image"] as? String {
            self.receivername = loginMenteeButMentorDicData["firstname"] as? String ?? ""
          let profileImage = strUrl.appending(imageProfile)
            print("img",imageProfile)
            self.receiverimageUrl = imageProfile
            imgViewProfile.sd_setImage(with: URL(string: profileImage), placeholderImage: UIImage(named: "defaultProfileImage"))
        }
        
        if self.strIamfrom  == "staff" {
            if let imageProfile = loginMenteeButMentorDicData["profile_pic"] as? String {
                
                let profileImage = TakeStockInChildrenConstant.StaffImageBaseURL.appending(imageProfile)
                
                self.receiverimageUrl = imageProfile
                
                imgViewProfile.sd_setImage(with: URL(string: profileImage), placeholderImage: UIImage(named: "defaultProfileImage"))
            }
            self.lblUserName.text = loginMenteeButMentorDicData["name"] as? String
        } else {
            self.lblUserName.text = self.totalName(dicData: loginMenteeButMentorDicData)

        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        print("reload")
        self.isLoadMoreList = false
        
        let contentInsets = UIEdgeInsets.zero as UIEdgeInsets
        self.scrollView1.contentInset = contentInsets;
        self.scrollView1.scrollIndicatorInsets = contentInsets;
        
        
        
        
        NotificationCenter.default.addObserver(self, selector: #selector(appMovedToForeground), name: UIApplication.willEnterForegroundNotification, object: nil)
        
        NotificationCenter.default.addObserver(self, selector: #selector(appMovedToBackground), name: UIApplication.didEnterBackgroundNotification, object: nil)
        
        

        
        
        
        
        
        NotificationCenter.default.addObserver (
            self,
            selector: #selector(self.keyboardDidShow),
            name: UIResponder.keyboardWillShowNotification,
            object: nil)
        
        NotificationCenter.default.addObserver (
            self,
            selector: #selector(self.keyboardWillBeHidden),
            name: UIResponder.keyboardWillHideNotification,
            object: nil)
        
        self.txtViewChat.text = "Enter your message"
        
//        if (UserDefaults.standard.value(forKey: "Log_user_ID")) == nil {
//             self.showAlertView(title: "Alert", msg: "Please Login once more..", controller: self) {}
//        } else {
//            Log_User_ID = String(describing: UserDefaults.standard.value(forKey: "Log_user_ID")!)
//        }
        
        loginUserProPicURL = UserDefaults.standard.value(forKey: "profile_pic") as? String
        
        self.tblChatMessage.alwaysBounceVertical = false
        IQKeyboardManager.shared.enable = false
        loginUserTypeCheck()

//        //for getting new msgs while openning chatvc
//        NotificationCenter.default.addObserver(self, selector: #selector(onReceiveNotification(_:)), name: NSNotification.Name(rawValue: "newChatMessageAlert"), object: nil)
    }
    
    
    
    
    
    @objc func appMovedToForeground() {
        print("App moved to ForeGround!")
        self.socketMethods()
    }
    
    
    @objc func appMovedToBackground() {
        if isSocketConnected {
            self.disconnectSocket()
            print("socket disconnected")
        }
        print("App moved to Background!")
    }
    
    
    
    
    override func viewDidAppear(_ animated: Bool) {
        print("reload viewDidAppear")
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(true)
        
        if isSocketConnected {
            self.disconnectSocket()
        }
        URLSession.shared.invalidateAndCancel()
        IQKeyboardManager.shared.enable = true
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        delegate?.passdata(tag: "frommentor")
        self.navigationController?.popViewController(animated: true)
    }
    
    override func viewDidDisappear(_ animated: Bool) {
        super.viewDidDisappear(animated)
        
        if isSocketConnected {
            self.disconnectSocket()
        }
        
        
        
        
        
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillShowNotification, object: nil)
        
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillHideNotification, object: nil)
        
        URLSession.shared.invalidateAndCancel()
        IQKeyboardManager.shared.enable = true
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    //After getting notification reload chat msgs list
    @objc func onReceiveNotification(_ notification:Notification) {
        // Do something now //reload tableview
       // self.getChatMessages()
    }
    
    
    //MARK::  API Call
    func getMentorDetails() {
     
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            ApiManager.sharedInstance.getMentorDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    self.stopActivityIndicator()
                  //  print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        let mentorDetails  = json["mentor_details"] as? NSDictionary
                        
                        if let mentor = mentorDetails {
                            
                            self.loginMenteeButMentorDicData = mentor
                            self.UI {
                                self.menteeDataLoad()
                            }
                            
                        }
                        
                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    //    self.stopActivityIndicator()
                    
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    func getChatMessages(id:Int) {
        
        self.startActivityIndicator()
       // self.arrChatMessages.removeAll()
        var parameter = [String:String]()
        
        if self.strIamfrom == "mentee" {
            parameter["mentee_id"] = "\(id)"
        } else { //Mentor or Staff
            parameter["staff_id"] = "\(id)"
        }
        
        MentorApiManager().mentee_chat(parameter: parameter, chatBy: chatBy, pageList: self.pageList) { (json) in
            self.stopActivityIndicator()
            print("chat called-------",json,self.chatBy)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
                self.chatCode = dicData["chat_code"] as! String
                self.totalChat = dicData["count_message"] as? Int ?? 0
                print("total chat",self.totalChat ?? 0)
                self.deviceType = dicData["device_type"] as? String ?? ""
                self.timezone = dicData["timezone"] as? String ?? ""
                print("time zone",self.timezone)
               
                
                if self.isLoadMoreList {
                    
                    self.copyArray = self.arrChatMessages
                    self.tempChatMessages = dicData["threads"] as? [[String : Any]] ?? []
                    for item in self.tempChatMessages.reversed() {
                        self.tempanotherArray.append(item)
                        print("item",item)
                    }
                    self.arrChatMessages.removeAll()
                    self.arrChatMessages = self.tempanotherArray + self.copyArray
                    if self.arrChatMessages.count > 0 {
                    self.UI {
                        self.tblChatMessage.reloadData()
                    }
                    }
                }
                else {
                    
                    self.tempArray = dicData["threads"] as? [[String : Any]] ?? []
                    self.arrChatMessages = self.tempArray.reversed()//dicData["threads"] as? [[String : Any]] ?? []
                    
                    self.socketMethods()
                    
                    if self.arrChatMessages.count > 0 {
                        self.UI {
                          //  self.tblChatMessage.scrollToBottom()
                            self.tblChatMessage.reloadData()
                        }
                    }
                }
                
                self.isLoadMoreList = false
                
                
                
                
                
            } else {
                self.UI {
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    if loginMode == "Mentor" {
                        self.logOutMentor()
                    }
                    else {
                        self.logOutMentee()
                    }
                    /*
                    Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                }
            }
        }
    }
    
    
    
    
    
    func getChatMessagesMenteeWithMentor(id:String) {
        
        self.startActivityIndicator()
       // self.arrChatMessages.removeAll()
        var parameter = [String:String]()
        parameter["mentor_id"] = id
        
        MentorApiManager().mentee_chat(parameter: parameter, chatBy: chatBy, pageList: self.pageList) { (json) in
            self.stopActivityIndicator()
            print("chat called",json,self.chatBy)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
                self.totalChat = dicData["count_message"] as? Int ?? 0
                print("total chat",self.totalChat ?? 0)
                self.chatCode = dicData["chat_code"] as! String
                self.deviceType = dicData["device_type"] as? String ?? ""
                self.timezone = dicData["timezone"] as? String ?? ""
                print("time zone",self.timezone)
                
                
                
                if self.isLoadMoreList {
                    
                    self.copyArray = self.arrChatMessages
                    self.tempChatMessages = dicData["threads"] as? [[String : Any]] ?? []
                    for item in self.tempChatMessages.reversed() {
                        self.tempanotherArray.append(item)
                        print("item",item)
                    }
                    self.arrChatMessages.removeAll()
                    self.arrChatMessages = self.tempanotherArray + self.copyArray
                    if self.arrChatMessages.count > 0 {
                    self.UI {
                        self.tblChatMessage.reloadData()
                    }
                    }
                }
                else {
                    self.tempArray = dicData["threads"] as? [[String : Any]] ?? []
                    self.arrChatMessages = self.tempArray.reversed()//dicData["threads"] as? [[String : Any]] ?? []
                    
                    self.socketMethods()
                    
                    if self.arrChatMessages.count > 0 {
                        self.UI {
                          //  self.tblChatMessage.scrollToBottom()
                            self.tblChatMessage.reloadData()
                        }
                    }
                }
                
                self.isLoadMoreList = false
                
                
            } else {
                self.UI {
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    if loginMode == "Mentor" {
                        self.logOutMentor()
                    }
                    else {
                        self.logOutMentee()
                    }
                    /*
                    Common().showAlertView(title: "", msg: (json["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                }
            }
        }
    }
    
    
    func logOutMentor(){
            if self.connectedToNetwork() {
                let token  = UserDefaults.standard.string(forKey: "mentorToken")!
                
                let headers = [
                    "Authorizations": token,
                    "Content-Type": "application/x-www-form-urlencoded"
                ]
                print(headers)
                

                let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.mentorLogOut)
                print("MENTEELISTURL\(url)")
                Alamofire.request(url, method:.post, parameters: nil , headers: headers).responseJSON { response in
                    switch response.result {
                    case .success:
                        print(response)
                        let dictVal = response.result.value
                        print("dictVal\(String(describing: dictVal))")
                        let dictMain:NSDictionary = dictVal as! NSDictionary
                        let status = dictMain["status"] as? Bool
                        print("Status\(String(describing: status))")
                        if(status == true){
                            DispatchQueue.main.async{
                                UserDefaults.standard.setValue(nil, forKey: "mentorUserDetails")
                                UserDefaults.standard.setValue(nil, forKey: "loginMode")
                                let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
                                let nav = UINavigationController(rootViewController: loginVC)
                                nav.navigationBar.isHidden = true;
                                nav.navigationBar.barStyle = .default
                                appDelegate.window?.rootViewController = nav
                               //print("TakeStockInChildrenConstant.mentorUserData\(TakeStockInChildrenConstant.mentorUserData)")
                                /*
                                DispatchQueue.main.async(execute: { () -> Void in
                                  //  self.stopActivityIndicator()
                                    let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                    alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                        
                                        self.navigationController?.popViewController(animated: true)
                                    }))
                                    self.present(alert, animated: true, completion: nil)
                                })
                                */
                            }
                        } else {
                            //self.stopActivityIndicator()
                            let alert = UIAlertController(title: "Alert", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                            }))
                            self.present(alert, animated: true, completion: nil)
                        }
                    case .failure(let error):
                        print(error)
                       // self.stopActivityIndicator()
                       // self.showAlertAction(withTitle: "Alert", message: "Something is going wrong")
                    }
                }
            } else
            {
               // showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        }
        
        
        func logOutMentee() {
            if self.connectedToNetwork() {
                // self.startActivityIndicator()
                let token  = UserDefaults.standard.string(forKey: "token")!
                
                let headers = [
                    "Authorizations": token,
                    "Content-Type": "application/x-www-form-urlencoded"
                ]
                //let header
                print(headers)
                
                let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeLogOut)
                print("MENTEELISTURL\(url)")
                Alamofire.request(url, method:.get, parameters: nil , headers: headers).responseJSON { response in
                    switch response.result {
                    case .success:
                        print(response)
                        let dictVal = response.result.value
                        print("dictVal\(String(describing: dictVal))")
                        let dictMain:NSDictionary = dictVal as! NSDictionary
                        let status = dictMain["status"] as? Bool
                        print("Status\(String(describing: status))")
                        if(status == true){
                            DispatchQueue.main.async{
                                UserDefaults.standard.setValue(nil, forKey: "userDetails")
                                UserDefaults.standard.setValue(nil, forKey: "loginMode")
                                //print("TakeStockInChildrenConstant.mentorUserData\(TakeStockInChildrenConstant.mentorUserData)")
                                /*
                                let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
                                self.navigationController?.popToViewController(loginVC, animated: true)
                                */
                                /*
                                DispatchQueue.main.async(execute: { () -> Void in
                                    //  self.stopActivityIndicator()
                                    let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                    alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                            
                                        
                                    }))
                                    self.present(alert, animated: true, completion: nil)
                                })
                                */
                                
                                let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "FirstViewController")
                                let nav = UINavigationController(rootViewController: loginVC)
                                nav.navigationBar.isHidden = true;
                                nav.navigationBar.barStyle = .default
                                appDelegate.window?.rootViewController = nav
                                
                            }
                        }else{
                            //self.stopActivityIndicator()
                            let alert = UIAlertController(title: "Alert", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                            }))
                            self.present(alert, animated: true, completion: nil)
                        }
                    case .failure(let error):
                        print(error)
                        // self.stopActivityIndicator()
                        // self.showAlertAction(withTitle: "Alert", message: "Something is going wrong")
                    }
                }
            } else
            {
                // showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        }
    
    
    //MARK:: Socket
    func disconnectSocket() {
        var objData = [String:String]()
        
        objData.updateValue(self.LoginID, forKey: "id")
        self.socket.emit("roomExit", objData)
        
        isSocketConnected = false
        socket.disconnect()
        print("Spcket disconnected")
    }
     //https://tsicmentorapp.org:3700/
    //"http://209.59.156.100:3700/"
    //https://mentorappdev.tsic.org:3700/
    //https://tsicmentorapp.org:3700/
    
    //https://mentorappdev.tsic.org:3700/
    
    //https://tsicmentorapp.org:3700/

    //https://mentorappdev.tsic.org:3700/
    func socketMethods() {
        print("Socket function called")
        self.startActivityIndicator()
        manager = SocketManager(socketURL: URL(string: "https://tsicmentorapp.org:3700/")!, config: [.log(true), .compress])
        socket = manager.defaultSocket
        
        socket.on(clientEvent: .connect) {data, ack in
            print("socket connected")
            self.isSocketConnected = true
            var objData = [String:String]()
            
            objData.updateValue(self.LoginID, forKey: "id")
            objData.updateValue(self.chatCode, forKey: "chat_code")
            print("objdata",objData)
            self.socket.emit("connected", objData)
            self.stopActivityIndicator()
            
        }
        
        
        socket.on("new_participant_joined") { data, ack in
            
            guard var arrset = data[0] as? [String: Any] else {return}
            let id = arrset["id"] as? String ?? ""
            if self.LoginID != id {
                self.isrefresh = true
                self.tblChatMessage.reloadData()
            }
            
            print("new participant joinded data",data,ack)
        }
        
        socket.on("receiveMessage") {data, ack in
            print("Data received")
            
            guard var objArr = data[0] as? [String: Any] else {return}
            print("Socket Received Data:: \(objArr)")
            

           // var objData = [String:String]()
           // objData.updateValue("utc", forKey: "utc")

            objArr.updateValue("utc", forKey: "utc")
            
//            let isRead = objArr["is_read_to_id"]! as? NSNumber
//
//            objData.updateValue(objArr["seller_id"]! as! String, forKey: "seller_id")
//            objData.updateValue(objArr["buyer_id"]! as! String, forKey: "buyer_id")
//            objData.updateValue(objArr["from_where"]! as! String, forKey: "from_where")
//            objData.updateValue(objArr["message"]! as! String, forKey: "message")
//            objData.updateValue(objArr["device_token"]! as! String, forKey: "device_token")
//            objData.updateValue(objArr["sender_userid"]! as! String, forKey: "sender_userid")
//            objData.updateValue(String(describing: isRead!), forKey: "is_read_to_id")
//            objData.updateValue(objArr["created_at"]! as! String, forKey: "created_at")
//
//            print(objData)

            if self.LoginID != String(describing: objArr["sender_id"]!) {
                self.arrChatMessages.append(objArr)
                self.tblChatMessage.reloadData()
               // self.tblChatMessage.scrollToBottom()
            }
            else {
                self.isFromSocket = true
                self.arrChatMessages.append(objArr)
                self.tblChatMessage.reloadData()
              //  self.tblChatMessage.scrollToBottom()
                
                //MARK: --> OFF CODE FOR 13 August 2020 06:55 pm self.loginUserTypeCheck() add append and reload date and scrollbottom
                
                //self.loginUserTypeCheck()
            }
        }
        socket.connect()
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    
    
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        print("textfield did end editing")
        removeTextViewFromScreen()
    }
    
    // MARK: - TextView Delegates
    func textViewDidBeginEditing(_ textView: UITextView) {
        if textView.text == "Enter your message" {
            textView.text = ""
        }
    }
    
    func textViewDidChange(_ textView: UITextView) {
        var height = 0
        if textView.contentSize.height > textView.frame.size.height {
            height = Int(constraintVwChatHeight.constant + 20)
            
            constraintVwChatHeight.constant = CGFloat(height > maxHeightChat ? maxHeightChat : height)
            self.vwChatTextContainer.layoutIfNeeded()
           // self.tblChatMessage.scrollToBottom()
        } else if textView.contentSize.height < textView.frame.size.height - 20 {
            height = Int(constraintVwChatHeight.constant - 20)
            
            constraintVwChatHeight.constant = CGFloat(height < minHeightChat ? minHeightChat : height)
            self.vwChatTextContainer.layoutIfNeeded()
          //  self.tblChatMessage.scrollToBottom()
        } else {
            
        }
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        removeTextViewFromScreen()
       // self.tblChatMessage.scrollToBottom()
    }
    
    
    
    //MARK:: UITableViewDataSource
    //number of rows in table view
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrChatMessages.count
    }
    
    //create a cell for each table view row
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        var cell = ChatTableViewCell()
        
        if arrChatMessages.count > 0 {
            //create a new cell if needed or reuse an old one
            let dictChatData = arrChatMessages[indexPath.row] as Dictionary
          
            print(dictChatData)
            let sender_userid  = String(describing: dictChatData["sender_id"]!)
            
            if sender_userid == self.LoginID {
                cell = (tableView.dequeueReusableCell(withIdentifier: "User", for: indexPath) as! ChatTableViewCell)
                /*
                if self.valueMode == "dark" {
                    cell.lblUser.textColor = .black
                }
                */
                
                let trimmedStr = (dictChatData["message"] as? String)?.trimmingCharacters(in: .whitespaces)//whitespacesAndNewlines
    
                cell.lblUser.text = dictChatData["message"] as? String ?? ""//trimmedStr
                //print("trimmedStr from user:: \(String(describing: trimmedStr))")
            //.sd_setImage(with: URL(string: self.loginUserProPicURL), placeholderImage: UIImage(named: "ChatUserImg"))
                
                let seentag = dictChatData["receiver_is_read"] as? Int ?? 0
                print("seentag",seentag)
                let isSeen = String(describing: dictChatData["receiver_is_read"]!)
                print("seen data",isSeen)
                if seentag == 1 || isrefresh {
                    //cell.seenBtn.setImage(UIImage(named: "MessageSeen"), for: .normal)
                    print("msg seen")
                    cell.imgSeen.image = UIImage(named: "MessageSeen")
                }
                else if seentag == 0 {
                    print("msg unseen")
                //    cell.seenBtn.setImage(UIImage(named: "MessageUnSeen"), for: .normal)
                    cell.imgSeen.image = UIImage(named: "MessageUnSeen")
                }
                else {
                    print("not set")
                }
            } else {
                cell = (tableView.dequeueReusableCell(withIdentifier: "Other", for: indexPath) as! ChatTableViewCell)
                let trimmedStr = (dictChatData["message"] as? String)?.trimmingCharacters(in: .whitespaces)
                cell.lblOther.text = dictChatData["message"] as? String ?? ""//trimmedStr
                cell.imgOther.layer.cornerRadius = cell.imgOther.frame.size.width/2
                cell.imgOther.clipsToBounds = true
                cell.imgOther.image = imgViewProfile.image
                receiver_id  = String(describing: dictChatData["receiver_id"]!)
            }
            
            let createdTime = String(describing: dictChatData["created_date"]!).components(separatedBy: " ")

            let date = Date()
            let formatter = DateFormatter()
            formatter.dateFormat = "MM-dd-yyyy"   //"MM-dd-yyyy"
            let result = formatter.string(from: date)
            if createdTime[0] == String(describing: result) {
                
                if (dictChatData["utc"] as? String) != nil{
                    self.sendMessaage = true

                     cell.lblTime.text = self.getTime(strTime: String(describing: dictChatData["created_date"]!))
                }else{
                    self.sendMessaage = false

                     cell.lblTime.text = self.getTime(strTime: String(describing: dictChatData["created_date"]!))
                }

               
                
            } else {
                cell.lblTime.text = self.getDate(strDate: String(describing: dictChatData["created_date"]!))
            }
        }
        
        cell.selectionStyle = .none
        return cell
    }
    
    func tableView(_ tableView: UITableView, canEditRowAt indexPath: IndexPath) -> Bool {
        return false
    }
    
    // MARK: - Tableview Delegates
    /*
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        var height : CGFloat = 0.0
        
        if arrChatMessages.count > 0 {
            let dictChatData = arrChatMessages[indexPath.row] as Dictionary
            let lblText = UILabel()
            lblText.numberOfLines = 0
            lblText.lineBreakMode = .byWordWrapping//.byWordWrapping
            let trimmedStr = (dictChatData["message"] as? String)?.trimmingCharacters(in: .whitespaces)//replacingOccurrences(of: "^\\s*+$", with: "", options: .regularExpression)// (dictChatData["message"] as? String)?.replacingOccurrences(of: "\\s+$", with: "", options: .regularExpression)
            lblText.text = trimmedStr
            
            let attributedText = NSAttributedString(string:trimmedStr!, attributes:  [NSAttributedString.Key.font : lblText.font!])
            
            //print("Text messages :: \(String(describing: dictChatData["message"]))")
            let widthText = (self.view.frame.size.width-180)
            //print("widthText :: \(widthText)")
            
            let rect = attributedText.boundingRect(with: CGSize(width: widthText, height: CGFloat.greatestFiniteMagnitude), options: .usesLineFragmentOrigin, context: nil)
            print(rect.size.height)
            height = (rect.size.height + 40) > 65 ? (rect.size.height + 40) : 65
        }
        
        return ceil(height+20)
    
    }
    */
    func scrollTableViewToBottom() {
        if (self.tblChatMessage.contentSize.height > self.tblChatMessage.frame.size.height) {
            let offset = CGPoint(x: 0, y: self.tblChatMessage.contentSize.height - self.tblChatMessage.frame.size.height) as CGPoint
            self.tblChatMessage.setContentOffset(offset, animated: true)
        }
    }
    
     func getDate(strDate: String) -> String {
        let formatter = DateFormatter()
        formatter.dateFormat = "MM-dd-yyyy HH:mm:ss"
        
        let dateDeadline = formatter.date(from: strDate)
        formatter.dateFormat = "MM-dd-yyyy"
        let strDateDeadline = formatter.string(from: dateDeadline!)
        return strDateDeadline
    }
    
    func getTime(strTime: String) -> String {
        print(strTime)
        
       // if sendMessaage {
            let formatter = DateFormatter()
            formatter.dateFormat = "MM-dd-yyyy HH:mm:ss"
            let dateDeadline = formatter.date(from: strTime)

            formatter.dateFormat = "hh:mm a"
            let strDateDeadline = formatter.string(from: dateDeadline!)
            return strDateDeadline
       // }

        // create dateFormatter with UTC time format
//        let dateFormatter = DateFormatter()
//        dateFormatter.dateFormat = "MM-dd-yyyy HH:mm:ss"
//        dateFormatter.timeZone = NSTimeZone(name: "UTC") as TimeZone?
//        let date = dateFormatter.date(from: strTime)
//
//        // change to a readable time format and change to local time zone
//        dateFormatter.dateFormat = "hh:mm a"
//        dateFormatter.timeZone = NSTimeZone.local
//        let strDateDeadline = dateFormatter.string(from: date!)
//        print(strDateDeadline)
//         return strDateDeadline
    }
    
    //MARK:: Function Notification
    @objc func keyboardDidShow (notification:NSNotification) {
        if let keyboardFrame: NSValue = notification.userInfo?[UIResponder.keyboardFrameEndUserInfoKey] as? NSValue {
            let keyboardRectangle = keyboardFrame.cgRectValue
            let keyboardHeight = keyboardRectangle.height
            
            constraintViewChatBottom.constant = keyboardHeight
            //let tabBarHeight = self.tabBarController?.tabBar.frame.size.height
            //constraintViewChatBottom.constant = keyboardHeight - tabBarHeight!
            self.vwChatTextContainer.layoutIfNeeded()
            //self.tblChatMessage.scrollToBottom()
        }
    }
    
    @objc func keyboardWillBeHidden (notification:NSNotification) {
        constraintViewChatBottom.constant = 0;
    }
    
    /*@objc func tableReload (notification:NSNotification) {
        self.getChatMessages()
    }*/
    
    func displayTextViewOnScreen() {
        txtViewChat.becomeFirstResponder()
    }
    
    func removeTextViewFromScreen() {
        if txtViewChat.text == "" {
            txtViewChat.text = "Enter your message"
        } else {
            txtViewChat.resignFirstResponder()
        }
        
        constraintVwChatHeight.constant = 60
        //[SLAnimUtil animEffect:SLA_EFFECT_POP_IN view:self.btnChatAreaDisplay time:0.5];
        let totalRows = 0 //[_session listAllMessages].count -1 as Int
        
        if (totalRows > 0) {
            self.tblChatMessage.setContentOffset(CGPoint(x: 0, y: CGFloat.greatestFiniteMagnitude), animated: true)
        }
    }
    
    var userType = ""
    
    @IBAction func btnSubmitChatAction(_ sender: Any) {
        if txtViewChat.text == "Enter your message" {
            
            self.showAlertView(title: "Sorry", msg: "Can't send empty message", controller: self) {}
        } else {
            if validate(textView: txtViewChat) {
                if self.connectedToNetwork() {
                    if self.isSocketConnected {
                        //do something
                        
                        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                        
                        if loginMode=="Mentor" {
                            if chatBy == "staff" {
                                userType = "staff"
                            } else {
                                userType = "mentee"
                            }
                             fromWhereMessageSend = "mentor"
                        } else {
                            if chatBy == "menteestaff" {
                                  userType = "menteestaff"
                            } else {
                                userType = "mentee"
                            }
                             fromWhereMessageSend = "mentee"
                        }

                
                        let date = Date()
                        let formatter = DateFormatter()
                        formatter.dateFormat = "MM-dd-yyyy HH:mm:ss" //"yyyy-MM-dd HH:mm:ss"
                        let result = formatter.string(from: date)
                        
                        print("fromWhereMessageSend==\(String(describing: fromWhereMessageSend))")
                        
                        var msgObj = [String:Any]()
                        msgObj.updateValue(self.chatCode, forKey: "chat_code")
                        
                        let loginMode2 = UserDefaults.standard.value(forKey: "loginMode") as! String
                        
                        if loginMode2=="Mentor" {
                            msgObj.updateValue(self.senderIDForStaff, forKey: "receiver_id")
                        } else {
                            if chatBy == "menteestaff" {
                                userType = "menteestaff"
                                msgObj.updateValue(self.senderIDForStaff, forKey: "receiver_id")
                            } else {
                                userType = "mentee"
                                msgObj.updateValue(self.senderIDForMentor, forKey: "receiver_id")
                            }
                        }
                     
                        msgObj.updateValue(self.LoginID, forKey: "sender_id")
                        msgObj.updateValue(self.sender_Name, forKey: "sender_name")
                        msgObj.updateValue(self.lblUserName.text!, forKey: "receiver_name")
                        msgObj.updateValue(txtViewChat.text!, forKey: "message")
                        msgObj.updateValue(fromWhereMessageSend!, forKey: "from_where")
                        msgObj.updateValue(userType, forKey: "type")
                        msgObj.updateValue("0", forKey: "receiver_is_read")
                        msgObj.updateValue(firebaseKeyReceiver!, forKey: "device_token")
                        msgObj.updateValue(result, forKey: "created_date")
                        msgObj.updateValue(self.timezone, forKey: "time_zone")
                        msgObj.updateValue(self.deviceType, forKey: "device_type")

//                        msgObj.updateValue(product_Name!, forKey: "product_name")
//                        msgObj.updateValue(firebaseKeyReceiver!, forKey: "device_token")
//                        msgObj.updateValue(Log_User_ID!, forKey: "sender_userid")
//                        msgObj.updateValue(sender_Name!, forKey: "sender_name")
//                        msgObj.updateValue("0", forKey: "is_read_to_id")
//                        msgObj.updateValue(result, forKey: "created_at")
                         print("msgObj==\(msgObj)")

                        socket.emit("sendMessage", msgObj)
                        self.isrefresh = false
                        
                        //MARK: --> OFF CODE FOR 13 August 2020 06:55 pm self.loginUserTypeCheck() add dispatch aync after
                        
                     //   self.loginUserTypeCheck()
                        // 3.34 - 6.33 =
                        //self.isSend = true
                        msgObj.updateValue("utc", forKey: "utc")
                     //   self.arrChatMessages.append(msgObj)
                        print("self.arrChatMessages :: \(self.arrChatMessages)")
                    //    self.tblChatMessage.scrollToBottom()
                     //  self.tblChatMessage.reloadData()
                       
                        
                        self.txtViewChat.text = ""
                       DispatchQueue.main.asyncAfter(deadline: .now() + 0.2) {
                        self.btnChat.isUserInteractionEnabled = true
                       }

                    } else {
                        self.showAlertView(title: "Error", msg: "There were some problems connecting to the server, Please try again later", controller: self) {
                        }
                       
                    }
                } else {
                    self.showAlertView(title: "Error", msg: "No connection to Internet", controller: self) {
                    }
                    
                }
            } else {
                // do something else
                self.showAlertView(title: "Sorry", msg: "Can't send empty message", controller: self) {
                }
            }
        }
    }
    
    @IBAction func btnChatDisplayAction(_ sender: UIButton) {
        if (sender.tag == 1) {
            displayTextViewOnScreen()
        } else {
            removeTextViewFromScreen()
        }
    }
    
    
    func validate(textView: UITextView) -> Bool {
        guard let text = textView.text,
            !text.trimmingCharacters(in: CharacterSet.whitespacesAndNewlines).isEmpty else {

                return false
        }
        return true
    }
}

extension UITableView {
    func scrollToBottom() {
        DispatchQueue.main.async {
            let indexPath = IndexPath(
                row: self.numberOfRows(inSection: self.numberOfSections-1 ) - 1,
                section: self.numberOfSections - 1)
            if indexPath.row > 0 {
                self.scrollToRow(at: indexPath, at: .bottom, animated: true)
            }
        }
    }
}

/*
 
 
 */


extension ChatVC: UIScrollViewDelegate {
    
    // MARK:- UIScrollViewDelegate
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        if scrollView == self.tblChatMessage {
            // calculates where the user is in the y-axis
            let offsetY = scrollView.contentOffset.y
            print("offsetY",offsetY)
            let contentHeight = scrollView.contentSize.height
            print("height",scrollView.frame.size.height)
            if offsetY > 0 && (offsetY >= (contentHeight - scrollView.frame.size.height)) {
                print("offset checking")
                if self.arrChatMessages.count > 0 && self.totalChat ?? 0 > self.arrChatMessages.count
                    && !self.isLoadMoreList {
                    self.pageList += 1
                    self.isLoadMoreList = true
                    if self.temploginMode == "Mentor" {
                        self.getChatMessages(id: loginMenteeButMentorDicData["id"] as! Int)
                       // self.getChatMessagesPaginate(id: loginMenteeButMentorDicData["id"] as! Int)
                    }
                    else {
                        if self.strIamfrom == "staff" {
                            print("staff chat")
                            self.getChatMessages(id: loginMenteeButMentorDicData["id"] as! Int)
                           // self.getChatMessagesPaginate(id: loginMenteeButMentorDicData["id"] as! Int)
                            
                        } else {
                            print("mentee data load")
                            self.getChatMessagesMenteeWithMentor(id: senderIDForMentor)
                           // self.getChatMessagesMenteeWithMentorPaginate(id: senderIDForMentor)
                        }
                    }
                    
                    
                }
                else
                {
                    print("No tab")
                }
            }
        }
    }
}
