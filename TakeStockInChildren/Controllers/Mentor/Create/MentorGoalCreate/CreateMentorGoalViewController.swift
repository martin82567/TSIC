//
//  CreateMentorGoalViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 20/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
//import CarbonKit
import Alamofire
class CreateMentorGoalViewController: BaseViewController {
    
    @IBOutlet weak var tableViewGoalList: UITableView!
    
    var arrtableStatus = [NSDictionary]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        pendingApiCalling()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
    }
    
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- Api Server Call for Loading Goal Table
    func pendingApiCalling() {
        let userDetails:NSMutableDictionary = [
            "type" : "goal",
        ]
        if self.connectedToNetwork() {
            startActivityIndicator()
            MentorApiManager.mentorSharedInstance.getMentorGoalList(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("PENDINGJSON\(json)")
                    self.stopActivityIndicator()
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        guard let dataTemp = json["data"] as? NSDictionary else {
                            return
                        }
                        
                        guard let tableStatus = json["data"] as? NSDictionary else {
                            return
                        }
                        self.arrtableStatus = (dataTemp["datalist"] as? [NSDictionary])!
                        print("self.arrtableStatus\(self.arrtableStatus)")
                        if self.arrtableStatus.count > 0 {
                            self.tableViewGoalList.reloadData()
                            //self.stopActivityIndicator()
                        } else {
                            //self.tableViewGoalList.reloadData()
                            //self.stopActivityIndicator()
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.logOutMentor()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                    
                })
            })
        } else {
            showAlertAction(withTitle: "Alert", message: "Unable to connect" )
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
    
    //MARK:-EditGoals
    @objc func buttonEditGoalDetails(sender: UIButton) {
        let buttonTag = sender.tag
        print("buttonTag\(buttonTag)")
        
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "EditCreatedGoalTaskChallengeViewController") as? EditCreatedGoalTaskChallengeViewController
        if arrtableStatus.count > 0 {
            vc?.strTitle = (arrtableStatus[buttonTag ]["name"] as? String)!
            vc?.strDescription = (arrtableStatus[buttonTag]["description"] as? String)!
            vc?.strStartDate = (arrtableStatus[buttonTag]["start_date"] as? String)!
            vc?.strEndDate = (arrtableStatus[buttonTag]["end_date"] as? String)!
            vc?.idCreate = arrtableStatus[buttonTag]["id"] as? Int
            print("vc?.idCreate\(String(describing: vc?.idCreate))")
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
    //MARK:-ButtonAction
    @IBAction func buttonAddNewCreationAction(_ sender: Any) {
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "CreateNewGoalViewController") as? CreateNewGoalViewController
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
}

// MARK:: UITableViewDatasource
extension CreateMentorGoalViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrtableStatus.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "GoalTableViewCell") as! GoalTableViewCell
        if arrtableStatus.count > 0 {
            let dicStatus: NSDictionary = arrtableStatus[indexPath.row]
            cell.lableTitle.text = dicStatus["name"] as? String
            cell.labelDescription.text = dicStatus["description"] as? String
            cell.labelStartDate.text = dicStatus["start_date"] as? String
            cell.labelEndDate.text = dicStatus["end_date"] as? String
            cell.buttonEditGoal.tag = indexPath.row
            cell.buttonEditGoal.addTarget(self, action: #selector(buttonEditGoalDetails(sender:)), for: .touchUpInside)
        }
        return cell
    }
}

// MARK:: UITableViewDelegate
extension CreateMentorGoalViewController: UITableViewDelegate {
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "AssignGoalViewController") as? AssignGoalViewController
        if arrtableStatus.count > 0 {
            vc?.strGoalTitle = (arrtableStatus[indexPath.item]["name"] as? String)!
            vc?.strGoalDescription = (arrtableStatus[indexPath.item]["description"] as? String)!
            vc?.strCreateGoalStartDate = (arrtableStatus[indexPath.item]["start_date"] as? String)!
            vc?.strGoalEndDate = (arrtableStatus[indexPath.item]["end_date"] as? String)!
            vc?.goalId = arrtableStatus[indexPath.item]["id"] as? Int
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
}




