//
//  MyMenteeListViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 12/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class MyMenteeListViewController: BaseViewController, MentorMenuControllerDelegate, MenuControllerDelegate, datapass {
    
    
    
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var backgroundimage: UIImageView!
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    @IBOutlet weak var tableViewMyMenteeList: UITableView!
    
    var arrMenteeList = [NSDictionary]()
    var DicMenteeList = NSDictionary()

    var isImageHidden : Bool = true
    var videoURLStr : String = ""
    let menteeClicked = "mentee"
    let staffClicked = "staff"
    var valueMode: String?
    
    var identifyButtonClicked = ""
    
    @IBOutlet weak var btnSideMenuOutlet: UIButton!
    @IBOutlet weak var btnMenteeOutlet: UIButton!
    @IBOutlet weak var btnStaffOutlet: UIButton!
    @IBOutlet weak var lblTitle: UILabel!
    
    
    //MARK:: UIVIew
    override func viewDidLoad() {
        super.viewDidLoad()
        //self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
        //darkmodeChange()
       // NotificationCenter.default.addObserver(self, selector: #selector(changeColor), name: .menteebackgroundColor, object: nil)
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
            self.btnSideMenuOutlet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
            self.btnSideMenuOutlet.addTarget(self, action: #selector(MeetingViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
            
            getStaffList()
        }
    }
    
    @objc func changeColor() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            backgroundimage.image = UIImage(named: "BG4")
            mainView.backgroundColor = UIColor(hexString: "#0E0F27")
            headerView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableViewMyMenteeList.backgroundColor = UIColor(hex: "#0E0F27")
            
        }
        else if mode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            mainView.backgroundColor = .white
            headerView.backgroundColor = UIColor(hexString: "#A7AE3B")
            tableViewMyMenteeList.backgroundColor = UIColor.white
        }
        
        getStaffList()
        
    }
    
    func passdata(tag: String) {
        print("passdata")
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            getMenteeList()
        }
        else {
           getStaffList()
        }
    }
    
    
    
    func darkmodeChange(){
        if valueMode == "dark" {
            backgroundimage.image = UIImage(named: "BG4")
            mainView.backgroundColor = UIColor(hex: "#0E0F27")
            headerView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableViewMyMenteeList.backgroundColor = UIColor(hex: "#0E0F27")
            
        }
        else if valueMode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            mainView.backgroundColor = .white
            headerView.backgroundColor = UIColor(hexString: "#A7AE3B")
            tableViewMyMenteeList.backgroundColor = UIColor.white
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        getStaffList()
    }
    
    //MARK:- SideMenu
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
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getMentorDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    //print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        guard let mentorDetails = json["mentor_details"] as? NSDictionary else {
                            return
                        }
                       
                        self.arrMenteeList.removeAll()
                        
                        self.arrMenteeList = [mentorDetails] //dictMain["data"] as! [NSDictionary]
                        self.DicMenteeList = mentorDetails
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
                    self.logOutMentor()
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
            
            print("MENTEELISTURLFORSTAFF >>>>>>> \(totalBaseUrl)")
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
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    if loginMode == "Mentor"{
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
                        self.logOutMentee()
                        //self.stopActivityIndicator()
                        /*
                        let alert = UIAlertController(title: "Alert", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                        }))
                        self.present(alert, animated: true, completion: nil)
                        */
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
                    newViewController.strIamfrom = ""
                    newViewController.firebaseKeyReceiver = ""
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
extension MyMenteeListViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMenteeList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let myMenteeListCell = tableView.dequeueReusableCell(withIdentifier: "MyMenteeListTableViewCell") as! MyMenteeListTableViewCell
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            print("dark set")
            myMenteeListCell.viewMenteeCell.backgroundColor = UIColor(hex: "#232137")
            myMenteeListCell.labelMyMenteeName.textColor = .white
            myMenteeListCell.lblNewChatCount.textColor = .white
        }
        else if mode == "light" {
            print("light set")
            myMenteeListCell.viewMenteeCell.backgroundColor = .white
            myMenteeListCell.labelMyMenteeName.textColor = .black
            myMenteeListCell.lblNewChatCount.textColor = .black
        }
        if identifyButtonClicked == menteeClicked {
            if arrMenteeList.count > 0 {
                let dicMentee: NSDictionary = arrMenteeList[indexPath.row]
                
                
                let dicStaff: NSDictionary = arrMenteeList[indexPath.row]
                
                let name : String = ((dicStaff["name"] as? String)!)
                myMenteeListCell.labelMyMenteeName.text = name
                let newMsgCount = dicStaff["unread_chat_count"]!
                myMenteeListCell.lblNewChatCount.text = String(describing:newMsgCount)
                
                //let totalName = self.totalName(dicData: dicMentee)
                //myMenteeListCell.labelMyMenteeName.text = totalName
                
                let image : String? = dicMentee["profile_pic"] as? String
                if let img = image {
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    
                    if loginMode == "Mentor" {
                        videoURLStr = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(img)
                    } else {
                        videoURLStr = TakeStockInChildrenConstant.MentorImageBaseURL.appending(img)
                    }
                    
                    print("videoURLStr::\(videoURLStr)")
                    myMenteeListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
                }
            }
        } else {
            if arrMenteeList.count > 0 {
                let dicMentee: NSDictionary = arrMenteeList[indexPath.row]
                
                let name : String = ((dicMentee["name"] as? String)!)
                myMenteeListCell.labelMyMenteeName.text = name
                let newMsgCount = dicMentee["unread_chat_count"]!
                myMenteeListCell.lblNewChatCount.text = String(describing:newMsgCount)
                
                
                let image : String? = dicMentee["profile_pic"] as? String
                
                if let img = image {
                    videoURLStr = TakeStockInChildrenConstant.StaffImageBaseURL.appending(img)
                    
                    print("videoURLStr:: \(videoURLStr)")
                    myMenteeListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
                }
            }
        }
        myMenteeListCell.buttonShowMenteeDetailsTap.tag = indexPath.row
        myMenteeListCell.buttonShowMenteeDetailsTap.addTarget(self, action: #selector(buttonShowMyMenteeDetailsAction), for: .touchUpInside)
        
        return myMenteeListCell
    }
    
}

// MARK:: UITableViewDatasource
extension MyMenteeListViewController: UITableViewDelegate {
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 100.0
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        print("indexpath.row\(indexPath.row)")
        
        
        
        self.UI {
            
            let obj = self.arrMenteeList[indexPath.row]
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
            newViewController.fromtag = "mentee_staff"
            self.navigationController?.pushViewController(newViewController, animated: true)
            
        }
        
        
        
        /*
        let index = indexPath.row
        
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
                    vc?.arrMenteeList = arrData[indexPath.row] as! NSDictionary
                }
            } else {
                let profileImage = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(arrMenteeList[index]["profile_pic"] as! String)
                vc?.strMenteeImageUrl = profileImage
                vc?.strIamFrom = identifyButtonClicked
                let arrData = self.DicMenteeList["data"] as! NSArray
                vc?.arrMenteeList = arrData[indexPath.row] as! NSDictionary
            }
            self.navigationController?.pushViewController(vc!, animated: true)
        } else {
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
            newViewController.strIamfrom = "staff"
            newViewController.firebaseKeyReceiver = " "
            newViewController.delegate = self
            newViewController.loginMenteeButMentorDicData = arrMenteeList[index]
            newViewController.receiverTypeCheck = "staff"
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
