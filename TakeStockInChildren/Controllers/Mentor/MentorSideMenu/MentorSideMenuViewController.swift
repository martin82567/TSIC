//
//  MentorSideMenuViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 27/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

protocol MentorMenuControllerDelegate {
    func showHideMentorMenuController(_ isShown:Bool)
}
class MentorSideMenuViewController: UIViewController {
    
    
    @IBOutlet weak var mainView: UIView!
    
    @IBOutlet weak var closeLabel: UILabel!
    @IBOutlet weak var backgroundView: UIView!
    @IBOutlet weak var versionLbl: UILabel!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var tableViewMenu: UITableView!
    @IBOutlet weak var imageViewMentor: UIImageView!
    @IBOutlet weak var labelMentorName: UILabel!
    @IBOutlet weak var labelMentorEmail: UILabel!
    
    let appDelegate = AppDelegate()
    var delegate: MentorMenuControllerDelegate?
    
    var valueMode: String?
    fileprivate var mentoaTargetController:UIViewController!
    //Prevois -->
    //var arrMenu = ["Mentee Uploads", "Resources", "Mentor Toolkit", "App Help", "Log Out"]
    var arrMenu = ["Resources", "Mentor Toolkit", "App Help","Update Password","Log Out"]//["Mentee Goals", "Meeting",]
    var dicJsonMentorResponse = NSArray()
    var arrImageSideMenu: [String] = ["JournalsSideMenu","ResourceSideMenu", "MentorToolKitSideMenu", "icons8-password-reset-48","Menu_Settings","LogOutSideMenu"]
    
    override func viewDidLoad() {
        super.viewDidLoad()
       // self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechaged()
        self.tableViewMenu.rowHeight = 44
        self.getUserDetails()
        
        //getVersionNumber()
    }
   func getVersionNumber() {
        print("release: \(UIApplication.release)")
        print("build: \(UIApplication.build)")
        print("version: \(UIApplication.version)")
      // self.versionLbl.text = "   Version: \(UIApplication.release)"
    }
    
    
    func darkmodechaged() {
        if valueMode == "dark" {
            labelMentorName.textColor = .white
            labelMentorEmail.textColor = .white
            
            tableViewMenu.backgroundColor = .black
            headerView.backgroundColor = .black
          //  backgroundView.backgroundColor = .black
            closeLabel.textColor = .white
        } else if valueMode == "light" {
            labelMentorName.textColor = .black
            labelMentorEmail.textColor = .black
            
            tableViewMenu.backgroundColor = .white
            headerView.backgroundColor = .white
           // backgroundView.backgroundColor = .white
            closeLabel.textColor = .black
        }
    }

    override func touchesBegan(_ touches: Set<UITouch>, with event: UIEvent?) {
        hideMentorMenuController()
    }
    
