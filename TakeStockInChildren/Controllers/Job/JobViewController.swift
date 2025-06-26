//
//  JobViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 18/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class JobViewController: BaseViewController {
    
    @IBOutlet weak var tableViewJob: UITableView!
    
    let appDelegate = AppDelegate()
    var tapGesture = UITapGestureRecognizer()
    
    var arrJob = [NSDictionary]()
    var arrMyJob = [NSDictionary]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        getJobList()
        // tableViewCompletedDetails.setContentOffset(.zero, animated:true)
    }
    
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:: Function Api Calling
    func getJobList () {
        //guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            let searchDetails:NSString = ""
            
            print(searchDetails)
            ApiManager.sharedInstance.getJobList(SearchText: searchDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    
                    print("JobList json :: \(String(describing: json))")
                    //self.dataView?.text = String(describing: json)
                    DispatchQueue.main.async(execute: {() -> Void in
                        //let dataTemp = json["data"]! as! NSDictionary
                        self.arrJob = json["datalist"] as! [NSDictionary]
                        print("JobList json :: \(self.arrJob)")
                        
                        if(self.arrJob.count > 0){
                            self.tableViewJob.reloadData()
                            self.stopActivityIndicator()
                        }else{
                            self.stopActivityIndicator()
                        }
                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                    self.logOutMentee()
                })
            })
        } else {
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
                            self.appDelegate.window?.rootViewController = nav
                            
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
    
}

// MARK:: UITableViewDatasource
extension JobViewController: UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrJob.count
        
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "JobTableViewCell") as! JobTableViewCell
        if arrJob.count > 0 {
            let dicJob: NSDictionary = arrJob[indexPath.item]
            cell.labelJobTitle.text = dicJob["job_title"] as? String
            var htmlText = (dicJob["summary"] as? String)
            cell.labelJobDescription.text = htmlText?.htmlToString
            cell.buttonApplyJobNow.tag = indexPath.row
            cell.buttonApplyJobNow.addTarget(self, action: #selector(buttonSelected), for: .touchUpInside)
        }
        return cell
    }
    
    @objc func buttonSelected(sender: UIButton) {
        print(sender.tag)
        let btnApply = sender as! UIButton
        let index = Int(btnApply.tag)
        print("Clicked Index : \(String(describing: btnApply.tag))")
        
        if (sender as AnyObject).tag == index {
            if let url = URL(string: (arrJob[index]["application_url"])! as! String) {
                UIApplication.shared.open(url, options: [:])
                //UIApplication.shared.canOpenURL(url as URL)
            }
        }
    }
}

// MARK:: UITextFieldDelegate
extension JobViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
    }
    
}


