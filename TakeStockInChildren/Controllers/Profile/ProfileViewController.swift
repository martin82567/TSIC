//
//  ProfileViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 12/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
import SDWebImage
import ShimmerSwift
import SocketIO
class ProfileViewController: BaseViewController,UINavigationControllerDelegate, UIImagePickerControllerDelegate,MenuControllerDelegate,backRefresh,backrefreshfive, backrefreshsix,backrefreshseven,msgCenterrefresh,sessionMangementrefresh{
    
    @IBOutlet weak var lblEdate: UILabel!
    @IBOutlet weak var lblSdate: UILabel!
    
    @IBOutlet weak var viewmsgMentee: ShimmeringView!
    @IBOutlet weak var messagelabelMentee: UILabel!
    @IBOutlet weak var topBackgroundImage: UIImageView!
    @IBOutlet weak var lablBadgeCount: UILabel!
    @IBOutlet weak var badgeImage: UIImageView!
    
    @IBOutlet weak var badgeChatCountMentor: UILabel!
    @IBOutlet weak var buttonMenu: UIButton!
    @IBOutlet weak var imageViewEditProfile: UIImageView!
    @IBOutlet weak var buttonEdit: UIButton!
    @IBOutlet weak var tableViewProfileDisplay: UITableView!
    @IBOutlet weak var imageViewProfile: UIImageView!
    @IBOutlet weak var textFieldUserNameProfile: UITextField!
    @IBOutlet weak var labelEmailProfile: UILabel!
    @IBOutlet weak var headerview: UIView!
    @IBOutlet weak var labelcurrentAddressProfile: UILabel!
    @IBOutlet weak var labelAgencyProfile: UILabel!
    @IBOutlet weak var labelNoOfGoalsProfile: UILabel!
    
    @IBOutlet weak var backgroudvieww: UIView!
    var dicUserDetails = NSDictionary()
    var firstName :String? = ""
    var middleName :String? = ""
    var lastName :String? = ""
    var videoURLStr : String = ""
    var valueMode : String?
    var profileImage : UIImage!
    var arrImageSideMenu = [String]()
    var arrSideMenuItem = [String]()
    //var rrImageSideMenu: [String] = ["MeetingWithMentor","ChatWithMentor","Goals","Resources",""]
    //var arrSideMenuItem = //["MEETING WITH MENTOR", "CHAT WITH MENTOR", "MY GOALS", "TAKE STOCK ASSIGNMENTS", "RESOURCES"]
    
    var isProfile      : Bool = true
    var isFromMenu  : Bool = false
    var isFirstTime : Bool = false
    
    var badgevalue = [String]()
    var Messages = [[String:Any]]()
    
    var socket: SocketIOClient!
    var manager: SocketManager!

    var menteeTimer: Timer!
    var menteeeID: String?
    override func viewDidLoad() {
        super.viewDidLoad()
        
       // videoSocket()
        
        UIApplication.shared.applicationIconBadgeNumber = 0
        self.arrImageSideMenu = ["Session","ChatMentorProfile","MyGoals","MenteeAssignments","Resources","25"]
        self.arrSideMenuItem = ["MENTOR SESSIONS", "CHAT WITH MENTOR", "MY GOALS", "TAKE STOCK ASSIGNMENTS", "RESOURCES","ANNOUNCEMENTS"]
        
        self.menteeTimer = Timer.scheduledTimer(timeInterval: 4.0, target: self, selector: #selector(fireTimer), userInfo: nil, repeats: true)
        
     //   self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechnaged()
       // NotificationCenter.default.addObserver(self, selector: #selector(colorchnaged), name: .menteebackgroundColor, object: nil)

        
        badgeChatCountMentor.layer.cornerRadius = badgeChatCountMentor.frame.width/2
        badgeChatCountMentor.layer.masksToBounds = true
        
        
        
        textFieldUserNameProfile.delegate = self as! UITextFieldDelegate
        let tapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(ProfileViewController.cellTappedMethod(_:)))
        imageViewProfile.isUserInteractionEnabled = true
        imageViewProfile.addGestureRecognizer(tapGestureRecognizer)
        
        // Set automatic dimensions for row height
        tableViewProfileDisplay.estimatedRowHeight = 80
        tableViewProfileDisplay.rowHeight = UITableView.automaticDimension
        // Do any additional setup after loading the view.
        
       // viewmsgMentee.contentView = messagelabelMentee
       // viewmsgMentee.isShimmering = true
       // viewmsgMentee.isHidden = true
       //  self.getmenteemessagee()
         getmenteeTimeZone()
         self.getUserDetails()
         faqApi()
    }
    
    
    
