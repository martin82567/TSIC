//
//  MentorViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 31/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MentorViewController: BaseViewController,MenuControllerDelegate{
    
    @IBOutlet weak var buttonMenu: UIButton!
    @IBOutlet weak var tableViewMentor: UITableView!
    @IBOutlet weak var labelMentorName: UILabel!
    @IBOutlet weak var imageViewMentorProfile: UIImageView!
    
    var dicUserDetails = NSDictionary()
    var mentorPhoneNumber : String = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()
        getMentorDetails()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        self.buttonMenu.addTarget(self, action: #selector(MentorViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
    }
    
    @IBAction func btnChatAction(_ sender: Any) {
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
            newViewController.loginMenteeButMentorDicData = self.dicUserDetails
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
    
    
    // MARK:: UIStatusBar
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
    
    //MARK:: Function Api Calling
    func getMentorDetails () {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getMentorDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        self.dicUserDetails = json["mentor_details"] as! NSDictionary
                        print("GetdicuserDetails\(self.dicUserDetails)")
                        let firstName = (self.dicUserDetails["firstname"] as! String)
                        let middleName = (self.dicUserDetails["middlename"] as! String)
                        let lastName = (self.dicUserDetails["lastname"] as! String)
                        print("name\(firstName)")
                        self.labelMentorName.text     = firstName + " " + middleName + " " + lastName
                        print("self.labelMentorName.text\(self.labelMentorName.text)")
                        self.tableViewMentor.reloadData()
                        self.stopActivityIndicator()
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
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    @IBAction func buttonCallAction(_ sender: UIButton) {
        print("PhoneNumber\(mentorPhoneNumber)")
        
        if let url = NSURL(string: "tel://\(mentorPhoneNumber))"), UIApplication.shared.canOpenURL(url as URL) {
            //UIApplication.shared.openURL(url as URL)
            UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)//(url, options: [:], completionHandler: nil)
        }
    }
    
}

// MARK:: UITableViewDatasource
extension MentorViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let myMenteeDetailsCell = tableView.dequeueReusableCell(withIdentifier: "MyMentorDetailsTableViewCell") as! MyMentorDetailsTableViewCell
        
        if (dicUserDetails != nil) {
            if let emailMentor = dicUserDetails["email"] as? String {
                myMenteeDetailsCell.labelMentorEmailCell.text = emailMentor
            }
            if let phoneMentor = dicUserDetails["phone"] as? String {
                myMenteeDetailsCell.labelMentorPhoneCell.text = phoneMentor
                mentorPhoneNumber = phoneMentor
            }
            if let addressMentor = dicUserDetails["address"] as? String {
                myMenteeDetailsCell.labelMentorAddressCell.text = addressMentor
            }
            if let imageProfile = dicUserDetails["image"] as? String {
                let profileImage = TakeStockInChildrenConstant.MentorImageBaseURL.appending(imageProfile)
                imageViewMentorProfile.sd_setImage(with: URL(string: profileImage), placeholderImage: UIImage(named: "defaultProfileImage"))
            }
            myMenteeDetailsCell.buttonCallTapped.tag = indexPath.row
            print(myMenteeDetailsCell.buttonCallTapped.tag)
            myMenteeDetailsCell.buttonCallTapped.addTarget(self, action: #selector(buttonCallAction(_:)), for: .touchUpInside)
        }
        return myMenteeDetailsCell
    }
}

