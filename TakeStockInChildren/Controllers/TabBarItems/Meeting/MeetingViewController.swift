//
//  MeetingViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 25/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import EventKit
import Alamofire
protocol backRefresh {
    func backAction(name: String)
}
class MeetingViewController: BaseViewController, MenuControllerDelegate, UITextViewDelegate,Aftercencelmeeting {

    @IBOutlet var safearacolor: UIView!
    @IBOutlet weak var tableVRequestedMeetingLising: UITableView!
    @IBOutlet weak var lblInsideTblevToShowThatNoAnyMeeings: UILabel!
    
    @IBOutlet weak var btnUpcomingOutlet: UIButton!
    @IBOutlet weak var btnRequestOutlet: UIButton!
    @IBOutlet weak var btnPastOutlet: UIButton!
    @IBOutlet weak var viewPopUp: UIView!
    @IBOutlet weak var txtVRescheduleNote: UITextView!
    
    @IBOutlet weak var buttonMenu: UIButton!
    
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    
    let upcoming = "upcoming"
    let past = "past"
    let request = "request"
    
    @IBOutlet weak var mainvew: UIView!
    @IBOutlet weak var backgroundimage: UIImageView!
    @IBOutlet weak var Headerimage: UIImageView!
    var identifyButtonClicked = ""

    @IBOutlet weak var topheaderimage: UIImageView!
    var arrAssignMeetingLising : NSMutableArray = []

    var idUseForMeetingReschedule = ""
    var sessionServerDateandTime = ""
    var describeText = ""
    var titleSet = ""
    var eventIdentifier = ""
    var fromMenu:Bool = false
    var valuemode: String?
    
    let eventStore = EKEventStore()
    var delegate : backRefresh!
    
    var fortag: String?
    
    var fromtag: String?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
     //   self.valuemode = UserDefaults.standard.value(forKey: "mode") as? String
     //   darkmodechanged()
     //   NotificationCenter.default.addObserver(self, selector: #selector(colorChanged), name: .menteebackgroundColor, object: nil)
        
        switch EKEventStore.authorizationStatus(for: .event) {
        case .authorized:
            print("aithorized")
          //  insertEvent(store: eventStore)
            case .denied:
                print("Access denied")
            case .notDetermined:
            // 3
                eventStore.requestAccess(to: .event, completion:
                  {[weak self] (granted: Bool, error: Error?) -> Void in
                      if granted {
                        print("granted")
                     //   self!.insertEvent(store: eventStore)
                      } else {
                            print("Access denied")
                      }
                })
                default:
                    print("Case default")
        }
        
        
        
        /*
        identifyButtonClicked = request
        btnRequestOutlet.titleLabel?.textAlignment = .center
        btnRequestOutlet.titleLabel?.numberOfLines = 0
        btnUpcomingOutlet.titleLabel?.textAlignment = .center
        btnUpcomingOutlet.titleLabel?.numberOfLines = 0
        btnPastOutlet.titleLabel?.textAlignment = .center
        btnPastOutlet.titleLabel?.numberOfLines = 0
        */
        
