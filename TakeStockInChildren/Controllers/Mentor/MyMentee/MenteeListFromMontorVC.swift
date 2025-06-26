//
//  MenteeListFromMontorVC.swift
//  TakeStockInChildren
//
//  Created by Aquarious  on 13/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
protocol backrefreshthree {
    func backrefreshthree(name: String)
}
class MenteeListFromMontorVC: BaseViewController, datapass {
    
    @IBOutlet var safearaview: UIView!
    @IBOutlet weak var backgroundimage: UIImageView!
    
    @IBOutlet weak var headerview: UIView!
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    @IBOutlet weak var tblMenteeList: UITableView!
    
    var arrMenteeList = [NSDictionary]()
    var DicMenteeList = NSDictionary()
    
    var isImageHidden : Bool = true
    var videoURLStr : String = ""
    var selectedIndex : Int?
    var valueMode : String?
    
    @IBOutlet weak var lblTitle: UILabel!
    var delegate : backrefreshthree!
    
    var tag: String?
    var firstname: String?
    var middleName: String?
    var lastName: String?
    var Id: String?
    //MARK:- UIView LifeCycle
    override func viewDidLoad() {
        super.viewDidLoad()
     //   self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
     //   darkmodechange()
        getMenteeList()
    }
    
    
    func darkmodechange() {
        if self.valueMode == "dark" {
            headerview.backgroundColor = UIColor(hex: "#0E0F27")
            backgroundimage.image = UIImage(named: "BG4")
        }
        else if self.valueMode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
           // headerview.backgroundColor = UIColor(hex: "#A7AE3B")
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    
    
    func passdata(tag: String) {
        print("rag")
         getMenteeList()
    }
    
    
    //MARK:- API Service Call
    func getMenteeList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            guard let token  = UserDefaults.standard.string(forKey: "mentorToken") else {
                return self.showAlert(_sourceController: self, _msg: "MenteeListFromMentor Token Missing")
            }
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeList)
            
            print("MENTEELIST URL :: \(url)")
            
            self.arrMenteeList.removeAll()
            
