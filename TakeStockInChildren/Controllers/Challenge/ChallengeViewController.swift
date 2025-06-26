//
//  ChallengeViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 18/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class ChallengeViewController: BaseViewController {
    
    @IBOutlet weak var collectionViewStatusChallenge: UICollectionView!
    @IBOutlet weak var tableViewChallenge: UITableView!
    
    var arrChallengeStaus = ["Pending","Completed"]
    var arrtableStatus = [NSDictionary]()
    var isPending: Bool = true
    var selectedIndex : Int?
    var presentWorkStatus : Int?
    var strHeaderTitle : String = ""
    var cell = ChallengeCollectionViewCell()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        //manageAutoLayOut()
        selectedIndex = 0
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewChallenge.estimatedRowHeight = 80
        tableViewChallenge.rowHeight = UITableView.automaticDimension
        tableViewChallenge.setContentOffset(.zero, animated:true)
        //arrtableStatus.removeAll()
        if(selectedIndex == 0){
            isPending = true
            pendingApiCalling()
        }
        else{
            isPending = false
            pendingApiCalling()
        }
        collectionViewStatusChallenge.reloadData()
    }

    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
   
    //MARK:- Api Calling for Loading Goal Table
    func pendingApiCalling(){
        let userDetails:NSMutableDictionary = [
            "type" : "challenge",
            "search_text" :""
        ]
        if self.connectedToNetwork() {
            if(isPending == true){
                self.startActivityIndicator()
                ApiManager.sharedInstance.getgGoalList(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        print("JSON\(json)")
                        DispatchQueue.main.async(execute: {() -> Void in
                            let dataTemp = json["data"]! as! NSDictionary
                            print("DATA\(dataTemp)")
                            self.arrtableStatus = (dataTemp["datalist"] as? [NSDictionary])!
                            print(self.arrtableStatus)
                            
                            if(self.arrtableStatus.count > 0){
                                print(self.arrtableStatus.count)
                                self.tableViewChallenge.reloadData()
                                self.stopActivityIndicator()
                            }
                            else{
                                self.tableViewChallenge.reloadData()
                                self.showAlert(_sourceController: self, _msg: "No pending Challenges")
                                self.stopActivityIndicator()
                            }
                        })
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        /*
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                        */
                        self.stopActivityIndicator()
                        self.logOutMentee()
                    })
                })
            }
            else{
                self.startActivityIndicator()
                ApiManager.sharedInstance.getCompleteGoalList(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        
                        print("JSON\(json)")
                        DispatchQueue.main.async(execute: {() -> Void in
                            let dataTemp = json["data"]! as! NSDictionary
                            self.arrtableStatus.removeAll()
                            self.arrtableStatus = dataTemp["datalist"] as! [NSDictionary]
                            
                            if(self.arrtableStatus.count > 0){
                                self.tableViewChallenge.reloadData()
                                self.stopActivityIndicator()
                            }
                            else{
                                self.showAlert(_sourceController: self, _msg: "No challenges completed")
                                self.stopActivityIndicator()
                            }
                        })
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        /*
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                        */
                        self.stopActivityIndicator()
                        self.logOutMentee()
                    })
                })
            }
        }
        else {
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
    
    @IBAction func buttonCompletedDetailsTap(_ sender: Any) {
        let getbtnDeleteIndex = sender as! UIButton
        let index = Int(getbtnDeleteIndex.tag)
        
        let vc = UIStoryboard.init(name: "Main", bundle: Bundle.main).instantiateViewController(withIdentifier: "CompletedDetailsViewController") as? CompletedDetailsViewController
        if(arrtableStatus.count > 0){
        let assign = self.arrtableStatus[index]["assign_id"] as! Int
        vc?.strAssignId = String(assign) ?? ""
        vc?.strGoalName = (arrtableStatus[index]["name"] as? String) ?? ""
        vc?.strDescription = (arrtableStatus[index]["description"] as? String) ?? ""
        vc?.strStartDate = (arrtableStatus[index]["start_date"] as? String) ?? ""
        vc?.strEndDate = (arrtableStatus[index]["end_date"] as? String) ?? ""
        vc?.isType = 3
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}



// MARK:: UICollectionViewViewDatasource
extension ChallengeViewController: UICollectionViewDataSource {
    func collectionView(_ collectionView: UICollectionView,
                        numberOfItemsInSection section: Int) -> Int {
        return arrChallengeStaus.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        cell = collectionView.dequeueReusableCell(withReuseIdentifier: "ChallengeCollectionViewCell", for: indexPath) as! ChallengeCollectionViewCell
        if arrChallengeStaus.count > 0 {
            cell.labelStatusCell.text = arrChallengeStaus[indexPath.item]
        }
        if (indexPath.row == selectedIndex) {
            cell.labelStatusCell.textColor = UIColor(red: 167.0/255.0, green: 174.0/255.0, blue: 59.0/255.0, alpha: 1.0)
        } else {
            cell.labelStatusCell.textColor = UIColor.black
        }
        return cell
    }
}

// MARK:: UICollectionViewViewDelegate
extension ChallengeViewController: UICollectionViewDelegate {
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        if(indexPath.row == 0){
            selectedIndex = indexPath.row
            arrtableStatus.removeAll()
            collectionViewStatusChallenge.reloadData()
            isPending = true
            pendingApiCalling()
            tableViewChallenge.reloadData()
        }
        else{
            selectedIndex = indexPath.row
            arrtableStatus.removeAll()
            collectionViewStatusChallenge.reloadData()
            isPending = false
            pendingApiCalling()
            tableViewChallenge.reloadData()
        }
    }
}

extension ChallengeViewController: UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView,
                        layout collectionViewLayout: UICollectionViewLayout,
                        sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((collectionViewStatusChallenge.frame.size.width - 30) / 2)
        return CGSize(width: width, height: 36)
    }
}

