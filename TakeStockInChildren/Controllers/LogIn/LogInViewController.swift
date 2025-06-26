//
//  LogInViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 08/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
//import Crashlytics
import ShimmerSwift
import SocketIO
import FirebaseMessaging

class LogInViewController: BaseViewController {
    @IBOutlet weak var msgViewbackend: UIView!
    @IBOutlet weak var msgLable: UILabel!
    @IBOutlet weak var welcomeLabel: UILabel!
    @IBOutlet weak var tolabel: UILabel!
    @IBOutlet weak var checkBtn: UIButton!
    @IBOutlet weak var backgroundImage: UIImageView!
    
    @IBOutlet weak var chatmsgLabel: UILabel!
    @IBOutlet weak var secondView: UIView!
    @IBOutlet weak var firstview: UIView!
    @IBOutlet weak var topConstraintViewEmail: NSLayoutConstraint!
    @IBOutlet weak var textFieldEmailId: UITextField!
    @IBOutlet weak var textFieldPassword: UITextField!
    @IBOutlet weak var buttonLogIn: UIButton!
    
    
    @IBOutlet weak var lblEdate: UILabel!
    @IBOutlet weak var lblSdate: UILabel!
    var iconClick = true
    var loginMode : String = ""
    var isType : String = ""
    var fromResetPassword : Bool = false
    var valueMode : String?
    var Messages = [[String:Any]]()
    var checkSelected: Bool = false
    var disclaimerid:Int?
    var Urlredirect: String?
    var socket: SocketIOClient!
    var manager: SocketManager!
    var isSocketConnected: Bool = false
    var senderId:Int = 0
    var senderType = ""
    var emailValue = ""
    override func viewDidLoad() {
        super.viewDidLoad()
        self.checkBtn.layer.cornerRadius = 2.0
        self.checkBtn.backgroundColor = .white
        self.checkBtn.layer.borderColor = UIColor.black.cgColor
        self.checkBtn.layer.borderWidth = 1.0
        getDisclaimermessage()
        //Crashlytics.sharedInstance().crash()
         manageAutoLayOut()
        textFieldEmailId.text = emailValue
        textFieldEmailId.isEnabled = false
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodeChnage()
        // Do any additional setup after loading the view.
        
      //  msgViewbackend.contentView = msgLable
      //  msgViewbackend.isShimmering = true
        
        if loginMode == "Mentor" {
            getMessageMentor()
            buttonLogIn.setTitle("Log In", for: UIControl.State.normal)
        } else {
            getmenteemessage()
            buttonLogIn.setTitle("Log In", for: UIControl.State.normal)
        }
        
        let tap = UITapGestureRecognizer(target: self, action: #selector(doubleTapped))
        tap.numberOfTapsRequired = 1
        chatmsgLabel.addGestureRecognizer(tap)
        chatmsgLabel.isUserInteractionEnabled = true

        
       // navigationController?.interactivePopGestureRecognizer?.isEnabled = false
        Messaging.messaging().token { [weak self] token, error in
            if let error = error {
                print("Error fetching FCM registration token: \(error)")
            } else if let token = token {
                UserDefaults.standard.set(token, forKey: "firebase_token")
            }
        }
        
//        if let email = UserDefaults.standard.string(forKey: "username"), let password = UserDefaults.standard.string(forKey: "password"), UserDefaults.standard.bool(forKey: "isFaceIdEnabled") == true {
//            BioMetricAuthentication.authenticateBiometrics(controller: self) { [weak self] in
//                self?.textFieldEmailId.text = email
//                self?.textFieldPassword.text = password
//            }
//        }
    }
    
    
    
    @objc func doubleTapped() {
        print("tap")
        if let url = URL(string: self.Urlredirect ?? "") {
            UIApplication.shared.open(url)
        }
    }
    
    
    @IBAction func checkBtnAction(_ sender: Any) {
        print("selcted------")
        if !checkSelected {
            checkBtn.setImage(UIImage(named: "checkmark"), for: .normal)
        } else {
            checkBtn.setImage(UIImage(named: ""), for: .normal)
        }
        self.checkSelected = !checkSelected
    }
    
    
    func getDisclaimermessage() {
        MentorApiManager().disclaimerApi() { (json) in
            print("access json-------",json)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
                let disclaimdata = dicData["disclamer"] as! NSDictionary
                self.disclaimerid = disclaimdata["id"] as? Int ?? 0
                var statement = disclaimdata["statement"] as? String ?? ""
                self.Urlredirect = disclaimdata["url"] as? String ?? ""
                print("disclaimer id",self.disclaimerid)
                
                DispatchQueue.main.async {
                    let str = "Click here"
                    let fulldes = "\(statement) \(str)"
                    let multiplierAttributedString: NSMutableAttributedString = NSMutableAttributedString(string: fulldes)
                    multiplierAttributedString.setColor(color: UIColor(hexString: "ef6c00"), forText: statement)
                    // multiplierAttributedString.setColor(color: UIColor(hexString:"0000FF"), forText: str)
                    multiplierAttributedString.setLink(forText: str)
                    self.chatmsgLabel.attributedText = multiplierAttributedString
                    
                    
                    //  self.chatmsgLabel.text = statement
                    
                }
                
            } else {
                print("error")
            }
        }
        
    }
    
