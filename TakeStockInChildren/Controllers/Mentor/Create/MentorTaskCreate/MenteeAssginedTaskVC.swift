//
//  MenteeAssginedTaskVC.swift
//  TakeStockInChildren
//
//  Created by Aquarious  on 15/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class MenteeAssginedTaskVC: BaseViewController {
    
    @IBOutlet weak var tblTaskList: UITableView!
    var arrTaskList = [NSDictionary]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        pendingApiCalling()
    }
    
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:-ButtonBackAction
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    
    //MARK:- Api Calling for Loading Goal Table
    func pendingApiCalling(){
        let userDetails:NSMutableDictionary = [
            "type" : "task",
        ]
        if self.connectedToNetwork() {
            
            //self.showActivityIndicatory(uiView: self.view)
            // self.viewBgLoading.isHidden = false
            self.startActivityIndicator()
            //
            MentorApiManager.mentorSharedInstance.getMentorGoalList(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("PENDINGJSON\(json)")
                    DispatchQueue.main.async(execute: {() -> Void in
                        let dataTemp = json["data"]! as! NSDictionary
                        print("DATA\(dataTemp)")
                        self.arrTaskList = (dataTemp["datalist"] as? [NSDictionary])!
                        print("self.arrtableStatus\(self.arrTaskList)")
                        if(self.arrTaskList.count > 0){
                            self.tblTaskList.reloadData()
                            self.stopActivityIndicator()
                            //self.actInd.stopAnimating()
                            // self.viewBgLoading.isHidden = true
                        } else{
                            self.tblTaskList.reloadData()
                            self.stopActivityIndicator()
                            //self.actInd.stopAnimating()
                            // self.viewBgLoading.isHidden = true
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                    self.actInd.stopAnimating()
                    self.logOutMentor()
                    //self.viewBgLoading.isHidden = true
                    
                })
            })
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
                                let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
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
    @objc func buttonEditTaskDetails(sender: UIButton){
        let buttonTag = sender.tag
        print("buttonTag\(buttonTag)")
        
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "EditCreatedGoalTaskChallengeViewController") as? EditCreatedGoalTaskChallengeViewController
        if (arrTaskList.count > 0) {
            let dicTask = arrTaskList[buttonTag]
            
            vc?.strTitle       = (dicTask["name"] as? String)!
            vc?.strDescription = (dicTask["description"] as? String)!
            vc?.strStartDate   = (dicTask["start_date"] as? String)!
            vc?.strEndDate     = (dicTask["end_date"] as? String)!
            vc?.idCreate       = dicTask["id"] as? Int
            
            print("vc?.idCreate\(String(describing: vc?.idCreate))")
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
    //MARK:ButtonAction
    @IBAction func buttonAddNewCreationAction(_ sender: Any) {
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "CreateNewTaskViewController") as? CreateNewTaskViewController
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}

// MARK:: UITableViewDatasource
extension MenteeAssginedTaskVC: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrTaskList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "TaskListTableViewCell") as! TaskListTableViewCell
        if (arrTaskList.count > 0) {
            let dicTask: NSDictionary = arrTaskList[indexPath.row]
            
            cell.lableTitle.text       = dicTask["name"] as? String
            cell.labelDescription.text = dicTask["description"] as? String
            cell.labelStartDate.text   = dicTask["start_date"] as? String
            cell.labelEndDate.text     = dicTask["end_date"] as? String
            
            cell.buttonEditTask.tag = indexPath.row
            cell.buttonEditTask.addTarget(self, action: #selector(buttonEditTaskDetails(sender:)), for: .touchUpInside)
        }
        return cell
        
    }
}

// MARK:: UITableViewDelegate
extension MenteeAssginedTaskVC: UITableViewDelegate {
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "AssignTaskViewController") as? AssignTaskViewController
        if (arrTaskList.count > 0) {
            let dicTask: NSDictionary = arrTaskList[indexPath.row]
            
            vc?.strTaskTitle = (dicTask["name"] as? String)!
            vc?.strTaskDescription = (dicTask["description"] as? String)!
            vc?.strCreateTaskStartDate = (dicTask["start_date"] as? String)!
            vc?.strTaskEndDate = (dicTask["end_date"] as? String)!
            vc?.taskId = dicTask["id"] as? Int
        }
        
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}


