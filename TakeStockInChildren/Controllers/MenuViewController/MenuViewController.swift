//
//  MenuViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 09/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

protocol MenuControllerDelegate {
    func showHideMenuController(_ isShown:Bool)
}

class MenuViewController: UIViewController {
    
    @IBOutlet weak var versionlbl: UILabel!
    
    @IBOutlet weak var closeLabel: UILabel!
    @IBOutlet weak var topmenuView: UIView!
    
    @IBOutlet weak var backgroundView: UIView!
    @IBOutlet weak var tableViewMenu: UITableView!
    @IBOutlet weak var imageViewMentee: UIImageView!
    @IBOutlet weak var labelMenteeName: UILabel!
    @IBOutlet weak var labelMenteeEmail: UILabel!
    
    @IBOutlet weak var versionLbl: UILabel!
    let appDelegate = AppDelegate()
    var delegate: MenuControllerDelegate?
    fileprivate var targetController:UIViewController!
    
    var valueMode : String?
    
    var arrMenu = ["Resources", "Mentor Toolkit", "App Help", "Update Password","Log Out"]
    
   // var arrMenu = ["Goals", "Mentee Toolkit", "Assignments", "Resources", "App Help", "Update Password","Log Out"]//["Job","Goal","Tasks","Challenges","E-learning","Resource","Journals","Help","Log Out"]
    var arrImageSideMenu: [String] = ["GoalSideMenu","ResourceSideMenu","TasksSideMenu","ResourceSideMenu","HelpSideMenu", "icons8-password-reset-48","LogOutSideMenu"]//["JobSideMenu","GoalSideMenu","TasksSideMenu","ChallengeSideMenu","E-learningSideMenu","ResourceSideMenu","JournalsSideMenu","HelpSideMenu","LogOutSideMenu"]

    override func viewDidLoad() {
        super.viewDidLoad()
       // self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechanged()
        self.getUserDetails()
        self.tableViewMenu.rowHeight = 44
        let dicUserDetails = UserDefaults.standard.value(forKey: "userDetails") as! NSDictionary
        
        if dicUserDetails.count > 0 {
            if dicUserDetails.count > 0 {
                if dicUserDetails.count > 0 {
                    /*
                    var videoURLStr = ""
                    if (dicUserDetails["image"] == nil) {
                        videoURLStr = dicUserDetails["image"] as! String
                    } else {
                        videoURLStr = TakeStockInChildrenConstant.UserImageBaseURL.appending(dicUserDetails["image"] as! String)
                        print("url",videoURLStr)
                    }
                    
                    let videoURL = NSURL (string: videoURLStr)
                    print("ImageProfile videoURL:: \(String(describing: videoURL)))")
                    imageViewMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
                    */
                    
                    let firstName = (dicUserDetails["firstname"] as! String)
                    let middleName = (dicUserDetails["middlename"] as! String)
                    let lastName = (dicUserDetails["lastname"] as! String)
                    labelMenteeName.text     = firstName + " " + middleName + " " + lastName
                    
                    //            lblTitle.text = (dicUserDetails["firstname"] as! String)
                    labelMenteeEmail.text = (dicUserDetails["email"] as! String)
                    //imgProfile.image
                }
                
            }
        }
        getVersionNumber()
    }
    func getVersionNumber() {
         print("release: \(UIApplication.release)")
         print("build: \(UIApplication.build)")
         print("version: \(UIApplication.version)")
        //self.versionlbl.text = "   Version: \(UIApplication.release)"
     }
    func darkmodechanged() {
        if self.valueMode == "dark"{
            labelMenteeName.textColor = .white
            labelMenteeEmail.textColor = .white
            
            tableViewMenu.backgroundColor = UIColor(hexString: "#0E0F27")
            topmenuView.backgroundColor = UIColor(hexString: "#0E0F27")
            //backgroundView.backgroundColor = .black
            closeLabel.textColor = .white
            
            
        } else if self.valueMode == "light" {
            labelMenteeName.textColor = .black
            labelMenteeEmail.textColor = .black
            
            tableViewMenu.backgroundColor = .white
            topmenuView.backgroundColor = .white
            //backgroundView.backgroundColor = .white
            closeLabel.textColor = .black
        }
        
    }
    
    
    
    
    