    func getmenteemessage() {
        MentorApiManager().menteehomemessageApi() { (json) in
            print("access json-------",json)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
                self.Messages = dicData["messaging"] as? [[String : Any]] ?? []
                let dic = self.Messages.first
                let messageText = dic?["message"] as? String ?? ""
                let sdate = dic?["start_datetime"] as? String ?? ""
                let edate = dic?["end_datetime"] as? String ?? ""
                
                DispatchQueue.main.async {
                    if messageText == "" {
                        self.msgViewbackend.isHidden = true
                    }
                    //self.messagelabelMentee.pushTransition(0.4)
                    
                    self.msgLable.text = messageText
                    // self.lblSdate.text = sdate
                    // self.lblEdate.text = edate
                }
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
                print("error")
            }
        }
    }
    
    func getMessageMentor() {
        MentorApiManager().mentorhomemessageApi() { (json) in
            print("access json-------",json)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
                self.Messages = dicData["messaging"] as? [[String : Any]] ?? []
                let dic = self.Messages.first
                let messageText = dic?["message"] as? String ?? ""
                let sdate = dic?["start_datetime"] as? String ?? ""
                let edate = dic?["end_datetime"] as? String ?? ""
               
                DispatchQueue.main.async {
                    if messageText == "" {
                        self.msgViewbackend.isHidden = true
                    }
                   // self.messageLabelMentor.startAnimating()
                    self.msgLable.text = messageText
                    self.lblSdate.text = sdate
                    self.lblEdate.text = edate
                }
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
                print("error")
            }
        }
        
        
    }
    
    
    
    
    
    func darkmodeChnage() {
        if self.valueMode == "dark" {
            backgroundImage.image = UIImage(named: "BG4")
            welcomeLabel.textColor = .white
            tolabel.textColor = .white
            firstview.backgroundColor = UIColor(hex: "#0E0F27")
            secondView.backgroundColor = UIColor(hex: "#0E0F27")
            
            textFieldEmailId.attributedPlaceholder = NSAttributedString(string: "Email",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldEmailId.textColor = UIColor(named: "MainTextColor")
            
            textFieldPassword.attributedPlaceholder = NSAttributedString(string: "Password",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldPassword.textColor = UIColor(named: "MainTextColor")
        }
        else if self.valueMode == "light" {
            backgroundImage.image = UIImage(named: "BackgroundImage")
            welcomeLabel.textColor = .black
            tolabel.textColor = .black
            firstview.backgroundColor = .white
            secondView.backgroundColor = .white
            
            
            textFieldEmailId.attributedPlaceholder = NSAttributedString(string: "Email",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldEmailId.textColor = .black
            
            textFieldPassword.attributedPlaceholder = NSAttributedString(string: "Password",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldPassword.textColor = .black
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
    }
    
    //    override func viewWillDisappear(_ animated: Bool) {
    //        super.viewWillDisappear(animated)
    //        dismiss(animated: true, completion: nil)
    //    }
    
    //MARK:-Manage Autolayout
    func manageAutoLayOut(){
        switch UIScreen.main.bounds.height{
        case 480:
            topConstraintViewEmail.constant = 50
        case 568:
            topConstraintViewEmail.constant = 50
        case 667:
            topConstraintViewEmail.constant = 50
        case 736:
            topConstraintViewEmail.constant = 70
        case 812:
            topConstraintViewEmail.constant = 120
        case 896:
            topConstraintViewEmail.constant = 120
        default:
            topConstraintViewEmail.constant = 50
        }
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        if(fromResetPassword == true){
            let appDelegate = UIApplication.shared.delegate as? AppDelegate
            let loginController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
            let nav = UINavigationController(rootViewController: loginController)
            nav.navigationBar.isHidden = true;
            appDelegate?.window!.rootViewController = nav
        }else{
            self.navigationController?.popViewController(animated: true)
        }
    }
    
    //MARK:- Button Action
    @IBAction func buttonHideUnhideAction(_ sender: Any) {
        if(iconClick == true) {
            textFieldPassword.isSecureTextEntry = false
        } else {
            textFieldPassword.isSecureTextEntry = true
        }
        iconClick = !iconClick
    }
    
    @IBAction func buttonLogInAction(_ sender: Any) {
        var voip_token = UserDefaults.standard.value(forKey: "voip_token")
        if(voip_token == nil) {
            voip_token = ""
        }
        let email = self.textFieldEmailId.text
        let password = self.textFieldPassword.text
        let trimmedPassword = password?.trim()
        
        // Checking Validation of the text fields
        if(textFieldEmailId.text == ""){
            showAlert(_sourceController: self, _msg: "Error: You must provide an email address.Please try again with a valid email address. To validate your email address, please contact your local Take Stock in Children program.")
        }
        else if(trimmedPassword == ""){
            showAlert(_sourceController: self, _msg: AlertMessage.login_blankPassword)
        }
        else if !isValidEmail(emailStr: email!) {
            showAlert(_sourceController: self, _msg: AlertMessage.login_InvalidEmail)
        }
        else if (trimmedPassword?.count)! < 6 {
            showAlert(_sourceController: self, _msg: AlertMessage.login_passwordMinimumLength)
        }
        else if !checkSelected {
            showAlert(_sourceController: self, _msg: AlertMessage.msgDisclaimer)
        }
        else {
            
            //let fkey = (UserDefaults.standard.value(forKey: "firebase_token") != nil) ?  UserDefaults.standard.value(forKey: "firebase_token")! : "zhdfggasthfhesytf764rtgycjt56tgryt4gycv648c"
        
            
            if self.connectedToNetwork() {
                let fcmToken = UserDefaults.standard.value(forKey: "firebase_token")
                var voip_token = UserDefaults.standard.value(forKey: "voip_token")
                if(voip_token == nil) {
                    voip_token = ""
                }
                
                ///Unified SignIn
                let userDetails:NSMutableDictionary = [
                    "email" : email!,
                    "password" : trimmedPassword!,
                    "latitude" : "",//UserDefaults.standard.data(forKey: "latitude")!,
                    "longitude" : "",//UserDefaults.standard.data(forKey: "longitude")!,
                    "firebase_id" : fcmToken ?? "",
                    "voip_device_token": voip_token!,
                    "device_type": "iOS",
                    "waiver_statement_id": self.disclaimerid
                ]
                if(loginMode == "Mentee") {
                    //TODO: Mentee Login
                    
                    print("Mentee loginmode\(userDetails)")
                    self.startActivityIndicator()
                    ApiManager.sharedInstance.logIn(postId: 1, userDetails: userDetails, onSuccess: { json in
                        DispatchQueue.main.async {
                            self.stopActivityIndicator()
                            let jsonDic : NSDictionary = (json["data"] as? NSDictionary)!
                            print("JsonDic\(jsonDic)")
                            self.stopActivityIndicator()
                            print("Login json :: \(String(describing: jsonDic["token"]))")
                            UserDefaults.standard.set(jsonDic["token"] as? String, forKey: "token")
                            guard let loginUserDetails = jsonDic["user_details"] as? NSDictionary else {
                                return
                            }
                            
                            UserDefaults.standard.set(nil, forKey: "mentorUserDetails")
                            UserDefaults.standard.set(loginUserDetails, forKey: "userDetails")
                            
                            UserDefaults.standard.set(email!, forKey: "username")
                            UserDefaults.standard.set(trimmedPassword!, forKey: "password")
                       
                            UserDefaults.standard.set("Mentee",forKey: "loginMode")

//                            self.senderId = loginUserDetails.object(forKey: "id") as! Int
//                            self.senderType = "Mentee"
//                          self.videoSocket()
//                            self.socket.on("reqReceived") { data, ack in
//                                print("request received data=============================================",data,ack)
//                            }
                            DispatchQueue.main.async(execute: {() -> Void in
                                
                                let homeController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "TabBarViewController")
                                let appDelegate = UIApplication.shared.delegate as! AppDelegate
                                appDelegate.window?.rootViewController = homeController
                                
                                print("navigate")
                            })
                        }
                    }, onFailure: { error in
                        DispatchQueue.main.sync(execute: {() -> Void in
                            self.stopActivityIndicator()
                            
                            let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                            alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                            self.present(alert, animated: true, completion: nil)
                        })
                    })
                }
                else {
                    //TODO: Mentor Login
                    self.startActivityIndicator()
                    
                    print("Mentor loginmode\(userDetails)")
                    ApiManager.sharedInstance.unifiedLogIn(postId: 1, userDetails: userDetails, onSuccess: { json in
                        DispatchQueue.main.async {
                            self.stopActivityIndicator()
                            print("json\(json)")
                            let mentorJsonDic : NSDictionary = (json["data"] as? NSDictionary)!
                            
                            //print("MentorJsonDic\(mentorJsonDic)")
                            print("Login json ::>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> \(String(describing: mentorJsonDic["token"])) \(String(describing: mentorJsonDic["user_type"]))")
                            if mentorJsonDic["user_type"] as? String == "mentor"{
                            UserDefaults.standard.set(mentorJsonDic["token"] as? String, forKey: "mentorToken")
                            }
                            else{
                                UserDefaults.standard.set(mentorJsonDic["token"] as? String, forKey: "token")
                            }
                            
                            guard let loginUserDetails = mentorJsonDic["user_details"] as? NSDictionary else {
                                return
                            }
//                            self.senderId = loginUserDetails.object(forKey: "id") as! Int
//                           36+ self.senderType = "Mentor"
//                            self.videoSocket()
//                            self.socket.on("reqReceived") { data, ack in
//                                print("request received data=============================================",data,ack)
//                            }
                            
                            UserDefaults.standard.set(email!, forKey: "username")
                            UserDefaults.standard.set(trimmedPassword!, forKey: "password")
                            
                            if mentorJsonDic["user_type"] as? String == "mentor" {
                                print("i am mentor")

                                UserDefaults.standard.set(loginUserDetails, forKey: "mentorUserDetails")
                                UserDefaults.standard.set(nil, forKey: "userDetails")
                                UserDefaults.standard.set("Mentor",forKey: "loginMode")
                                DispatchQueue.main.async(execute: {() -> Void in
                                    
                                    let homeController = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorTabBarViewController")
                                    let appDelegate = UIApplication.shared.delegate as! AppDelegate
                                    appDelegate.window?.rootViewController = homeController
                                    
                                    print("navigate")
                                    //                                let homeController = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorProfileViewController")
                                    //                                let appDelegate = UIApplication.shared.delegate as! AppDelegate
                                    //                                appDelegate.window?.rootViewController = homeController
                                    
                                })
                            }
                            else if mentorJsonDic["user_type"] as? String == "mentee" {
                                print("i am mentee")
                                UserDefaults.standard.set(nil, forKey: "mentorUserDetails")
                                UserDefaults.standard.set(loginUserDetails, forKey: "userDetails")
                                UserDefaults.standard.set("Mentee",forKey: "loginMode")
                                
                                DispatchQueue.main.async(execute: {() -> Void in
                                    
                                    let homeController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "TabBarViewController")
                                    let appDelegate = UIApplication.shared.delegate as! AppDelegate
                                    appDelegate.window?.rootViewController = homeController
                                    
                                    print("navigate")
                                })
                            }
                            
                            
                            // }
                            //                        alert.addAction(acceptAction)
                            //                        //                    alert.addAction(UIAlertAction(title: "Ok", style: UIAlertActionStyle.default, handler: nil))
                            //                        self.present(alert, animated: true, completion: nil)
                            
                            //UserDefaults.standard.setValue(json, forKey: "userDetails")
                            //self.appDelegateInbase?.registerFirebaseToken()
                        }
                    }, onFailure: { error in
                        DispatchQueue.main.sync(execute: {() -> Void in
                            self.stopActivityIndicator()
                            let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                            alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                            self.present(alert, animated: true, completion: nil)
                        })
                    })
                }
            }
            else {
                showAlert(_sourceController: self, _msg: "Error: You must be connected to WiFi or Cellular service to use the Take Stock App. Please check your internet connection and try again.")
            }
        }
    }
    func videoSocket() {
        manager = SocketManager(socketURL: URL(string: TakeStockInChildrenConstant.SocketURL)!, config: [.log(false), .compress, .forceWebsockets(true)])
        
        socket = manager.defaultSocket
        socket.on(clientEvent: .connect) {data, ack in
            print("socket connected=============================================")
            self.isSocketConnected = true
            var objData = [String:String]()
            
            objData.updateValue(self.senderType, forKey: "type")
            objData.updateValue(String(self.senderId), forKey: "id")
            print("objdata",objData)
            self.socket.emit("connected",objData)
            print("After socket connect Data via chat================================",data,ack)
          
       }
        
        self.socket.connect()
    }

    @IBAction func buttonSignUpAction(_ sender: Any) {
    }
    
    @IBAction func buttonForgetPasswordAction(_ sender: Any) {
        if(loginMode == "Mentee"){
            //1. Create the alert controller.
            let alert = UIAlertController(title: "Enter Your Registered Email", message: "", preferredStyle: .alert)
            
            //2. Add the text field. You can configure it however you need.
            alert.addTextField { (textField) in
                textField.text = " "
                
            }
            
            // 3. Grab the value from the text field, and print it when the user clicks OK.
            //let emailStr : String = alert.textFields![0].text!
            
            alert.addAction(UIAlertAction(title: "SEND", style: .default, handler: {(action) in self.forgetPasswordApiCalling(emailStr: alert.textFields![0].text!)}))
            alert.addAction(UIAlertAction(title: "Cancel", style: .cancel, handler: nil))
            
            // 4. Present the alert.
            self.present(alert, animated: true, completion: nil)
        }else{
            //showAlertAction(withTitle: "TakeStockInChildren", message: "ForgotPasswordNotImplementedForMentor")
            
            
            let alert = UIAlertController(title: "Enter Your Registered Email", message: "", preferredStyle: .alert)
            
            //2. Add the text field. You can configure it however you need.
            alert.addTextField { (textField) in
                textField.text = " "
                
            }
            
            // 3. Grab the value from the text field, and print it when the user clicks OK.
            //let emailStr : String = alert.textFields![0].text!
            
            alert.addAction(UIAlertAction(title: "SEND", style: .default, handler: {(action) in self.forgetPasswordApiCalling(emailStr: alert.textFields![0].text!)}))
            alert.addAction(UIAlertAction(title: "Cancel", style: .cancel, handler: nil))
            
            // 4. Present the alert.
            self.present(alert, animated: true, completion: nil)
        }
    }
    
    //MARK:: ForgotPassword Api Calling
    func forgetPasswordApiCalling(emailStr: String) {
        
        let trimmedEmail = emailStr.trim()
        let userDetails:NSMutableDictionary = [
            "email" : trimmedEmail
        ]
        print("userDetails :: \(userDetails)")
        if self.connectedToNetwork() {
            if(loginMode == "Mentee"){
                self.startActivityIndicator()
                ApiManager.sharedInstance.forgotPass(postId: 1, userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        let userData : NSDictionary = (json["data"] as? NSDictionary)!
                        self.loginMode = (userData["user_type"] as? String)!
                        self.stopActivityIndicator()
                        
                        let msg = "A One-Time-Password (OTP) password has been sent to your email address on file. You will need the OTP to establish your new password on the next screen. The OTP will expire in 30 minutes."
                        let alert = UIAlertController(title: "Take Stock In Children", message: msg, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            DispatchQueue.main.async(execute: {() -> Void in
                                self.dismiss(animated: true, completion: nil)
                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                                let resetPasswordVC = storyBoard.instantiateViewController(withIdentifier: "ResetPasswordViewController") as! ResetPasswordViewController
                                resetPasswordVC.StrEmail = emailStr
                                resetPasswordVC.loginModeForResetPassword = self.loginMode
                                self.present(resetPasswordVC, animated:true, completion:nil)
                            })
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    }
        }, onFailure: { error in
            print("coming to menee error >>>>>>>>>>>>>>>>>")
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
        } else{
                self.startActivityIndicator()
                MentorApiManager.mentorSharedInstance.mentorForgotPass(postId: 1, userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.stopActivityIndicator()
                        let msg = "A One-Time-Password (OTP) password has been sent to your email address on file. You will need the OTP to establish your new password on the next screen. The OTP will expire in 30 minutes."
                        let alert = UIAlertController(title: "Take Stock In Children", message: msg, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            DispatchQueue.main.async(execute: {() -> Void in
                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                                let resetPasswordVC = storyBoard.instantiateViewController(withIdentifier: "ResetPasswordViewController") as! ResetPasswordViewController
                                resetPasswordVC.StrEmail = emailStr
                                resetPasswordVC.loginModeForResetPassword = self.loginMode
                                self.present(resetPasswordVC, animated:true, completion:nil)
                            })
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
}

// MARK:: UITextFieldDelegate
extension LogInViewController: UITextFieldDelegate {
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
}