    func backSession(name: String) {
        self.getUserDetails()
    }
    
    
    func backref2(name: String) {
        self.getUserDetails()
    }
    
    
    override func viewWillDisappear(_ animated: Bool) {
        self.menteeTimer.invalidate()
    }
    
    
    func faqApi() {
        
        
        
        MentorApiManager().FAQListmentee() { (json) in
                print("access json-------",json)
                let status = json["status"] as! Bool
                if status == true {
                    let dicData = json["data"] as! NSDictionary
                    let url = dicData["mentee_faq"] as? String ?? ""
                    CommonFAQDATA.sharedinstance.faqmenteeurl = url
                } else {
                    print("error")
                    self.logOutMentee()
                }
            }
        
        
        
    }
    
    
    
    
    
    
    func getmenteemessagee() {
        
        
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
                            self.viewmsgMentee.isHidden = true
                        }
                        //self.messagelabelMentee.pushTransition(0.4)
                        
                        self.messagelabelMentee.text = messageText
                      //  self.lblSdate.text = sdate
                        //self.lblEdate.text = edate
                        
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
               
                DispatchQueue.main.async {
                    if messageText == "" {
                        self.viewmsgMentee.isHidden = true
                    }
                   // self.messageLabelMentor.startAnimating()
                 //   self.msgLable.text = messageText
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
    
    
    func backAction(name: String) {
        self.getUserDetails()
    }
    
    
    func backrefreshfive(name: String) {
           self.getUserDetails()
       }
    
    
    
    func backrefreshseven(name: String) {
        self.getUserDetails()
    }
    
    func backrefreshSix(name: String) {
        self.getUserDetails()
    }
    
    
    
    
    class func topViewController(controller: UIViewController? = UIApplication.shared.keyWindow?.rootViewController) -> UIViewController? {
        if let navigationController = controller as? UINavigationController {
            return topViewController(controller: navigationController.visibleViewController)
        }
        if let tabController = controller as? UITabBarController {
            if let selected = tabController.selectedViewController {
                return topViewController(controller: selected)
            }
        }
        if let presented = controller?.presentedViewController {
            return topViewController(controller: presented)
        }
        return controller
    }
    
    
    //MARK:// Video Call Socket
    func videoSocket() {
        
//       //https://mentorappdev.tsic.org:3700/
        //https://mentorappdev.tsic.org:3000/
        print("Socket function called")
        manager = SocketManager(socketURL: URL(string: "https://mentorappdev.tsic.org:3000/")!, config: [.log(true), .compress, .forceWebsockets(true) ])
        socket = manager.defaultSocket
        
        socket.on(clientEvent: .connect) {data, ack in
            print("socket connected")
          //  self.isSocketConnected = true
            var objData = [String:Any]()
            objData.updateValue("mentee", forKey: "type")
            objData.updateValue("11", forKey: "id")
            self.socket.emit("connected",objData)
            
        }

        
        socket.connect()
    }
    
    
    
    
    
    func getmenteemessage() {
        
        
            MentorApiManager().menteehomemessageApi() { (json) in
                print("access json-------",json)
                let status = json["status"] as! Bool
                
                if status == true {
                    let dicData = json["data"] as! NSDictionary
                    self.Messages = dicData["messaging"] as? [[String : Any]] ?? []
                    let dic = self.Messages.first
                  //  let messageText = dic?["message"] as? String ?? ""
                    
                    DispatchQueue.main.async {
                        
                        
                        self.arrImageSideMenu = ["Session","ChatMentorProfile","MyGoals","MenteeAssignments","Resources","25"]
                        self.arrSideMenuItem = ["MENTOR SESSIONS", "CHAT WITH MENTOR", "MY GOALS", "TAKE STOCK ASSIGNMENTS", "RESOURCES","ANNOUNCEMENTS"]
                        
                        
                        for item in self.Messages {
                            self.arrImageSideMenu.append("Resources")
                            
                            let message = item["message"] as? String ?? ""
                            let concatmessage = message + ";"
                            
                            let startdate = item["start_datetime"] as? String ?? ""
                            let concatestartate = startdate + ";"
                            
                            let enddate = item["end_datetime"] as? String ?? ""
                           
                            
                            
                            let concateMsgnadData = concatmessage + concatestartate + enddate
                            print("concatall",concateMsgnadData)
                            
                            self.arrSideMenuItem.append(concateMsgnadData)
                            
                            self.tableViewProfileDisplay.reloadData()
                        }
                        
                        
                        //self.messagelabelMentee.text = messageText
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
    
    
    
     func getDate(strDate: String) -> String {
        let formatter = DateFormatter()
        formatter.dateFormat = "MM-dd-yyyy HH:mm:ss"
        
        let dateDeadline = formatter.date(from: strDate)
        formatter.dateFormat = "MM-dd-yyyy"
        let strDateDeadline = formatter.string(from: dateDeadline!)
        return strDateDeadline
    }
    
    
    
    @objc func fireTimer() {
            //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
            //    print("token==\(token)")
            if self.connectedToNetwork() {
               // self.startActivityIndicator()
                
                ApiManager.sharedInstance.getUserDetails(onSuccess: { json in
                    DispatchQueue.main.async {
                       // print("UserDetails json :: \(String(describing: json))")
                        DispatchQueue.main.async(execute: {() -> Void in
                            
                            guard var userDetails = (json["user_details"] as? NSDictionary)?.mutableCopy() as? NSMutableDictionary else{
                                return
                            }
                            
                            if (userDetails["upcoming_meeting"] as? NSDictionary) == nil {
                                userDetails.removeObject(forKey: "upcoming_meeting")
                            }

                           // print("USERDETAILSMENTEE\(userDetails)")
                            self.dicUserDetails = userDetails
                            UserDefaults.standard.set(userDetails, forKey: "userDetails")
                         
                            self.setValues()
                            self.tableViewProfileDisplay.reloadData()
                          //  self.stopActivityIndicator()
                        })
                    }
                }, onFailure: { error in
                    
                    self.logOutMentee()
                    /*
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                    */
                })
            } else {
                showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        
    }
    
    
    
    
    @objc func colorchnaged() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark"{
            topBackgroundImage.image = UIImage(named: "BGProfiledark11")
            tableViewProfileDisplay.backgroundColor = UIColor(hexString: "#0E0F27")
            headerview.backgroundColor = UIColor(hexString: "#0E0F27")
            
            labelAgencyProfile.textColor = .white
            labelNoOfGoalsProfile.textColor = .white
            
            textFieldUserNameProfile.textColor =  .white
        }
        else if mode == "light" {
            tableViewProfileDisplay.backgroundColor = .white
            topBackgroundImage.image = UIImage(named: "BGProfiledark")
             headerview.backgroundColor = UIColor(hexString: "#A7AE3B")
            
            labelAgencyProfile.textColor = .black
            labelNoOfGoalsProfile.textColor = .black
            
            textFieldUserNameProfile.textColor = .black
        }
        self.arrImageSideMenu.removeAll()
        self.arrSideMenuItem.removeAll()
        self.arrImageSideMenu = ["Session","ChatMentorProfile","MyGoals","MenteeAssignments","Resources"]
        self.arrSideMenuItem = ["MENTOR SESSIONS", "CHAT WITH MENTOR", "MY GOALS", "TAKE STOCK ASSIGNMENTS", "RESOURCES"]
        self.tableViewProfileDisplay.reloadData()
        
        
    }
    
    
    func darkmodechnaged() {
        if self.valueMode == "dark"{
                   topBackgroundImage.image = UIImage(named: "BGProfiledark11")
                   tableViewProfileDisplay.backgroundColor = UIColor(hexString: "#0E0F27")
                   headerview.backgroundColor = UIColor(hex: "#0E0F27")
            labelAgencyProfile.textColor = .white
            labelNoOfGoalsProfile.textColor = .white
            
            textFieldUserNameProfile.textColor =  .white
            
        

               }
               else if self.valueMode == "light" {
                   tableViewProfileDisplay.backgroundColor = .white
                   topBackgroundImage.image = UIImage(named: "BGProfiledark")
            headerview.backgroundColor = UIColor(hex: "#A7AE3B")
            
            labelAgencyProfile.textColor = .black
            labelNoOfGoalsProfile.textColor = .black
            
            textFieldUserNameProfile.textColor =  .black
               }
        
    }
    
    override func viewDidAppear(_ animated: Bool) {
        print("Work")
        self.menteeTimer = Timer.scheduledTimer(timeInterval: 4.0, target: self, selector: #selector(fireTimer), userInfo: nil, repeats: true)
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewProfileDisplay.setContentOffset(.zero, animated:true)
        self.buttonMenu.addTarget(self, action: #selector(ProfileViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
       
    }
    
    func getmenteeTimeZone() {
        MentorApiManager().getTimeZoneMentee(onSuccess: { (response) in
            let status = response["status"] as? Bool
            if let responseStatus = status {
                if responseStatus == true {
                    let arrGeofenceData = response["data"] as? NSDictionary
                    
                    if let timezone = arrGeofenceData {
                    TakeStockInChildrenConstant.menteeTimeZone = ""

                let timeZone = timezone["timezone"] as! String
                        
                TakeStockInChildrenConstant.menteeTimeZone = timeZone
                        
                    } else {
                       
                    }
                }
            } else {
                print("No Geofence data")
            }
        }, onFailure: { (response) in
            self.logOutMentee()
        })
    }
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- SideMenu
    func showHideMenuController(_ isShown: Bool) {
        buttonMenu.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowSideMenu() {
        let objSideMenu = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "MenuViewController") as! MenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMenuInController(self)
    }
    
    //MARK:- Set Profile Image from PhotoLibrary or Camera
    @objc func cellTappedMethod(_ sender:AnyObject){
        actionSheetPopUpForSettingProfileImage()
    }
    
    func actionSheetPopUpForSettingProfileImage() {
        
        let alert:UIAlertController=UIAlertController(title: "Choose Image", message: nil, preferredStyle: UIAlertController.Style.actionSheet)
        
        let cameraAction = UIAlertAction(title: "Camera", style: UIAlertAction.Style.default)
        {
            UIAlertAction in
            self.selectImageFromCamera()
        }
        let gallaryAction = UIAlertAction(title: "Library", style: UIAlertAction.Style.default)
        {
            UIAlertAction in
            self.selectImageFromPhotoLibrary()
        }
        let cancelAction = UIAlertAction(title: "Cancel", style: UIAlertAction.Style.cancel)
        {
            UIAlertAction in
        }
        alert.addAction(cameraAction)
        alert.addAction(gallaryAction)
        alert.addAction(cancelAction)
        
        //------------------------
        // Present the controller
        //-------------------------
        if UIDevice.current.userInterfaceIdiom == .phone
        {
            self.present(alert, animated: true, completion: nil)
        }
        else
        {
            // popover=UIPopoverController(contentViewController: alert)
        }
    }
    func selectImageFromPhotoLibrary() {
        // UIImagePickerController is a view controller that lets a user pick media from their photo library.
        let imagePickerController = UIImagePickerController()
        
        // Only allow photos to be picked, not taken.
        imagePickerController.sourceType = .photoLibrary
        
        // Make sure ViewController is notified when the user picks an image.
        imagePickerController.delegate = self
        present(imagePickerController, animated: true, completion: nil)
    }
    
    func selectImageFromCamera() {
        // UIImagePickerController is a view controller that lets a user pick media from their photo library.
        let imagePickerController = UIImagePickerController()
        
        // Only allow photos to be taken.
        if (UIImagePickerController .isSourceTypeAvailable(UIImagePickerController.SourceType.camera)) {
            imagePickerController.delegate = self
            imagePickerController.allowsEditing = true
            imagePickerController.sourceType = UIImagePickerController.SourceType.camera
            imagePickerController.cameraCaptureMode = .photo
            present(imagePickerController, animated: true, completion: nil)
        } else {
            let alert = UIAlertController(title: "Camera Not Found", message: "This device has no Camera", preferredStyle: .alert)
            let ok = UIAlertAction(title: "OK", style:.default, handler: nil)
            alert.addAction(ok)
            present(alert, animated: true, completion: nil)
        }
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        // The info dictionary may contain multiple representations of the image. You want to use the original.
        guard let possibleImage = info[.originalImage] as? UIImage
            else {
                fatalError("Expected a dictionary containing an image, but was provided the following: \(info)")
                //return
        }
        // Set photoImageView to display the selected image.
        imageViewProfile.image = possibleImage
        
        self.profileImage = possibleImage
        print("self.profileImage\(String(describing: self.profileImage))")
        imageViewProfile.clipsToBounds = true
        
        // Dismiss the picker.
        dismiss(animated: true, completion: nil)
        setUpdateUserDetails()
        //self.viewSave.isHidden = false
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        // Dismiss the picker if the user canceled.
        dismiss(animated: true, completion: nil)
    }
    
    //MARK:-ButtonAction
    @IBAction func buttonEditAction(_ sender: Any) {
        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
        let myMenteeListVc = storyBoardMain.instantiateViewController(withIdentifier: "MenteeEditProfileViewController") as! MenteeEditProfileViewController
        self.navigationController?.pushViewController(myMenteeListVc, animated: true)
    }
    
    //MARK:: Function Api Calling
    func getUserDetails () {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                     print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        
                        guard var userDetails = (json["user_details"] as? NSDictionary)?.mutableCopy() as? NSMutableDictionary else{
                            return
                        }
                        
                        if (userDetails["upcoming_meeting"] as? NSDictionary) == nil {
                            userDetails.removeObject(forKey: "upcoming_meeting")
                        }
                        
                        self.Messages = userDetails["affiliate_system_messaging"] as? [[String : Any]] ?? []

                      //  print("USERDETAILSMENTEE\(userDetails)")
                        self.dicUserDetails = userDetails
                        UserDefaults.standard.set(userDetails, forKey: "userDetails")
                     
                        self.setValues()
                        
                        
                        let dic = self.Messages.first
                        let messageText = dic?["message"] as? String ?? ""
                       // let sdate = dic?["start_datetime"] as? String ?? ""
                       // let edate = dic?["end_datetime"] as? String ?? ""
                        DispatchQueue.main.async {
                            if messageText == "" {
                                self.viewmsgMentee.isHidden = true
                            }
                            //self.messagelabelMentee.pushTransition(0.4)
                            
                            self.messagelabelMentee.text = messageText
                          //  self.lblSdate.text = sdate
                            //self.lblEdate.text = edate
                            
                        }
                        
                        
                      //  self.arrImageSideMenu = ["Session","ChatMentorProfile","MyGoals","MenteeAssignments","Resources"]
                      //  self.arrSideMenuItem = ["MENTOR SESSIONS", "CHAT WITH MENTOR", "MY GOALS", "TAKE STOCK ASSIGNMENTS", "RESOURCES"]
                        
                        /*
                        for item in self.Messages {
                            self.arrImageSideMenu.append("Resources")
                            
                            let message = item["message"] as? String ?? ""
                            let concatmessage = message + ";"
                            
                            let startdate = item["start_datetime"] as? String ?? ""
                            let concatestartate = startdate + ";"
                            
                            let enddate = item["end_datetime"] as? String ?? ""
                           
                            
                            
                            let concateMsgnadData = concatmessage + concatestartate + enddate
                            print("concatall",concateMsgnadData)
                            
                            self.arrSideMenuItem.append(concateMsgnadData)
                            
                        }
                        */
                            
                        
                        
                        self.tableViewProfileDisplay.reloadData()
                        self.stopActivityIndicator()
                    })
                }
            }, onFailure: { error in
                print("Error---========",error)
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    
                    let alert = UIAlertController(title: "TSIC", message: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    
                    
                    
                  //  self.logOutMentee()
                    
                    
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
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
    
    
    
    
    func values(fromCSVString str: String) -> [String] {
        let separators = CharacterSet(charactersIn: ";")
        return str.components(separatedBy: separators)
    }
    
    
    
    //MARK:- SetValues
    func setValues() {
       // print("test",dicUserDetails)
        if dicUserDetails.count > 0 {
            if (dicUserDetails["image"] == nil) {
                videoURLStr = dicUserDetails["image"] as! String
            } else {
                videoURLStr = TakeStockInChildrenConstant.UserImageBaseURL.appending(dicUserDetails["image"] as! String)
              //  print("IMAGEURL\(videoURLStr)")
            }
            
            let videoURL = NSURL (string: videoURLStr)
            imageViewProfile.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
            
            firstName = (dicUserDetails["firstname"] as? String)
            middleName = (dicUserDetails["middlename"] as? String)
            lastName = (dicUserDetails["lastname"] as? String)
            
            
            UserCredential.shared.firstname = firstName ?? ""
            UserCredential.shared.middlename = middleName ?? ""
            UserCredential.shared.lastname = lastName ?? ""
            
            textFieldUserNameProfile.text      = firstName! + " " + middleName! + " " + lastName!
            //labelEmailProfile.text         = dicUserDetails["email"] as? String
            let noOfAchievedGoals = String(dicUserDetails["unread_goal"] as! Int)
            let taskAchived = String(dicUserDetails["unread_task"] as! Int)
            let noOfSessions = String(dicUserDetails["schedule_session_count"] as! Int) //session_count
            
            
            //labelcurrentAddressProfile.text       = dicUserDetails["current_living_details"] as? String
            labelAgencyProfile.text  = dicUserDetails["linked_agency_name"] as? String
            
            self.menteeeID = String(dicUserDetails["id"] as? Int ?? 0)
            UserCredential.shared.id = self.menteeeID ?? ""
            
            let unreadChatCount = String(dicUserDetails["mentor_mentee_chat_count"] as? Int ?? 0)

            badgeChatCountMentor.text = unreadChatCount
            
            
            let sessionLogCountMentee = "\(dicUserDetails["sum_mentor_session_log_count"] as? Int ?? 0)"
            labelNoOfGoalsProfile.text = sessionLogCountMentee + " " + "Sessions Logged"
            
            let sessionLoglabel = dicUserDetails["session_log_label_no"] as? Int ?? 0
            //print("loglabel",sessionLoglabel)
            
            
            var messageCenterCount = dicUserDetails["message_center_count"] as? Int ?? 0
            var meesageCount = String(messageCenterCount)
            
            if sessionLoglabel == 0 {
                badgeImage.isHidden = true
                lablBadgeCount.isHidden = true
                
            }
            else if sessionLoglabel == 1 {
              badgeImage.isHidden = true
                lablBadgeCount.isHidden = true
            }
            else if sessionLoglabel == 2 {
                badgeImage.isHidden = false
                badgeImage.image = UIImage(named: "bronze_medal")
                lablBadgeCount.isHidden = false
                lablBadgeCount.text = sessionLogCountMentee
            }
            else if sessionLoglabel == 3 {
                badgeImage.isHidden = false
                badgeImage.image = UIImage(named: "silver_medal")
                lablBadgeCount.isHidden = false
                lablBadgeCount.text = sessionLogCountMentee
            }
            else if sessionLoglabel == 4 {
                badgeImage.isHidden = false
                badgeImage.image = UIImage(named: "gold_medal")
                lablBadgeCount.isHidden = false
                lablBadgeCount.text = sessionLogCountMentee
            }
            
            
            badgevalue.removeAll()
            badgevalue.append(noOfSessions)
            badgevalue.append(unreadChatCount)
            badgevalue.append(noOfAchievedGoals)
            badgevalue.append(taskAchived)
            badgevalue.append("")
            badgevalue.append(meesageCount)
            badgevalue.append("")
            //print("badgevalue",badgevalue)
            self.tableViewProfileDisplay.reloadData()
            
            
            let staffCount = "\(dicUserDetails["mentee_staff_chat_count"] as? Int ?? 0)"
            if staffCount == "0" {
                self.tabBarController?.tabBar.items![1].badgeValue = nil
            }
            else {
                self.tabBarController?.tabBar.items![1].badgeValue = staffCount
            }
            if noOfSessions == "0"{
                self.tabBarController?.tabBar.items![2].badgeValue = nil
            }
            else {
                self.tabBarController?.tabBar.items![2].badgeValue = noOfSessions
            }
            
            self.showUpcomingMeeting(dicJsonMentorResponse: dicUserDetails)
        }
    }
    
    func showUpcomingMeeting(dicJsonMentorResponse: NSDictionary) {
        let upcomingMeeting = dicJsonMentorResponse["upcoming_meeting"] as? [String:Any]
        
        if (UserDefaults.standard.value(forKey: "Mentee_LastUpcomingMeetinng") as? UInt64 ?? 0) == upcomingMeeting?["id"] as? UInt64 ?? 0 {
            return
        }
        
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
        let schduledDate = formatter.date(from: (upcomingMeeting?["schedule_time"] as? String) ?? "") ?? Date()
        let formattedSchduledDate = DateTodayFormatter().stringFromDate(date: schduledDate as NSDate) ?? ""
        
        let alertController = UIAlertController(style: .alert, title: "REMINDER! UPCOMING SESSION", message: "Don’t forget! You have an upcoming mentor session at \(formattedSchduledDate) with \(upcomingMeeting?["firstname"] ?? "") \(upcomingMeeting?["lastname"] ?? "")")
        alertController.addAction(UIAlertAction(title: "ok", style: .default, handler: { [weak self] action in
            alertController.dismiss(animated: true)
        }))
        self.present(alertController, animated: true)
        
        UserDefaults.standard.set(upcomingMeeting?["id"] as? UInt64 ?? 0, forKey: "Mentee_LastUpcomingMeetinng")
    }
    
    //MARK:- Update User Details
    func setUpdateUserDetails() {
        if self.connectedToNetwork() {
            let token  = UserDefaults.standard.string(forKey: "token")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UpdateUserDetails)
            var parameters = [String: String]()
            
           self.startActivityIndicator()
            
            print("firstName\(firstName)")
            print("middleName\(middleName)")
            print("lastName\(lastName)")
            parameters["firstname"] = firstName
            parameters["middlename"] = middleName
            parameters["lastname"] = lastName
            
            print(parameters)
            
            Alamofire.upload(multipartFormData: { multipartFormData in
                if self.profileImage != nil {
                    let imageData = self.profileImage.jpegData(compressionQuality: 6.0)
                    print("ImageData\(imageData)")
                    multipartFormData.append(imageData!, withName: "userimage", fileName: "\(Date().timeIntervalSince1970).jpeg", mimeType: "image/jpeg")
                }
                for (key, value) in parameters {
                    multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
                }
                //                }
            }, to: url,
               method:.post,
               headers:headers as! HTTPHeaders,
               encodingCompletion: { encodingResult in
                switch encodingResult {
                case .success(let upload, _, _):
                    upload.responseString(completionHandler: { (response) in
                        print("GotIMAGENAMEUPDATE\(response)")
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Success", message: "Profile updated succesfully" as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                        //    self.getUserDetails()
                            self.tableViewProfileDisplay.reloadData()
                            self.stopActivityIndicator()
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    })
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    self.showAlertAction(withTitle: "Alert", message: "Profile is not updated")
                }
            })
            
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
}

// MARK:: UITableViewDatasource
extension ProfileViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrImageSideMenu.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        /*
        if indexPath.row > 4 {
            let cell = tableView.dequeueReusableCell(withIdentifier: "BadgePorofileMenteeTableViewCell") as! BadgePorofileMenteeTableViewCell
            print("arraysidemenu Item",self.arrSideMenuItem[indexPath.row])
            
            let semicoloncsv = self.arrSideMenuItem[indexPath.row]
            let semicolonvalues = values(fromCSVString: semicoloncsv)
            print("values",semicolonvalues)
            cell.msgLabel.text = semicolonvalues[0]
            cell.sdateLabel.text = semicolonvalues[1]
            cell.edateLabel.text = semicolonvalues[2]
            
           // cell.mainView.contentView = cell.msgLabel
           // cell.mainView.isShimmering = true
            
            return cell
       
        }
        */
       // else {
            let cell = tableView.dequeueReusableCell(withIdentifier: "ProfileTableViewCell") as! ProfileTableViewCell
            let mode = UserDefaults.standard.value(forKey: "mode") as? String
            if mode == "dark"{
                 cell.contentView.backgroundColor = UIColor(hexString: "#0E0F27")
                cell.labelSideMenuCell.textColor = .white
                cell.ViewOfBackground.backgroundColor = .black
            }
            else if mode == "light" {
                cell.contentView.backgroundColor = .white
                cell.labelSideMenuCell.textColor  = .black
                cell.ViewOfBackground.backgroundColor = .white
            }
            if arrImageSideMenu.count > 0 {
                cell.imageListCell.image = UIImage(named: arrImageSideMenu[indexPath.row])
            }
            if arrSideMenuItem.count > 0 {
                cell.labelSideMenuCell.text = arrSideMenuItem[indexPath.row]
            }
            
            cell.labelCount.layer.cornerRadius = cell.labelCount.frame.width/2
            cell.labelCount.layer.masksToBounds = true
            if badgevalue.count > 0 {
                let checkvalue = badgevalue[indexPath.row]
                if checkvalue == "" || checkvalue == "0" {
                    cell.labelCount.isHidden = true
                }
                else {
                    cell.labelCount.isHidden = false
                }
            }
            if badgevalue.count > 0 {
                cell.labelCount.text = badgevalue[indexPath.row]
            }
            
            return cell
      //  }
        
    }
}
//22July
// MARK:: UITableViewdDelegate
extension ProfileViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if (indexPath.row == 0) {
            print("mentor sessions")
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "SessionManagementMenteeViewController") as! SessionManagementMenteeViewController
                newViewController.fromMenu = true
               // newViewController.fromMenu = true
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                
                /*
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MeetingViewController") as! MeetingViewController
                newViewController.fromMenu = true
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                */
            }
        } else if(indexPath.row == 1){
            /*
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MontorListFromMenteeVC") as! MontorListFromMenteeVC
                //newViewController.strIamfrom = ""
                newViewController.delegate =  self
                self.navigationController?.pushViewController(newViewController, animated: true)
                
                /*
                let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                newViewController.strIamfrom = ""
                self.navigationController?.pushViewController(newViewController, animated: true)*/
            }
            
            */
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MontorListFromMenteeVC") as! MontorListFromMenteeVC
                newViewController.firstName =  self.firstName
                newViewController.middleName =  self.middleName
                newViewController.lastName =  self.lastName
                newViewController.Id = self.menteeeID
                newViewController.delegate =  self
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            
        } else if (indexPath.row == 2) {
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "GoalViewController") as! GoalViewController
            goalVc.delegate = self
            self.navigationController?.pushViewController(goalVc, animated: true)
        } else if(indexPath.row == 3) {
            let taskVc = UIStoryboard(name: "Task", bundle: nil).instantiateViewController(withIdentifier: "TaskViewController") as! TaskViewController
            taskVc.delegate = self
            self.navigationController?.pushViewController(taskVc, animated: true)
            
        } else if(indexPath.row == 4) {
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "ResourceViewController") as! ResourceViewController
            
            self.navigationController?.pushViewController(resourceVc, animated: true)
        }
        else if(indexPath.row == 5) {
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "MessageCenterViewController") as! MessageCenterViewController
            resourceVc.type = "mentee"
            resourceVc.delegate = self
            self.navigationController?.pushViewController(resourceVc, animated: true)
        }
        /*
        else if(indexPath.row == 6) {
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MontorListFromMenteeVC") as! MontorListFromMenteeVC
                newViewController.firstName =  self.firstName
                newViewController.middleName =  self.middleName
                newViewController.lastName =  self.lastName
                newViewController.Id = self.menteeeID
                newViewController.tag = "twillo"
                /*
                 var firstName :String? = ""
                 var middleName :String? = ""
                 var lastName :String? = ""
                 var menteeeID: String?
                 */
                
                //newViewController.strIamfrom = ""
                newViewController.delegate =  self
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            
            
        }
        */
        
        
        //else if(indexPath.row == 3) {