    //MARK:: Function Api Calling
    func getUserDetails () {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
            
            ApiManager.sharedInstance.getUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                   // print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        guard let userDetails = json["user_details"] as? NSDictionary else{
                            return
                        }
                        let imageurl = userDetails["image"] as? String ?? ""
                        self.imageViewMentee.sd_setImage(with: URL(string: TakeStockInChildrenConstant.UserImageBaseURL + imageurl), placeholderImage: UIImage(named: "defaultProfileImage"))
                        
                        

                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.logOutMentee()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                })
            })
        } else {
            print("unable connect")
        }
    }
    
    
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        //UserDefaults.standard.set(false, forKey: "isProfile")
        self.tabBarController?.tabBar.isHidden = false
        tableViewMenu.setContentOffset(.zero, animated:true)
    }
    
    override func touchesBegan(_ touches: Set<UITouch>, with event: UIEvent?) {
        hideMenuController()
    }
    
    func showMenuInController(_ controller:UIViewController) {
        
        delegate?.showHideMenuController(true)
        self.targetController = controller
        //self.view.backgroundColor = UIColor.clear
        //self.MainTV.backgroundColor = UIColor.white
        self.view.frame = CGRect(x: -self.view.frame.size.width, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
        controller.addChild(self)
        controller.view.addSubview(self.view)
        UIView.animate(withDuration: 0.3, delay: 0.0, options: .curveEaseIn, animations: {
            
            self.view.frame = CGRect(x: 0, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
            self.view.layoutIfNeeded()
            
        }, completion: nil)
    }
    
    func hideMenuController() {
        
        delegate?.showHideMenuController(false)
        
        UIView.animate(withDuration: 0.3, delay: 0.0, options: .curveEaseIn, animations: {
            
            self.view.frame = CGRect(x: -self.view.frame.size.width, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
            self.view.layoutIfNeeded()
            
        }, completion:{ finished in
            
            self.view.removeFromSuperview()
            self.removeFromParent()
            
        })
    }
    
    func hidePrevious(){
        UIView.animate(withDuration: 0.3, delay: 0.0, options: .curveEaseIn, animations: {
            
            self.view.frame = CGRect(x: -self.view.frame.size.width, y: 0, width: self.view.frame.size.width, height: self.view.frame.size.height)
            self.view.layoutIfNeeded()
            
        }, completion:{ finished in
            
            self.view.removeFromSuperview()
            self.removeFromParent()
            
        })
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
                            DispatchQueue.main.async(execute: { () -> Void in
                                //  self.stopActivityIndicator()
                                let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                    
                                    self.navigationController?.popViewController(animated: true)
                                }))
                                self.present(alert, animated: true, completion: nil)
                            })
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
    @IBAction func buttonCloseAction(_ sender: Any) {
        hideMenuController()
    }
}

// MARK:: UITableViewDatasource
extension MenuViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMenu.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "MenuTableViewCell") as! MenuTableViewCell
        let valueMode = UserDefaults.standard.value(forKey: "mode") as? String
        if valueMode == "dark" {
            cell.contentView.backgroundColor = UIColor(hexString: "#0E0F27")
            cell.labelMenuIcon.textColor = .white
            cell.imageViewMenuIcon.tintColor = .white
        }
        else if valueMode == "light" {
            cell.contentView.backgroundColor = .white
            cell.labelMenuIcon.textColor = .black
            
            cell.imageViewMenuIcon.tintColor = .black
        }
        
        if arrMenu.count > 0 {
            cell.labelMenuIcon.text = arrMenu[indexPath.row]
        }
        if arrImageSideMenu.count > 0 {
            cell.imageViewMenuIcon.image = UIImage(named: arrImageSideMenu[indexPath.row])
        }
        if (indexPath.row == 8) {
            cell.viewLineOne.isHidden = false
            cell.viewLineTwo.isHidden = false
        } else {
            cell.viewLineOne.isHidden = true
            cell.viewLineTwo.isHidden = true
        }
        return cell
    }
}

