//
//  SessionManagementViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/30/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
import EventKit
import FSCalendar

protocol SessionmangementRefresh{
    func backRefresh9(name: String)
}
class SessionManagementViewController: BaseViewController, videoAction,reschedule,cancel,deny, didSelectDelegate{
   
    let eventStore = EKEventStore()
    var strMeetingID = ""
    var canceldateTime = ""
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    var data: [NSArray.Element] = [NSArray.Element]()
    var dataWithSessionType: NSDictionary = NSDictionary()
    @IBOutlet weak var buttonMenu: UIButton!
    @IBOutlet weak var calenderView: FSCalendar!
    
    @IBOutlet weak var switchView: UIButton!
    @IBOutlet weak var sesiontableview: UITableView!
    var rearrMyJournalListing : NSMutableArray = []
    var uparrMyJournalListing : NSMutableArray = []
    var paarrMyJournalListing : NSMutableArray = []
    
    var requestedArr : NSMutableArray = []
    var upcomingArr : NSMutableArray = []
    var pastArr : NSMutableArray = []
    
    var delegate: SessionmangementRefresh!
    override func viewDidLoad() {
        super.viewDidLoad()
        sesiontableview.isHidden = true
        switchView.setTitle("", for: .normal)
        calenderView.delegate = self
        calenderView.dataSource = self
        calenderView.appearance.titleDefaultColor = UIColor(named: "CalenderColor")
    }
    override func viewDidAppear(_ animated: Bool) {
        sessionmanagementAPICALL()
    }
    
    
    @IBAction func switchViewButtonPressed(_ sender: UIButton) {
        if sesiontableview.isHidden {
            if #available(iOS 13.0, *) {
                switchView.setImage(UIImage(systemName: "calendar"), for: .normal)
            } else {
                switchView.setImage(UIImage(named: "calendar2"), for: .normal)
            }
            
            sesiontableview.isHidden = false
            calenderView.isHidden = true
        }else {
            if #available(iOS 13.0, *) {
                switchView.setImage(UIImage(systemName: "list.dash.header.rectangle"), for: .normal)
            } else {
                switchView.setImage(UIImage(named: "notee"), for: .normal)
            }
            sesiontableview.isHidden = true
            calenderView.isHidden = false
        }
    }
    
    @IBAction func backAction(_ sender: Any) {
        self.delegate?.backRefresh9(name: "Hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    func cancelAction(canceldateTime: String,strMeetingID: String) {
        self.strMeetingID = strMeetingID
        self.canceldateTime = canceldateTime
        
        self.readEvents(canceldateTime.toDate())
        self.serviceCallForCancelMeeting()
        
    }
    
    func didSelect(data: NSDictionary) {
        let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "MentorSession", bundle: nil)
        let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "ShowSessionViewController") as! ShowSessionViewController
        newViewController.obj = data
        self.navigationController?.pushViewController(newViewController, animated: true)
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
    
    func denyAction(id: String) {
        self.strMeetingID = id
        serviceCallForDeclineMeeting()
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
    
    func rescheduleAction(sender: UIButton, objEdit: NSDictionary) {
        
        print("coming to rescheduleAction")
        let btnTextOutlet = sender.currentTitle
        
        if btnTextOutlet == "EDIT" {
            self.btnEdit(sender: sender, objForEdit: objEdit)
        } else {
        //    let objForEdit = arrMyJournalListing[sender.tag] as! NSDictionary
            
            let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
            
            self.UI {
                let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
                newViewController.assignValueAfterPOP = {() -> Void in
                    self.sessionmanagementAPICALL()
                }
                newViewController.arrMyMenteeListForEdit = objEdit
                self.navigationController?.pushViewController(newViewController, animated: true)
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
 
    func sessionmanagementAPICALL() {
        print("sessionmanagementAPICALL called")
        self.startActivityIndicator()
        MentorApiManager().AllmeetingListing(completion: { (response) in
            self.stopActivityIndicator()
            let status = response["status"] as! Bool
            
            if status == true {
                let Data = response["data"] as! NSDictionary
                let arrOne = Data["requested"] as! NSArray
                let arrTwo = Data["upcoming"] as! NSArray
                let arrThree = Data["past"] as! NSArray
                print("arrrrr====",arrOne,arrTwo,arrThree)
                
                if arrOne.count == 0 && arrTwo.count == 0 && arrThree.count == 0{
                    self.UI {
                        /*
                        if self.identifyButtonClicked == self.upcoming {
                            self.lblForNoMettigFound.text = "No Confirmed Sessions Found"
                        } else if self.identifyButtonClicked == self.request {
                            self.lblForNoMettigFound.text = "Not Any Requested Sessions"
                        } else if self.identifyButtonClicked == self.past {
                            self.lblForNoMettigFound.text = "No Past Session"
                        }
                        self.lblForNoMettigFound.isHidden = false
                        self.tableVSessionList.isHidden = true
                        */
                    }
                } else {
                    self.rearrMyJournalListing = arrOne.mutableCopy() as! NSMutableArray
                    self.uparrMyJournalListing = arrTwo.mutableCopy() as! NSMutableArray
                    self.paarrMyJournalListing = arrThree.mutableCopy() as! NSMutableArray
                    
                    
                    self.UI { [self] in
                        //self.lblForNoMettigFound.isHidden = true
                       // self.tableVSessionList.isHidden = false
                        self.data = [arrOne,arrTwo,arrThree].flatMap { $0 }
                        print("data is data",self.data)
                        self.dataWithSessionType = Data
                        print("self.dataWithSessionType",self.dataWithSessionType)
                        self.calenderView.reloadData()
                        self.sesiontableview.reloadData()
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
    
    @IBAction func addSession(_ sender: Any) {
        
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
            newViewController.checkIamFrom = "createMeeting"
            newViewController.assignValueAfterPOP = {() -> Void in
                self.sessionmanagementAPICALL()
            }
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
    @IBAction func createsesion(_ sender: Any) {
        
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "MentorMeeting", bundle: nil)
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MentorMeetingCreatorVC") as! MentorMeetingCreatorVC
            newViewController.checkIamFrom = "createMeeting"
            newViewController.assignValueAfterPOP = {() -> Void in
                self.sessionmanagementAPICALL()
            }
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    
   }
    @IBAction func viewsessionBtn(_ sender: Any) {
        print("View Session btn")
        
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionisLoggedViewController") as! SessionisLoggedViewController
       // newViewController.iAmFrom = "meeting"
       // newViewController.fromtag = "past"
       // newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
        /*
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionLogListViewController") as! SessionLogListViewController
       // newViewController.iAmFrom = "session"
       // newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        */
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


extension SessionManagementViewController : UITableViewDelegate, UITableViewDataSource {
    
    
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
extension SessionManagementViewController: FSCalendarDelegate,FSCalendarDataSource,FSCalendarDelegateAppearance {
    
    
    func calendar(_ calendar: FSCalendar, numberOfEventsFor date: Date) -> Int {
        let numEventsForDate = numEvents(date: date)
        if numEventsForDate.count > 0 {
            let numberOfDots = numEventsForDate.filter{ $0 > 0 }
            return numberOfDots.count
        }else {
            return 0
        }

    }
    
    func calendar(_ calendar: FSCalendar, appearance: FSCalendarAppearance, eventDefaultColorsFor date: Date) -> [UIColor]? {
        let color1 = UIColor.orange //requested
        let color2 = UIColor.red //past
        let color3 = UIColor.green //upcoming
        let numEventsForDate = numEvents(date: date)
        if numEventsForDate.count > 0 {
        let reNum = numEventsForDate[0]
        let upNum = numEventsForDate[1]
        let paNum = numEventsForDate[2]
        if reNum > 0 {
            if paNum > 0 {
                if upNum > 0 {
                    return [color1, color2, color3]
                }else{
                    return [color1, color2, color2]
                }
            }else {
                return [color1, color3]
            }
        }
        else {
            if paNum > 0 {
            return [color2, color3]
            }
            else{
                return [color3]
            }
        }
        }else{
            return [calendar.appearance.eventDefaultColor]
        }
    }
    
    func calendar(_ calendar: FSCalendar, didSelect date: Date, at monthPosition: FSCalendarMonthPosition) {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        let date2Formatter = DateFormatter()
        date2Formatter.dateFormat = "yyyy-MM-dd"
        
        let datestring2 : String = dateFormatter.string(from:date)
        let bottomSheetVC = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "BottomSheetViewController") as! BottomSheetViewController
        if #available(iOS 15.0, *) {
//            func scheduleDate(elem: NSArray.Element) -> String{
//                let str = (elem as! NSDictionary)["schedule_time"]! as! String
//                let dateObj = dateFormatter.date(from: String(str.dropLast(9)))
//                let dateStr = date2Formatter.string(from: dateObj!)
//                return dateStr
//            }
            func convertDate(elem: NSArray.Element) -> String{
                let str = (elem as! NSDictionary)["schedule_time"]! as! String
                let dateObj = dateFormatter.date(from: String(str.dropLast(9)))
                let dateStr = date2Formatter.string(from: dateObj!)
                return dateStr
            }
            let dateFiltereData = data.filter { convertDate(elem: $0) == datestring2 }
            print("dateFiltereData|||",dateFiltereData)
            bottomSheetVC.sessionListForDate = dateFiltereData

            self.requestedArr = NSMutableArray(array: rearrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
            self.pastArr = NSMutableArray(array: paarrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
            self.upcomingArr = NSMutableArray(array: uparrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
            print("self.PastArr",self.pastArr)
            if let sheet = bottomSheetVC.sheetPresentationController{
                sheet.detents = [.medium(), .large()]
            }
        }
        else {
            // Fallback on earlier versions
        }
        bottomSheetVC.rearrMyJournalListing = requestedArr
        bottomSheetVC.uparrMyJournalListing = upcomingArr
        bottomSheetVC.paarrMyJournalListing = pastArr
//        self.present(bottomSheetVC, animated: true)
        self.navigationController?.pushViewController(bottomSheetVC, animated: true)
    }
    func calendar(_ calendar: FSCalendar, shouldSelect date: Date, at monthPosition: FSCalendarMonthPosition) -> Bool {
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        let datestring2 : String = dateFormatter.string(from:date)
        let time = data.map { ($0 as! NSDictionary)["schedule_time"]! }
        var scheduledTime = time as! [String]
        
        if !scheduledTime.isEmpty
        {
            let date2Formatter = DateFormatter()
            date2Formatter.dateFormat = "yyyy-MM-dd"
            let timeDate = scheduledTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}
            let newArr = timeDate.map{date2Formatter.string(from: $0) }
            let numberOfEvents:Int = newArr.filter{$0 == datestring2}.count
            if numberOfEvents > 0  {
                return true
            }else {
                return false
            }
        }
        else {
            return false
        }
    }
    func calendar(_ calendar: FSCalendar, appearance: FSCalendarAppearance, eventSelectionColorsFor date: Date) -> [UIColor]? {
        return [UIColor.white,UIColor.white,UIColor.white]
    }
    func numEvents(date:Date) -> [Int] {
        let date2Formatter = DateFormatter()
        date2Formatter.dateFormat = "yyyy-MM-dd"
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        
        let datestring2 : String = dateFormatter.string(from:date)
        let requested = self.dataWithSessionType
        if requested.count > 0 {
        var arrOne = requested["requested"] as! NSArray
        var arrTwo = requested["upcoming"] as! NSArray
        var arrThree = requested["past"] as! NSArray
        
        print("arrayOne|||",arrOne)

        
        let reTime = arrOne.map { ($0 as! NSDictionary)["schedule_time"]! as! String }
        let upTime = arrTwo.map { ($0 as! NSDictionary)["schedule_time"]! as! String}
        let paTime = arrThree.map { ($0 as! NSDictionary)["schedule_time"]! as! String}
        
        
        let reTimeDate = reTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}
        let upTimeDate = upTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}
        let paTimeDate = paTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}

        let reArr = reTimeDate.map{date2Formatter.string(from: $0) }
        let upArr = upTimeDate.map{date2Formatter.string(from: $0) }
        let paArr = paTimeDate.map{date2Formatter.string(from: $0) }

        
        let reNum:Int = reArr.filter{$0 == datestring2}.count
        var upNum:Int = upArr.filter{$0 == datestring2}.count
        var paNum:Int = paArr.filter{$0 == datestring2}.count
        return [reNum,upNum,paNum]
        }else {
            return []
        }
        
    }
}