//            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Task", bundle:nil)
//            let challengeVc = storyBoardMain.instantiateViewController(withIdentifier: "ChallengeViewController") as! ChallengeViewController
//
//            self.navigationController?.pushViewController(challengeVc, animated: true)
     //   }
//            else if(indexPath.row == 4) {
//            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//            let jobVc = storyBoardMain.instantiateViewController(withIdentifier: "JobViewController") as! JobViewController
//
//            self.navigationController?.pushViewController(jobVc, animated: true)
//        }
        //        else if(indexPath.row == 4){
        //            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
        //            let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "ElearningViewController") as! ElearningViewController
        //
        //            self.navigationController?.pushViewController(goalVc, animated: true)
        //        }
        //        else if(indexPath.row == 5){
        //            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
        //            let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "ElearningViewController") as! ElearningViewController
        //
        //            self.navigationController?.pushViewController(goalVc, animated: true)
        //        }
        //  ElearningViewController
    }
}

// MARK:: UITextFieldDelegate
extension ProfileViewController: UITextFieldDelegate {
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
    
    func textFieldDidBeginEditing(textField: UITextField) {
        textField.text = ""
    }
}



extension UIView {
    func pushTransition(_ duration:CFTimeInterval) {
            let animation:CATransition = CATransition()
            animation.timingFunction = CAMediaTimingFunction(name:
            CAMediaTimingFunctionName.easeInEaseOut)
        animation.type = CATransitionType.push
        animation.subtype = CATransitionSubtype.fromTop
            animation.duration = duration
        layer.add(animation, forKey: CATransitionType.push.rawValue)
        }
}
