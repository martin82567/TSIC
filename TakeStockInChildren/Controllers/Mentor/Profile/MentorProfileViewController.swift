//
//  MentorProfileViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 07/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
import SDWebImage
import CoreLocation
import ShimmerSwift
import TwilioChatClient

class MentorProfileViewController: BaseViewController, UITextFieldDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate, MentorMenuControllerDelegate,backrefreshtwo,backrefreshthree,backrefreshSessionlog,msgCenterrefresh,SessionmangementRefresh{
   
   
    @IBOutlet weak var messageTxtView: UITextView!
    @IBOutlet weak var lblEdate: UILabel!
    @IBOutlet weak var lblSdate: UILabel!
    @IBOutlet weak var viewmsg: UIView!
    @IBOutlet weak var messageLabelMentor: UILabel!
    @IBOutlet weak var applogged: UILabel!
    
    @IBOutlet weak var topbackgroundimage: UIImageView!
    
    @IBOutlet weak var backgroundview: UIView!
    @IBOutlet weak var lblSessionBadgeCount: UILabel!
    @IBOutlet weak var medalImage: UIImageView!
    
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var badgeCountMentor: UILabel!
    @IBOutlet weak var buttonMenu: UIButton!
    @IBOutlet weak var imageViewEdit: UIImageView!
    @IBOutlet weak var buttonEdit: UIButton!
    //@IBOutlet weak var imgPhone: UIImageView!
    //@IBOutlet weak var textFieldPhone: UITextField!
    @IBOutlet weak var tableSubview: UIView!
    @IBOutlet weak var tableViewProfileDisplay: UITableView!
    @IBOutlet weak var imageViewProfile: UIImageView!
    @IBOutlet weak var textFieldMentorNameProfile: UITextField!
    //@IBOutlet weak var imgEmail: UIImageView!
    //@IBOutlet weak var labelEmailProfile: UILabel!
    //@IBOutlet weak var imgAddress: UIImageView!
    //@IBOutlet weak var labelAddressProfile: UILabel!
    @IBOutlet weak var labelAgencyProfile: UILabel!
    @IBOutlet weak var labelSessionNo: UILabel!

