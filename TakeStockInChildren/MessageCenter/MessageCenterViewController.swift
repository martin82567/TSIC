//
//  MessageCenterViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 5/7/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

protocol msgCenterrefresh {
    func backref2(name: String)
}
class MessageCenterViewController: BaseViewController {

    @IBOutlet weak var tableview: UITableView!
    var type: String?
    var tempMessagedata = [[String:Any]]()
    var Messages = [[String:Any]]()
    var totalValue = Int()
    var take : Int = 0
    var pageList : Int = 0
    var isLoadMoreList : Bool = false
    var delegate: msgCenterrefresh?
    override func viewDidLoad() {
        super.viewDidLoad()
        tableview.separatorStyle = .none
        
    
        if type == "mentor" {
            getMessageCneetrApiMentor()
        }
        else {
            getMessageCneetrApiMnetee()
        }
 4    //  alerthshow()
        // Do any additional setup after loading the view.
    }
    
    func alerthshow() {
        let alert = UIAlertController(title: "TSIC", message: "Announcements Coming Soon!!", preferredStyle: .alert)
        alert.addAction(UIAlertAction(title: "OK", style: .default, handler: { (UIAlertAction) in
            self.navigationController?.popViewController(animated: true)
        }))
        present(alert, animated: true)

    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        self.isLoadMoreList = false
    }
    
    @IBAction func btnback(_ sender: Any) {
        self.delegate?.backref2(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    func getMessageCneetrApiMnetee() {
        var parameter = [String: String]()
        parameter["page"] = "\(self.take)"
        parameter["take"] = "\(self.pageList)"
        self.startActivityIndicator()
        MentorApiManager().messagecntermentee(parameter: parameter) { (json) in
                self.stopActivityIndicator()
                print("access json-------",json)
                let status = json["status"] as! Bool
                if status == true {
                    let dicData = json["data"] as! NSDictionary
                    self.totalValue = dicData["count_messages"] as? Int ?? 0
                    DispatchQueue.main.async {
                        if self.isLoadMoreList {
                            self.tempMessagedata = dicData["messages"] as? [[String : Any]] ?? []
                            
                            for item in self.tempMessagedata {
                                self.Messages.append(item)
                            }
                        }
                        else {
                            self.Messages = dicData["messages"] as? [[String : Any]] ?? []
                        }
                       
                        if self.Messages.count > 0 {
                            self.isLoadMoreList = false
                            self.tableview.reloadData()
                        }
                    }
                } else {
                    print("error")
                    self.logOutMentee()
                }
            }
    }
    
    func getMessageCneetrApiMentor() {
        var parameter = [String: String]()
        parameter["page"] = "\(self.take)"
        parameter["take"] = "\(self.pageList)"
        self.startActivityIndicator()
        MentorApiManager().messagecntermentor(parameter: parameter) { (json) in
                print("access json-------",json)
            self.stopActivityIndicator()
                let status = json["status"] as! Bool
            if status == true {
                let dicData = json["data"] as! NSDictionary
                self.totalValue = dicData["count_messages"] as? Int ?? 0
                DispatchQueue.main.async {
                    if self.isLoadMoreList {
                        self.tempMessagedata = dicData["messages"] as? [[String : Any]] ?? []
                        for item in self.tempMessagedata {
                            self.Messages.append(item)
                        }
                    }
                    else {
                        self.Messages = dicData["messages"] as? [[String : Any]] ?? []
                    }
                    if self.Messages.count > 0 {
                        self.isLoadMoreList = false
                        self.tableview.reloadData()
                    }
                }
            }
                else {
                    self.logOutMentor()
                }
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


}

extension MessageCenterViewController: UITableViewDelegate,UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        self.Messages.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
            let cell = tableView.dequeueReusableCell(withIdentifier: "MessageCenterTableViewCell") as! MessageCenterTableViewCell
            let dict = self.Messages[indexPath.row]
            print("arraysidemenu Item",self.Messages[indexPath.row])
        
            cell.messageLbl.text = dict["message"] as? String
            cell.sDateLabel.text = dict["created_at"] as? String
            
            let status = dict["created_by"] as? Int
            if status == 1 {
                cell.edateLabel.text = "System Admin"
            }
            else {
                cell.edateLabel.text = "Affiliate Office"
            }
            
            return cell
    }
}



extension MessageCenterViewController: UIScrollViewDelegate {
    
    // MARK:- UIScrollViewDelegate
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        if scrollView == self.tableview {
            // calculates where the user is in the y-axis
            let offsetY = scrollView.contentOffset.y
            let contentHeight = scrollView.contentSize.height
            if offsetY > 0 && (offsetY > (contentHeight - scrollView.frame.size.height)) {
                if self.Messages.count > 0 && self.totalValue > self.Messages.count
                    && !self.isLoadMoreList {
                    self.pageList += 1
                    self.isLoadMoreList = true
                    if type == "mentor" {
                        self.getMessageCneetrApiMentor()
                    }
                    else {
                        self.getMessageCneetrApiMnetee()
                    }
                    
                }
                else
                {
                    print("No tab")
                }
            }
        }
    }
}
