//
//  NotesSessionVC.swift
//  TakeStockInChildren
//
//  Created by Aquarious  on 19/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class NotesSessionVC: BaseViewController, MentorMenuControllerDelegate {
    let MentorstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
    @IBOutlet weak var headerimage: UIImageView!
    @IBOutlet var safeareaView: UIView!
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    @IBOutlet weak var backgroundimage: UIImageView!
    @IBOutlet weak var mainView: UIView!
    
    @IBOutlet weak var tableVSessionList: UITableView!
    @IBOutlet weak var lblForHeaderTitle: UILabel!
    @IBOutlet weak var btnBackOutLet: UIButton!
    @IBOutlet weak var lblForNoMettigFound: UILabel!
    @IBOutlet weak var constraintHeightTabBar: NSLayoutConstraint!
    @IBOutlet weak var btnAddForSession_or_Meeting: UIButton!
    
    @IBOutlet weak var viewmain: UIView!
    
    var arrMyJournalListing : NSMutableArray = []
    let upcoming = "upcoming"
    let past = "past"
    let request = "request"
    var valueMode : String?
    
    var identifyButtonClicked = ""
    
    var strMeetingID = ""
    
    
    func plusButtonHide() {
        btnAddForSession_or_Meeting.isHidden = true
        btnAddForSession_or_Meeting.isEnabled = false
    }
    
    func plusButtonUnHide() {
        btnAddForSession_or_Meeting.isHidden = false
        btnAddForSession_or_Meeting.isEnabled = true
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
      //  self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
     //   darkmodechnaged()
      //  NotificationCenter.default.addObserver(self, selector: #selector(colorChange), name: .backgroundColor, object: nil)
        identifyButtonClicked = upcoming
        
        loadSessionData()
    }
    
    func darkmodechnaged() {
        if self.valueMode == "dark" {
            
            safeareaView.backgroundColor = UIColor(hexString: "#0E0F27")
            backgroundimage.image = UIImage(named: "BG4")
            mainView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableVSessionList.backgroundColor = UIColor(hexString: "#0E0F27")
            viewmain.backgroundColor = UIColor(hexString: "#0E0F27")
        }
        else if self.valueMode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            mainView.backgroundColor = .white
            tableVSessionList.backgroundColor = .white
            viewmain.backgroundColor = .white
        }
    }
    
    @objc func colorChange() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            
            safeareaView.backgroundColor = .black
            backgroundimage.image = UIImage(named: "BG4")
            mainView.backgroundColor = UIColor(hexString: "#0E0F27")
            tableVSessionList.backgroundColor = UIColor(hexString: "#0E0F27")
            viewmain.backgroundColor = UIColor(hexString: "#0E0F27")
            
            self.headerimage.image = UIImage(named: "Meetingbg")
        }
        else if mode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            mainView.backgroundColor = .white
            tableVSessionList.backgroundColor = .white
            viewmain.backgroundColor = .white
            
            
            self.headerimage.image = UIImage(named: "Meeting BG")
        }
        tableVSessionList.reloadData()
    }
    
    func loadSessionData() {
        self.tableVSessionList.estimatedRowHeight = 226
        self.btnBackOutLet.isHidden = false
        self.btnBackOutLet.isEnabled = true
        self.btnBackOutLet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
        self.btnBackOutLet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
        serviceCallToGetSessionListing()
        self.lblForHeaderTitle.text = "SESSION LOGS"
        self.headerimage.image = UIImage(named: "Meeting BGdark")
        /*
        if self.valueMode == "dark" {
            self.headerimage.image = UIImage(named: "Meetingbg")
        }
        else if self.valueMode == "light" {
            self.headerimage.image = UIImage(named: "Meeting BG")
        }
        */
    }
    
    //MARK:- SideMenu
    func showHideMentorMenuController(_ isShown: Bool) {
        btnBackOutLet.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowMentorSideMenu() {
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
    }
    
    func loadMeetingSessionData() {
        self.tableVSessionList.estimatedRowHeight = 309
        
        self.btnBackOutLet.isHidden = false
        self.btnBackOutLet.isEnabled = true
        
        self.btnBackOutLet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
        self.btnBackOutLet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
        
        serviceCallTogetUpcommingMettings()
        self.lblForHeaderTitle.text = "SESSIONS LOGS"
        self.headerimage.image = UIImage(named: "Meeting BGdark")
        /*
        if self.valueMode == "dark" {
            self.headerimage.image = UIImage(named: "Meetingbg")
        }
        else if self.valueMode == "light" {
            self.headerimage.image = UIImage(named: "Meeting BG")
        }
        */
        
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    
    @IBAction func btnAddSession(_ sender: Any) {
        self.UI {
            let newViewController = self.MentorstoryBoard.instantiateViewController(withIdentifier: "MentorSessionCreatorVC") as! MentorSessionCreatorVC
            newViewController.assignValueAfterPOP = {() -> Void in
                self.serviceCallToGetSessionListing()
            }
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- API Service Call
    func serviceCallTogetUpcommingMettings() {
        var parameter = [String:String]()
        
        parameter["type"] = "upcoming"
        
        MentorApiManager().meetingListing(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                let arrData = response["data"] as! NSArray
                
                self.arrMyJournalListing.removeAllObjects()
                if arrData.count == 0 {
                    self.UI {
                        if self.identifyButtonClicked == self.upcoming {
                            self.lblForNoMettigFound.text = "No Meeting Found"
                        } else if self.identifyButtonClicked == self.request {
                            self.lblForNoMettigFound.text = "Not Any Requested Meetings"
                        } else if self.identifyButtonClicked == self.past {
                            self.lblForNoMettigFound.text = "No Past Meeting"
                        }
                   
                        self.lblForNoMettigFound.isHidden = false
                        self.tableVSessionList.isHidden = true
                    }
                } else {
                    self.arrMyJournalListing = arrData.mutableCopy() as! NSMutableArray
                    self.UI {
                        self.lblForNoMettigFound.isHidden = true
                        
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
                    self.logOutMentor()
                }
            }
        })
    }
    
    func serviceCallToGetSessionListing() {
        MentorApiManager().sessionListing(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                let arrData = response["data"] as! NSArray
                
                self.arrMyJournalListing.removeAllObjects()
                if arrData.count == 0 {
                    self.UI {
                        self.lblForNoMettigFound.text = "No Session Found"
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
                    self.logOutMentor()
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

extension NotesSessionVC : UITableViewDataSource , UITableViewDelegate {
    //MARK: TableViewDataSource , TableViewDelegate
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMyJournalListing.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        // Session Listing
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
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return UITableView.automaticDimension
    }
}