    var dicJsonMentorResponse:NSDictionary = [:]
    var dicJsonUpdateMentorResponse:NSDictionary = [:]
    var firstName :String = ""
    var middleName :String = ""
    var lastName :String = ""
    var wordCountFullName : String = ""
    var phone : String = ""
    var videoURLStr : String = ""
    var profileImage : UIImage!
    var arrImageSideMenu = [String]() //["GoalTaskChallenge"]
    var arrSideMenuItem = [String]()//["Log a session","Chat","LogOut", "Resources"]
    
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    let MentorstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)

    var geotifications: [Geotification] = []
    var locationManager = CLLocationManager()
    var valueMode : String?
    var badgeCountArray = [String]()
    var Messages = [[String:Any]]()
    var Messag = [[String:Any]]()
    var isTrigger: Bool =  false
    var timerprofile: Timer!
    var mentorId: String?
    override func viewDidLoad() {
        super.viewDidLoad()
        UIApplication.shared.applicationIconBadgeNumber = 0
        self.messageTxtView.layer.cornerRadius = 15.0
        self.arrImageSideMenu = ["LogSession","ScheduleSession","MentorProfileOptionFive", "25"]
        self.arrSideMenuItem = ["LOG A SESSION","SESSION MANAGEMENT","CHAT WITH MENTEE", "ANNOUNCEMENTS"]
        
        
        
        self.timerprofile  = Timer.scheduledTimer(timeInterval: 4.0, target: self, selector: #selector(fireTimer), userInfo: nil, repeats: true)
        
        _ = Timer.scheduledTimer(timeInterval: 10.0, target: self, selector: #selector(getLatLogForGeofence), userInfo: nil, repeats: true)

        /*
        if #available(iOS 12.0, *) {
            switch traitCollection.userInterfaceStyle {
            case .dark:
                print("dark set")
            case .light:
                print("light set")
            default:
                print("Not set")
            }
        } else {
            print("not working")
            // Fallback on earlier versions
        }
        */
        
       // self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechnaged()
       // NotificationCenter.default.addObserver(self, selector: #selector(userlogged), name: .backgroundColor, object: nil)
        
        
        badgeCountMentor.layer.cornerRadius = badgeCountMentor.frame.width/2
        badgeCountMentor.layer.masksToBounds = true
        
        
        let tapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(ProfileViewController.cellTappedMethod(_:)))
        imageViewProfile.isUserInteractionEnabled = true
        imageViewProfile.addGestureRecognizer(tapGestureRecognizer)
        // Do any additional setup after loading the view.
        
        //Timer.scheduledTimer(timeInterval: 60.0, target: self, selector: #selector(MentorProfileViewController.getLatLogForGeofence), userInfo: nil, repeats: true)

       // viewmsg.contentView = messageLabelMentor
       // viewmsg.isShimmering = true
       // viewmsg.isHidden = true
        getmentorTimeZone()
    //    getLatLogForGeofence()
        self.getUserDetails()
        faqApi()
        //retrivetoken()
       self.getMessageMentorr()
    }
    
    
    override func viewDidAppear(_ animated: Bool) {
        self.timerprofile  = Timer.scheduledTimer(timeInterval: 4.0, target: self, selector: #selector(fireTimer), userInfo: nil, repeats: true)
    }
    
    func backRefresh9(name: String) {
        self.getUserDetails()
    }
    
    
    
    func backref2(name: String) {
        self.getUserDetails()
    }
    
    
    func backre(name: String) {
        self.getUserDetails()
    }
    
    
    
    override func viewWillDisappear(_ animated: Bool) {
        self.timerprofile.invalidate()
    }
    
    func retrivetoken() {
        /*
        ApiManager.sharedInstance.retrievechatToken { (data) in
            print("chat data", data)
            
            let chadtdata = data["data"] as? AnyObject
            let token = chadtdata?["access_token"] as? String ?? ""
        } onFailure: { (failue) in
            print("faliute")
        }
        */
        /*
        var chatManager = QuickstartChatManager()
            chatManager.login("hello") { (success) in
                DispatchQueue.main.async {
                    if success {
                       print("Logged in")
                    } else {
                        print("not loggedin")
                        let msg = "Unable to login - check the token URL in ChatConstants.swift"
                        self.displayErrorMessage(msg)
                    }
                }
            }
        */
    }
    
    private func displayErrorMessage(_ errorMessage: String) {
        let alertController = UIAlertController(title: "Error",
                                                message: errorMessage,
                                                preferredStyle: .alert)
        let okAction = UIAlertAction(title: "OK", style: .default, handler: nil)
        alertController.addAction(okAction)
        present(alertController, animated: true, completion: nil)
    }
    
    
    
    func faqApi() {
        MentorApiManager().FAQListmentor() { (json) in
                print("faq list json-------",json)
                let status = json["status"] as! Bool
                if status == true {
                    let dicData = json["data"] as! NSDictionary
                    let url = dicData["mentor_faq"] as? String ?? ""
                    CommonFAQDATA.sharedinstance.faqmentorurl = url
                    
                } else {
                    self.logOutMentor()
                    print("error")
                }
            }
        
        
        
    }

    
    
    
    
    
    
    func getMessageMentorr() {
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
                        self.viewmsg.isHidden = true
                    }
                   // self.messageLabelMentor.startAnimating()
                    
//                    self.messageTxtView.text = "The full message read: Amazon Web Services is reporting national communication o1utages that include large companies such as Shopify and Slack. We are expecting an outage in our Text Chatting and Web Video platforms. We are monitoring and will remove the message once the outage has been restored."
                    //self.viewmsg.isHidden = false
                    self.messageTxtView.text = messageText
                    
                    //self.lblSdate.text = sdate
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
                self.logOutMentor()
                print("error")
            }
        }
        
        
    }
    
    
    func refresh(name: String) {
        self.getUserDetails()
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
              //  let startdate = dic?["start_date"] as? String ?? ""
              //  let enddate = dic?["end_date"] as? String ?? ""
                
                DispatchQueue.main.async {
                    
                    self.arrImageSideMenu = ["LogSession","ScheduleSession","MentorProfileOptionFive","25"]
                    self.arrSideMenuItem = ["LOG A SESSION","SCHEDULE SESSIONS","CHAT WITH MENTEE","ANNOUNCEMENTS"]
                    
                    
                    for item in self.Messages {
                        self.arrImageSideMenu.append("MentorProfileOptionFive")
                        
                        let message = item["message"] as? String ?? ""
                        let concatmessage = message + ";"
                        
                        let startdate = item["start_datetime"] as? String ?? ""
                        let concatestartate = startdate + ";"
                        
                        let enddate = item["end_datetime"] as? String ?? ""
                       
                        
                        
                        let concateMsgnadData = concatmessage + concatestartate + enddate
                        print("concatall",concateMsgnadData)
                        
                        self.arrSideMenuItem.append(concateMsgnadData)
                    }
                   
                    
                    
                    
                    self.tableViewProfileDisplay.reloadData()
                   // self.messageLabelMentor.startAnimating()
                    //self.messageLabelMentor.text = messageText
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
                self.logOutMentor()
                print("error")
            }
        }
        
        
    }
    
    
    func values(fromCSVString str: String) -> [String] {
        let separators = CharacterSet(charactersIn: ";")
        return str.components(separatedBy: separators)
    }
    
    
    
    
    
    
    
    @objc func fireTimer() {
            if self.connectedToNetwork() {
               // self.startActivityIndicator()
                MentorApiManager.mentorSharedInstance.getMentorUserDetails(onSuccess: { json in
                    DispatchQueue.main.async {
                      //  print("MentorDetails json :: \(String(describing: json))")
                        DispatchQueue.main.async(execute: {() -> Void in
                            var userDetails = (json as NSDictionary).mutableCopy() as? NSMutableDictionary
                            
                            if (userDetails?["upcoming_meeting"] as? NSDictionary) == nil {
                                userDetails?.removeObject(forKey: "upcoming_meeting")
                            }
                            
                            if (userDetails?["past_meeting"] as? NSDictionary) == nil {
                                userDetails?.removeObject(forKey: "past_meeting")
                            }
                            
                            self.dicJsonMentorResponse = userDetails ?? [:]
                            print("dicJsonMentorResponse\(self.dicJsonMentorResponse)")
                            self.setValues()
                            self.tableViewProfileDisplay.reloadData()
                           // self.stopActivityIndicator()
                        })
                    }
                }, onFailure: { error in
                    
                    
                   self.logOutMentor()
                    
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
    
    
    func backrefreshthree(name: String) {
         self.getUserDetails()
    }
    
    
    func darkmodechnaged() {
        if self.valueMode == "dark" {
            tableSubview.backgroundColor = UIColor(hexString: "#0E0F27")
            backgroundview.backgroundColor = UIColor(hexString: "#0E0F27")
            topbackgroundimage.image = UIImage(named: "BGProfiledark11")
            headerView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableViewProfileDisplay.backgroundColor = UIColor(hexString: "#0E0F27")
            
            labelAgencyProfile.textColor = .white
            labelSessionNo.textColor = .white
            applogged.textColor = .white
            textFieldMentorNameProfile.textColor = .white
            
            
        }
        else if self.valueMode == "light" {
            tableSubview.backgroundColor = .white
            backgroundview.backgroundColor = .white
            topbackgroundimage.image = UIImage(named: "BGProfiledark")
            headerView.backgroundColor = UIColor(hexString: "#A7AE3B")
            tableViewProfileDisplay.backgroundColor = .white
            labelAgencyProfile.textColor = .black
            labelSessionNo.textColor = .black
            applogged.textColor = .black
            textFieldMentorNameProfile.textColor = .black
        }
        
    
    }
    
    @objc func userlogged() {
       // view.backgroundColor = .red
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            backgroundview.backgroundColor = UIColor(hexString: "#0E0F27")
            topbackgroundimage.image = UIImage(named: "BGProfiledark11")
            headerView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableSubview.backgroundColor = UIColor(hexString: "#0E0F27")
            tableViewProfileDisplay.backgroundColor = UIColor(hexString: "#0E0F27")
            
            
            labelAgencyProfile.textColor = .white
            labelSessionNo.textColor = .white
            applogged.textColor = .white
            textFieldMentorNameProfile.textColor = .white
        }
        else if mode == "light" {
            tableSubview.backgroundColor = .white
            backgroundview.backgroundColor = .white
            topbackgroundimage.image = UIImage(named: "BGProfiledark")
            headerView.backgroundColor = UIColor(hex: "#A7AE3B")
            tableViewProfileDisplay.backgroundColor = .white
            
            
            labelAgencyProfile.textColor = .black
            labelSessionNo.textColor = .black
            applogged.textColor = .black
            textFieldMentorNameProfile.textColor = .black
        }
        arrImageSideMenu.removeAll()
        arrSideMenuItem.removeAll()
        self.arrImageSideMenu = ["LogSession","ScheduleSession","MentorProfileOptionFive"]
        self.arrSideMenuItem = ["LOG A SESSION","SCHEDULE A SESSION","CHAT WITH MENTEE"]
        tableViewProfileDisplay.reloadData()
        
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        tableViewProfileDisplay.setContentOffset(.zero, animated:true)
        self.buttonMenu.addTarget(self, action: #selector(MentorProfileViewController.messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
    }
    
    @IBAction func buttonFieldEditAction(_ sender: Any) {
        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Mentor", bundle:nil)
        let myMenteeListVc = storyBoardMain.instantiateViewController(withIdentifier: "EditMentorProfileViewController") as! EditMentorProfileViewController
        self.navigationController?.pushViewController(myMenteeListVc, animated: true)
    }
    
    func numberOfWordsInMentorNameField() -> Bool {
        let strings : String! = textFieldMentorNameProfile.text
        let spaces = CharacterSet.whitespacesAndNewlines.union(.punctuationCharacters)
        let words = strings.components(separatedBy: spaces)
        print(words.count)
        if(words.count < 4){
            return true
        }
        return false
    }
    //MARK:- SideMenu
    func showHideMentorMenuController(_ isShown: Bool) {
        buttonMenu.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowMentorSideMenu() {
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
    }

    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }

    //MARK::Api Service Call
    
    func getmentorTimeZone() {
        MentorApiManager().getTimeZoneMentor(onSuccess: { (response) in
            let status = response["status"] as? Bool
            if let responseStatus = status {
                if responseStatus == true {
                    let arrGeofenceData = response["data"] as? NSDictionary
                    
                    if let timezone = arrGeofenceData {
                    TakeStockInChildrenConstant.mentorTimeZone = ""

                let timeZone = timezone["timezone"] as! String
                        
                TakeStockInChildrenConstant.mentorTimeZone = timeZone
                        
                    } else {
                       
                    }
                }
            } else {
                print("No Geofence data")
            }
        }, onFailure: { (response) in
            self.logOutMentor()
        })
    }
    
    func checkWithimeteronrnot() {
        if !isTrigger {
            
            self.isTrigger = true
            let center = UNUserNotificationCenter.current()
                         let content = UNMutableNotificationContent()
                         content.title = "Reminder"
                         content.body = "You are within 1 Miles"
                        content.sound = .default
                        // let trigger = UNTimeIntervalNotificationTrigger(timeInterval: 10, repeats: false)
                         let request = UNNotificationRequest(identifier: "reminder", content: content, trigger: nil)
                     center.add(request) { (error) in
                         if error != nil {
                             print("Error notification",error?.localizedDescription)
                         }
                     }
            
            
        }
    }
    
    
    
    
    
    @objc func getLatLogForGeofence() {
            print("lat lon fence called")
            MentorApiManager().getLatLogForGeofance(onSuccess: { (response) in
               // print("geofence",response)
                let status = response["status"] as? Bool
                if let responseStatus = status {
                    if responseStatus == true {
                        let arrGeofenceData = response["data"] as? NSArray
                        
                        if let arrGeofenceData = arrGeofenceData {
                            self.UI {
                                self.onAdd(arrGeofences: arrGeofenceData)
                            }
                        } else {
                            self.UI {
                                self.showAlertView(title: "Alert!", msg: "Nothing assign to you", controller: self, okClicked: {})
                            }
                        }
                    }
                } else {
                    print("No Geofence data")
                }
            }, onFailure: { (response) in
               // self.logOutMentor()
            })
    }
    /*
    @objc func getLatLogForGeofence() {
        print("lat lon fence called")
        MentorApiManager().getLatLogForGeofance(onSuccess: { (response) in
            print("geofence",response)
            let status = response["status"] as? Bool
            if let responseStatus = status {
                if responseStatus == true {
                    let arrGeofenceData = response["data"] as? NSArray
                    
                    if let arrGeofenceData = arrGeofenceData {
                        self.UI {
                            self.onAdd(arrGeofences: arrGeofenceData)
                        }
                    } else {
                        self.UI {
                            self.showAlertView(title: "Alert!", msg: "Nothing assign to you", controller: self, okClicked: {})
                        }
                    }
                }
            } else {
                print("No Geofence data")
            }
        }, onFailure: { (response) in
            
        })
    }
    */
    
    
    
    
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
                            
                            
                            let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "FirstViewController")
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
    
    
    func getUserDetails() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            MentorApiManager.mentorSharedInstance.getMentorUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    print("MentorDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        var userDetails = (json as NSDictionary).mutableCopy() as? NSMutableDictionary
                        
                        if (userDetails?["upcoming_meeting"] as? NSDictionary) == nil {
                            userDetails?.removeObject(forKey: "upcoming_meeting")
                        }
                        
                        if (userDetails?["past_meeting"] as? NSDictionary) == nil {
                            userDetails?.removeObject(forKey: "past_meeting")
                        }
                        
                        UserDefaults.standard.setValue(userDetails, forKey: "mentorUserDetails")
                        self.dicJsonMentorResponse = (userDetails ?? [:]) as NSDictionary
                        self.Messages = json["affiliate_system_messaging"] as? [[String : Any]] ?? []
                        
                      //  print("mssg",self.Messages)
                        
                        print("dicJsonMentorResponse\(self.dicJsonMentorResponse)")
                        self.setValues()
                        
                        
                        let dic = self.Messages.first
                        let messageText = dic?["message"] as? String ?? ""
                      //  let sdate = dic?["start_datetime"] as? String ?? ""
                       // let edate = dic?["end_datetime"] as? String ?? ""
                       
                        DispatchQueue.main.async {
                            if messageText == "" {
                               self.viewmsg.isHidden = true
                            }
                           // self.viewmsg.isHidden = false
                            self.messageTxtView.text = messageText
                            
                           // self.messageLabelMentor.startAnimating()
                          //  self.messageLabelMentor.text = messageText
                            //self.lblSdate.text = sdate
                            //self.lblEdate.text = edate
                        }
                        
                      //  self.arrImageSideMenu = ["LogSession","ScheduleSession","MentorProfileOptionFive"]
                       // self.arrSideMenuItem = ["LOG A SESSION","SCHEDULE A SESSION","CHAT WITH MENTEE"]
                        
                        /*
                        for item in self.Messages {
                            self.arrImageSideMenu.append("MentorProfileOptionFive")
                            
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
                print("Error===",error)
                self.logOutMentor()
                /*
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    
                    let alert = UIAlertController(title: "TSIC", message: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    
                    
                })
                */
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK:- SetValues
    func setValues() {
       // print(dicJsonMentorResponse)
        if dicJsonMentorResponse.count > 0 {
            if (dicJsonMentorResponse["profile_pic"] == nil) {
                videoURLStr = dicJsonMentorResponse["profile_pic"] as! String
            } else {
                videoURLStr = TakeStockInChildrenConstant.MentorImageBaseURL.appending(dicJsonMentorResponse["profile_pic"] as! String)
            }
            print("imgurl",videoURLStr)
            imageViewProfile.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
            //TODO:firstname
            if String(describing: dicJsonMentorResponse["firstname"]!) == "" {
                firstName = ""
            } else {
                firstName = String(describing: dicJsonMentorResponse["firstname"]!)
                UserCredential.shared.firstname = firstName
            }
            //TODO:middleName
            if String(describing: dicJsonMentorResponse["middlename"]!) == "" {
                middleName = ""
            } else {
                middleName = String(describing: dicJsonMentorResponse["middlename"]!)
                UserCredential.shared.middlename = middleName
            }
            //TODO:lastname
            if String(describing: dicJsonMentorResponse["lastname"]!) == "" {
                lastName = ""
            } else {
                lastName = String(describing: dicJsonMentorResponse["lastname"]!)
                UserCredential.shared.lastname = lastName
            }
            
            //TODO:Mentor Profile Name
            textFieldMentorNameProfile.text = firstName + " " + middleName + " " + lastName
            
            
            //TODO: SchoolProfile
            if String(describing: dicJsonMentorResponse["agency_name"]!) == "" {
                labelAgencyProfile.text = ""
            } else {
                labelAgencyProfile.text = String(describing: dicJsonMentorResponse["agency_name"]!)
            }
            
            //TODO: SchoolProfile
            if String(describing: dicJsonMentorResponse["session_log_count"]!) == "" {
                labelSessionNo.text = ""
                lblSessionBadgeCount.text = ""
                 
            } else {
                labelSessionNo.text = String(describing: dicJsonMentorResponse["session_log_count"]!)
                lblSessionBadgeCount.text = String(describing: dicJsonMentorResponse["session_log_count"]!)
            }
            
            
            self.mentorId = String(dicJsonMentorResponse["id"] as? Int ?? 0)
            UserCredential.shared.id = self.mentorId ?? ""
            
            let unreadChatCount = String(dicJsonMentorResponse["mentor_mentee_chat_count"] as? Int ?? 0)

            badgeCountMentor.text = unreadChatCount
            
            
            let schedulesessioncountBafge = dicJsonMentorResponse["schedule_session_count"] as? Int ?? 0
            let sessionLoglabel = dicJsonMentorResponse["session_log_label_no"] as? Int ?? 0
            
            var messageCenterCount = dicJsonMentorResponse["message_center_count"] as? Int ?? 0
            var messageCount = String(messageCenterCount)
            
            print("loglabel",sessionLoglabel)
            
            if sessionLoglabel == 0 {
                medalImage.isHidden = true
                lblSessionBadgeCount.isHidden = true
            }
            else if sessionLoglabel == 1 {
              medalImage.isHidden = true
              lblSessionBadgeCount.isHidden = true
            }
            else if sessionLoglabel == 2 {
                medalImage.isHidden = false
                medalImage.image = UIImage(named: "bronze_medal")
                lblSessionBadgeCount.isHidden = false
            }
            else if sessionLoglabel == 3 {
                medalImage.isHidden = false
                medalImage.image = UIImage(named: "silver_medal")
                lblSessionBadgeCount.isHidden = false
            }
            else if sessionLoglabel == 4 {
                medalImage.isHidden = false
                medalImage.image = UIImage(named: "gold_medal")
                lblSessionBadgeCount.isHidden = false
            }
            
            
        
            badgeCountArray.removeAll()
            let logCount = String(schedulesessioncountBafge)
            
            let count_Session = String(describing:
                dicJsonMentorResponse["session_log_count"] ?? 0)
            badgeCountArray.append("0")
            badgeCountArray.append(logCount)
            badgeCountArray.append(unreadChatCount)
            badgeCountArray.append(messageCount)
            
            if (dicJsonMentorResponse["upcoming_meeting"] as? [String:Any])?.count ?? 0 > 0 {
                self.showUpcomingMeeting(dicJsonMentorResponse: dicJsonMentorResponse)
            } else if (dicJsonMentorResponse["past_meeting"] as? [String:Any])?.count ?? 0 > 0 {
                self.showPastMeeting(dicJsonMentorResponse: dicJsonMentorResponse)
            }
            
            /*//TODO:Email
            if String(describing: dicJsonMentorResponse["email"]!) == "" {
                imgEmail.isHidden = true
                labelEmailProfile.text = ""
            } else {
                imgEmail.isHidden = false
                labelEmailProfile.text = String(describing: dicJsonMentorResponse["email"]!)
            }
            
            //TODO:Phone
            if String(describing: dicJsonMentorResponse["phone"]!) == "" {
                imgPhone.isHidden = true
                textFieldPhone.text = ""
            } else {
                imgPhone.isHidden = false
                textFieldPhone.text = String(describing: dicJsonMentorResponse["phone"]!)
            }
            phone = textFieldPhone.text!*/
            
         let mentorStaffcount = "\(dicJsonMentorResponse["mentor_staff_chat_count"] as? Int ?? 0)"
            if mentorStaffcount == "0"{
                self.tabBarController?.tabBar.items![1].badgeValue = nil
            }
            else {
                self.tabBarController?.tabBar.items![1].badgeValue = mentorStaffcount
            }
          
            if logCount == "0"{
                 self.tabBarController?.tabBar.items![2].badgeValue = nil
            }
            else {
                 self.tabBarController?.tabBar.items![2].badgeValue = logCount
            }
         
            /*
            if count_Session == "0"{
                self.tabBarController?.tabBar.items![3].badgeValue = nil
            }
            else {
                
                self.tabBarController?.tabBar.items![3].badgeValue = count_Session
                
            }
            */
            
          
        }
    }
    
    func showPastMeeting(dicJsonMentorResponse:NSDictionary) {
        let pastMeeting = dicJsonMentorResponse["past_meeting"] as? [String:Any]
        
        if (UserDefaults.standard.value(forKey: "Mentor_LastPastMeetinng") as? UInt64 ?? 0) == pastMeeting?["id"] as? UInt64 ?? 0 {
            return
        }
        
        let alertController = UIAlertController(style: .alert, title: "REMINDER TO LOG A SESSION", message: "You had a scheduled session at \(pastMeeting?["schedule_time"] ?? ""). Donn't forget to log the session")
        alertController.addAction(UIAlertAction(title: "Log Now", style: .default, handler: { [weak self] action in
            let newViewController = self?.MentorstoryBoard.instantiateViewController(withIdentifier: "MentorSessionCreatorVC") as! MentorSessionCreatorVC
            newViewController.assignValueAfterPOP = {() -> Void in
//                        self.serviceCallToGetSessionListing()
            }
            self?.navigationController?.pushViewController(newViewController, animated: true)
            alertController.dismiss(animated: true)
        }))
        alertController.addAction(UIAlertAction(title: "Already done", style: .default, handler: { action in
            alertController.dismiss(animated: true)
        }))
        self.present(alertController, animated: true)
        
        UserDefaults.standard.set(pastMeeting?["id"] as? UInt64 ?? 0, forKey: "Mentor_LastPastMeetinng")
    }
    
    func showUpcomingMeeting(dicJsonMentorResponse: NSDictionary) {
        let upcomingMeeting = dicJsonMentorResponse["upcoming_meeting"] as? [String:Any]
        
        if (UserDefaults.standard.value(forKey: "Mentor_LastUpcomingMeetinng") as? UInt64 ?? 0) == upcomingMeeting?["id"] as? UInt64 ?? 0 {
            return
        }
        
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
        let schduledDate = formatter.date(from: (upcomingMeeting?["schedule_time"] as? String) ?? "") ?? Date()
        let formattedSchduledDate = DateTodayFormatter().stringFromDate(date: schduledDate as NSDate) ?? ""
        
        let alertController = UIAlertController(style: .alert, title: "REMINDER! UPCOMING SESSION", message: "Don’t forget! You have an upcoming mentee session at \(formattedSchduledDate) with \(upcomingMeeting?["firstname"] ?? "") \(upcomingMeeting?["lastname"] ?? "")")
        alertController.addAction(UIAlertAction(title: "ok", style: .default, handler: { [weak self] action in
            alertController.dismiss(animated: true)
            
            if (dicJsonMentorResponse["upcoming_meeting"] as? [String:Any])?.count ?? 0 > 0 {
                self?.showPastMeeting(dicJsonMentorResponse: dicJsonMentorResponse)
            }
        }))
        self.present(alertController, animated: true)
        
        UserDefaults.standard.set(upcomingMeeting?["id"] as? UInt64 ?? 0, forKey: "Mentor_LastUpcomingMeetinng")
    }
    
    //MARK:- Update User Details
    func setUpdateUserDetails() {
        if self.connectedToNetwork() {
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!

            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorUpdateUserDetails)
            
           // print("URL\(url)")
            var parameters = [String: String]()

            self.startActivityIndicator()
            print("firstName: \(firstName)")
            print("middleName: \(middleName)")
            print("lastName: \(lastName)")
            
            parameters["firstname"] = String(describing: firstName)
            parameters["middlename"] = String(describing: middleName)
            parameters["lastname"] = String(describing: lastName)

            print(parameters)

            Alamofire.upload(multipartFormData: { multipartFormData in
                if self.profileImage != nil {
                    let imageData = self.profileImage.jpegData(compressionQuality: 6.0)
                    print("ImageData\(String(describing: imageData))")
                    multipartFormData.append(imageData!, withName: "image", fileName: "\(Date().timeIntervalSince1970).jpeg", mimeType: "image/jpeg")
                
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
                        //self.viewBGLoading.isHidden = true
                        
                     //   self.getUserDetails()
                        //self.tableViewProfileDisplay.reloadData()
                        // self.viewSave.isHidden = false
                       // self.dicJsonUpdateMentorResponse = (response as? NSDictionary)!
//
//                        var data = response["data"] as! NSDictionary
//print("data\(data)")
//                        var img = data["image"]
//                        print("image\(img)")
                        // self.showAlert(_sourceController: self, _msg: "Data Updated succesfully")
                        let alert = UIAlertController(title: "Success", message: "Profile updated succesfully" as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in

                    self.stopActivityIndicator()
                            
//                            self.actInd.stopAnimating()
//                            self.viewBGLoading.isHidden = true
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    })

                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
//                    self.actInd.stopAnimating()
//                    self.viewBGLoading.isHidden = true
                    self.showAlertAction(withTitle: "Alert", message: "Profile is not updated")
                }
            })

        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
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
        //        picker?.delegate = self
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
        //print("self.profileImage\(self.profileImage)")
        imageViewProfile.clipsToBounds = true

        // Dismiss the picker.
        dismiss(animated: true, completion: nil)
        setUpdateUserDetails()
    }

    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        // Dismiss the picker if the user canceled.
        dismiss(animated: true, completion: nil)
    }
    
//    @IBAction func buttonEditAction(_ sender: Any) {
////        imageViewEditProfile.isHidden = true
////        buttonEdit.isHidden = true
////        viewSave.isHidden = false
////        imageViewSaveIcon.isHidden = false
////        buttonSave.isHidden = false
////        textFieldUserNameProfile.isUserInteractionEnabled = true
//    }
//
//    @IBAction func buttonSaveAction(_ sender: Any) {
//        //print("USERDETAILS\(String(describing: textFieldUserNameProfile.text))")
//
//        firstName = ""
//        lastName = ""
//        middleName = ""
//
//        let fullName : String = self.textFieldUserNameProfile.text ?? ""
//        let fullNameArr : [String] = fullName.components(separatedBy: " ")
//
//        if(fullNameArr.count != 0){
//            if(fullNameArr.count == 3){
//                firstName = fullNameArr[0]
//                middleName = fullNameArr[1]
//                lastName = fullNameArr[2]
//            }
//            else if(fullNameArr.count == 2){
//                firstName = fullNameArr[0]
//                lastName = fullNameArr[1]
//            }
//            else{
//                firstName = fullNameArr[0]
//            }
//
//        }
//        else{
//            print("NO USERNAME")
//        }
//
//        setUpdateUserDetails()
//        imageViewEditProfile.isHidden = false
//        buttonEdit.isHidden = false
//        viewSave.isHidden = true
//        imageViewSaveIcon.isHidden = true
//        buttonSave.isHidden = true
//        textFieldUserNameProfile.isUserInteractionEnabled = false
//    }
//
//    @IBAction func buttonEditPhoneAction(_ sender: Any) {
//        imageViewPhoneEdit.isHidden = true
//        buttonEditPhone.isHidden = true
//        viewPhoneSave.isHidden = false
//        imageViewSaveIcon.isHidden = false
//        buttonPhoneSave.isHidden = false
//        textFieldPhone.isUserInteractionEnabled = true
//    }
    
    
//    @IBAction func buttonPhoneSaveAction(_ sender: Any) {
//
//        setUpdateUserDetails()
//
//        viewSave.isHidden = true
//        imageViewSave.isHidden = true
//        buttonSave.isHidden = true
//        imageViewEdit.isHidden = false
//        buttonEdit.isHidden = false
////        imageViewEditProfile.isHidden = false
////        buttonEdit.isHidden = false
////        viewSave.isHidden = true
////        imageViewSaveIcon.isHidden = true
////        buttonSave.isHidden = true
//        textFieldPhone.isUserInteractionEnabled = false
//    }
//    @IBAction func buttonLogOutAction(_ sender: Any) {
//
//        let alert = UIAlertController(title: "Logging Out", message: "Are you sure you want to logout From Mentor", preferredStyle: UIAlertController.Style.alert)
//
//        let acceptAction = UIAlertAction(title: "Yes", style: .default) { (_) -> Void in
//            UserDefaults.standard.setValue(nil, forKey: "mentorUserDetails")
//
//            let appDelegate = UIApplication.shared.delegate as? AppDelegate
//            let loginController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
//            let nav = UINavigationController(rootViewController: loginController)
//            nav.navigationBar.isHidden = true;
//            appDelegate?.window!.rootViewController = nav
//        }
//        let cancelAction = UIAlertAction(title: "No", style: .cancel) { (_) -> Void in
//
//        }
//        alert.addAction(acceptAction)
//        alert.addAction(cancelAction)
//        self.present(alert, animated: true, completion: nil)
//    }
//
    @IBAction func buttonMenuAction(_ sender: Any) {
//        let alert = UIAlertController(title: "TakeStockInChildren", message: "Not Implemented..", preferredStyle: UIAlertController.Style.alert)
//        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
//            print("Action")
//        }))
//        self.present(alert, animated: true, completion: nil)
    }
}

// MARK:: UITableViewDatasource
extension MentorProfileViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrImageSideMenu.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        /*
        if indexPath.row > 2 {
            let cell = tableView.dequeueReusableCell(withIdentifier: "BadgeProfiileTableViewCell") as! BadgeProfiileTableViewCell
            print("arraysidemenu Item",self.arrSideMenuItem[indexPath.row])
            
            let semicoloncsv = self.arrSideMenuItem[indexPath.row]
            let semicolonvalues = values(fromCSVString: semicoloncsv)
            print("values",semicolonvalues)
            
         //   DispatchQueue.global(qos: .userInteractive).async {
              //  DispatchQueue.main.async {
               // cell.mainView.contentView = cell.msgLabel
               // cell.mainView.isShimmering = true
            //    DispatchQueue.main.async {
                    cell.msgLabel.text = semicolonvalues[0]
                    cell.sDatavalues.text = semicolonvalues[1]
                    cell.eDatevalues.text = semicolonvalues[2]
             //   }
                   
              //
           // }
          //  }x
            
            
            print("data",semicolonvalues[0],semicolonvalues[1],semicolonvalues[2])
            
            
            
           
            
            return cell
        }
        */
     //   else {
            
            
            let cell = tableView.dequeueReusableCell(withIdentifier: "MentorProfileTableViewCell") as! MentorProfileTableViewCell
            let mode = UserDefaults.standard.value(forKey: "mode") as? String
            if mode == "dark"{
                print("dark phase")
                cell.contentView.backgroundColor = UIColor(hexString: "#0E0F27")
                cell.labelMentorOptionsMennu.textColor = .white
                cell.mainView.backgroundColor = UIColor(hexString: "#0E0F27")
            }
            else if mode == "light" {
                print("light phase")
                cell.contentView.backgroundColor = .white
                cell.labelMentorOptionsMennu.textColor  = .black
                cell.mainView.backgroundColor = .white
            }
            
            
            
            if arrImageSideMenu.count > 0 {
                cell.imageViewMentorIcon.image = UIImage(named: arrImageSideMenu[indexPath.row])
            }
            if arrSideMenuItem.count > 0 {
                cell.labelMentorOptionsMennu.text = arrSideMenuItem[indexPath.row]
                
                if UIDevice.current.screenType.rawValue ==  "iPhone 4 or iPhone 4S" || UIDevice.current.screenType.rawValue ==  "iPhone 5, iPhone 5s, iPhone 5c or iPhone SE" {
                    cell.labelMentorOptionsMennu.font = cell.labelMentorOptionsMennu.font.withSize(11)
                } else if UIDevice.current.screenType.rawValue == "iPhone 6, iPhone 6S, iPhone 7 or iPhone 8" {
                    cell.labelMentorOptionsMennu.font = cell.labelMentorOptionsMennu.font.withSize(14)
                } else {
                    cell.labelMentorOptionsMennu.font = cell.labelMentorOptionsMennu.font.withSize(16)
                }
            }
            cell.cellBadgeCount.layer.cornerRadius = cell.cellBadgeCount.frame.width/2
            cell.cellBadgeCount.layer.masksToBounds = true

            if badgeCountArray.count > 0 {
                let value = badgeCountArray[indexPath.row]
                if value == "0"{
                    cell.cellBadgeCount.isHidden = true
                }
                else {
                    cell.cellBadgeCount.isHidden = false
                }
            }
            if badgeCountArray.count > 0 {
                cell.cellBadgeCount.text = badgeCountArray[indexPath.row]
            }
            
            
            return cell
            
     //   }
        
        /*
        if indexPath.row > 2 {
            cell.mainView.backgroundColor = UIColor.init(hexString: "a0a628")
            cell.imageViewMentorIcon.isHidden =  true
            cell.labelMentorOptionsMennu.textAlignment = .center
            cell.mentorRIghticon.isHidden = true
        }
        */
        
    }
}

