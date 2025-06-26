//
//  GoalViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 10/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
//import Crashlytics
protocol backrefreshsix {
    func backrefreshSix(name: String)
}
class GoalViewController: BaseViewController {
    
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var topHeaderimage: UIImageView!
    @IBOutlet weak var backgroundimageview: UIImageView!
    @IBOutlet weak var imageViewMenu: UIImageView!
    @IBOutlet weak var tableViewGoal: UITableView!
    @IBOutlet weak var collectionViewStatusGoal: UICollectionView!
    
    @IBOutlet weak var floatBtn: UIButton!
    var arrGoalStaus = ["Pending","Completed"]
    var arrGoalList = [NSDictionary]()
    var isPending: Bool = true
    var selectedIndex : Int?
    var presentWorkStatus : Int?
    var cell = GoalStatusCollectionViewCell()
    var isFromMenu: Bool = false
    var valueMode : String?
    
    var delegate : backrefreshsix!
    override func viewDidLoad() {
        super.viewDidLoad()
     //   self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
        
        let value = UserDefaults.standard.string(forKey: "loginMode")
        if value == "Mentor" {
            floatBtn.isHidden = false
        }
        else if value == "Mentee" {
            floatBtn.isHidden = true
        }
        
        selectedIndex = 0
       // darkmodechanged()
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = true
        tableViewGoal.setContentOffset(.zero, animated:true)
        print("ViewWillAppearSelectedIndex\(String(describing: selectedIndex))")
        //arrtableStatus.removeAll()
        if(selectedIndex == 0){
            isPending = true
            pendingApiCalling()
        } else{
            isPending = false
            pendingApiCalling()
        }
        collectionViewStatusGoal.reloadData()
    }
    
    
    func darkmodechanged() {
        if self.valueMode == "dark" {
            topHeaderimage.image = UIImage(named: "Arcdark11")
            tableViewGoal.backgroundColor = UIColor(hex: "#0E0F27")
            backgroundimageview.image = UIImage(named: "BG4")
        }
        else if self.valueMode == "light" {
            topHeaderimage.image = UIImage(named: "Arc")
            tableViewGoal.backgroundColor = .white
            backgroundimageview.image = UIImage(named: "BackgroundImage")
        }
        
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        isFromMenu = false
        dismiss(animated: true, completion: nil)
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.delegate?.backrefreshSix(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func buttonAddNewCreationAction(_ sender: Any) {
        let vc = UIStoryboard.init(name: "Main", bundle: Bundle.main).instantiateViewController(withIdentifier: "CreateMenteeGoalVC") as? CreateMenteeGoalVC
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    //MARK:- Api Calling for Loading Goal Table
    func pendingApiCalling(){
        let userDetails:NSMutableDictionary = [
            "type" : "goal",
            "search_text" :""
        ]
        if self.connectedToNetwork() {
            if(isPending == true) {
                self.startActivityIndicator()
                
                ApiManager.sharedInstance.getgGoalList(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        print("PENDING Goal List for Mentee :: \(json)")
                        DispatchQueue.main.async(execute: {() -> Void in
                            let dataTemp = json["data"]! as! NSDictionary
                            print("DATA\(dataTemp)")
                            self.arrGoalList = (dataTemp["datalist"] as? [NSDictionary])!
                            print("arrGoalList:: \(self.arrGoalList)")
                            if(self.arrGoalList.count > 0){
                                self.tableViewGoal.reloadData()
                                self.stopActivityIndicator()
                            }
                            else{
                                self.tableViewGoal.reloadData()
                                self.showAlert(_sourceController: self, _msg: "No  Pending Goals")
                                self.stopActivityIndicator()
                            }
                        })
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                        self.stopActivityIndicator()
                        
                    })
                })
            } else {
                self.startActivityIndicator()
                ApiManager.sharedInstance.getCompleteGoalList(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        print("Get CompleteGoalList ByMentee :: \(json)")
                        DispatchQueue.main.async(execute: {() -> Void in
                            let dataTemp = json["data"]! as! NSDictionary
                            self.arrGoalList.removeAll()
                            self.arrGoalList = dataTemp["datalist"] as! [NSDictionary]
                            if (self.arrGoalList.count > 0) {
                                self.tableViewGoal.reloadData()
                                self.stopActivityIndicator()
                            }
                            else{
                                self.tableViewGoal.reloadData()
                                self.showAlert(_sourceController: self, _msg: "No Goals Completed")
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
        if arrGoalList.count > 0 {
            let assign = self.arrGoalList[index]["assign_id"] as! Int
            vc?.strAssignId = String(assign) ?? ""
            vc?.strGoalName = (arrGoalList[index]["name"] as? String) ?? ""
            vc?.strDescription = (arrGoalList[index]["description"] as? String) ?? ""
            vc?.strStartDate = (arrGoalList[index]["start_date"] as? String) ?? ""
            vc?.strEndDate = (arrGoalList[index]["end_date"] as? String) ?? ""
            vc?.isType = 1
        }
        self.navigationController?.pushViewController(vc!, animated: true)
    }
}

// MARK:: UICollectionViewViewDatasource
extension GoalViewController: UICollectionViewDataSource {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        if (arrGoalStaus.count == 0) {
            return 0
        }
        return arrGoalStaus.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        cell = collectionView.dequeueReusableCell(withReuseIdentifier: "GoalStatusCollectionViewCell", for: indexPath) as! GoalStatusCollectionViewCell
        if arrGoalStaus.count > 0 {
            cell.labelGoalStatusCell.text = arrGoalStaus[indexPath.item]
        }
        if (indexPath.row == selectedIndex) {
            cell.labelGoalStatusCell.textColor = UIColor(red: 167.0/255.0, green: 174.0/255.0, blue: 59.0/255.0, alpha: 1.0)
        } else {
            cell.labelGoalStatusCell.textColor = UIColor.black
        }
        return cell
    }
}

// MARK:: UICollectionViewViewDelegate
extension GoalViewController: UICollectionViewDelegate {
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        if (indexPath.row == 0) {
            selectedIndex = indexPath.row
            arrGoalList.removeAll()
            collectionViewStatusGoal.reloadData()
            isPending = true
            pendingApiCalling()
            tableViewGoal.reloadData()
        } else {
            selectedIndex = indexPath.row
            arrGoalList.removeAll()
            collectionViewStatusGoal.reloadData()
            isPending = false
            pendingApiCalling()
            tableViewGoal.reloadData()
        }
    }
}

extension GoalViewController: UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView,
                        layout collectionViewLayout: UICollectionViewLayout,
                        sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((collectionView.frame.size.width - 30) / 2)
        return CGSize(width: width, height: 36)
    }
}

// MARK:: UITableViewDatasource
extension GoalViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrGoalList.count
        
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "RecommendedGoalTableViewCell") as! RecommendedGoalTableViewCell
        
