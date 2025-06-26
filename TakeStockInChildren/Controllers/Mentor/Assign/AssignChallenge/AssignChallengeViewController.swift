//
//  AssignChallengeViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 29/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class AssignChallengeViewController: BaseViewController {
    
    @IBOutlet weak var tableViewAssignChallenge: UITableView!
    @IBOutlet weak var tableViewAddMentee: UITableView!
    @IBOutlet weak var viewPopUpForSelectMentee: UIView!
    
    var strChallengeTitle : String = ""
    var strChallengeDescription : String = ""
    var strCreateChallengeStartDate : String = ""
    var strChallengeEndDate : String = ""
    var arrMenteeList = [NSDictionary]()
    var dataList = [NSDictionary]()
    var assignedMentee = [NSDictionary]()
    var menteeId : Int?
    var challengeId : Int?
    var imageURLStr : String = ""
    var assignedMenteeName: [String] = []
    var assignedMenteeIndex: [String] = []
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.navigationController?.setNavigationBarHidden(true, animated: animated)
        self.tabBarController?.tabBar.isHidden = false
        viewPopUpForSelectMentee.isHidden = true
        tableViewAssignChallenge.setContentOffset(.zero, animated:true)
        tableViewAddMentee.setContentOffset(.zero, animated:true)
        assignedMenteeList()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        self.navigationController?.setNavigationBarHidden(false, animated: animated)
    }
    
    //MARK:-GetMenteeList
    func getMenteeList(){
        if self.connectedToNetwork()
        {
            startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeList)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    print("dictVal\(dictVal)")
                    
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    print("dictMain\(dictMain)")
                    self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                    
                    self.tableViewAddMentee.reloadData()
                    self.tableViewAssignChallenge.reloadData()
                    self.stopActivityIndicator()
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            }
        } else
        {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK:-AssignedMenteeList
    func assignedMenteeList(){
        let userDetails:NSMutableDictionary = [
            "goaltask_id"    : self.challengeId,
            "type" : "challenge",
        ]
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            MentorApiManager.mentorSharedInstance.assignedMenteeListGoalTaskChallenge(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("AssignedMenteeList\(json)")
                    self.stopActivityIndicator()
                    DispatchQueue.main.async(execute: {() -> Void in
                        let status = json["status"] as? Int
                        print("STATUS\(status)")
                        if(status == 1){
                            let dictVal = json["data"]
                            let dictMain:NSDictionary = dictVal as! NSDictionary
                            print("dictMain\(dictMain)")
                            self.assignedMentee = dictMain["mentee_list"] as! [NSDictionary]
                            print("AssignedMentee\(self.assignedMentee.count)")
                            
                            for item in self.assignedMentee{
                                self.assignedMenteeIndex.append("\(item["id"] ?? "nil")")
                                var TotalName = ""
                                
                                let firstName = item["firstname"] as? String
                                let middleName = item["middlename"] as? String
                                let lastName = item["lastname"] as? String
                                
                                if let fName = firstName{
                                    TotalName = fName
                                }
                                
                                if let mName = middleName{
                                    if mName != ""{
                                        
                                        TotalName += " \(mName)"
                                        
                                    }
                                }
                                
                                if let LName = lastName{
                                    if LName != ""{
                                        
                                        TotalName += " \(LName)"
                                        //TotalName = " \(LName)"
                                    }
                                }
                                print("Totalname\(TotalName)")
                                
                                self.assignedMenteeName.append(TotalName)
                                print("self.menteename\(self.assignedMenteeName)")
                            }
                            self.tableViewAssignChallenge.reloadData()
                            self.tableViewAssignChallenge.setContentOffset(.zero, animated:true)
                        }else{
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let alert = UIAlertController(title: "Alert", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
            
        }
        else {
            showAlertAction(withTitle: "Alert", message: "Unable to connect" )
        }
    }
    
    //MARK:-AssignAMentee
    func assignMentee(){
        let userDetails:NSMutableDictionary = [
            "goaltask_id"    : self.challengeId,
            "type" : "challenge",
            "mentee_id"      : self.menteeId
        ]
        if self.connectedToNetwork() {
            
            self.startActivityIndicator()
            MentorApiManager.mentorSharedInstance.assignGoalTaskChallenge(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("PENDINGJSON\(json)")
                    DispatchQueue.main.async(execute: {() -> Void in
                        let dataTemp = json["data"]! as! NSDictionary
                        print("DATA\(dataTemp.count)")
                        self.stopActivityIndicator()
                        let alertController = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: .alert)
                        let actionOk = UIAlertAction(title: "Ok", style: .cancel) { (action) in
                            self.viewPopUpForSelectMentee.isHidden = true
                            self.assignedMenteeList()
                        }
                        alertController.addAction(actionOk)
                        self.present(alertController, animated: true, completion: nil)
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let alert = UIAlertController(title: "Alert", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        }
        else {
            showAlertAction(withTitle: "Alert", message: "Unable to connect" )
        }
    }
    
    //MARK:-DeleteAAssignedMentee
    @objc func buttonDeleteMentee(sender: UIButton){
        let index = sender.tag
        print("buttonTag\(index)")
        let deleteId = assignedMenteeIndex[index]
        print("deletementeeId\(deleteId)")
        if self.connectedToNetwork()
        {
            startActivityIndicator()
            guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "DeleteMenteeChallenge Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    
                })
            }
            // let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let deleteParameters:NSMutableDictionary = [
                "goaltask_id" : self.challengeId,
                "type" : "challenge",
                "mentee_id" : deleteId
            ]
            print("deleteParameters\(deleteParameters)")
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.DeleteAssignedMenteeGoalTaskChallenge)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters:deleteParameters as! Parameters, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    print("dictVal\(dictVal)")
                    self.assignedMentee.remove(at: index)
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    
                    let alertController = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: .alert)
                    let actionOk = UIAlertAction(title: "Ok", style: .cancel) { (action) in
                        self.assignedMenteeList()
                    }
                    alertController.addAction(actionOk)
                    self.present(alertController, animated: true, completion: nil)
                    print("arrofimagescount\(self.assignedMenteeName.count)")
                    self.tableViewAssignChallenge.reloadData()
                    self.stopActivityIndicator()
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            }
        } else
        {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    
    //MARK:-AddMentee
    @IBAction func buttonAddMenteePopUp(_ sender: Any) {
        viewPopUpForSelectMentee.isHidden = false
        getMenteeList()
        // self.tabBarController?.tabBar.isHidden = false
    }
    
    //MARK:-CloseAddMenteePopUp
    @IBAction func buttonCloseAddMenteePopUp(_ sender: Any) {
        viewPopUpForSelectMentee.isHidden = true
    }
    
    //MARK:-ButtonBackAction
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
}

// MARK:: UITableViewDatasource
extension AssignChallengeViewController: UITableViewDataSource,UITableViewDelegate {
    func numberOfSections(in tableView: UITableView) -> Int {
        if(tableView == tableViewAssignChallenge){
            return 3
        }else{
            return 1
        }
    }
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if(tableView == tableViewAssignChallenge){
            if(section == 0){
                return 1
            }else if(section == 1){
                return 1
            }else{
                return assignedMentee.count
            }
        }else{
            return self.arrMenteeList.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if (tableView == tableViewAssignChallenge) {
            // if(indexPath.row == 0){
            if (indexPath.section == 0) {
                let cell = tableView.dequeueReusableCell(withIdentifier: "AssignedChallengeDetailsTableViewCell") as! AssignedChallengeDetailsTableViewCell
                cell.labelChallengeTitle.text = strChallengeTitle
                cell.labelDescription.text = strChallengeDescription
                cell.labelStartDate.text = strCreateChallengeStartDate
                cell.labelEndDate.text = strChallengeEndDate
                return cell
            } else if (indexPath.section == 1) {
                let cell = tableView.dequeueReusableCell(withIdentifier: "HeaderAssignedmenteeListTableViewCell") as! HeaderAssignedmenteeListTableViewCell
                if (assignedMentee.count == 0) {
                    cell.lableAssignmentStatus.text = "No Mentee Assigned"
                } else {
                    cell.lableAssignmentStatus.text = "Assigned Mentee"
                }
                return cell
            } else {
                let cell = tableView.dequeueReusableCell(withIdentifier: "AssignedMenteeListTableViewCell") as! AssignedMenteeListTableViewCell
                if assignedMenteeName.count > 0 {
                    print("assignedMenteeName\(assignedMenteeName)")
                    cell.lableAssignedMenteeName.text = assignedMenteeName[indexPath.row]
                }
                if (assignedMentee.count > 0) {
                    let dicAssignMentee: NSDictionary = assignedMentee[indexPath.row]
                    imageURLStr = TakeStockInChildrenConstant.MenteeImageBaseURL.appending(dicAssignMentee["image"] as! String)
                    print("imageURLStr\(imageURLStr)")
                    cell.imageViewAssignedMentee.sd_setImage(with: URL(string: imageURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
                    
                    cell.buttonDeleteMentee.tag = indexPath.row
                    cell.buttonDeleteMentee.addTarget(self, action: #selector(buttonDeleteMentee(sender:)), for: .touchUpInside)
                    
                    let  presentWorkStatus = dicAssignMentee["assign_status"] as? Int
                    print("presentWorkStatus\(String(describing: presentWorkStatus))")
                    if (presentWorkStatus == 0) {
                        cell.labelStausAssignedMentee.text = "Pending"
                    } else if(presentWorkStatus == 1) {
                        cell.labelStausAssignedMentee.text = "In Progress"
                    } else {
                        cell.labelStausAssignedMentee.text = "Completed"
                    }
                } else {
                    showAlert(_sourceController: self, _msg: "No mentee is assigned till now")
                }
                return cell
            }
        } else {
            let cell = tableView.dequeueReusableCell(withIdentifier: "UnAssignedMenteeListTableViewCell") as! UnAssignedMenteeListTableViewCell
            
            let name : String = ((arrMenteeList[indexPath.row]["firstname"] as? String)!)  + " " + ((arrMenteeList[indexPath.row]["middlename"] as? String)!) + " " + ((arrMenteeList[indexPath.row]["lastname"] as? String)!)
            cell.labelUnassignedMenteeName.text = name as? String
            return cell
        }
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if (tableView == tableViewAssignChallenge) {
            print(indexPath.row)
        } else {
            if arrMenteeList.count > 0 {
                print("menteeid\(String(describing: arrMenteeList[indexPath.row]["id"]))")
                let alert = UIAlertController(title: "Alert", message: "Do you want to assign this mentee", preferredStyle: UIAlertController.Style.alert)
                menteeId = arrMenteeList[indexPath.row]["id"] as? Int
                
                let acceptAction = UIAlertAction(title: "Yes", style: .default) { (_) -> Void in
                    self.assignMentee()
                }
                let cancelAction = UIAlertAction(title: "No", style: .cancel) { (_) -> Void in
                }
                alert.addAction(acceptAction)
                alert.addAction(cancelAction)
                self.present(alert, animated: true, completion: nil)
            }
        }
    }
    
}