// MARK:: UITableViewDelegate
extension MenuViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, viewForFooterInSection section: Int) -> UIView? {
        let vw = UIView()
        vw.backgroundColor = UIColor.clear
        let titleLabel = UILabel(frame: CGRect(x:10,y: vw.frame.origin.y + 20 ,width:350,height:50))
        titleLabel.numberOfLines = 0;
        titleLabel.lineBreakMode = .byWordWrapping
        titleLabel.backgroundColor = UIColor.clear
        titleLabel.font = UIFont(name: "Raleway-light", size: 18)
       // titleLabel.text  = "Footer text here"
        titleLabel.text = "   Version: \(UIApplication.release)"

        vw.addSubview(titleLabel)
        return vw
    }

    func tableView(_ tableView: UITableView, heightForFooterInSection section: Int) -> CGFloat {
        return 100
    }
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if (indexPath.row == 0) {
            
                        let resourceVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ResourceViewController")
                        self.navigationController?.pushViewController(resourceVc, animated: true)
                        hideMenuController()
            /*//MARK:-JobMenu
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let jobVc = storyBoardMain.instantiateViewController(withIdentifier: "JobViewController") as! JobViewController
            self.navigationController?.pushViewController(jobVc, animated: true)
            hideMenuController()
        } else if(indexPath.row == 1) {*/
          
//            //MARK:-GoalMenu
//            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//            let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "GoalViewController") as! GoalViewController
//            self.navigationController?.pushViewController(goalVc, animated: true)
//           hideMenuController()
        } else if (indexPath.row == 1) {
            //MARK:-TaskMenu
            
            let helpVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ToolKitViewController")
            self.navigationController?.pushViewController(helpVc, animated: true)
            /*
            let taskVc = UIStoryboard(name: "Task", bundle: nil).instantiateViewController(withIdentifier: "TaskViewController")
            self.navigationController?.pushViewController(taskVc, animated: true)
           hideMenuController()
            */
        } /*else if (indexPath.row == 3) {
            //MARK:-ChallengeMenu
            let challengeVc = UIStoryboard(name: "Task", bundle: nil).instantiateViewController(withIdentifier: "ChallengeViewController")
            self.navigationController?.pushViewController(challengeVc, animated: true)
            hideMenuController()
        } else if(indexPath.row == 4) {
            //MARK:-E-learningMenu
            let elearningVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ElearningViewController")
            self.navigationController?.pushViewController(elearningVc, animated: true)
            hideMenuController()
        }*/ else if(indexPath.row == 2) {
            
            
            let helpVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "HelpTabViewController")
            self.navigationController?.pushViewController(helpVc, animated: true)
            hideMenuController()