    func showMentorMenuInController(_ controller:UIViewController) {
        delegate?.showHideMentorMenuController(true)
        self.mentoaTargetController = controller
        self.view.frame = CGRect(x: -self.view.frame.size.width, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
        controller.addChild(self)
        controller.view.addSubview(self.view)
        UIView.animate(withDuration: 0.3, delay: 0.0, options: .curveEaseIn, animations: {
            
            self.view.frame = CGRect(x: 0, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
            self.view.layoutIfNeeded()
            
        }, completion: nil)
    }
    
    func hideMentorMenuController() {
        delegate?.showHideMentorMenuController(false)
        UIView.animate(withDuration: 0.3, delay: 0.0, options: .curveEaseIn, animations: {
            
            self.view.frame = CGRect(x: -self.view.frame.size.width, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
            self.view.layoutIfNeeded()
            
        }, completion:{ finished in
            self.view.removeFromSuperview()
            self.removeFromParent()
        })
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
                            
                            /*
                            let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
                            let nav = UINavigationController(rootViewController: loginVC)
                            nav.navigationBar.isHidden = true;
                            nav.navigationBar.barStyle = .default
                            self.appDelegate.window?.rootViewController = nav
                            */
                           //print("TakeStockInChildrenConstant.mentorUserData\(TakeStockInChildrenConstant.mentorUserData)")
                            DispatchQueue.main.async(execute: { () -> Void in
                              //  self.stopActivityIndicator()
                                let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                    
                                    self.navigationController?.popViewController(animated: true)
                                }))
                                self.present(alert, animated: true, completion: nil)
                            })
                        
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
    
    @IBAction func buttonMentorMenuCloseAction(_ sender: Any) {
        hideMentorMenuController()
    }
    
    //MARK:- API Service Call
    func getUserDetails() {
        if self.connectedToNetwork() {
            MentorApiManager.mentorSharedInstance.getMentorUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    print("MentorDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        var dicUserDetails = NSDictionary()
                        
                        dicUserDetails = json as NSDictionary
                      //  print("dicJsonMentorResponse\(dicUserDetails)")
                        
                        
                        if dicUserDetails.count > 0 {
                            var videoURLStr = ""
                            if (dicUserDetails["profile_pic"] == nil) {
                                // videoURLStr = dicUserDetails["profile_pic"] as! String
                            } else {
                                videoURLStr = TakeStockInChildrenConstant.MentorImageBaseURL.appending(dicUserDetails["profile_pic"] as! String)
                            }
                            
                            self.imageViewMentor.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
                            
                            let firstName = (dicUserDetails["firstname"] as! String)
                            let middleName = (dicUserDetails["middlename"] as! String)
                            let lastName = (dicUserDetails["lastname"] as! String)
                            self.labelMentorName.text     = firstName + " " + middleName + " " + lastName
                            self.labelMentorEmail.text = (dicUserDetails["email"] as! String)
                        }
                        
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.logOutMentor()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                })
            })
        } else {
            //showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    func logOutAction() {
        let alert = UIAlertController(title: "Logging Out", message: "Are you sure you want to logout", preferredStyle: UIAlertController.Style.alert)
        
        let acceptAction = UIAlertAction(title: "Yes", style: .default) { (_) -> Void in
            UserDefaults.standard.setValue(nil, forKey: "mentorUserDetails")
            UserDefaults.standard.setValue(nil, forKey: "loginMode")
            
            self.logOutMentor()
            let appDelegate = UIApplication.shared.delegate as? AppDelegate
            let loginController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "FirstViewController")
            let nav = UINavigationController(rootViewController: loginController)
            nav.navigationBar.isHidden = true;
            appDelegate?.window!.rootViewController = nav
        }
        let cancelAction = UIAlertAction(title: "No", style: .cancel) { (_) -> Void in
            
        }
        alert.addAction(acceptAction)
        alert.addAction(cancelAction)
        self.present(alert, animated: true, completion: nil)
    }
    
    
    func alertModeDarkLight(message: String?) {
        let alert = UIAlertController(title: "Appearance", message: message, preferredStyle: UIAlertController.Style.alert)
        
        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
            print("accept")
        }
        alert.addAction(acceptAction)
        self.present(alert, animated: true, completion: nil)
        
    }
}

// MARK:: UITableViewDatasource
extension MentorSideMenuViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMenu.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "MentorSideMenuTableViewCell") as! MentorSideMenuTableViewCell
        /*
        let valueMode = UserDefaults.standard.value(forKey: "mode") as? String
               if valueMode == "dark" {
                   cell.imageViewSideMenu.tintColor = .white
                cell.contentView.backgroundColor = .black
                   cell.labelSideMenuName.textColor = .white
               }
               else if valueMode == "light" {
                   cell.imageViewSideMenu.tintColor = .black
                   cell.contentView.backgroundColor = .white
                   cell.labelSideMenuName.textColor = .black
               }
        */
        if arrMenu.count > 0 {
            cell.labelSideMenuName.text = arrMenu[indexPath.row]
        }
        cell.imageViewSideMenu.image = UIImage(named: arrImageSideMenu[indexPath.row] )
//        if (indexPath.row == 1)  {
//            cell.viewLineOne.isHidden = false
//            cell.viewLineTwo.isHidden = false
//        } else {
            cell.viewLineOne.isHidden = true
            cell.viewLineTwo.isHidden = true
//        }
        return cell
    }
}


