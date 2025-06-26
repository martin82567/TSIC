//
//  CreateMentorChallengeViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 20/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
//import CarbonKit
import Alamofire
class CreateMentorChallengeViewController: BaseViewController {

    @IBOutlet weak var tableViewChallengeList: UITableView!
    
    var arrtableStatus = [NSDictionary]()
    
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
    
    //MARK:- Api Calling for Loading Goal Table
    func pendingApiCalling(){
        let userDetails:NSMutableDictionary = [
            "type" : "challenge",
        ]
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            //self.showActivityIndicatory(uiView: self.view)
            // self.viewBgLoading.isHidden = false
            MentorApiManager.mentorSharedInstance.getMentorGoalList(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("PENDINGJSON\(json)")
                    DispatchQueue.main.async(execute: {() -> Void in
                        let dataTemp = json["data"]! as! NSDictionary
                        print("DATA\(dataTemp)")
                        self.arrtableStatus = (dataTemp["datalist"] as? [NSDictionary])!
                        print("self.arrtableStatus\(self.arrtableStatus)")
                        if(self.arrtableStatus.count > 0){
                            self.tableViewChallengeList.reloadData()
                            self.stopActivityIndicator()
                           // self.actInd.stopAnimating()
                            // self.viewBgLoading.isHidden = true
                        }
                        else{
                            self.tableViewChallengeList.reloadData()
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
                    self.logOutMentor()
                    //self.actInd.stopAnimating()
                    //                        self.viewBgLoading.isHidden = true
                    
                })
            })
        }
        else {
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
    
    //MARK:-EditGoals
    @objc func buttonEditChallengeDetails(sender: UIButton){
        let buttonTag = sender.tag
        print("buttonTag\(buttonTag)")
        
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "EditCreatedGoalTaskChallengeViewController") as? EditCreatedGoalTaskChallengeViewController
        if(arrtableStatus.count > 0){
            vc?.strTitle = (arrtableStatus[buttonTag ]["name"] as? String)!
            vc?.strDescription = (arrtableStatus[buttonTag]["description"] as? String)!
            vc?.strStartDate = (arrtableStatus[buttonTag]["start_date"] as? String)!
            vc?.strEndDate = (arrtableStatus[buttonTag]["end_date"] as? String)!
            vc?.idCreate = arrtableStatus[buttonTag]["id"] as? Int
            print("vc?.idCreate\(vc?.idCreate)")
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
    //MARK:-ButtonAction
    @IBAction func buttonAddNewCreationAction(_ sender: Any) {
            let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "CreateNewChallengeViewController") as? CreateNewChallengeViewController
            self.navigationController?.pushViewController(vc!, animated: true)
    }
}

// MARK:: UITableViewDatasource
extension CreateMentorChallengeViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrtableStatus.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ChallengeListTableViewCell") as! ChallengeListTableViewCell
        if (arrtableStatus.count > 0) {
            let dicStatus: NSDictionary = arrtableStatus[indexPath.row]
            cell.lableTitle.text = dicStatus["name"] as? String
            cell.labelDescription.text = dicStatus["description"] as? String
            cell.labelStartDate.text = dicStatus["start_date"] as? String
            cell.labelEndDate.text = dicStatus["end_date"] as? String
            
            cell.buttonEditChallenge.tag = indexPath.row
            cell.buttonEditChallenge.addTarget(self, action: #selector(buttonEditChallengeDetails(sender:)), for: .touchUpInside)
            
        }
        return cell
    }
}

// MARK:: UITableViewDelegate
extension CreateMentorChallengeViewController: UITableViewDelegate {
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let vc = UIStoryboard.init(name: "Mentor", bundle: Bundle.main).instantiateViewController(withIdentifier: "AssignChallengeViewController") as? AssignChallengeViewController
        if (arrtableStatus.count > 0) {
            let dicStatus: NSDictionary = arrtableStatus[indexPath.row]
            vc?.strChallengeTitle = (dicStatus["name"] as? String)!
            vc?.strChallengeDescription = (dicStatus["description"] as? String)!
            vc?.strCreateChallengeStartDate = (dicStatus["start_date"] as? String)!
            vc?.strChallengeEndDate = (dicStatus["end_date"] as? String)!
            vc?.challengeId = dicStatus["id"] as? Int
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}