        if self.valueMode == "dark" {
            cell.contentView.backgroundColor = UIColor(hex: "#0E0F27")
            cell.mainView.backgroundColor = UIColor(hex: "#0E0F27")
            cell.labelHeadCell.textColor = .white
            cell.labelDescriptionCell.textColor = .white
        }
        else if self.valueMode == "light" {
          //  cell.contentView.backgroundColor = .white
            cell.mainView.backgroundColor = .white
            cell.labelHeadCell.textColor = .black
            cell.labelDescriptionCell.textColor = .white
        }
        
        cell.labelHeadCell.text = ""
        cell.labelDescriptionCell.text = ""
        if (arrGoalList.count > 0) {
            let dicGoal = arrGoalList[indexPath.item]
            
            if (isPending == true) {
                cell.imgCompletedGoal.isHidden = true
                cell.buttonCompletedDetails.isHidden = true
                cell.buttonCompletedDetails.isUserInteractionEnabled = false
                
                presentWorkStatus = dicGoal["datastatus"] as? Int
                
                if (presentWorkStatus == 1) {
                    cell.imgInProgress.isHidden = false
                } else {
                    cell.imgInProgress.isHidden = true
                }
                print("presentWorkStatus\(presentWorkStatus)")
            } else {
                cell.imgInProgress.isHidden = true
                cell.imgCompletedGoal.isHidden = false
                cell.buttonCompletedDetails.isHidden = false
                cell.buttonCompletedDetails.isUserInteractionEnabled = true
                cell.buttonCompletedDetails.tag = indexPath.row
                cell.buttonCompletedDetails.addTarget(self, action: #selector(buttonCompletedDetailsTap(_:)), for: .touchUpInside)
            }
            cell.labelHeadCell.text = dicGoal["name"] as? String
            cell.labelDescriptionCell.text = dicGoal["description"] as? String
            
            let strGoalOwner = String(describing: dicGoal["user_type"]!)
            if strGoalOwner == "mentor" {
                cell.imgMenteeOwnerBadge.isHidden = true
            } else {
                cell.imgMenteeOwnerBadge.isHidden = false
            }
        } else {
            
        }
        return cell
    }
}

// MARK:: UITableViewDelegate
extension GoalViewController: UITableViewDelegate {
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        let vc = UIStoryboard.init(name: "Main", bundle: Bundle.main).instantiateViewController(withIdentifier: "PendingDetailsViewController") as? PendingDetailsViewController
        
        if (arrGoalList.count > 0) {
            let dicGoal: NSDictionary = arrGoalList[indexPath.item]
            
            let assign = dicGoal["assign_id"] as! Int
            vc?.strAssignId = String(assign)
            vc?.strGoalName = (dicGoal["name"] as? String ?? "")
            vc?.strDescription = (dicGoal["description"] as? String ?? "")
            vc?.strStartDate = (dicGoal["start_date"] as? String ?? "")
            vc?.strEndDate = (dicGoal["end_date"] as? String ?? "")
            vc?.isType = 1
            vc?.completedTask = "pending"
            vc?.presentedWorkStatusOnDetailsScreen = dicGoal["datastatus"] as? Int
        } //print("Goal.presentedWorkStatusOnDetailsScreen\(vc?.presentedWorkStatusOnDetailsScreen)")
        self.navigationController?.pushViewController(vc!, animated: true)
    }
    
}