            Alamofire.request(url, method:.post, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    let dictVal = response.result.value
                    
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    print("MENTEELIST:: \(dictMain)")
                    self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                    self.DicMenteeList = dictMain
                    self.tblMenteeList.reloadData()
                   // self.getChatMessages(id: (dictMain["id"] as? Int)!)
                    
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
    
    func getChatMessages(id:Int) {
        
        self.startActivityIndicator()
       // self.arrChatMessages.removeAll()
        var parameter = [String:String]()
        
//        if self.strIamfrom == "mentee" {
//            parameter["mentee_id"] = "\(id)"
//        } else { //Mentor or Staff
//            parameter["staff_id"] = "\(id)"
//        }
        
        MentorApiManager().mentee_chat(parameter: parameter, chatBy: "Mentor", pageList: 1) { (json) in
            self.stopActivityIndicator()
            print("chat called-------",json)
            let status = json["status"] as! Bool
            
            if status == true {
                let dicData = json["data"] as! NSDictionary
               // self.chatCode = dicData["chat_code"] as! String
               
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
        
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.delegate?.backrefreshthree(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    
    @objc func btnCallMenteeAction(sender: UIButton){
        let index = sender.tag
        if arrMenteeList.count > 0 {
            let dicMentee: NSDictionary = self.arrMenteeList[index]
            print("CallMentee:: \(String(describing: dicMentee["cell_phone_number"]!))")
            
            let strPhNo = String(describing: dicMentee["cell_phone_number"]!).makeAColl()
            //print(strPhNo)
        }
        
    }
}



// MARK:: UITableViewDatasource
extension MenteeListFromMontorVC: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMenteeList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let myMenteeListCell = tableView.dequeueReusableCell(withIdentifier: "MyMenteeListTableViewCell") as! MyMenteeListTableViewCell
        if self.valueMode == "dark" {
           // myMenteeListCell.contentView.backgroundColor = .black
            myMenteeListCell.viewMenteeCell.backgroundColor = UIColor(hex: "#232137")
            myMenteeListCell.labelMyMenteeName.textColor = .white
            myMenteeListCell.lblMenteeSchool.textColor = .white
            myMenteeListCell.lblMenteeUpcomingDate.textColor = .white
        }
        else if self.valueMode == "light" {
           // myMenteeListCell.contentView.backgroundColor = .white
            myMenteeListCell.viewMenteeCell.backgroundColor = .white
            myMenteeListCell.labelMyMenteeName.textColor = .black
            myMenteeListCell.lblMenteeSchool.textColor = .black
            myMenteeListCell.lblMenteeUpcomingDate.textColor = .black
        }
        
        
        if arrMenteeList.count > 0 {
            let dicMentee: NSDictionary = arrMenteeList[indexPath.row]
            
            
            let totalName = self.totalName(dicData: dicMentee)
            myMenteeListCell.labelMyMenteeName.text = totalName
            myMenteeListCell.lblMenteeSchool.text = dicMentee["school_name"] as? String
            myMenteeListCell.lblMenteeUpcomingDate.text = dicMentee["upcoming_meeting_date"] as? String
            let newMsgCount = dicMentee["unread_chat_count"]!
            myMenteeListCell.lblNewChatCount.text = String(describing:newMsgCount)
            
            
            
            let image : String? = dicMentee["image"] as? String
            if let img = image { 
                videoURLStr = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(img)
                
                print("videoURLStr::\(videoURLStr)")
                myMenteeListCell.imageViewMyMentee.layer.cornerRadius = myMenteeListCell.imageViewMyMentee.frame.size.width/2
                myMenteeListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
            }
        }
//        myMenteeListCell.btnMenteeCall.tag = indexPath.row
//        myMenteeListCell.btnMenteeCall.addTarget(self, action: #selector(btnCallMenteeAction), for: .touchUpInside)
        
        return myMenteeListCell
    }
    
}

// MARK:: UITableViewDatasource
extension MenteeListFromMontorVC: UITableViewDelegate {
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 110.0
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        print("indexpath.row---\(indexPath.row)")
        
        
    
       // if tag == "twillo" {
            
            
            
            selectedIndex = indexPath.row
            let index = indexPath.row
            
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            
            self.UI {
                
                let obj = self.arrMenteeList[indexPath.row]
                let mainStoryboard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "TwilloTextChatViewController") as! TwilloTextChatViewController
                newViewController.OtheruserId = String(obj["id"] as?  Int ?? 0)
                newViewController.OtheruserName = obj["firstname"] as? String
                newViewController.OtherchatCode = obj["code"] as? String
                
                newViewController.firstname = self.firstname
                newViewController.middleName = self.middleName
                newViewController.lastName = self.lastName
                newViewController.Id = self.Id
                newViewController.csid = obj["channel_sid"] as? String
                
                
                newViewController.receiverTypeCheck = "mentee"
                newViewController.receiverimageUrl = TakeStockInChildrenConstant.MenteeImageBaseURL
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
            
            
            selectedIndex = indexPath.row
            let index = indexPath.row
            
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                newViewController.strIamfrom = "mentee"
                newViewController.loginMenteeButMentorDicData = self.arrMenteeList[index]
                newViewController.firebaseKeyReceiver = self.arrMenteeList[index]["firebase_id"] as? String
                newViewController.receiverTypeCheck = "mentee"
                newViewController.delegate = self
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            
        }
        */
        
        
        
        
        
        
        
        
        
        
        
        /*let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "MyMenteeDetailsViewController") as? MyMenteeDetailsViewController
            
            
            if arrMenteeList.count > 0 {
                let dicMentee: NSDictionary = self.arrMenteeList[index]
                let name : String = ((dicMentee["firstname"] as? String)!)  + " " + ((dicMentee["middlename"] as? String)!) + " " + ((dicMentee["lastname"] as? String)!)
                
                vc?.strMenteeName = name
                vc?.strMenteeEmail = dicMentee["email"] as! String
                vc?.strMenteePhoneNumber = dicMentee["cell_phone_number"] as! String
                vc?.strMenteeCurrentLivingDetails = dicMentee["current_living_details"] as! String
                let profileImage = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(dicMentee["image"] as! String)
                vc?.strMenteeImageUrl = profileImage
                vc?.strIamFrom = "mentee"
                
                let arrData = self.DicMenteeList["data"] as! NSArray
                vc?.arrMenteeList = arrData[indexPath.row] as! NSDictionary
            }
            
            self.navigationController?.pushViewController(vc!, animated: true)
        }
            
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
            self.UI {
                if self.identifyButtonClicked == self.menteeClicked{
                    let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                    newViewController.strIamfromm = ""
                    self.navigationController?.pushViewController(newViewController, animated: true)
                } else {
                    guard let dicData = self.DicMenteeList["data"] as? NSDictionary else{
                        return
                    }
                    let arrData = dicData["staffs"] as! NSArray
                    
                    let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
                    newViewController.strIamfromm = "staff"
                    newViewController.loginMenteeButMentorDicData = arrData[indexPath.row] as! NSDictionary
                    self.navigationController?.pushViewController(newViewController, animated: true)
                }
            }
        }*/
    }
}


extension String {
    enum RegularExpressions: String {
        case phone = "^\\s*(?:\\+?(\\d{1,3}))?([-. (]*(\\d{3})[-. )]*)?((\\d{3})[-. ]*(\\d{2,4})(?:[-.x ]*(\\d+))?)\\s*$"
    }
    
    func isValid(regex: RegularExpressions) -> Bool { return isValid(regex: regex.rawValue) }
    func isValid(regex: String) -> Bool { return range(of: regex, options: .regularExpression) != nil }
    
    func onlyDigits() -> String {
        let filtredUnicodeScalars = unicodeScalars.filter { CharacterSet.decimalDigits.contains($0) }
        return String(String.UnicodeScalarView(filtredUnicodeScalars))
    }
    
    func makeAColl() {
        guard   isValid(regex: .phone),
            let url = URL(string: "tel://\(self.onlyDigits())"),
            UIApplication.shared.canOpenURL(url) else { return }
        if #available(iOS 10, *) {
            UIApplication.shared.open(url)
        } else {
            UIApplication.shared.openURL(url)
        }
    }
}