        if fromtag == "assignrequested" {
            serviceCallToGetAssignMeetingListingForMentee()
        }
        else if fromtag == "assignupcoming" {
            serviceCallToGetAssignMeetingListingForMenteeUpcomming()
        }
       
    }
    
    @objc func colorChanged() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
                   Headerimage.image = UIImage(named: "Meetingbg")
                   backgroundimage.image = UIImage(named: "BG4")
                   mainvew.backgroundColor = UIColor(hex: "#0E0F27")
                   safearacolor.backgroundColor = UIColor.black
               }
               else if mode == "light" {
                   Headerimage.image = UIImage(named: "Meeting BG")
                   backgroundimage.image = UIImage(named: "BackgroundImage")
                   mainvew.backgroundColor = .white
                   safearacolor.backgroundColor = UIColor(hex: "#A0A628")
               }
        
        tableVRequestedMeetingLising.reloadData()
    }
    
    func darkmodechanged() {
        if self.valuemode == "dark" {
            Headerimage.image = UIImage(named: "Meetingbg")
            backgroundimage.image = UIImage(named: "BG4")
            mainvew.backgroundColor = UIColor(hex: "#0E0F27")
            safearacolor.backgroundColor = UIColor.black
        }
        else if self.valuemode == "light" {
            Headerimage.image = UIImage(named: "Meeting BG")
            backgroundimage.image = UIImage(named: "BackgroundImage")
            mainvew.backgroundColor = .white
            safearacolor.backgroundColor = UIColor(hex: "#A0A628")
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        
        self.viewPopUp.isHidden = true
        self.tabBarController?.tabBar.isHidden = false
        if fromMenu {
            self.buttonMenu.setImage(UIImage(named: "Back"), for: UIControl.State.normal)
            self.buttonMenu.addTarget(self, action: #selector(btnBackAction(_:)), for: UIControl.Event.touchUpInside)
        } else { 
            self.buttonMenu.setImage(UIImage(named: "Menu"), for: UIControl.State.normal)
            self.buttonMenu.addTarget(self, action: #selector(MeetingViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
        }
        
        //UITextView PlaceHolder
        txtVRescheduleNote.text = "You can add a small note"
        
        txtVRescheduleNote.textColor = UIColor.lightGray
        txtVRescheduleNote.font = UIFont(name: "verdana", size: 18.0)
        txtVRescheduleNote.returnKeyType = .done
        txtVRescheduleNote.delegate = self
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.delegate?.backAction(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnCancelActionPOPUP(_ sender: Any) {
        self.viewPopUp.isHidden = true
    }
    
    @IBAction func btnAddNotes(_ sender: Any) {
        if txtVRescheduleNote.text == "You can add a small note" || txtVRescheduleNote.hasText == false  {
            self.showAlertView(title: "Alert!", msg: "Reschedule Note is missing", controller: self) {
                
            }
        } else {
            self.serviceCallForRescheduleMeeting()
        }
    }
    
    func requestedAction() {
        btnUpcomingOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        identifyButtonClicked = self.request
        serviceCallToGetAssignMeetingListingForMentee()
    }
  
    func upcommingAction() {
        btnUpcomingOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        identifyButtonClicked = self.upcoming
        
        serviceCallToGetAssignMeetingListingForMenteeUpcomming()
    }
    
    func pastAction() {
        btnUpcomingOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnRequestOutlet.setTitleColor(UIColor.darkGray, for: .normal)
        btnPastOutlet.setTitleColor(UIColor.greenColourForApp, for: .normal)
        identifyButtonClicked = self.past
        serviceCallToGetAssignMeetingListingForMenteePast()
    }
    
    
    @IBAction func btnRequestedAction(_ sender: Any) {
      self.requestedAction()
    }
    
    @IBAction func btnUpcomingAction(_ sender: Any) {
       self.upcommingAction()
    }
    
    @IBAction func btnPastAction(_ sender: Any) {
      self.pastAction()
    }
    
    
    
    //MARK:-> read event
    
    
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
    
    
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- UITextViewDelegates
    func textViewDidBeginEditing(_ textView: UITextView) {
        if textView.text == "You can add a small note" {
            textView.text = ""
            textView.textColor = UIColor.black
            textView.font = UIFont(name: "verdana", size: 18.0)
        }
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        if textView.text == "" {
            textView.text = "You can add a small note"
            textView.textColor = UIColor.lightGray
            textView.font = UIFont(name: "verdana", size: 18.0)
        }
    }
    
    //MARK:- SideMenu
    func showHideMenuController(_ isShown: Bool) {
        buttonMenu.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowSideMenu() {
        let objSideMenu = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "MenuViewController") as! MenuViewController
        // let objSideMenu = self.storyboard?.instantiateViewController(withIdentifier: "MenuViewController") as! MenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMenuInController(self)
    }
    
    func dataReloadAfterSchedule() {
        
        if identifyButtonClicked==request {
           self.requestedAction()
        } else if identifyButtonClicked == upcoming {
            self.upcommingAction()
        }
    }
    
    func serviceCallForYesNo(check:String) {
        print("service call yes or no")
        var parameter = [String:String]()
        parameter["meeting_id"] = idUseForMeetingReschedule
        
        if check=="Yes" {
            parameter["web_status"] = "2"
        } else {
            parameter["web_status"] = "1"
        }
        
        ApiManager().serviceCallTOCheckMeetinParticipation(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                self.serviceCallToGetAssignMeetingListingForMentee()
                /*
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.serviceCallToGetAssignMeetingListingForMentee()
                    })
                }
                */
            } else {
                self.UI{
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                }
            }
        })
    }
    
    func serviceCallForAcceptMeeting() {
        print("service call accept metting")
        var parameter = [String:String]()
        parameter["meeting_id"] = idUseForMeetingReschedule
        parameter["status_id"] = "1"
        
        ApiManager().acceptMeetingByMentee(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                print("accept metting call")
                self.serviceCallToGetAssignMeetingListingForMentee()
                /*
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.serviceCallToGetAssignMeetingListingForMentee()
                    })
                }
                */
            } else {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                }
            }
        })
        
    }
    
    
    func serviceCallForRescheduleMeeting() {
        var parameter = [String:String]()
        parameter["meeting_id"] = idUseForMeetingReschedule
        parameter["note"] = txtVRescheduleNote.text
        
        ApiManager().rescheduleMeetingByMentee(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                self.UI {
                    self.txtVRescheduleNote.text = "You can add a small note"
                    self.viewPopUp.isHidden = true

                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.dataReloadAfterSchedule()
                    })
                }
            } else {
                self.UI {
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    self.logOutMentee()
                }
            }
        })
    }
    
    func serviceCallToGetAssignMeetingListingForMenteeUpcomming() {
        ApiManager().upcommingMeetingmentee(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            print("confirm tab")
            if status == true {
                let dicData = response["data"] as! NSDictionary
                let arrData = dicData["meeting"] as? NSArray
                
                self.arrAssignMeetingLising.removeAllObjects()
                if arrData!.count==0 {
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.text = "No Confirmed Sessions Found" // //No Past Metting Found
                        self.tableVRequestedMeetingLising.isHidden = true
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = false
                    }
                } else {
                    self.arrAssignMeetingLising = arrData?.mutableCopy() as! NSMutableArray
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = true

                        self.tableVRequestedMeetingLising.isHidden = false
                        
                        self.tableVRequestedMeetingLising.reloadData()
                    }
                }
            } else {
                self.UI{
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    self.logOutMentee()
                }
            }
        })
    }
    
    func serviceCallToGetAssignMeetingListingForMenteePast() {
        ApiManager().pastMeetingmentee(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                let dicData = response["data"] as! NSDictionary
                
                let arrData = dicData["meeting"] as? NSArray
                
                
                self.arrAssignMeetingLising.removeAllObjects()
                if arrData!.count == 0 {
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.text = "No Session Found"// //
                        self.tableVRequestedMeetingLising.isHidden = true
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = false
                    }
                } else {
                    self.arrAssignMeetingLising = arrData?.mutableCopy() as! NSMutableArray
                    
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = true
                        self.tableVRequestedMeetingLising.isHidden = false
                        self.tableVRequestedMeetingLising.reloadData()
                    }
                }
            } else {
                self.UI{
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    self.logOutMentee()
                }
            }
        })
        
    }
    
    func serviceCallToGetAssignMeetingListingForMentee() {
        ApiManager().meetingListingForMentee(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                let dicData = response["data"] as! NSDictionary
                
                let arrData = dicData["meeting"] as? NSArray

                
                self.arrAssignMeetingLising.removeAllObjects()
                if arrData!.count == 0 {
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.text = "No Past Session Found" //No Confirmed Sessions Found //
                        self.tableVRequestedMeetingLising.isHidden = true
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = false
                    }
                } else {
                    self.arrAssignMeetingLising = arrData?.mutableCopy() as! NSMutableArray
                    
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = true

                        self.tableVRequestedMeetingLising.isHidden = false
                        
                        self.tableVRequestedMeetingLising.reloadData()
                    }
                }
            } else {
                self.UI{
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    self.logOutMentee()
                }
            }
        })
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
    
    
    @objc func btnAccept(sender:UIButton) {
        let objForAccept = arrAssignMeetingLising[sender.tag] as! NSDictionary
        idUseForMeetingReschedule =  "\(objForAccept["id"] as! NSNumber)"
        sessionServerDateandTime = objForAccept["schedule_time"] as? String ?? ""
        titleSet = objForAccept["title"] as? String ?? ""
        describeText = objForAccept["description"] as? String ?? ""
        print("date and time", sessionServerDateandTime)
        let currentTitle  = sender.currentTitle!
        

        let myalert = UIAlertController(title: "Title", message: "Would you like to save this meeting to your calendar?", preferredStyle: UIAlertController.Style.alert)

        myalert.addAction(UIAlertAction(title: "Yes", style: .default) { (action:UIAlertAction!) in

                self.insertEvent(store: self.eventStore)

            })
        
        myalert.addAction(UIAlertAction(title: "No", style: .default) { (action:UIAlertAction!) in
        })
        
        self.present(myalert, animated: true)
        
        if currentTitle == "Yes" {
              self.UI {
            self.serviceCallForYesNo(check: "Yes")
            }
        } else {
            self.UI {
                self.serviceCallForAcceptMeeting()
            }
        }
    }
    
    
    
    
    func insertEvent(store: EKEventStore) {
        
          print("mentee insert event")
          let event:EKEvent = EKEvent(eventStore: store)
          let startDate = Date()
          // 2 hours
        _ = startDate.addingTimeInterval(2 * 60 * 60)
        //let reversed = String(str.reversed())

        event.title = titleSet
        print("concat",sessionServerDateandTime.convertDate())
        event.startDate = sessionServerDateandTime.convertDate()
        event.endDate = sessionServerDateandTime.convertDate().addingTimeInterval(2 * 60 * 60)
        event.notes = describeText
          event.calendar = store.defaultCalendarForNewEvents
          do {
              try store.save(event, span: .thisEvent)
               eventIdentifier = event.eventIdentifier
            self.navigationController?.popViewController(animated: true)
          } catch let error as NSError {
          print("failed to save event with error : \(error)")
          }
          print("Saved Event")
        
    }
    
    
    
    func cancelMettingPOP(name: String) {
        serviceCallToGetAssignMeetingListingForMentee()
    }
    
    
    
    @objc func btnReschedule(sender:UIButton) {
        let objForReschedule = arrAssignMeetingLising[sender.tag] as! NSDictionary
        idUseForMeetingReschedule =  "\(objForReschedule["id"] as! NSNumber)"
        
        
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionCancelViewController") as! SessionCancelViewController
        newViewController.idUseForMeetingReschedule = idUseForMeetingReschedule
        newViewController.delgatecancelNote =  self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
        
        /*
        let currentTitle  = sender.currentTitle!
        if currentTitle == "No" {
              self.UI {
                self.serviceCallForYesNo(check: "No")
            }
        } else {
            self.UI {
                self.viewPopUp.isHidden = false
                
            }
        }
        
        
        */
    }
    
    
    @objc func videopush(sender:UIButton) {
        
        
        
            
            
            
            
            let objForEdit = arrAssignMeetingLising[sender.tag] as! NSDictionary
            
            let senderid = objForEdit["user_id"] as? Int
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
            receiverId = String(objForEdit["created_by"] as? Int ?? 0)
            
            
            let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                   let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
            newViewController.senderId = String(senderid ?? 0)
            newViewController.senderType = sendertype
          newViewController.receiverId = receiverId
           newViewController.receiverType = types
            newViewController.fromWhereTag = "viaChat"
            self.navigationController?.pushViewController(newViewController, animated: true)
        
        
    }
    
    

}