// MARK:: UITableViewDatasource
extension ChallengeViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if(arrtableStatus.count == 0){
            print("NoData")
            self.actInd.stopAnimating()
        }
        return arrtableStatus.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ChallengeTableViewCell") as! ChallengeTableViewCell
        cell.labelHeadCell.text = ""
        cell.labelDescriptionCell.text = ""
        if (arrtableStatus.count > 0) {
            if (isPending == true) {
                cell.imageViewCompletedChallengeCell.isHidden = true
                cell.buttonCompleted.isHidden = true
                cell.buttonCompleted.isUserInteractionEnabled = false
                presentWorkStatus = arrtableStatus[indexPath.item]["datastatus"] as? Int
                
                if (presentWorkStatus == 1) {
                    cell.imageViewInProgress.isHidden = false
                } else {
                    cell.imageViewInProgress.isHidden = true
                }
                print("presentWorkStatus\(String(describing: presentWorkStatus))")
            } else {
                cell.imageViewInProgress.isHidden = true
                cell.imageViewCompletedChallengeCell.isHidden = false
                cell.buttonCompleted.isHidden = false
                cell.buttonCompleted.isUserInteractionEnabled = true
                cell.buttonCompleted.tag = indexPath.row
                cell.buttonCompleted.addTarget(self, action: #selector(buttonCompletedDetailsTap(_:)), for: .touchUpInside)
            }
            cell.labelHeadCell.text = arrtableStatus[indexPath.item]["name"] as? String
            cell.labelDescriptionCell.text = arrtableStatus[indexPath.item]["description"] as? String
        } else {
            
        }
        
        return cell
    }
}

// MARK:: UITableViewDelegate
extension ChallengeViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        let vc = UIStoryboard.init(name: "Main", bundle: Bundle.main).instantiateViewController(withIdentifier: "PendingDetailsViewController") as? PendingDetailsViewController
        if (arrtableStatus.count > 0) {
            let assign = self.arrtableStatus[indexPath.item]["assign_id"] as! Int
            vc?.strAssignId = String(assign) ?? ""
            vc?.strGoalName = (arrtableStatus[indexPath.item]["name"] as? String) ?? ""
            vc?.strDescription = (arrtableStatus[indexPath.item]["description"] as? String) ?? ""
            vc?.strStartDate = (arrtableStatus[indexPath.item]["start_date"] as? String) ?? ""
            vc?.strEndDate = (arrtableStatus[indexPath.item]["end_date"] as? String) ?? ""
            vc?.isType = 3
            vc?.presentedWorkStatusOnDetailsScreen = arrtableStatus[indexPath.item]["datastatus"] as? Int
        }
//print("Challenge.presentedWorkStatusOnDetailsScreen\(vc?.presentedWorkStatusOnDetailsScreen)")
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}

