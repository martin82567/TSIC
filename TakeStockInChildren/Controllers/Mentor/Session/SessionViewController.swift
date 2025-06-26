//
//  SessionViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 16/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import EventKit
import Alamofire
protocol backrefreshtwo {
    func refresh(name: String)
}
class SessionViewController: BaseViewController, MentorMenuControllerDelegate {
    let MentorstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    @IBOutlet var safearacolor: UIView!
    @IBOutlet weak var backgroundImage: UIImageView!
    @IBOutlet weak var tableVSessionList: UITableView!
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var lblForHeaderTitle: UILabel!
    @IBOutlet weak var imgVHeader: UIImageView!
    @IBOutlet weak var btnBackOutLet: UIButton!
    @IBOutlet weak var lblForNoMettigFound: UILabel!
    @IBOutlet weak var constraintHeightTabBar: NSLayoutConstraint!
    @IBOutlet weak var stackVOfTabBar: UIStackView!
    @IBOutlet weak var btnAddForSession_or_Meeting: UIButton!
    @IBOutlet weak var btnUpcomingOutlet: UIButton!
    @IBOutlet weak var btnRequestOutlet: UIButton!
    @IBOutlet weak var btnPastOutlet: UIButton!
    
    var arrMyJournalListing : NSMutableArray = []
    var iAmFrom = "meeting"
    var iAmFromProfile: Bool = false
    let upcoming = "upcoming"
    let past = "past"
    let request = "request"
    
    var identifyButtonClicked = ""
    
    var strMeetingID = ""
    var canceldateTime = ""
    var valueMode : String?
    
    let eventStore = EKEventStore()
    
    var delegate: backrefreshtwo!
    
    
    var fromtag: String?
    
    func hideTopBarButton(){
        stackVOfTabBar.isHidden = true
        constraintHeightTabBar.constant = 0
    }
    
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
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechange()
      //  NotificationCenter.default.addObserver(self, selector: #selector(chnageColor), name: .backgroundColor, object: nil)
      //  identifyButtonClicked = upcoming
      //  btnRequestOutlet.titleLabel?.textAlignment = .center
       // btnRequestOutlet.titleLabel?.numberOfLines = 0
       // btnUpcomingOutlet.titleLabel?.textAlignment = .center
       // btnUpcomingOutlet.titleLabel?.numberOfLines = 0
       // btnPastOutlet.titleLabel?.textAlignment = .center
      //  btnPastOutlet.titleLabel?.numberOfLines = 0
        