// MARK:: UITableViewDelegate
extension MentorSideMenuViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, viewForFooterInSection section: Int) -> UIView? {
        let vw = UIView()
        vw.backgroundColor = UIColor.clear

        let titleLabel = UILabel(frame: CGRect(x:10,y: vw.frame.origin.y + 20 ,width:350,height:50))
        titleLabel.numberOfLines = 0;
        titleLabel.lineBreakMode = .byWordWrapping
        titleLabel.backgroundColor = UIColor.clear
        titleLabel.font = UIFont(name: "Raleway-light", size: 16)
        titleLabel.text = "   Version: \(UIApplication.release)"

        vw.addSubview(titleLabel)
        return vw
    }

    func tableView(_ tableView: UITableView, heightForFooterInSection section: Int) -> CGFloat {
        return 100
    }
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        /*if indexPath.row == 0 {
            //TODO:- Mentee Goals
            let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "CreateMentorGoalViewController") as? CreateMentorGoalViewController
            self.navigationController?.pushViewController(vc!, animated: true)
        } else if indexPath.row == 1 {
            //TODO:- Meeting
            let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionViewController") as! SessionViewController
                newViewController.iAmFrom = "meeting"
                newViewController.iAmFromProfile = true
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
        } else*/ if indexPath.row == 0 {
            //TODO:- Mentee Uploads List
            /*
            let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "MenteeReportListVC") as! MenteeReportListVC
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
            */
            
            let elearningVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ElearningViewController")
            self.navigationController?.pushViewController(elearningVc, animated: true)
            
        } else if indexPath.row == 1 {
            //TODO: Resource
            let helpVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ToolKitViewController")
            self.navigationController?.pushViewController(helpVc, animated: true)
            /*
            let url = NSURL(string:"https://drive.google.com/file/d/1f45-CkpruU34gMB1t1qyGxApZSrdZo2D/view?usp=sharing")!
            UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)
            */
            //.canOpenURL(url as URL)
            //https://drive.google.com/file/d/1f45-CkpruU34gMB1t1qyGxApZSrdZo2D/view?usp=sharing

        } else if indexPath.row == 2 {
            
            let helpVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "HelpTabViewController")
            self.navigationController?.pushViewController(helpVc, animated: true)
            //TODO:- Mentor Toolkit
            
        } else if indexPath.row == 3 {
            
            
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "UpdatePasswordViewController") as! UpdatePasswordViewController
            resourceVc.loginModeForResetPassword = "mentor"
            self.navigationController?.pushViewController(resourceVc, animated: true)
            
            /*
            if let url = URL(string: CommonFAQDATA.sharedinstance.faqmentorurl) {
                UIApplication.shared.open(url)
            }
            */
            
            /*
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "MessageCenterViewController") as! MessageCenterViewController
            resourceVc.type = "mentor"
            self.navigationController?.pushViewController(resourceVc, animated: true)
            
            
            */
           
            //TODO:- App Help
//            let url = NSURL(string:"https://www.takestockinchildren.org/mentortoolkit")!
//            UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)//.canOpenURL(url as URL)
            
            
                 // hideMenuController()
            
            
        }
