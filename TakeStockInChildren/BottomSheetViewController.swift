//
//  bottomSheetViewController.swift
//  TakeStockInChildren
//
//  Created by Divij Jindal on 14/07/22.
//  Copyright © 2022 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
import EventKit
import Foundation

class BottomSheetViewController: BaseViewController, reschedule, deny, cancel, didSelectDelegate, videoAction {
    
    var strMeetingID = ""
    var canceldateTime = ""
    let eventStore = EKEventStore()
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    var rearrMyJournalListing : NSMutableArray = []
    var uparrMyJournalListing : NSMutableArray = []
    var paarrMyJournalListing : NSMutableArray = []
    var sessionListForDate = [NSArray.Element]()
    var data: [NSArray.Element] = [NSArray.Element]()
    var selectedDates = [NSArray.Element]()
    
    var delegate: SessionmangementRefresh!
    
    
    @IBOutlet weak var backButton: UIButton!
    @IBOutlet weak var sessionTableViewCOntroller: UITableView!
    override func viewDidLoad() {
        super.viewDidLoad()
        backButton.setTitle("", for: .normal)
        sessionTableViewCOntroller.delegate = self
        sessionTableViewCOntroller.dataSource = self
    }
    
    override func viewDidAppear(_ animated: Bool) {
        sessionmanagementAPICALL()
    }
    