extension MeetingViewController : UITableViewDataSource , UITableViewDelegate {
    
    //MARK: TableViewDataSource , TableViewDelegate
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrAssignMeetingLising.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier:String = "MentorTblCell"
        var cell:MentorTblCell? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? MentorTblCell
        
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("MentorTblCell", owner: self, options: nil)!
            cell = nib[0] as? MentorTblCell
        }
        if  self.fromtag == "assignrequested" {
            cell?.btnAcceptOulet.addTarget(self, action: #selector(btnAccept(sender:)), for: UIControl.Event.touchUpInside)
            cell?.btnAcceptOulet.tag = indexPath.row
            
            cell?.btnRescheduleOulet.addTarget(self, action: #selector(btnReschedule(sender:)), for: UIControl.Event.touchUpInside)
            
            cell?.btnRescheduleOulet.tag = indexPath.row
            cell?.viewForAllButton.isHidden = false
            
            cell?.videobtn.isHidden = true
            
            if arrAssignMeetingLising.count > 0 {
                let dataDic = arrAssignMeetingLising[indexPath.row] as! NSDictionary
                cell?.loadDataRequested(dicData: dataDic)
            }
        } else if self.fromtag == "assignupcoming" {
            
            cell?.videobtn.isHidden = false

            cell?.videobtn.addTarget(self, action: #selector(videopush(sender:)), for: UIControl.Event.touchUpInside)
            cell?.videobtn.tag = indexPath.row
            
            
            
            cell?.btnAcceptOulet.addTarget(self, action: #selector(btnAccept(sender:)), for: UIControl.Event.touchUpInside)
            cell?.btnAcceptOulet.tag = indexPath.row
            
            cell?.btnRescheduleOulet.addTarget(self, action: #selector(btnReschedule(sender:)), for: UIControl.Event.touchUpInside)
            
            cell?.btnRescheduleOulet.tag=indexPath.row
            cell?.viewForAllButton.isHidden = false
            
            if arrAssignMeetingLising.count > 0 {
                let dataDic = arrAssignMeetingLising[indexPath.row] as! NSDictionary
                cell?.loadDataUpcomming(dicData: dataDic)
            }
        }
        /* else if identifyButtonClicked == past {
            
            if arrAssignMeetingLising.count > 0 {
                let dataDic = arrAssignMeetingLising[indexPath.row] as! NSDictionary
                cell?.videobtn.isHidden = true
                cell?.loadDataPast(dicData: dataDic)
            }
        }
        */
        
        // cell?.selectionStyle = .none
        return cell!
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return UITableView.automaticDimension
    }
}


extension String {
    func convertDate(byFormat : String = "yyyy-MM-dd HH:mm:ss" , byZone : String = "America/New_York") -> Date {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = byFormat
        if byZone != "" {
            dateFormatter.timeZone = TimeZone(abbreviation: byZone) // "UTC"
        }
        let outdate = dateFormatter.date(from: self) ?? Date()
        return outdate
    }
}