//        else if indexPath.row == 4 {
//
//            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//            let settingsVc = storyBoardMain.instantiateViewController(withIdentifier: "SettingsViewController") as! SettingsViewController
////            resourceVc.loginModeForResetPassword = "mentor"
//            self.navigationController?.pushViewController(settingsVc, animated: true)
//        }
        else if indexPath.row == 4 {
            
            
            logOutAction()
            /*
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdent ifier: "UpdatePasswordViewCont       roller") as! UpdatePasswordViewController
            resourceVc.loginModeForResetPassword = "mentor"
            self.navigationController?.pushViewController(resourceVc, animated: true)
            */
           
            
        }
        
        
        /*
         class CommonFAQDATA {
             static let CommonFAQDATA = CommonFAQDATA()
             var faqmentorurl: String = ""
             var faqmenteeurl: String = ""
         }
         */
        
            /*
        else if indexPath.row == 4 {
            mainView.backgroundColor = .black
           // NotificationCenter.default.post(name: .backgroundColor, object: nil)
            
            let Mode = UserDefaults.standard.value(forKey: "mode") as? String
            
            
            if #available(iOS 13.0, *) {
                switch traitCollection.userInterfaceStyle {
                case .dark:
                    print("dark set",Mode ?? "")
                    if Mode == "dark" {
                        UserDefaults.standard.set("dark", forKey: "mode")
                        alertModeDarkLight(message: "Already Set Dark Mode")
                    }
                    else if Mode == "light" {
                        UserDefaults.standard.set("dark", forKey: "mode")
                        alertModeDarkLight(message: "Set Dark Mode")
                    }
                case .light:
                    print("light set",Mode ?? "")
                    if Mode == "light" {
                        UserDefaults.standard.set("dark", forKey: "mode")
                        alertModeDarkLight(message: "Set Dark Mode")
                    }
                    else if Mode == "dark" {
                        UserDefaults.standard.set("light", forKey: "mode")
                        alertModeDarkLight(message: "Set Light Mode")
                    }
                default:
                  //  UserDefaults.standard.set("light", forKey: "mode")
                    print("Not set")
                }
                
                
            }
            else if #available(iOS 12.0, *) {
                    switch traitCollection.userInterfaceStyle {
                    case .dark:
                        print("dark set",Mode ?? "")
                        if Mode == "dark" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                            alertModeDarkLight(message: "Already Set Dark Mode")
                        }
                        else if Mode == "light" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                            alertModeDarkLight(message: "Set Dark Mode")
                        }
                    case .light:
                        print("light set",Mode)
                        if Mode == "light" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                            alertModeDarkLight(message: "Set Dark Mode")
                        }
                        else if Mode == "dark" {
                            UserDefaults.standard.set("light", forKey: "mode")
                            alertModeDarkLight(message: "Set light Mode")
                        }
                    default:
                      //  UserDefaults.standard.set("light", forKey: "mode")
                        print("Not set")
                    }
            } else if #available(iOS 11, *) {
                    print(" ios 11")
                    if Mode == "dark" {
                        
                    print("when dark set light")
                    UserDefaults.standard.set("light", forKey: "mode")
                        alertModeDarkLight(message: "Set light Mode")
                    }
                    else if Mode == "light" {
                        print("when light set dark")
                        UserDefaults.standard.set("dark", forKey: "mode")
                        alertModeDarkLight(message: "Set Dark Mode")
                    }
                    print("set color for ios 11")
                    // Fallback on earlier versions
                }

            NotificationCenter.default.post(name: .backgroundColor, object: nil)
            
            tableViewMenu.reloadData()
            let valueMode = UserDefaults.standard.value(forKey: "mode") as? String
            if valueMode == "dark"{
                labelMentorName.textColor = .white
                labelMentorEmail.textColor = .white
                
                tableViewMenu.backgroundColor = .black
                headerView.backgroundColor = .black
               // backgroundView.backgroundColor = .black
                closeLabel.textColor = .white
            } else if valueMode == "light" {
                labelMentorName.textColor = .black
                labelMentorEmail.textColor = .black
                
                tableViewMenu.backgroundColor = .white
                headerView.backgroundColor = .white
              //  backgroundView.backgroundColor = .white
                closeLabel.textColor = .black
            }
            
            /*
            
            */
            
            /*
            if indexPath.row == 4 {
                if Mode == "dark" {
                self.arrMenu[4] = "Appearance: Dark"
                }
                else if Mode == "light" {
                    self.arrMenu[4] = "Appearance: Light"
                }
            }
            self.tableViewMenu.reloadRows(at: [indexPath], with: .fade)
            */
            
        }
        */
    }
}



extension Notification.Name {
    static let backgroundColor = Notification.Name("bgcolor")
    static let color = Notification.Name("color")
}
extension UIApplication {
    static var release: String {
        return Bundle.main.object(forInfoDictionaryKey: "CFBundleShortVersionString") as! String? ?? "x.x"
    }
    static var build: String {
        return Bundle.main.object(forInfoDictionaryKey: "CFBundleVersion") as! String? ?? "x"
    }
    static var version: String {
        return "\(release).\(build)"
    }
}
