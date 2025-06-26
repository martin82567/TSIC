//
//  SessionLogListViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/29/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
protocol backrefreshSessionlog {
    func backre(name: String)
}
class SessionLogListViewController: BaseViewController{

    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    let MentorstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
    var arrMyJournalListing : NSMutableArray = []
    var delegate: backrefreshSessionlog!
    @IBOutlet weak var tableVSessionList: UITableView!
    override func viewDidLoad() {
        super.viewDidLoad()
        self.serviceCallToGetSessionListing()
    }
    
    @IBAction func add(_ sender: Any) {
        
        self.UI {
            let newViewController = self.MentorstoryBoard.instantiateViewController(withIdentifier: "MentorSessionCreatorVC") as! MentorSessionCreatorVC
            newViewController.assignValueAfterPOP = {() -> Void in
                self.serviceCallToGetSessionListing()
            }
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
        
    }
    
    @IBAction func backAction(_ sender: Any) {
        self.delegate?.backre(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    func serviceCallToGetSessionListing() {
        self.startActivityIndicator  ()
        MentorApiManager().sessionListing(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                self.stopActivityIndicator()
                let arrData = response["data"] as! NSArray
                print("arrDataInserviceCallToGetSessionListing",arrData)
                self.arrMyJournalListing.removeAllObjects()
                if arrData.count == 0 {
                    self.UI {
                        //self.lblForNoMettigFound.text = "No Session Found"
                        self.tableVSessionList.isHidden = true
                    }
                } else {
                    self.arrMyJournalListing = arrData.mutableCopy() as! NSMutableArray
                    self.UI {
                        self.tableVSessionList.isHidden = false
                        self.tableVSessionList.reloadData()
                    }
                }
            } else {
                self.UI{
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    //self.logOutMentor()
                }
            }
        })
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
    
    
    
    

}


//MARK: TableViewDataSource , TableViewDelegate
extension SessionLogListViewController : UITableViewDataSource , UITableViewDelegate {
    
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMyJournalListing.count
    }
    
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let cellIdentifier:String = "SessionListingCell2"
        var cell:SessionListingCell2? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? SessionListingCell2
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("SessionListingCell2", owner: self, options: nil)!
            cell = nib[0] as? SessionListingCell2
        }
        if arrMyJournalListing.count > 0 {
            let dataDic = arrMyJournalListing[indexPath.row] as! NSDictionary
            print(dataDic)
            cell?.loadDataSessionListing(dicData: dataDic)
        }
        cell?.selectionStyle = .none
        return cell!
        
    }


}
