//
//  JournalsListingViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class JournalsListingViewController: BaseViewController {
    
    @IBOutlet weak var tableViewJournalsListing: UITableView!
    
    var arrMyJournalListing = [NSDictionary]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewJournalsListing.setContentOffset(.zero, animated:true)
        getMyJournalListing()
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- API Service Call
    func getMyJournalListing() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager().myJournalList(onSuccess: { (response) in
                let arrData = response["data"] as! NSArray
                print("arrjournals\(arrData)")
                self.arrMyJournalListing.removeAll()
                if arrData.count == 0 {
                    
                } else {
                    self.arrMyJournalListing = (arrData.mutableCopy() as! NSMutableArray) as! [NSDictionary]
                    print("arrMyJournalListing\(self.arrMyJournalListing.count)")
                    self.UI {
                        if(self.arrMyJournalListing.count > 0){
                        self.tableViewJournalsListing.reloadData()
                        self.stopActivityIndicator()
                        }else{
                        self.stopActivityIndicator()
                        }
                    }
                }
                
            }, onFailure: { (error) in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.logOutMentee()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                })
            })
        } else {
            self.showAlert(_sourceController: self,_msg: "Unable to connect")
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
    
    //MARK: ButtonBackAction
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:-AddJournalAction
    @IBAction func buttonAddJournalsAction(_ sender: Any) {
        let journalsVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "JournalsViewController")
        self.navigationController?.pushViewController(journalsVc, animated: true)
    }
}

// MARK:: UITableViewDatasource
extension JournalsListingViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMyJournalListing.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "JournalListingTableViewCell") as! JournalListingTableViewCell
        if arrMyJournalListing.count > 0 {
            cell.journalsTitle.text = arrMyJournalListing[indexPath.item]["title"] as? String ?? ""
            cell.journalsDescription.text = arrMyJournalListing[indexPath.row]["description"] as?  String ?? ""
            let responseDateTiming = arrMyJournalListing[indexPath.row]["updated_at"] as? String
            if let dateTiming = responseDateTiming {
                let dateComponents = dateTiming.components(separatedBy: " ")
                let splitDate = dateComponents[0]
                let splitTime = dateComponents[1]
                cell.journalsCreatedDate.text = splitDate
                cell.journalsCreatedTime.text = splitTime
            }
        }
        return cell
    }
}



