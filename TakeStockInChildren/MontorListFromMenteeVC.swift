//
//  MontorListFromMenteeVC.swift
//  TakeStockInChildren
//
//  Created by Moumita  on 16/01/20.
//  Copyright © 2020 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
protocol backrefreshfive {
    func backrefreshfive(name: String)
}
class MontorListFromMenteeVC: BaseViewController, MentorMenuControllerDelegate, MenuControllerDelegate,datapass {
    
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var backgroundimageview: UIImageView!
    
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    @IBOutlet weak var tableViewMyMenteeList: UITableView!
    
    
    var arrTempMentee = [[String : AnyObject]]()
   // var arrMenteeList = [Dictionary<String, Any>]()
    var arrMenteeList = [NSDictionary]()
    var DicMenteeList = NSDictionary()
    var valueMode : String?

    var isImageHidden : Bool = true
    var videoURLStr : String = ""
    let menteeClicked = "mentee"
    let staffClicked = "staff"
    var chatBy = ""
    var identifyButtonClicked = ""
    
    var firstName :String? = ""
    var middleName :String? = ""
    var lastName :String? = ""
    var Id: String?
    var tag: String?
    
    @IBOutlet weak var btnSideMenuOutlet: UIButton!
    @IBOutlet weak var btnMenteeOutlet: UIButton!
    @IBOutlet weak var btnStaffOutlet: UIButton!
    @IBOutlet weak var lblTitle: UILabel!
    var delegate : backrefreshfive!
    
    
    //MARK:: UIVIew
    override func viewDidLoad() {
        super.viewDidLoad()
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodeChanged()
      //   self.tabBarController?.tabBar.items![1].badgeValue = nil
       // self.tabBarController?.tabBar.items![3].badgeValue = nil

        
        self.identifyButtonClicked = menteeClicked
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            self.btnSideMenuOutlet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
            self.btnSideMenuOutlet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
            self.btnMenteeOutlet.setTitle("Mentee", for: .normal)
            getMenteeList()
        } else {
            self.btnSideMenuOutlet.setImage(UIImage(named: "Back"), for: UIControl.State.normal)
            self.btnSideMenuOutlet.addTarget(self, action: #selector(btnBackAction), for: UIControl.Event.touchUpInside)
            
            getMentorDetails()
        }
    }
    
    
    func darkmodeChanged() {
        if self.valueMode == "dark" {
            backgroundimageview.image = UIImage(named: "BG4")
            tableViewMyMenteeList.backgroundColor = UIColor(hex: "#0E0F27")
            headerView.backgroundColor = UIColor(hex: "#0E0F27")
        }
        else if self.valueMode == "light" {
            backgroundimageview.image = UIImage(named: "BackgroundImage")
            tableViewMyMenteeList.backgroundColor = .white
            headerView.backgroundColor = UIColor(hex: "#A0A628")
        }
    }
    
