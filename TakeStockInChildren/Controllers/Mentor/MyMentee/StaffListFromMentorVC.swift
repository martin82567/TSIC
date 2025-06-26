//
//  StaffListFromMentorVC.swift
//  TakeStockInChildren
//
//  Created by Aquarious  on 14/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class StaffListFromMentorVC: BaseViewController, MentorMenuControllerDelegate,datapass {
    
    
    @IBOutlet weak var headerview: UIView!
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    @IBOutlet weak var tblStaffList: UITableView!
    
    @IBOutlet weak var imageBackground: UIImageView!
    var arrStaffList = [NSDictionary]()
    var dicStaffList = NSDictionary()
    
    var isImageHidden : Bool = true
    var videoURLStr : String = ""
    var selectedIndex : Int?
    let menteeClicked = "mentee"
    let staffClicked = "staff"
    var valueMode : String?
    
    var identifyButtonClicked = ""
    
    @IBOutlet weak var btnSideMenuOutlet: UIButton!
    //@IBOutlet weak var btnMenteeOutlet: UIButton!
    //@IBOutlet weak var btnStaffOutlet: UIButton!
    @IBOutlet weak var lblTitle: UILabel!
    
   
    override func viewDidLoad() {
        super.viewDidLoad()
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkModechange()
     //   NotificationCenter.default.addObserver(self, selector: #selector(changeMode), name: .backgroundColor, object: nil)
        //   self.tabBarController?.tabBar.items![1].badgeValue = nil
        // self.tabBarController?.tabBar.items![3].badgeValue = nil
       
//        UserDefaults.standard.setValue("IsStaff", forKey: "fromStaff")
//        UserDefaults.standard.synchronize()
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            self.btnSideMenuOutlet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
            self.btnSideMenuOutlet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
            getStaffList()
        }
      
    }
    
    @objc func changeMode() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if  mode == "dark" {
            imageBackground.image = UIImage(named: "BG4")
            tblStaffList.backgroundColor = .black
            headerview.backgroundColor = UIColor(hexString: "#0E0F27")
        }
        else if mode == "light"{
            tblStaffList.backgroundColor = .white
            imageBackground.image = UIImage(named: "BackgroundImage")
            headerview.backgroundColor = UIColor(hexString: "#B1B82C")
        }
        getStaffList()
    }
    
    
    func darkModechange() {
        if self.valueMode == "dark" {
            imageBackground.image = UIImage(named: "BG4")
            tblStaffList.backgroundColor = .black
            headerview.backgroundColor = UIColor(hexString: "#0E0F27")
        }
        else if self.valueMode == "light"{
            tblStaffList.backgroundColor = .white
            imageBackground.image = UIImage(named: "BackgroundImage")
            headerview.backgroundColor = UIColor(hexString: "#A0A628")
        }
    }
    
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
    }
    
    //MARK:- SideMenu
    @objc func messageShowMentorSideMenu() {
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
    }
    
    func showHideMentorMenuController(_ isShown: Bool) {
        btnSideMenuOutlet.isUserInteractionEnabled = !isShown
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    func passdata(tag: String) {
        print("tag data",tag)
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            getStaffList()
        }
    }
    
    
    
    //MARK:- API Service Call
    func getStaffList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            var getToken = ""
            
            var totalBaseUrl = ""
            
            var methodType = ""
            
            let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
            
            if loginMode=="Mentor" {
                guard let token  = UserDefaults.standard.string(forKey: "mentorToken") else{
                    return self.showAlert(_sourceController: self, _msg: "Mentor Token Missing")
                }
                
                getToken = token
                methodType = "POST"
                totalBaseUrl = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.StaffList)
            } else {
                guard let token  = UserDefaults.standard.string(forKey: "token") else{
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
            
            print("MENTEEStaffLISTURL>>>>>>>\(totalBaseUrl)")
            self.arrStaffList.removeAll()
            
            Alamofire.request(totalBaseUrl, method:HTTPMethod(rawValue: methodType)!, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    
                    if loginMode == "Mentor" {
                        self.arrStaffList = dictMain["data"] as! [NSDictionary]
                        
                        self.dicStaffList = dictMain
                    } else {
                        guard let dicOfStaff = dictMain["data"] as? NSDictionary else{
                            return
                        }
                        self.arrStaffList = dicOfStaff["staffs"] as! [NSDictionary]
                        
                        self.dicStaffList = dictMain
                    }
                    
                    self.tblStaffList.reloadData()
                    self.stopActivityIndicator()
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    if loginMode == "Mentor" {
                        self.logOutMentor()
                    }
                    else {
                        self.logOutMentee()
                    }
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
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
    
    @objc func buttonShowMyMenteeDetailsAction(sender: UIButton) {
        
        print("mentee details")
        //MARK: --> Date 4.09.2020 code off 06.09 pm
        /*
        let index = sender.tag
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "MyMenteeDetailsViewController") as? MyMenteeDetailsViewController
            
            if identifyButtonClicked == menteeClicked {
                if arrStaffList.count > 0 {
                    let dicMentee: NSDictionary = self.arrStaffList[index]
                    let name : String = ((dicMentee["firstname"] as? String)!)  + " " + ((dicMentee["middlename"] as? String)!) + " " + ((dicMentee["lastname"] as? String)!)
                    
                    vc?.strMenteeName = name
                    vc?.strMenteeEmail = dicMentee["email"] as! String
                    vc?.strMenteePhoneNumber = dicMentee["cell_phone_number"] as! String
                    vc?.strMenteeCurrentLivingDetails = dicMentee["current_living_details"] as! String
                    let profileImage = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(dicMentee["image"] as! String)
                    vc?.strMenteeImageUrl = profileImage
                    vc?.strIamFrom = identifyButtonClicked
                    
                    let arrData = self.dicStaffList["data"] as! NSArray
                    vc?.arrMenteeList = arrData[index] as! NSDictionary
                }
            } else {
                
            }
            self.navigationController?.pushViewController(vc!, animated: true)
        } else {
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            self.UI {
                if self.identifyButtonClicked == self.menteeClicked{
                    let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                    newViewController.strIamfrom = ""
                    self.navigationController?.pushViewController(newViewController, animated: true)
                } else {
                    guard let dicData = self.dicStaffList["data"] as? NSDictionary else{
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
        */
    }
}


// MARK:: UITableViewDatasource
extension StaffListFromMentorVC: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrStaffList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let staffListCell = tableView.dequeueReusableCell(withIdentifier: "MyMenteeListTableViewCell") as! MyMenteeListTableViewCell
        /*
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            print("dark set")
            staffListCell.contentView.backgroundColor = .black
            staffListCell.viewMenteeCell.backgroundColor = UIColor(hexString: "#232137")
            staffListCell.labelMyMenteeName.textColor = .white
        }
        else if mode == "light" {
            print("light set")
            staffListCell.contentView.backgroundColor = .white
            staffListCell.viewMenteeCell.backgroundColor = .white
            staffListCell.labelMyMenteeName.textColor = .black
        }
        */
        if arrStaffList.count > 0 {
            let dicStaff: NSDictionary = arrStaffList[indexPath.row]
            
            let name : String = ((dicStaff["name"] as? String)!)
            staffListCell.labelMyMenteeName.text = name
            let newMsgCount = dicStaff["unread_chat_count"]!
            staffListCell.lblNewChatCount.text = String(describing:newMsgCount)
            
            let image : String? = dicStaff["profile_pic"] as? String
            
            if let img = image {
                videoURLStr = TakeStockInChildrenConstant.StaffImageBaseURL.appending(img)
                
                print("videoURLStr:: \(videoURLStr)")
                staffListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
            }
        }
        staffListCell.buttonShowMenteeDetailsTap.tag = indexPath.row
        staffListCell.buttonShowMenteeDetailsTap.addTarget(self, action: #selector(buttonShowMyMenteeDetailsAction), for: .touchUpInside)
        
        return staffListCell
    }
    
}

// MARK:: UITableViewDatasource
extension StaffListFromMentorVC: UITableViewDelegate {
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 100.0
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        print("indexpath.row\(indexPath.row)")
        
        
        
        self.UI {
            
            let obj = self.arrStaffList[indexPath.row]
            let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "TwilloTextChatViewController") as! TwilloTextChatViewController
            newViewController.OtheruserId = obj["id"] as? String
            newViewController.OtheruserName = obj["name"] as? String
            newViewController.OtherchatCode = obj["code"] as? String
            newViewController.csid = obj["channel_sid"] as? String
            
            UserDefaults.standard.setValue("IsStaff", forKey: "fromStaff")
                   UserDefaults.standard.synchronize()
            newViewController.firstname = UserCredential.shared.firstname
            newViewController.middleName = UserCredential.shared.middlename
            newViewController.lastName = UserCredential.shared.lastname
            newViewController.Id = UserCredential.shared.id
            newViewController.fromtag = "mentor_staff"
            self.navigationController?.pushViewController(newViewController, animated: true)
            
        }
        
        
        /*
        selectedIndex = indexPath.row
        let index = indexPath.row
        
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
        let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
        newViewController.strIamfrom = "staff"
        // newViewController.firebaseKeyReceiver = self.arrMenteeList[index]["firebase_id"] as? String
        newViewController.firebaseKeyReceiver = ""
        newViewController.loginMenteeButMentorDicData = arrStaffList[index]
        newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        */
    }
}