    @IBAction func backButtonPressed(_ sender: UIButton) {
        print("backBUttonPressed")
        self.delegate?.backRefresh9(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    func serviceCallForCancelMeeting() {
        var parameter = [String:String]()
        parameter["meeting_id"] = strMeetingID
        
        MentorApiManager().meetingCancel(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.sessionmanagementAPICALL()
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
    
    func actionVideo(senderId: String, senderType: String, receiverid: String, recieverType: String, fromwheretag: String) {
        //print("All==============",senderId,senderType,receiverid,recieverType,fromwheretag)
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        newViewController.senderId = senderId
        newViewController.senderType = senderType
      newViewController.receiverId = receiverid
       newViewController.receiverType = recieverType
        newViewController.fromWhereTag = fromwheretag
        self.navigationController?.pushViewController(newViewController, animated: true)
    }
    
    func didSelect(data: NSDictionary) {
        let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
        let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "ShowSessionViewController") as! ShowSessionViewController
        newViewController.obj = data
        self.navigationController?.pushViewController(newViewController, animated: true)
    }
    
    func sessionmanagementAPICALL() {
//        self.startActivityIndicator()
//        MentorApiManager().AllmeetingListing(completion: { (response) in
//            self.stopActivityIndicator()
//            let status = response["status"] as! Bool
//
//            if status == true {
//                let Data = response["data"] as! NSDictionary
//                let arrOne = Data["requested"] as! NSArray
//                let arrTwo = Data["upcoming"] as! NSArray
//                let arrThree = Data["past"] as! NSArray
//                print("arrrrr====",arrOne,arrTwo,arrThree)
//
//                if arrOne.count == 0 && arrTwo.count == 0 && arrThree.count == 0{
//                    self.UI {
//                        /*
//                        if self.identifyButtonClicked == self.upcoming {
//                            self.lblForNoMettigFound.text = "No Confirmed Sessions Found"
//                        } else if self.identifyButtonClicked == self.request {
//                            self.lblForNoMettigFound.text = "Not Any Requested Sessions"
//                        } else if self.identifyButtonClicked == self.past {
//                            self.lblForNoMettigFound.text = "No Past Session"
//                        }
//                        self.lblForNoMettigFound.isHidden = false
//                        self.tableVSessionList.isHidden = true
//                        */
//                        self.sessionTableViewCOntroller.reloadData()
//                    }
//                } else {
//                    self.rearrMyJournalListing = arrOne.mutableCopy() as! NSMutableArray
//                    self.uparrMyJournalListing = arrTwo.mutableCopy() as! NSMutableArray
//                    self.paarrMyJournalListing = arrThree.mutableCopy() as! NSMutableArray
//                    self.data = [arrOne,arrTwo,arrThree].flatMap { $0 }
//                    self.UI { [self] in
//                        //self.lblForNoMettigFound.isHidden = true
//                       // self.tableVSessionList.isHidden = false
////                        self.data = [arrOne,arrTwo,arrThree].flatMap { $0 }
////                        print("data is data",self.data)
////                        self.dataWithSessionType = Data
////                        print("self.dataWithSessionType",self.dataWithSessionType)
//
//                    }
//                }
//            } else {
//                self.UI{
//                    self.logOutMentor()
//                }
//            }
//        })
        
    }

    func serviceCallForDeclineMeeting() {
        var parameter = [String:String]()
        
        parameter["meeting_id"] = strMeetingID
        
        MentorApiManager().no_reschedule_meeting(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                        self.sessionmanagementAPICALL()
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

    func denyAction(id: String) {
        self.strMeetingID = id
        serviceCallForDeclineMeeting()
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

    func rescheduleAction(sender: UIButton, objEdit: NSDictionary) {
        
        print(" in bottomSheet VC rescheduleAction")
        let btnTextOutlet = sender.currentTitle
        
        if btnTextOutlet == "EDIT" {
            self.btnEdit(sender: sender, objForEdit: objEdit)
        } else {
        //    let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
            
            self.UI {
                print("rescheduleAction====>  1")
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
                newViewController.assignValueAfterPOP = {() -> Void in
                    self.sessionmanagementAPICALL()
                }
                print("rescheduleAction====>  1")

                newViewController.arrMyMenteeListForEdit = objEdit
                self.navigationController?.pushViewController(newViewController, animated: true)
//                self.present(newViewController, animated: true)
            }
        }
    }
    
    @objc func btnEdit(sender:UIButton, objForEdit: NSDictionary) {
        //let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
        
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
        
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
             newViewController.checkIamFrom = "editMeeting"
            newViewController.assignValueAfterPOP = {() -> Void in
                
            }
            newViewController.arrMyMenteeListForEdit = objForEdit
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }

    func cancelAction(canceldateTime: String,strMeetingID: String) {
        self.strMeetingID = strMeetingID
        self.canceldateTime = canceldateTime
        
        self.readEvents(canceldateTime.toDate())
        self.serviceCallForCancelMeeting()
        
    }
    
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
    @objc func methodOne(){
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionViewController") as! SessionViewController
        newViewController.iAmFrom = "meeting"
        newViewController.fromtag = "request"
       // newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    @objc func methodTwo(){
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionViewController") as! SessionViewController
        newViewController.iAmFrom = "meeting"
        newViewController.fromtag = "upcoming"
       // newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    @objc func methodThree(){
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionViewController") as! SessionViewController
        newViewController.iAmFrom = "meeting"
        newViewController.fromtag = "past"
       // newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
}

extension BottomSheetViewController : UITableViewDelegate, UITableViewDataSource {
        
        
        func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
            return 6
        }
        
        func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
            switch indexPath.row {
            case 0:
                let cell = tableView.dequeueReusableCell(withIdentifier: "HeaderTableViewCellOne")
                    as! HeaderTableViewCellOne
                cell.btnOne.addTarget(self, action: #selector(methodOne), for: .touchUpInside)
                return cell
                //request
            case 1:
                let cell = tableView.dequeueReusableCell(withIdentifier: "RequestingTableViewCell") as! RequestingTableViewCell
                cell.collectionViewone.register(UINib(nibName: "PastSessionCollectionViewCell", bundle: nil), forCellWithReuseIdentifier: "PastSessionCollectionViewCell")
                cell.selectionStyle = .none
                
                
                cell.reqarr = self.rearrMyJournalListing
                if cell.reqarr.count == 0 {
                    cell.placeholderOne.text = "No Session Found!"
                }
                else{
                    cell.placeholderOne.isHidden = true
                }
                cell.rescheduleDelegate = self
                cell.cancelDelegate = self
                cell.denydeleagate = self
                
                cell.collectionViewone.reloadData()
                
                return cell
            case 2:
                let cell = tableView.dequeueReusableCell(withIdentifier: "HeaderTableViewCellTwo") as! HeaderTableViewCellTwo
                cell.btnTwo.addTarget(self, action: #selector(methodTwo), for: .touchUpInside)
                return cell
                //Upcoming
            case 3:
                let cell = tableView.dequeueReusableCell(withIdentifier: "UpcomingTableViewCell") as! UpcomingTableViewCell
                cell.collectionViewtwo.register(UINib(nibName: "UpcomingSessionCollectionViewCell", bundle: nil), forCellWithReuseIdentifier: "UpcomingSessionCollectionViewCell")
                cell.uparr = self.uparrMyJournalListing
                cell.selectionStyle = .none
                
                if cell.uparr.count == 0{
                    cell.placeholdertwo.text = "No Session Found!"
                }
                else{
                    cell.placeholdertwo.isHidden = true
                }
                
                cell.videoDelegate = self
                
                
                cell.collectionViewtwo.reloadData()
                return cell
            case 4:
                let cell = tableView.dequeueReusableCell(withIdentifier: "HeaderTableViewCellThree") as! HeaderTableViewCellThree
                
                cell.btnThree.addTarget(self, action: #selector(methodThree), for: .touchUpInside)
                return cell
                //Past
            case 5:
                let cell = tableView.dequeueReusableCell(withIdentifier: "PastTableViewCell") as! PastTableViewCell
                
                cell.collectionViewThree.register(UINib(nibName: "RequestSessionCollectionViewCell", bundle: nil), forCellWithReuseIdentifier: "RequestSessionCollectionViewCell")
                cell.selectionStyle = .none
                print("self.paarrMyJournalListing",self.paarrMyJournalListing)
                cell.pastArr = self.paarrMyJournalListing
                
                if cell.pastArr.count == 0{
                    cell.placeholderthree.text = "No Session Found!"
                }
                else{
                    cell.placeholderthree.isHidden = true
                }
                cell.delegate = self
                
                cell.collectionViewThree.reloadData()
                return cell

            default:
                return UITableViewCell()
            }
            
        }
        
        func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
            switch indexPath.row {
            case 0:
                if self.rearrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return UITableView.automaticDimension
                }
                
            case 1:
                if self.rearrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return 450
                }
                
            case 2:
                
                if uparrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return UITableView.automaticDimension
                }
                
                
            case 3:
                if uparrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return 300
                }
                
            case 4:
                if paarrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return UITableView.automaticDimension
                }
            case 5:
                
                if paarrMyJournalListing.count == 0 {
                    return 0
                }
                else {
                    return 350
                }
            default:
                return 0
            }
        }
        
    }
    