// MARK:: UITableViewdDelegate
extension MentorProfileViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if (indexPath.row == 0) {
            //TODO: Log A Session
            self.UI {
                //let mentorSessionStoryboard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
                //let newViewController = mentorSessionStoryboard.instantiateViewController(withIdentifier: "MentorSessionCreatorVC") as! MentorSessionCreatorVC
                //self.navigationController?.pushViewController(newViewController, animated: true)
                
                let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionLogListViewController") as! SessionLogListViewController
                //newViewController.iAmFrom = "session"
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                
                /*
                let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionViewController") as! SessionViewController
                newViewController.iAmFrom = "session"
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                */
            }
        } else if (indexPath.row == 1) {
            //TODO: Schedule a session
            self.UI {
                let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionManagementViewController") as! SessionManagementViewController
                //newViewController.iAmFrom = "session"
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                /*
                let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "SessionViewController") as? SessionViewController
                vc!.iAmFromProfile = true
                vc?.delegate = self
                self.navigationController?.pushViewController(vc!, animated: true)
                */
            }
        } else if (indexPath.row == 2) {
          
            //TODO: Chat with mentee
            /*
            self.UI {
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "MenteeListFromMontorVC") as! MenteeListFromMontorVC
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            */
            
            
            self.UI {
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "MenteeListFromMontorVC") as! MenteeListFromMontorVC
                newViewController.delegate = self
                newViewController.firstname = self.firstName
                newViewController.middleName = self.middleName
                newViewController.lastName = self.lastName
                newViewController.Id = self.mentorId
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            
            
        }
        else if (indexPath.row == 3) {
            
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "MessageCenterViewController") as! MessageCenterViewController
            resourceVc.type = "mentor"
            resourceVc.delegate = self
            self.navigationController?.pushViewController(resourceVc, animated: true)
            
        }
        
        /*
        else if (indexPath.row == 4) {
            /*
            self.UI {
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "TwilloTextChatViewController") as! TwilloTextChatViewController
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            */
            self.UI {
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "MenteeListFromMontorVC") as! MenteeListFromMontorVC
                newViewController.delegate = self
                newViewController.tag = "twillo"
                newViewController.firstname = self.firstName
                newViewController.middleName = self.middleName
                newViewController.lastName = self.lastName
                newViewController.Id = self.mentorId
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            
            
            
            
        }
    */
        
        /*else if (indexPath.row == 5) {
            //TODO: Resource
            let elearningVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ElearningViewController")
            self.navigationController?.pushViewController(elearningVc, animated: true)
            
            
            //showAlert(_sourceController: self, _msg: "No resources found")
        }
        {
            let alert = UIAlertController(title: "Logging Out", message: "Are you sure you want to logout From Mentor", preferredStyle: UIAlertController.Style.alert)
            
            let acceptAction = UIAlertAction(title: "Yes", style: .default) { (_) -> Void in
                UserDefaults.standard.setValue(nil, forKey: "mentorUserDetails")
                UserDefaults.standard.setValue(nil, forKey: "loginMode")

                let appDelegate = UIApplication.shared.delegate as? AppDelegate
                let loginController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
                let nav = UINavigationController(rootViewController: loginController)
                nav.navigationBar.isHidden = true;
                appDelegate?.window!.rootViewController = nav
            }
            let cancelAction = UIAlertAction(title: "No", style: .cancel) { (_) -> Void in
                
            }
            alert.addAction(acceptAction)
            alert.addAction(cancelAction)
            self.present(alert, animated: true, completion: nil)
        }*/
    }
}

