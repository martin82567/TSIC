//
//  SessionManagementMenteeViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/1/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
import EventKit
import FSCalendar


protocol sessionMangementrefresh {
    func backSession(name: String)
}
class SessionManagementMenteeViewController: UIViewController,btnAcceptsession,btnReshuduleSession,videoPushMenteefromBtn,Aftercencelmeeting{
  
    
    @IBOutlet weak var switchViewButton: UIButton!
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    @IBOutlet weak var celendarView: FSCalendar!
    @IBOutlet weak var menteeSession: UITableView!
    let eventStore = EKEventStore()
    var eventIdentifier = ""
    var showCelenderView = true
    var rearrMyJournalListing : NSMutableArray = []
    var uparrMyJournalListing : NSMutableArray = []
    var data:[NSArray.Element] = []
    var dataWithSessionType: NSDictionary = NSDictionary()
    var requestedArr : NSMutableArray = []
    var upcomingArr : NSMutableArray = []
    var colorArr: [UIColor] = []
    
    @IBOutlet weak var buttonMenu: UIButton!
    var fromMenu:Bool = false
    var delegate : sessionMangementrefresh!
    override func viewDidLoad() {
        super.viewDidLoad()
        switchViewButton.setTitle("", for: .normal)
        sevicecellMentee()
        menteeSession.isHidden = true
        celendarView.delegate = self
        celendarView.dataSource = self
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
        
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.delegate?.backSession(name: "hello")
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func switchViewButtonPressed(_ sender: UIButton) {
        if #available(iOS 13.0, *) {
            menteeSession.isHidden ? switchViewButton.setImage(UIImage(systemName: "calendar"), for: .normal) : switchViewButton.setImage(UIImage(systemName: "list.dash.header.rectangle"), for: .normal)
        } else {
            // Fallback on earlier versions
        }
        menteeSession.isHidden = !menteeSession.isHidden
        celendarView.isHidden = !celendarView.isHidden
        
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        
       // self.viewPopUp.isHidden = true
        self.tabBarController?.tabBar.isHidden = false
        if fromMenu {
            self.buttonMenu.setImage(UIImage(named: "Back"), for: UIControl.State.normal)
            self.buttonMenu.addTarget(self, action: #selector(btnBackAction(_:)), for: UIControl.Event.touchUpInside)
        } else {
         //   self.buttonMenu.setImage(UIImage(named: "Menu"), for`: UIControl.State.normal)
          //  self.buttonMenu.addTarget(self, action: #selector(MeetingViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
        }
        
        //UITextView PlaceHolder
     //   txtVRescheduleNote.text = "You can add a small note"
        
    //    txtVRescheduleNote.textColor = UIColor.lightGray
    //    txtVRescheduleNote.font = UIFont(name: "verdana", size: 18.0)
    //    txtVRescheduleNote.returnKeyType = .done
    //    txtVRescheduleNote.delegate = self
    }
    
    func videopushSet(senderId: String, senderType: String, receiverId: String, receiverType: String, fromWhereTag: String) {
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        
        newViewController.senderId = senderId
        newViewController.senderType = senderType
      newViewController.receiverId = receiverId
       newViewController.receiverType = receiverType
        newViewController.fromWhereTag = "viaChat"
        self.navigationController?.pushViewController(newViewController, animated: true)
    }
    
    func btnAccept(idUseForMeetingReschedule: String, sessionServerDateandTime: String, titleSet: String, describeText: String, currentTitle: String) {
        let myalert = UIAlertController(title: "Title", message: "Would you like to save this meeting to your calendar?", preferredStyle: UIAlertController.Style.alert)

        myalert.addAction(UIAlertAction(title: "Yes", style: .default) { (action:UIAlertAction!) in

            self.insertEvent(store: self.eventStore, titleSet: titleSet, sessionServerDateandTime: sessionServerDateandTime, describeText: describeText)

            })
        myalert.addAction(UIAlertAction(title: "No", style: .default) { (action:UIAlertAction!) in
        })
        self.present(myalert, animated: true)
        
        
        if currentTitle == "Yes" {
              self.UI {
                self.serviceCallForYesNo(check: "Yes", idUseForMeetingReschedule: idUseForMeetingReschedule)
            }
        } else {
            self.UI {
                self.serviceCallForAcceptMeeting(idUseForMeetingReschedule: idUseForMeetingReschedule)
            }
        }
        
    }
  
    func cancelMettingPOP(name: String) {
        sevicecellMentee()
    }
    
    func btnreschudle(idUseForMeetingReschedule: String, currentTitle: String) {
        
        let newViewController = self.MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "SessionCancelViewController") as! SessionCancelViewController
        newViewController.idUseForMeetingReschedule = idUseForMeetingReschedule
        newViewController.delgatecancelNote =  self
        self.navigationController?.pushViewController(newViewController, animated: true)
        

        
       // serviceCallForYesNo(check: "No", idUseForMeetingReschedule: idUseForMeetingReschedule)
        /*
        if currentTitle == "No" {
            serviceCallForYesNo(check: "No", idUseForMeetingReschedule: idUseForMeetingReschedule)
        }
        else {
            /*
            self.UI {
                self.viewPopUp.isHidden = false
                
            }
            */
        }
        */
        
    }
    
    func serviceCallForYesNo(check:String,idUseForMeetingReschedule: String) {
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
                self.sevicecellMentee()
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
    
    func serviceCallForAcceptMeeting(idUseForMeetingReschedule: String) {
        print("service call accept metting")
        var parameter = [String:String]()
        parameter["meeting_id"] = idUseForMeetingReschedule
        parameter["status_id"] = "1"
        
        ApiManager().acceptMeetingByMentee(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                print("accept metting call")
                self.sevicecellMentee()
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
    
    func insertEvent(store: EKEventStore, titleSet: String,sessionServerDateandTime: String,describeText: String) {
        
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

    func sevicecellMentee() {
        ApiManager().AllmeetingListingForMentee(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                let Data = response["data"] as! NSDictionary
                let arrOne = Data["requested"] as! NSArray
                let arrTwo = Data["upcoming"] as! NSArray
                print("array one",arrOne)
                print("array two",arrTwo)

                if arrOne.count == 0 && arrTwo.count == 0 {
                    print("No data")
                    /*
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.text = "No Past Session Found" //No Confirmed Sessions Found //
                        self.tableVRequestedMeetingLising.isHidden = true
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = false
                    }
                    */
                } else {
                    self.rearrMyJournalListing.removeAllObjects()
                    self.uparrMyJournalListing.removeAllObjects()
                    self.rearrMyJournalListing = arrOne.mutableCopy() as! NSMutableArray
                    self.uparrMyJournalListing = arrTwo.mutableCopy() as! NSMutableArray
                    self.data = [arrOne,arrTwo].flatMap { $0 }
                    self.dataWithSessionType = Data
                    DispatchQueue.main.async {
                        self.menteeSession.delegate = self
                        self.menteeSession.dataSource = self
                        self.celendarView.reloadData()
                        self.menteeSession.reloadData()
                    }
                  
                    /*
                    self.UI {
                        self.lblInsideTblevToShowThatNoAnyMeeings.isHidden = true

                        self.tableVRequestedMeetingLising.isHidden = false
                        
                        self.tableVRequestedMeetingLising.reloadData()
                    }
                    */
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
                            
                            let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "BaseLoginViewController")
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

    @objc func methodOne() {
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
        let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MeetingViewController") as! MeetingViewController
        newViewController.fromtag = "assignrequested"
        newViewController.fromMenu = true
     //   newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    
    @objc func methodTwo() {
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
        let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MeetingViewController") as! MeetingViewController
        newViewController.fromtag = "assignupcoming"
        newViewController.fromMenu = true
      //  newViewController.delegate = self
        self.navigationController?.pushViewController(newViewController, animated: true)
    }
    

}


extension SessionManagementMenteeViewController: UITableViewDelegate,UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 4
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        switch indexPath.row {
        case 0:
            let cell =  tableView.dequeueReusableCell(withIdentifier: "FirstheaderTableViewCell") as! FirstheaderTableViewCell
            cell.viewButtonone.addTarget(self, action: #selector(methodOne), for: .touchUpInside)
            return cell
        case 1:
            let cell = tableView.dequeueReusableCell(withIdentifier: "MenteeSessionTableViewCell") as! MenteeSessionTableViewCell
            cell.collectionviewMente.register(UINib(nibName: "SessionMenteeCollectionViewCell", bundle: nil), forCellWithReuseIdentifier: "SessionMenteeCollectionViewCell")
            
            cell.arrone = self.rearrMyJournalListing
            cell.acceptDelegate = self
            cell.reschuduledelegate = self
            print("array",cell.arrone)
            cell.selectionStyle = .none
            
            if cell.arrone.count == 0 {
                cell.placholderOne.text = "No Session Found!"
            }
            else {
                cell.placholderOne.isHidden = true
            }
            
            cell.collectionviewMente.reloadData()
            return cell
        case 2:
            let cell =  tableView.dequeueReusableCell(withIdentifier: "SecondheaderTableViewCell") as! SecondheaderTableViewCell
            cell.viewBtntwo.addTarget(self, action: #selector(methodTwo), for: .touchUpInside)
            return cell
        case 3:
            let cell = tableView.dequeueReusableCell(withIdentifier: "MenTeeSessionTableViewCellTwo") as! MenTeeSessionTableViewCellTwo
            cell.menteeSessioncollectionviewtwo.register(UINib(nibName: "MenteePastCollectionViewCell", bundle: nil), forCellWithReuseIdentifier: "MenteePastCollectionViewCell")
            cell.arrtwo = self.uparrMyJournalListing
            cell.selectionStyle = .none
            
            if cell.arrtwo.count == 0 {
                cell.upcomingSession.text = "No Session Found!"
            }
            else {
                cell.upcomingSession.isHidden = true
            }
            cell.videoDelegate = self
            
            cell.menteeSessioncollectionviewtwo.reloadData()
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
                return 410
            }
           
        case 2:
            if self.uparrMyJournalListing.count == 0 {
                return 0
            }
            else {
                return UITableView.automaticDimension
            }
            
        case 3:
            
            if self.uparrMyJournalListing.count == 0 {
                return 0
            }
            else {
                return 310
            }
        default:
            return 0
        }
    }
}
extension SessionManagementMenteeViewController: FSCalendarDelegate, FSCalendarDataSource, FSCalendarDelegateAppearance {
    
    func calendar(_ calendar: FSCalendar, numberOfEventsFor date: Date) -> Int {
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
            if numberOfEvents >= 2 {
                return 2
            }
            else{
                return numberOfEvents
            }
        }
        else{
            return 0
        }
    }

    func calendar(_ calendar: FSCalendar, appearance: FSCalendarAppearance, eventDefaultColorsFor date: Date) -> [UIColor]? {
        let date2Formatter = DateFormatter()
        date2Formatter.dateFormat = "yyyy-MM-dd"

        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"

        let datestring2 : String = dateFormatter.string(from:date)
    
        let arrOne = dataWithSessionType["requested"] as! NSArray
        let arrTwo = dataWithSessionType["upcoming"] as! NSArray

        print("arrayOne|||",arrOne)

        let reTime = arrOne.map { ($0 as! NSDictionary)["schedule_time"]! as! String }
        let upTime = arrTwo.map { ($0 as! NSDictionary)["schedule_time"]! as! String}


        let reTimeDate = reTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}
        let upTimeDate = upTime.map{date2Formatter.date(from: String($0.dropLast(9)))!}

        let reArr = reTimeDate.map{date2Formatter.string(from: $0) }
        let upArr = upTimeDate.map{date2Formatter.string(from: $0) }


        let reNum:Int = reArr.filter{$0 == datestring2}.count
        var upNum:Int = upArr.filter{$0 == datestring2}.count

        print(reNum,upNum,"numberOfEvents",reNum,datestring2)
//        return numberOfEvents
        let color1 = UIColor.orange //requested
        var color2 = UIColor.green //upcoming

        if reNum >= 1 || upNum >= 1 {
            if reNum >= 1 {
            if upNum >= 1 {
                self.colorArr = [color1,color2]
                return [color1,color2]
            }else{
                self.colorArr = [color1,color1]
                return [color1,color1]
            }
        }else{
            self.colorArr = [color1,UIColor.white]
            return [color2,UIColor.white]
        }
            
        }else {
            self.colorArr = [UIColor.white]
            return [UIColor.white]
        }
    }
    func calendar(_ calendar: FSCalendar, shouldSelect date: Date, at monthPosition: FSCalendarMonthPosition) -> Bool {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        let date2Formatter = DateFormatter()
        date2Formatter.dateFormat = "yyyy-MM-dd"

        let datestring2 : String = dateFormatter.string(from:date)
        func convertDate(elem: NSArray.Element) -> String{
            
            let str = (elem as! NSDictionary)["schedule_time"]! as! String
            let dateObj = dateFormatter.date(from: String(str.dropLast(9)))
            let dateStr = date2Formatter.string(from: dateObj!)
            return dateStr
        }
        self.requestedArr = NSMutableArray(array: rearrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
        self.upcomingArr = NSMutableArray(array: uparrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
        if self.requestedArr.count > 0 || self.upcomingArr.count > 0{
            return true
        }else{
            return false
        }

    }

    func calendar(_ calendar: FSCalendar, didSelect date: Date, at monthPosition: FSCalendarMonthPosition) {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "yyyy-MM-dd"
        let date2Formatter = DateFormatter()
        date2Formatter.dateFormat = "yyyy-MM-dd"

        let datestring2 : String = dateFormatter.string(from:date)
        let bottomSheetVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "MenteeBottomSheetVCViewController") as! MenteeBottomSheetVCViewController
        if #available(iOS 15.0, *) {
            func scheduleDate(elem: NSArray.Element) -> String{
                let str = (elem as! NSDictionary)["schedule_time"]! as! String
                let dateObj = dateFormatter.date(from: String(str.dropLast(9)))
                let dateStr = date2Formatter.string(from: dateObj!)
                return dateStr
            }
            func convertDate(elem: NSArray.Element) -> String{
                let str = (elem as! NSDictionary)["schedule_time"]! as! String
                let dateObj = dateFormatter.date(from: String(str.dropLast(9)))
                let dateStr = date2Formatter.string(from: dateObj!)
                return dateStr
            }
            let dateFiltereData = data.filter { scheduleDate(elem: $0) == datestring2 }
            print("dateFiltereData|||",dateFiltereData)
//            bottomSheetVC.sessionListForDate = dateFiltereData
            
            
            
//            print("rearrMyJournalListing1|||",rearrMyJournalListing[0] as! NSDictionary)
//            print("rearrMyJournalListing2||||",(rearrMyJournalListing[0] as! NSDictionary)["schedule_time"]!)
//            print("rearrMyJournalListing3|||",String((rearrMyJournalListing[0] as! NSDictionary)["schedule_time"]! as! String).dropLast(9))
//            print("rearrMyJournalListing4|||",convertDate(elem: rearrMyJournalListing[0]))
//            print("rearrMyJournalListing.filter{ convertDate(elem: $0) == datestring2}",NSMutableArray(array: rearrMyJournalListing.filter{ convertDate(elem: $0) == datestring2}))
            
            
            
            
            self.requestedArr = NSMutableArray(array: rearrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
            self.upcomingArr = NSMutableArray(array: uparrMyJournalListing.filter{ convertDate(elem: $0) == datestring2})
            print("self.rescheduledArr",self.requestedArr)
            if let sheet = bottomSheetVC.sheetPresentationController{
                sheet.detents = [.medium(), .large()]
            }
        }
        else {
            // Fallback on earlier versions
        }
        print("requestedArr",requestedArr)
        print("upcomingArr",upcomingArr)
        bottomSheetVC.rearrMyJournalListing = requestedArr
        bottomSheetVC.uparrMyJournalListing = upcomingArr
//        self.present(bottomSheetVC, animated: true)
        if requestedArr.count > 0 || upcomingArr.count > 0 {
            self.navigationController?.pushViewController(bottomSheetVC, animated: true)
        }else {
            
        }
    }
}
