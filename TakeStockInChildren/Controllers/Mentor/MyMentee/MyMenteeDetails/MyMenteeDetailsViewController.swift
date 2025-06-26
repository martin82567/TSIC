//
//  MyMenteeDetailsViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 12/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MyMenteeDetailsViewController: BaseViewController {
    @IBOutlet weak var labelMenteeName: UILabel!
    @IBOutlet weak var imageViewMenteeProfile: UIImageView!
    @IBOutlet weak var tblView: UITableView!
    
    var strMenteeName : String = ""
    var strMenteeEmail : String = ""
    var strMenteePhoneNumber : String = ""
    var strMenteeCurrentLivingDetails : String = ""
    var strMenteeImageUrl : String?
    var arrMenteeList = NSDictionary()

    var strIamFrom = ""
    
    @IBOutlet weak var lblHeader: UILabel!
    
    @IBOutlet weak var btnChatOutlet: UIButton!
    
    override func viewDidLoad() {
        super.viewDidLoad()
     
        if strIamFrom == "mentee" {
            self.lblHeader.text = "MENTEE"
            self.menteeDataLoad()
        } else {
            self.lblHeader.text = "STAFF"
            staffDataLoad()
        }
        self.tblView.dataSource = self
        self.tblView.reloadData()
    }

    func staffDataLoad() {
        print(arrMenteeList)
        
        if let staffName = arrMenteeList["name"] as? String {
              labelMenteeName.text = staffName
        }
       
        if let profile_pic = arrMenteeList["profile_pic"] as? String {
            let imgUrlStr = TakeStockInChildrenConstant.StaffImageBaseURL.appending(profile_pic)
            
            imageViewMenteeProfile.sd_setImage(with: URL(string: imgUrlStr), placeholderImage: UIImage(named: "defaultProfileImage"))
        }
        
    
        let mentorData = UserDefaults.standard.value(forKey: "mentorUserDetails") as? NSDictionary
        
        if let mentorData2 = mentorData {
            let is_chat_mentee = mentorData2["is_chat_staff"] as! Bool
            
            if is_chat_mentee==true {
                self.btnChatOutlet.isEnabled = true
                
                // self.btnChatOutlet.backgroundColor = UIColor.red
            } else {
                self.btnChatOutlet.isEnabled = false
                self.btnChatOutlet.backgroundColor = UIColor.gray
            }
        }
    }
    
    
    func menteeDataLoad() {
        if let nameMentee = strMenteeName as? String {
            labelMenteeName.text = strMenteeName
        }
        
        imageViewMenteeProfile.sd_setImage(with: URL(string: strMenteeImageUrl!), placeholderImage: UIImage(named: "defaultProfileImage"))
        
        let mentorData = UserDefaults.standard.value(forKey: "mentorUserDetails") as? NSDictionary
        
        if let mentorData2 = mentorData {
            let is_chat_mentee = mentorData2["is_chat_mentee"] as! Bool
            
            if is_chat_mentee==true {
                self.btnChatOutlet.isEnabled = true
                
                // self.btnChatOutlet.backgroundColor = UIColor.red
            } else {
                self.btnChatOutlet.isEnabled = false
                self.btnChatOutlet.backgroundColor = UIColor.gray
            }
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    @IBAction func buttonCallAction(_ sender: Any) {
        
    }
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    @IBAction func buttonChatAction(_ sender: Any) {
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
            newViewController.strIamfrom = self.strIamFrom
            newViewController.loginMenteeButMentorDicData = self.arrMenteeList
            
            //            newViewController.assignValueAfterPOP = {() -> Void in
            //                self.serviceCallTogetUpcommingMettings()
            //            }
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
}

// MARK:: UITableViewDatasource
extension MyMenteeDetailsViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let myMenteeDetailsCell = tableView.dequeueReusableCell(withIdentifier: "MyMenteeDetailsTableViewCell") as! MyMenteeDetailsTableViewCell
        
        if strIamFrom == "mentee" {
            myMenteeDetailsCell.addressImg.isHidden = false
            myMenteeDetailsCell.phoneImg.isHidden = false
            if let emailMentee = strMenteeEmail as? String {
                myMenteeDetailsCell.labelMenteeEmailCell.text = emailMentee
            }
            if let phoneMentee = strMenteePhoneNumber as? String {
                myMenteeDetailsCell.labelMenteePhoneCell.text = phoneMentee
            }
            if let addressMentee = strMenteeCurrentLivingDetails as? String {
                myMenteeDetailsCell.labelMenteeAddressCell.text = addressMentee
            }
            
            // myMenteeDetailsCell.labelMenteeEmailCell.text = strMenteeEmail
            myMenteeDetailsCell.labelMenteePhoneCell.text = strMenteePhoneNumber
            myMenteeDetailsCell.labelMenteeAddressCell.text = strMenteeCurrentLivingDetails
        } else {
            myMenteeDetailsCell.addressImg.isHidden = true
            myMenteeDetailsCell.phoneImg.isHidden = true
            
            if let emailStaff =  self.arrMenteeList["email"] as? String {
                myMenteeDetailsCell.labelMenteeEmailCell.text = emailStaff
            }
            
            if let address =  self.arrMenteeList["address"] as? String {
                myMenteeDetailsCell.labelMenteeAddressCell.text = address
            }
        }
        
        return myMenteeDetailsCell
    }
}