extension MentorProfileViewController {
    
    struct PreferencesKeys {
        static let savedItems = "savedItems"
    }
    
    // MARK:- Loading and saving functions
    func loadAllGeotifications() {
        geotifications = []
        guard let savedItems = UserDefaults.standard.array(forKey: PreferencesKeys.savedItems) else { return }
        for savedItem in savedItems {
            guard let geotification = NSKeyedUnarchiver.unarchiveObject(with: savedItem as! Data) as? Geotification else { continue }
            add(geotification: geotification)
        }
    }
    
    func onAdd(arrGeofences: NSArray) {
        if arrGeofences.count == 0 {
            self.removeAllGeotifications()
        }
        
        
        for index in 0 ..< arrGeofences.count {
            let dictDetails : NSDictionary = arrGeofences.object(at: index) as! NSDictionary
            let strLat = dictDetails["latitude"] as? String
            let strLong = dictDetails["longitude"] as? String
            
            let doubleLat = Double(strLat ?? "") ?? 0.0
            let doublelon = Double(strLong ?? "") ?? 0.0
            
            let id = dictDetails["id"] as! NSNumber
            let title = dictDetails["title"] as? String ?? ""
            
            let savedlat = UserDefaults.standard.double(forKey: "latitude")
            let savedlon = UserDefaults.standard.double(forKey: "longitude")
           // print("savedlat and long",savedlat,savedlon)
            print("lat and lon",savedlat,savedlon,strLat,strLong)
            let coordinate₀ = CLLocation(latitude: savedlat, longitude: savedlon) //34.54545 // 56.64646
            let coordinate₁ = CLLocation(latitude: doubleLat, longitude: doublelon) //59.32635 //18.072310

            let distanceInMeters = coordinate₀.distance(from: coordinate₁) / 1000// result is in meters
            let kms = String(format:"%.02f", distanceInMeters)
            print("meter",kms)
            if kms <= "622" {
                checkWithimeteronrnot()
                print("Within kilometer")
            }
            else {
                print("Not within")
            }
            
            
            let coordinate2D = CLLocationCoordinate2D(latitude:  Double((strLat as! NSString).doubleValue) , longitude: Double((strLong as! NSString).doubleValue))
            
            let coordinate = coordinate2D
            
//            Common().showAlertView(title: "test geo Lat", msg: strLong as! String, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//            })
//            
            let clampedRadius = min(1000, locationManager.maximumRegionMonitoringDistance)//1000
            let geotification = Geotification(coordinate: coordinate, radius: clampedRadius, identifier: "\(id)", note: title, eventType: .onEntry)
            add(geotification: geotification)
            // 2
            startMonitoring(geotification: geotification)
            saveAllGeotifications()
        }
    }
    