        /*
        if iAmFrom == "meeting" {
            requestedDataReload()
           // loadMettingData()
        } else {
            loadSessionData()
        }
        */
        if iAmFrom == "meeting" {
            serviceCallTogetUpcommingMettings()
        }
    }
    
    /*
    func loadSessionData() {
    //    hideTopBarButton()
        self.tableVSessionList.estimatedRowHeight = 226
        self.btnBackOutLet.isHidden = false
        self.btnBackOutLet.isEnabled = true
        self.btnBackOutLet.setImage(UIImage(named: "Back"), for: UIControl.State.normal)
        self.btnBackOutLet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
        serviceCallToGetSessionListing()
        self.lblForHeaderTitle.text = "SESSION LOGS"
        
        self.imgVHeader.image = UIImage(named: "Session BGdark")
        /*
        if self.valueMode == "dark" {
            self.imgVHeader.image = UIImage(named: "Session BGdark-1")
        }
        else if self.valueMode == "light" {
            self.imgVHeader.image = UIImage(named: "Session BG")
        }
        */
    }
    */
    
    //MARK:- SideMenu
    func showHideMentorMenuController(_ isShown: Bool) {
        btnBackOutLet.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowMentorSideMenu() {
        
        if iAmFrom=="session" {
            self.navigationController?.popViewController(animated: true)
        } else {
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
      }
    }
    
    func loadMettingData() {
        self.tableVSessionList.estimatedRowHeight = 309
        
        self.btnBackOutLet.isHidden = false
        self.btnBackOutLet.isEnabled = true
        if iAmFromProfile {
            self.btnBackOutLet.setImage(UIImage(named: "Back"), for: UIControl.State.normal)
            self.btnBackOutLet.addTarget(self, action: #selector(btnBackAction(_:)), for: UIControl.Event.touchUpInside)
        } else {
            self.btnBackOutLet.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
            self.btnBackOutLet.addTarget(self, action: #selector(messageShowMentorSideMenu), for: UIControl.Event.touchUpInside)
        }
        
        serviceCallTogetUpcommingMettings()
        self.lblForHeaderTitle.text = "SESSIONS"
        
        self.imgVHeader.image = UIImage(named: "Meeting BGdark")
        
        /*
        if self.valueMode == "dark" {
            self.imgVHeader.image = UIImage(named: "Meetingbg")
        }
        else if self.valueMode == "light" {
             self.imgVHeader.image = UIImage(named: "Meeting BG")
        }
        */
        
         //Meeting BGdark //Meeting BG
        
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.delegate?.refresh(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    
    @IBAction func btnAddSession(_ sender: Any) {
        if iAmFrom == "meeting" {
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
                newViewController.checkIamFrom = "createMeeting"
                newViewController.assignValueAfterPOP = {() -> Void in
                    self.serviceCallTogetUpcommingMettings()
                }
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
        } else {
            self.UI {
                let newViewController = self.MentorstoryBoard.instantiateViewController(withIdentifier: "MentorSessionCreatorVC") as! MentorSessionCreatorVC
                newViewController.assignValueAfterPOP = {() -> Void in
                    self.serviceCallToGetSessionListing()
                }
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
        }
    }
    
    func upcommingDataReload() {
        btnUpcomingOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        plusButtonHide()
       // plusButtonUnHide()
        identifyButtonClicked = self.upcoming
        loadMettingData()
    }
    
    func requestedDataReload() {
        btnUpcomingOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.darkGray, for: .normal)
       // plusButtonHide()
        plusButtonUnHide()
        identifyButtonClicked = self.request
        loadMettingData()
    }
    
    func pastDataReload() {
        btnUpcomingOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        
        plusButtonHide()
        identifyButtonClicked = self.past
        loadMettingData()
    }
    
    @IBAction func btnUpCommingAction(_ sender: Any) {
        upcommingDataReload()
    }
    
    @IBAction func btnRequestedAction(_ sender: Any) {
        requestedDataReload()
    }
    
    @IBAction func btnUpPastAction(_ sender: Any) {
        pastDataReload()
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- API Service Call
    func serviceCallForDeclineMeeting() {
        var parameter = [String:String]()
        
        parameter["meeting_id"] = strMeetingID
        
        MentorApiManager().no_reschedule_meeting(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                         self.requestedDataReload()
                    })
                }
            } else {
                self.UI{
                    self.logOutMentor()
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                }
            }
        })
    }
    
    func serviceCallForCancelMeeting() {
        var parameter = [String:String]()
        parameter["meeting_id"] = strMeetingID
        
        MentorApiManager().meetingCancel(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.requestedDataReload()
                    })
                }
            } else {
                self.UI{
                    self.logOutMentor()
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                }
            }
        })
    }
    
    func serviceCallTogetUpcommingMettings() {
        var parameter = [String:String]()
        if self.fromtag == upcoming {
            parameter["type"] = "upcoming"
        } else if self.fromtag == request {
            parameter["type"] = "requested"
        } else if self.fromtag == past {
            parameter["type"] = "past"
        }
        
        MentorApiManager().meetingListing(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                let arrData = response["data"] as! NSArray
                
                self.arrMyJournalListing.removeAllObjects()
                if arrData.count == 0 {
                    self.UI {
                        if self.identifyButtonClicked == self.upcoming {
                            self.lblForNoMettigFound.text = "No Confirmed Sessions Found"
                        } else if self.identifyButtonClicked == self.request {
                            self.lblForNoMettigFound.text = "Not Any Requested Sessions"
                        } else if self.identifyButtonClicked == self.past {
                            self.lblForNoMettigFound.text = "No Past Session"
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
    
    func serviceCallToGetSessionListing() {
        MentorApiManager().sessionListing(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                let arrData = response["data"] as! NSArray
                print("arr",arrData)
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
    
    @objc func btnEdit(sender:UIButton) {
        let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
        
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
        
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
             newViewController.checkIamFrom = "editMeeting"
            newViewController.assignValueAfterPOP = {() -> Void in
                self.serviceCallTogetUpcommingMettings()
            }
            newViewController.arrMyMenteeListForEdit = objForEdit
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
    
    @objc func rescheduleAction(sender : UIButton) {
        let btnTextOutlet = sender.currentTitle
        
        if btnTextOutlet == "EDIT" {
            self.btnEdit(sender: sender)
        } else {
            let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
            
            
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
                newViewController.assignValueAfterPOP = {() -> Void in
                    self.requestedDataReload()
                }
                newViewController.arrMyMenteeListForEdit = objForEdit
                self.navigationController?.pushViewController(newViewController, animated: true)
            }
        }
    }
    
    
    @objc func videochat(sender : UIButton) {
        
        
        let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
        
        let senderid = objForEdit["created_by"] as? Int
        print("senderid",senderid ?? "")
        
        var receiverId: String = ""
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("login mode",loginMode)
        var sendertype: String = ""
        var types: String = ""
        if loginMode == "Mentor"{
            sendertype = "mentor"
            types = "mentee"
        }
        else if loginMode == "Mentee" {
            sendertype = "mentee"
            types = "mentor"
        }
        print("types",types)
        
        
        
        let arrOfMenteeList = objForEdit["mentees"] as? NSArray
        
        if let menteesList = arrOfMenteeList {
            if menteesList.count == 1 {
                let dicMenteeList = menteesList[0] as! NSDictionary
                receiverId = String(dicMenteeList["id"] as? Int ?? 0)
            } else {
                for mentee in menteesList {
                    let dicMenteeList = mentee as! NSDictionary
                    receiverId = String(dicMenteeList["id"] as? Int ?? 0)
                }
            }
        }
        print("recieevrid",receiverId)
        
        
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        newViewController.senderId = String(senderid ?? 0)
        newViewController.senderType = sendertype
      newViewController.receiverId = receiverId
       newViewController.receiverType = types
        newViewController.fromWhereTag = "viaChat"
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    
    
    
    
    //MARK:--> Read Events
    
    
    func readEvents(_ givendate: Date) {
        let calendars = eventStore.calendars(for: .event)
        for calendar in calendars {
            //if calendar.source.title == "Local" {
                let oneMonthAgo = NSDate(timeIntervalSinceNow: -30*24*3600)
                let oneMonthAfter = NSDate(timeIntervalSinceNow: +30*24*3600)
            
                let predicate = eventStore.predicateForEvents(withStart: oneMonthAgo as Date, end: oneMonthAfter as Date, calendars: [calendar])
                let events = eventStore.events(matching: predicate)
                for event in events {
                    print("Insdie loop",givendate,event.startDate ?? "")
                    if event.startDate == givendate {
                        let identifier = event.eventIdentifier
                        print("indentity",identifier ?? "")
                        let eventToRemove = eventStore.event(withIdentifier: identifier ?? "")
                        if eventToRemove != nil {
                            do {
                                try eventStore.remove(eventToRemove!, span: .thisEvent, commit: true)
                                print("Delete Event")
                            } catch {
                                print("Error Delete",error.localizedDescription)
                                // Display error to user
                            }
                        }
                        
                        
                        
                    }


                }

            //}
        }


    }
    
    @objc func cancelAction(sender : UIButton) {
        let objForCancel = arrMyJournalListing[sender.tag] as! NSDictionary
        let cancelDate = objForCancel["date"] as? String ?? ""
        let canceltime = objForCancel["time"] as? String ?? ""
        canceldateTime = "\(cancelDate)\(" ")\(canceltime)"
        strMeetingID =  "\(objForCancel["id"] as! NSNumber)"
        self.readEvents(canceldateTime.toDate())
        self.serviceCallForCancelMeeting()
        print("cancel")
    }
    
    @objc func denyAction(sender : UIButton) {
        let objForCancel = arrMyJournalListing[sender.tag] as! NSDictionary
        strMeetingID =  "\(objForCancel["id"] as! NSNumber)"
        serviceCallForDeclineMeeting()
        print("deny")
    }
}

extension SessionViewController : UITableViewDataSource , UITableViewDelegate {
    
    //MARK: TableViewDataSource , TableViewDelegate
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrMyJournalListing.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if iAmFrom == "meeting" {
            if self.fromtag == self.upcoming {
                //TODO:: Already Scheduled Mentor Sessions/ Confirmed Mentor Sessions
                
                let cellIdentifier:String = "MeetingListingCell"
                var cell2:MeetingListingCell? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? MeetingListingCell
                if (cell2 == nil) {
                    var nib:Array = Bundle.main.loadNibNamed("MeetingListingCell", owner: self, options: nil)!
                    cell2 = nib[0] as? MeetingListingCell
                }
                                
                cell2?.videoBtn.addTarget(self, action: #selector(videochat(sender:)), for: .touchUpInside)
                cell2?.videoBtn.tag = indexPath.row
                
                
                
                if arrMyJournalListing.count > 0 {
                    let dataDic = arrMyJournalListing[indexPath.row] as! NSDictionary
                    cell2?.loadData(dicData: dataDic)
                    
                    let boolToCheckMentorCreated = dataDic["is_mentor_created"] as! Bool
                    
                    if boolToCheckMentorCreated == true {
                        cell2?.stackButton.isHidden = false
                    } else {
                        cell2?.stackButton.isHidden = true
                    }
                }
                cell2?.selectionStyle = .none
                return cell2!
            } else if self.fromtag == self.request {
                //TODO:: Scheduling Mentor Sessions
                let cellIdentifier:String = "MentorTblCell2"
                var cell2:MentorTblCell2? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? MentorTblCell2
                if (cell2 == nil) {
                    var nib:Array = Bundle.main.loadNibNamed("MentorTblCell2", owner: self, options: nil)!
                    cell2 = nib[0] as? MentorTblCell2
                }
                
                cell2?.btnRescheduleOutlet.addTarget(self, action: #selector(rescheduleAction(sender:)), for: .touchUpInside)
                cell2?.btnRescheduleOutlet.tag = indexPath.row
                
                cell2?.btnCancelOutlet.addTarget(self, action: #selector(cancelAction(sender:)), for: .touchUpInside)
                cell2?.btnCancelOutlet.tag = indexPath.row
                
                cell2?.btnDenyOutlet.addTarget(self, action: #selector(denyAction(sender:)), for: .touchUpInside)
                cell2?.btnDenyOutlet.tag = indexPath.row
                if arrMyJournalListing.count > 0 {
                    let dataDic = arrMyJournalListing[indexPath.row] as! NSDictionary
                    
                    cell2?.loadDataRequested(dicData: dataDic)
                }
                cell2?.selectionStyle = .none
                return cell2!
            } else {
                //TODO:: Completed Mentor Sessions
                let cellIdentifier:String = "MentorTblCell2"
                var cell2:MentorTblCell2? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? MentorTblCell2
                if (cell2 == nil) {
                    var nib:Array = Bundle.main.loadNibNamed("MentorTblCell2", owner: self, options: nil)!
                    cell2 = nib[0] as? MentorTblCell2
                }
                
                if arrMyJournalListing.count > 0 {
                    let dataDic = arrMyJournalListing[indexPath.row] as! NSDictionary
                    
                    cell2?.loadDataPast(dicData: dataDic)
                }
                cell2?.selectionStyle = .none
                return cell2!
            }
        } else {
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
    }
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        if iAmFrom == "meeting" {
            return UITableView.automaticDimension
        } else {
           return UITableView.automaticDimension
        }
    }
    
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if iAmFrom == "meeting" {
            if self.fromtag == self.upcoming {
                print("not select")
            }
            else if self.fromtag == self.request {
                print("not select")
            }
            else {
                
                let obj = arrMyJournalListing[indexPath.row]
                
                let resultNew = obj as? [String:Any]

                let islooged = resultNew?["is_logged"] as? Int
                if islooged == 1 {
                    print("Not Select")
                }
                else {
                    
                    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
                    let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "ShowSessionViewController") as! ShowSessionViewController
                    newViewController.obj = obj as? NSDictionary
                    self.navigationController?.pushViewController(newViewController, animated: true)
                    
                }
                
            }
        }
    }
    
}

extension UIColor {
    static var greenColourForApp = UIColor.init(red: 160/255, green: 166/255, blue: 40/255, alpha: 1)
}