    func passdata(tag: String) {
        print("passdata",tag)
        /*
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
         if loginMode == "Mentor" {
            getMenteeList()
        }
         else {
            getMentorDetails()
        }
        */
    }
    
    
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
    
    }
    
    //MARK:- SideMenu
    @objc func btnBackAction() {
        self.delegate?.backrefreshfive(name: "Hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    func showHideMenuController(_ isShown: Bool) {
        btnSideMenuOutlet.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowSideMenu() {
        let objSideMenu = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "MenuViewController") as! MenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMenuInController(self)
    }
    
    
    //MARK:: IBAction
    @IBAction func btnMenteeAction(_ sender: Any) {
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode=="Mentor" {
            self.btnMenteeOutlet.setTitle("Mentee", for: .normal)
            
            self.lblTitle.text = "MY MENTEE"
            self.btnStaffOutlet.setTitleColor(UIColor.gray, for: .normal)
            self.btnMenteeOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
            
            self.identifyButtonClicked = menteeClicked
            getMenteeList()
        } else {
            self.btnMenteeOutlet.setTitle("Mentor", for: .normal)
            
            self.lblTitle.text = "MY MENTOR"
            self.btnStaffOutlet.setTitleColor(UIColor.gray, for: .normal)
            self.btnMenteeOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
            
            self.identifyButtonClicked = menteeClicked
            getMentorDetails()
        }
    }
    
    @IBAction func btnStaffAction(_ sender: Any) {
        
        self.lblTitle.text = "MY STAFF"
        
        self.btnStaffOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        self.btnMenteeOutlet.setTitleColor(UIColor.gray, for: .normal)
        self.identifyButtonClicked = staffClicked
        getStaffList()
    }
    //MARK:- SideMenu
    func showHideMentorMenuController(_ isShown: Bool) {
        btnSideMenuOutlet.isUserInteractionEnabled = !isShown
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
    
    //MARK:- API Service Call
    func getMentorDetails () {
        print("get mentor details called")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getMentorDetails(onSuccess: { json in
                DispatchQueue.main.async {
                  //  print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        /*
                        guard let mentorDetails = json["mentor_details"] as? [Dictionary<String, Any>] else {
                            return
                        }
                        */
                        
                        let dictMain:NSDictionary = json as! NSDictionary
                        self.arrTempMentee = json["mentor_details"] as? [[String: AnyObject]] ?? []
                     //   let data = json["mentor_details"] as? [Dictionary<String, AnyObject>] ?? []
                        self.arrMenteeList = self.arrTempMentee as [NSDictionary]
                       
                        
                       // self.arrMenteeList = //dictMain["data"] as! [NSDictionary]
                        print("array",self.arrMenteeList)
                      //  getChatMessages(id: <#T##Int#>)
                        self.DicMenteeList = dictMain
                        self.tableViewMyMenteeList.reloadData()
                        
                        self.stopActivityIndicator()
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.logOutMentee()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
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
        
//        if self.strIamfrom == "mentee" {
//            parameter["mentee_id"] = "\(id)"
//        } else { //Mentor or Staff
//            parameter["staff_id"] = "\(id)"
//        }
        
        MentorApiManager().mentee_chat(parameter: parameter, chatBy: chatBy, pageList: 1) { (json) in
            self.stopActivityIndicator()
            print("chat called-------",json,self.chatBy)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
               // self.chatCode = dicData["chat_code"] as! String
               
        }
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
    
    
    
    
    func getMenteeList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            guard let token  = UserDefaults.standard.string(forKey: "mentorToken") else {
                return self.showAlert(_sourceController: self, _msg: "Token Missing")
            }
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeList)
            
            print("MENTEELISTURL\(url)")
            
            self.arrMenteeList.removeAll()
            
            Alamofire.request(url, method:.post, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    let dictVal = response.result.value
                    
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    
                    self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                    self.DicMenteeList = dictMain
                    self.tableViewMyMenteeList.reloadData()
                    
                    self.stopActivityIndicator()
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    func getStaffList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            var getToken = ""
            
            var totalBaseUrl = ""
            
            var methodType = ""
            
            let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
            
            if loginMode=="Mentor" {
                guard let token  = UserDefaults.standard.string(forKey: "mentorToken") else {
                    return self.showAlert(_sourceController: self, _msg: "Mentor Token Missing")
                }
                
                getToken = token
                methodType = "POST"
                totalBaseUrl = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.StaffList)
            } else {
                guard let token  = UserDefaults.standard.string(forKey: "token") else {
                    return self.showAlert(_sourceController: self, _msg: "Mentee Token Missing")
                }
                
                getToken = token
                methodType = "GET"
                
                totalBaseUrl = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeStaffList)
            }
            
            let headers = [
                "Authorizations": getToken,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            //let headers = ["Vauthtoken":"Bearer \(men)"]
            print(headers)
            
            print("MEN  TEELISTURLFORSTAFF >>>>>>> \(totalBaseUrl)")
            self.arrMenteeList.removeAll()
            
            Alamofire.request(totalBaseUrl, method:HTTPMethod(rawValue: methodType)!, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    
                    if loginMode == "Mentor" {
                        self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                        
                        self.DicMenteeList = dictMain
                    } else {
                        guard let dicOfStaff = dictMain["data"] as? NSDictionary else{
                            return
                        }
                        self.arrMenteeList = dicOfStaff["staffs"] as! [NSDictionary]
                        
                        self.DicMenteeList = dictMain
                    }
                    
                    self.tableViewMyMenteeList.reloadData()
                    self.stopActivityIndicator()
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @objc func buttonShowMyMenteeDetailsAction(sender: UIButton){
        let index = sender.tag
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "MyMenteeDetailsViewController") as? MyMenteeDetailsViewController
            
            if identifyButtonClicked == menteeClicked {
                if arrMenteeList.count > 0 {
                    let dicMentee: NSDictionary = self.arrMenteeList[index]
                    let name : String = ((dicMentee["firstname"] as? String)!)  + " " + ((dicMentee["middlename"] as? String)!) + " " + ((dicMentee["lastname"] as? String)!)
                    
                    vc?.strMenteeName = name
                    vc?.strMenteeEmail = dicMentee["email"] as! String
                    vc?.strMenteePhoneNumber = dicMentee["cell_phone_number"] as! String
                    vc?.strMenteeCurrentLivingDetails = dicMentee["current_living_details"] as! String
                    let profileImage = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(dicMentee["image"] as! String)
                    vc?.strMenteeImageUrl = profileImage
                    vc?.strIamFrom = identifyButtonClicked
                    
                    let arrData = self.DicMenteeList["data"] as! NSArray
                    vc?.arrMenteeList = arrData[index] as! NSDictionary
                }
            } else {
                let profileImage = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(arrMenteeList[index]["profile_pic"] as! String)
                vc?.strMenteeImageUrl = profileImage
                vc?.strIamFrom = identifyButtonClicked
                let arrData = self.DicMenteeList["data"] as! NSArray
                vc?.arrMenteeList = arrData[index] as! NSDictionary
            }
            self.navigationController?.pushViewController(vc!, animated: true)
        } else {
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            self.UI {
                if self.identifyButtonClicked == self.menteeClicked{
                    let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                    newViewController.loginMenteeButMentorDicData = self.arrMenteeList[index]
                    newViewController.senderIDForMentor = String(describing: self.arrMenteeList[index]["id"]!)
                    newViewController.firebaseKeyReceiver = self.arrMenteeList[index]["firebase_id"] as? String
                    newViewController.strIamfrom = ""
                    self.navigationController?.pushViewController(newViewController, animated: true)
                } else {
                    guard let dicData = self.DicMenteeList["data"] as? NSDictionary else{
                        return
                    }
                    let arrData = dicData["staffs"] as! NSArray
                    
                    let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                    newViewController.strIamfrom = "staff"
                    newViewController.loginMenteeButMentorDicData = arrData[index] as! NSDictionary
                    self.navigationController?.pushViewController(newViewController, animated: true)
                }
            }
        }
     
    }
}

// MARK:: UITableViewDatasource
extension MontorListFromMenteeVC: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMenteeList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        print("arr mentte list")
        let myMenteeListCell = tableView.dequeueReusableCell(withIdentifier: "MyMenteeListTableViewCell") as! MyMenteeListTableViewCell
        if self.valueMode == "dark" {
           // myMenteeListCell.contentView.backgroundColor = .black
            myMenteeListCell.viewMenteeCell.backgroundColor = UIColor(hex: "#232137")
            myMenteeListCell.labelMyMenteeName.textColor = .white
            myMenteeListCell.lastSessionLabel.textColor = .white
        }
        else if self.valueMode == "light" {
           // myMenteeListCell.contentView.backgroundColor = .white
            myMenteeListCell.viewMenteeCell.backgroundColor = .white
            myMenteeListCell.labelMyMenteeName.textColor = .black
            myMenteeListCell.lastSessionLabel.textColor = .black
        }
        
        if arrMenteeList.count > 0 {
            let dicMentee: NSDictionary = arrMenteeList[indexPath.row]
            
            
            let dicStaff: NSDictionary = arrMenteeList[indexPath.row]
            
            let mentorTypeCheck: Int = dicStaff["is_primary"] as? Int ?? 0
            
            if mentorTypeCheck == 1 {
                myMenteeListCell.viewMenteeCell.borderColor = UIColor.red
            }
            else {
                print("Not set")
            }
            
            
            let session_log_count: String = dicStaff["session_count"] as? String ?? ""
            let name : String = ((dicStaff["firstname"] as? String)!) + " " + ((dicStaff["lastname"] as? String)!)
            let count: Int = dicStaff["session_log_label_no"] as? Int ?? -1
            print("count",count)
            let sessionCount = dicStaff["session_log_count"] as? Int ?? -1
            let loglabel: String = dicStaff["session_log_label"] as? String ?? ""
            
            myMenteeListCell.labelMyMenteeName.text = name
            /*
            if count == 1 {
                myMenteeListCell.labelMyMenteeName.text = name
                
            }
            else {
                let fulldes = "\(name) (\(loglabel))"
                let multiplierAttributedString: NSMutableAttributedString = NSMutableAttributedString(string: fulldes)
                multiplierAttributedString.setColor(color: UIColor(hexString: "000000"), forText: name)
                multiplierAttributedString.setColor(color: UIColor(hexString:"FF0000"), forText: loglabel)
                myMenteeListCell.labelMyMenteeName.attributedText = multiplierAttributedString
            }
            */
            
            
            
            
            
            if count == 0 {
                myMenteeListCell.badgeImage.isHidden = true
                myMenteeListCell.badgeLabel.isHidden = true
            }
            else if count == 1 {
              myMenteeListCell.badgeImage.isHidden = true
              myMenteeListCell.badgeLabel.isHidden = true
            }
            else if count == 2 {
                myMenteeListCell.badgeLabel.isHidden = false
                myMenteeListCell.badgeLabel.text = "\(sessionCount)"
                myMenteeListCell.badgeImage.isHidden = false
                myMenteeListCell.badgeImage.image = UIImage(named: "bronze_medal")
            }
            else if count == 3 {
                myMenteeListCell.badgeLabel.isHidden = false
                myMenteeListCell.badgeLabel.text = "\(sessionCount)"
                myMenteeListCell.badgeImage.isHidden = false
                myMenteeListCell.badgeImage.image = UIImage(named: "silver_medal")
            }
            else if count == 4 {
                myMenteeListCell.badgeLabel.isHidden = false
                myMenteeListCell.badgeLabel.text = "\(sessionCount)"
                myMenteeListCell.badgeImage.isHidden = false
                myMenteeListCell.badgeImage.image = UIImage(named: "gold_medal")
            }
            let session_log_label: String = dicStaff["last_session_date"] as? String ?? ""
            
            
            myMenteeListCell.lastSessionLabel.text = "Last Session:\(" ") \(session_log_label)"
            let myString = String(count)

            print("coutnee",session_log_count)
            myMenteeListCell.numberofSessionlabel.text = "Number of Sessions: \(" ")\(myString)"
            //let newMsgCount = dicStaff["unread_chat_count"]!
            //myMenteeListCell.lblNewChatCount.text = String(describing:newMsgCount)
            
            myMenteeListCell.lblNewChatCount.isHidden = true
            //let totalName = self.totalName(dicData: dicMentee)
            //myMenteeListCell.labelMyMenteeName.text = totalName
            
            let image : String? = dicMentee["image"] as? String
            if let img = image {
                /*let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                
                if loginMode == "Mentor" {
                    videoURLStr = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(img)
                } else {
                    videoURLStr = TakeStockInChildrenConstant.MentorImageBaseURL.appending(img)
                }*/
                videoURLStr = TakeStockInChildrenConstant.MentorImageBaseURL.appending(img)
                print("videoURLStr::\(videoURLStr)")
                myMenteeListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
            }
        }
//        myMenteeListCell.buttonShowMenteeDetailsTap.tag = indexPath.row
//        myMenteeListCell.buttonShowMenteeDetailsTap.addTarget(self, action: #selector(buttonShowMyMenteeDetailsAction), for: .touchUpInside)
        
        return myMenteeListCell
    }
    
}

// MARK:: UITableViewDatasource
extension MontorListFromMenteeVC: UITableViewDelegate {
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 100.0
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        print("indexpath.rowtaa\(indexPath.row)")
        
        
      //  if tag == "twillo" {
            
            
            
            self.UI {
                
                let obj = self.arrMenteeList[indexPath.row]
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "TwilloTextChatViewController") as! TwilloTextChatViewController
                newViewController.OtheruserId = String(obj["id"] as? Int ?? 0)
                newViewController.OtheruserName = obj["firstname"] as? String
                newViewController.OtherchatCode = obj["code"] as? String
                UserDefaults.standard.setValue("IsStaff", forKey: "fromStaff")
                UserDefaults.standard.synchronize()

                newViewController.firstname = self.firstName
                newViewController.middleName = self.middleName
                newViewController.lastName = self.lastName
                newViewController.Id = self.Id
                newViewController.csid = obj["channel_sid"] as? String
                
                
                newViewController.receiverTypeCheck = "mentee"
                newViewController.imageBaseUrl = TakeStockInChildrenConstant.MentorImageBaseURL
                newViewController.strUrl = obj["image"] as? String ?? ""
                self.navigationController?.pushViewController(newViewController, animated: true)
                
                
                /*
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                newViewController.strIamfrom = "mentee"
                newViewController.loginMenteeButMentorDicData = self.arrMenteeList[index]
                newViewController.firebaseKeyReceiver = self.arrMenteeList[index]["firebase_id"] as? String
                newViewController.receiverTypeCheck = "mentee"
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
                */
            }
            
            
            
      //  }
        /*
        else {
            
            
            let index = indexPath.row
            
            let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
            newViewController.strIamfrom = ""//"staff"
            newViewController.senderIDForMentor = String(describing: arrMenteeList[index]["id"]!)
            newViewController.firebaseKeyReceiver = self.arrMenteeList[index]["firebase_id"] as? String
            newViewController.loginMenteeButMentorDicData = arrMenteeList[index]
            newViewController.receiverTypeCheck = "mentor"
            newViewController.delegate = self
            self.navigationController?.pushViewController(newViewController, animated: true)
                /*
                 let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                 self.UI {
                    if self.identifyButtonClicked == self.menteeClicked{
                        let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                        newViewController.strIamfrom = ""
                        self.navigationController?.pushViewController(newViewController, animated: true)
                    } else {
                        guard let dicData = self.DicMenteeList["data"] as? NSDictionary else{
                            return
                        }
                        let arrData = dicData["staffs"] as! NSArray
                        
                        let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                        newViewController.strIamfrom = "staff"
                        newViewController.loginMenteeButMentorDicData = arrData[indexPath.row] as! NSDictionary
                        self.navigationController?.pushViewController(newViewController, animated: true)
                    }
                }*/
            
            
        }
        */
    }
}




extension NSMutableAttributedString {
    func setColor(color: UIColor, forText stringValue: String) {
       let range: NSRange = self.mutableString.range(of: stringValue, options: .caseInsensitive)
        self.addAttribute(NSAttributedString.Key.foregroundColor, value: color, range: range)
    }
    func setLink(link: String = "",  forText stringValue: String)  {
        let range: NSRange = self.mutableString.range(of: stringValue, options: .caseInsensitive)
        self.addAttribute(NSAttributedString.Key.link, value: link, range: range)
    }
    func setParagraph(forText stringValue: String, allignment: NSTextAlignment) {
        let paragraph = NSMutableParagraphStyle()
        paragraph.alignment = allignment
        let range: NSRange = self.mutableString.range(of: stringValue, options: .caseInsensitive)
        self.addAttribute(NSAttributedString.Key.paragraphStyle, value: paragraph, range: range)
    }

}