//            //MARK:-ResorceMenu
//            let taskVc = UIStoryboard(name: "Task", bundle: nil).instantiateViewController(withIdentifier: "TaskViewController")
//            self.navigationController?.pushViewController(taskVc, animated: true)
//           hideMenuController()
        }/* else if(indexPath.row == 6) {
            //MARK:-JournalsMenu
            let journalsVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "JournalsListingViewController")
            self.navigationController?.pushViewController(journalsVc, animated: true)
            hideMenuController()
        }*/ else if(indexPath.row == 3) {
            //MARK:-HelpMenu
//
//            let resourceVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "ResourceViewController")
//            self.navigationController?.pushViewController(resourceVc, animated: true)
//            hideMenuController()
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "UpdatePasswordViewController") as! UpdatePasswordViewController
            resourceVc.loginModeForResetPassword = "mentee"
            self.navigationController?.pushViewController(resourceVc, animated: true)
            
        }
        else if(indexPath.row == 7) {
            
//
//            let helpVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "HelpTabViewController")
//            self.navigationController?.pushViewController(helpVc, animated: true)
//            hideMenuController()
           
            
            /*
            if let url = URL(string: CommonFAQDATA.sharedinstance.faqmenteeurl) {
                UIApplication.shared.open(url)
            }
            */
            
            
            /*
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "MessageCenterViewController") as! MessageCenterViewController
            resourceVc.type = "mentee"
            self.navigationController?.pushViewController(resourceVc, animated: true)
            */
        }
        else if(indexPath.row == 5){
            
//            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "UpdatePasswordViewController") as! UpdatePasswordViewController
//            resourceVc.loginModeForResetPassword = "mentee"
//            self.navigationController?.pushViewController(resourceVc, animated: true)
            
            
            /*
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let resourceVc = storyBoardMain.instantiateViewController(withIdentifier: "UpdatePasswordViewController") as! UpdatePasswordViewController
            resourceVc.loginModeForResetPassword = "mentee"
            self.navigationController?.pushViewController(resourceVc, animated: true)
           
            */
           
        }
        
        else if(indexPath.row == 4) {
            
            
            
            if self.connectedToNetwork() {
            //MARK:-LogOut
            let alert = UIAlertController(title: "Logging Out", message: "Are you sure you want to logout", preferredStyle: UIAlertController.Style.alert)
            
            let acceptAction = UIAlertAction(title: "Yes", style: .default) { (_) -> Void in
                self.logOutMentee()
                UserDefaults.standard.setValue(nil, forKey: "userDetails")
                UserDefaults.standard.setValue(nil, forKey: "loginMode")

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
            else {
                let alert = UIAlertController(title: "Title", message: "Not Connected To Internet", preferredStyle: UIAlertController.Style.alert)
                
                let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                }
                alert.addAction(acceptAction)
                self.present(alert, animated: true, completion: nil)
            }
            
            
        }
        
            /*
        else {
           // UserDefaults.standard.set("dark", forKey: "mode")
            
            
            
            let Mode = UserDefaults.standard.value(forKey: "mode") as? String
            if #available(iOS 12.0, *) {
                    switch traitCollection.userInterfaceStyle {
                    case .dark:
                        print("dark set",Mode ?? "")
                        if Mode == "dark" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                        }
                        else if Mode == "light" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                        }
                    case .light:
                        print("light set")
                        if Mode == "light" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                        }
                        else if Mode == "dark" {
                            UserDefaults.standard.set("dark", forKey: "mode")
                        }
                    default:
                      //  UserDefaults.standard.set("light", forKey: "mode")
                        print("Not set")
                    }
                } else {
                    print("below ios 12")
                    if Mode == "dark" {
                        
                    UserDefaults.standard.set("light", forKey: "mode")
                    }
                    else if Mode == "light" {
                        UserDefaults.standard.set("dark", forKey: "mode")
                    }
                    print("set color for ios 11")
                    // Fallback on earlier versions
                }
            
            
            
            /*
            let Mode = UserDefaults.standard.value(forKey: "mode") as? String
            if Mode == "dark" {
                UserDefaults.standard.set("light", forKey: "mode")
            }
            else if Mode == "light" {
                UserDefaults.standard.set("dark", forKey: "mode")
            }
            */
            NotificationCenter.default.post(name: .menteebackgroundColor, object: nil)
            
            tableViewMenu.reloadData()
            let valueMode = UserDefaults.standard.value(forKey: "mode") as? String
            if valueMode == "dark"{
                labelMenteeName.textColor = .white
                labelMenteeEmail.textColor = .white
                
                tableViewMenu.backgroundColor = UIColor(hexString: "#0E0F27")
                topmenuView.backgroundColor = .black
               // backgroundView.backgroundColor = .black
                closeLabel.textColor = .white
            } else if valueMode == "light" {
                labelMenteeName.textColor = .black
                labelMenteeEmail.textColor = .black
                
                tableViewMenu.backgroundColor = .white
                topmenuView.backgroundColor = .white
               // backgroundView.backgroundColor = .white
                closeLabel.textColor = .black
            }
            //let valueMode = UserDefaults.standard.value(forKey: "mode") as? String
            /*
            if valueMode == "dark" {
                UserDefaults.standard.set("light", forKey: "mode")
            }
            else if valueMode == "light" {
                UserDefaults.standard.set("dark", forKey: "mode")
            }
            */
            
        }
        */
    }
}

extension Notification.Name {
    static let menteebackgroundColor = Notification.Name("bgcolor")
    static let menteecolor = Notification.Name("color")
}