    func removeAllGeotifications() {
        geotifications = []
        guard let savedItems = UserDefaults.standard.array(forKey: PreferencesKeys.savedItems) else { return }
        for savedItem in savedItems {
            guard let geotification = NSKeyedUnarchiver.unarchiveObject(with: savedItem as! Data) as? Geotification else { continue }
            remove(geotification: geotification)
            stopMonitoring(geotification: geotification)
            updateGeotificationsCount()
        }
    }
    
    func saveAllGeotifications() {
        var items: [Data] = []
        for geotification in geotifications {
            let item = NSKeyedArchiver.archivedData(withRootObject: geotification)
            items.append(item)
        }
        UserDefaults.standard.set(items, forKey: PreferencesKeys.savedItems)
    }
    
    // MARK: Functions that update the model/associated views with geotification changes
    func add(geotification: Geotification) {
        geotifications.append(geotification)
        updateGeotificationsCount()
    }
    
    func remove(geotification: Geotification) {
        if let indexInArray = geotifications.index(of: geotification) {
            geotifications.remove(at: indexInArray)
        }
        updateGeotificationsCount()
    }
    
    func updateGeotificationsCount() {
      
       // navigationItem.rightBarButtonItem?.isEnabled = (geotifications.count < 20)
        
        AppDelegate().isUserAllowForLocalNotification()
    }
    
    
    
    func region(withGeotification geotification: Geotification) -> CLCircularRegion {
        // 1
        let region = CLCircularRegion(center: geotification.coordinate, radius: geotification.radius, identifier: geotification.identifier)
        // 2
        region.notifyOnEntry = (geotification.eventType == .onEntry)
        region.notifyOnExit = !region.notifyOnEntry
        return region
    }
    
    func startMonitoring(geotification: Geotification) {
        // 1
        if !CLLocationManager.isMonitoringAvailable(for: CLCircularRegion.self) {
            //showAlert(withTitle:"Error", message: "Geofencing is not supported on this device!")
            return
        }
        // 2
        if CLLocationManager.authorizationStatus() != .authorizedAlways {
            //showAlert(withTitle:"Warning", message: "Your geotification is saved but will only be activated once you grant Geotify permission to access the device location.")
        }
        // 3
        let region = self.region(withGeotification: geotification)
        // 4
        locationManager.startMonitoring(for: region)
    }
    
    func stopMonitoring(geotification: Geotification) {
        for region in locationManager.monitoredRegions {
            guard let circularRegion = region as? CLCircularRegion, circularRegion.identifier == geotification.identifier else { continue }
            locationManager.stopMonitoring(for: circularRegion)
        }
    }
}
