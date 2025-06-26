
//
//  MentorMeetingCreatorVC.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 8/23/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
//import IBAnimatable
import ImagePicker
import CoreLocation
import Alamofire
//import RLBAlertsPickers
import EventKit
class MentorMeetingCreatorVC: BaseViewController {

    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var lbl4: UILabel!
    @IBOutlet weak var lbl1: UILabel!
    @IBOutlet weak var lbl3: UILabel!
    
    @IBOutlet weak var lbl8: UILabel!
    @IBOutlet weak var lbl7: UILabel!
    @IBOutlet weak var lbl5: UILabel!
    @IBOutlet weak var lbl6: UILabel!
    @IBOutlet weak var lbl2: UILabel!
    
    @IBOutlet weak var spaceviewHeightconstant: NSLayoutConstraint!
    
    @IBOutlet weak var eightView: UIView!
    @IBOutlet weak var sevenview: UIView!
    @IBOutlet weak var sicVew: UIView!
    @IBOutlet weak var fiveView: UIView!
    @IBOutlet weak var fourView: UIView!
    @IBOutlet weak var thordview: UIView!
    @IBOutlet weak var SecondView: UIView!
    @IBOutlet weak var firstView: UIView!
    @IBOutlet weak var scrollbackground: UIView!
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var txtSessionMethodlocation: TextFieldPadding!
    @IBOutlet weak var txtMenteeName: TextFieldPadding!
    @IBOutlet weak var HeaderView: UIView!
    @IBOutlet weak var txtAddress: TextFieldPadding!
    @IBOutlet weak var txtSchoolLoc: TextFieldPadding!
    @IBOutlet weak var txtDate: TextFieldPadding!
    @IBOutlet weak var txtTimeFrom: TextFieldPadding!
    @IBOutlet weak var txtVDesc: TextViewPadding!
    @IBOutlet weak var tctAssignToMentee: TextFieldPadding!
    @IBOutlet weak var btnCreateMeetingOutlet: UIButton!
    @IBOutlet weak var lblHeaderTitle: UILabel!
    
    var assignValueAfterPOP : (()-> Void)? = nil

     var arrMenteeList = [NSDictionary]()
     var dateForServer = ""
     var timeForServer = ""

    var dummyArray = [[String:Any]]()
     var menteeName: [String] = []
     var menteeIndex: [String] = []
     var schoolIdIndex: [String] = []
     var mentorIdForServer = ""
    var menteeeSchoolCustom: [String] = []
    var latString = ""
    var logString = ""
    
    var arrMyMenteeListForEdit : NSDictionary = [:]
    var arrMentorSchoolList = [[String:Any]]()
    var arrSessionMethod = [[String: Any]]()
    var sessionLocation: [String] = []
    var sessionLocationId: [String] = []
    var schoolName: [String] = []
    var schoolIndex: [String] = []
    var schoolIdForServer = ""
    var arrSessionSpace: [String] = []

    var idUseForMeetingEdit = ""
    
    var checkIamFrom = ""
    
    var isScrollThePicker: Bool = false
    
    var editDateandTime = ""
    
    let eventStore = EKEventStore()
    
    var exactMenteename: String = ""
    
    var sessionLocationIdSeraver: String = ""

    var valueMode : String?

    
    
    @IBAction func sessionMethodlocaionAction(_ sender: Any) {
        uiForsessionMethodList()
    }
    
    
    
    func uiForsessionMethodList() {
        if self.sessionLocationId.count > 0 {  //arrMentorSchoolList.count
            self.sessionLocationIdSeraver = self.sessionLocationId[0]
            self.txtSessionMethodlocation.text = self.sessionLocation[0] //self.menteeIndex[0]//self.schoolName[0]
            
            let alert = UIAlertController(style: .alert, title: "Session Method", message: "Select the Session Method")
            
            let pickerViewValues: [[String]] = [self.sessionLocation.map { String($0).description }]
            
            alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
                DispatchQueue.main.async {
                    UIView.animate(withDuration: 1) {
                        /*
                        if self.menteeeSchoolCustom.last == "Affiliate office"
                        {
                            self.schoolIdForServer = "0" //self.menteeName[index.row]
                        }
                            */
                      //  else {
                            self.sessionLocationIdSeraver = self.sessionLocationId[index.row]
                     //   }
                       // self.schoolIdForServer = self.menteeName[index.row]//self.schoolIndex[index.row]
                        self.txtSessionMethodlocation.text = self.sessionLocation[index.row] //self.menteeeSchoolCustom[index.row]
                    }
                }
            }
            alert.addAction(title: "Done", style: .cancel)
            alert.show()
        }
    }
    
    
    
    
    @IBAction func assignAction(_ sender: Any) {
        self.uiForShowMenteeList()
    }
    
    @IBAction func locationAction(_ sender: Any) {
        if txtAddress.isUserInteractionEnabled {
            self.uiForShowSchoolList()
        }
        else {
            self.showAlertView(title: "Alert!", msg: "Please Select Mentee", controller: self) {}
        }
        
    }
    
    @IBAction func sessionSpaceAction(_ sender: Any) {
        self.sessionSpace()
    }
    
    @IBAction func dateAction(_ sender: Any) {
        
        if self.connectedToNetwork() {
        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
        let mentorLoginVc = storyBoardMain.instantiateViewController(withIdentifier: "CustomAlertPicker") as! CustomAlertPicker
        mentorLoginVc.isIamFromDate = true
        mentorLoginVc.delegate = self
        mentorLoginVc.modalPresentationStyle = .overCurrentContext
        
        self.tabBarController?.present(mentorLoginVc, animated: true, completion: nil)
        }
        else {
           print("Not working")
        }
        
    }
    
    @IBAction func timeAction(_ sender: Any) {
        if self.connectedToNetwork() {
        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
        let mentorLoginVc = storyBoardMain.instantiateViewController(withIdentifier: "CustomAlertPicker") as! CustomAlertPicker
        mentorLoginVc.isIamFromDate = false
        mentorLoginVc.delegate = self
        mentorLoginVc.modalPresentationStyle = .overCurrentContext
        self.tabBarController?.present(mentorLoginVc, animated: true, completion: nil)
        }
        else {
            print("Not working")
        }
        
    }
    
    @IBAction func btnSpaceAction(_ sender: Any) {
        self.uiForShowMenteeList()
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    func sessionSpace() {
        self.isScrollThePicker = false

       // self.arrSessionSpace.removeAll()
      //  self.arrSessionSpace.append("Take Stock Affiliate Office")
     //   self.arrSessionSpace.append("Student's actual school")
        if self.arrSessionSpace.count > 0 {
            let alert = UIAlertController(style: .alert, title: "Session Space", message: "")
            let pickerViewValues: [[String]] = [self.arrSessionSpace.map { String($0).description }]
            
            alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
                DispatchQueue.main.async {
                    UIView.animate(withDuration: 1) {
                        self.isScrollThePicker = true
                        self.txtSchoolLoc.text = self.arrSessionSpace[index.row]
                    }
                }
            }

           let alertAction = UIAlertAction(title: "Done", style: .default) { (alert) in
            if !self.isScrollThePicker{
                self.txtSchoolLoc.text = self.arrSessionSpace[0]
             }
            }
        
          alert.addAction(alertAction)
          alert.show()
        }
        
    }
    override func viewDidLoad() {
        super.viewDidLoad()
       // self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechnaged()
        self.spaceviewHeightconstant.constant = 0
        self.lbl5.isHidden = true
        
        txtSchoolLoc.delegate = self
        txtMenteeName.text = "Mentor Session"
        txtAddress.isUserInteractionEnabled = false
        // 2
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

        if self.checkIamFrom == "createMeeting" {
            lblHeaderTitle.text = "Schedule Session"
            btnCreateMeetingOutlet.setTitle("Schedule Session", for: UIControl.State.normal)
        } else if self.checkIamFrom == "editMeeting" {
            lblHeaderTitle.text = "Edit Meeting"
            btnCreateMeetingOutlet.setTitle("UPDATE", for: UIControl.State.normal)
            self.uiLoaddataForEdit(dicData: arrMyMenteeListForEdit)
        } else {
            lblHeaderTitle.text = "Reschedule Meeting"
            btnCreateMeetingOutlet.setTitle("Reschedule Meeting", for: UIControl.State.normal)
            self.uiLoaddataForEdit(dicData: arrMyMenteeListForEdit)
        }
        
        getMenteeList()
        getMentorSchoolList()
        getSessionMethod()
      //  getSchoolListNew()
    }
    
    
    func darkmodechnaged() {
        if self.valueMode == "dark" {
            
            
            
            mainView.backgroundColor = .black
            scrollbackground.backgroundColor = .black
            HeaderView.backgroundColor = .black
            
            
            firstView.backgroundColor = UIColor(hexString: "#0E0F27")
            SecondView.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            thordview.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            fourView.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            fiveView.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            sicVew.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            sevenview.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            eightView.backgroundColor = UIColor(hexString: "#0E0F27")//UIColor(hex: "#0E0F27")
            
            
            lbl1.textColor = .white
            lbl2.textColor = .white
            lbl3.textColor = .white
            lbl4.textColor = .white
            lbl5.textColor = .white
            lbl6.textColor = .white
            lbl7.textColor = .white
            lbl8.textColor = .white
            
            
           // txtMenteeName.attributedPlaceholder = NSAttributedString(string: "Email",
            //attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtMenteeName.textColor = .white
            
            
            
          //  tctAssignToMentee.attributedPlaceholder = NSAttributedString(string: "Email",
        //    attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            tctAssignToMentee.textColor = .white
            
            
          //  txtAddress.attributedPlaceholder = NSAttributedString(string: "Email",
          //  attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtAddress.textColor = .white
            
            
          //  txtSchoolLoc.attributedPlaceholder = NSAttributedString(string: "Email",
          //  attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtSchoolLoc.textColor = .white
            
            
           // txtDate.attributedPlaceholder = NSAttributedString(string: "Email",
            //attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtDate.textColor = .white
            
            
           // txtTimeFrom.attributedPlaceholder = NSAttributedString(string: "Email",
           // attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtTimeFrom.textColor = .white
            
        //    txtSessionMethodlocation.attributedPlaceholder = NSAttributedString(string: "Email",
        //    attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            txtSessionMethodlocation.textColor = .white
            
            scrollView.backgroundColor = UIColor(hexString: "#0E0F27")
            
            
            
        }
        else if self.valueMode == "light" {
            
            scrollView.backgroundColor = UIColor(hex: "")
            
            
            mainView.backgroundColor = .white
            scrollbackground.backgroundColor = .white
         //   HeaderView.backgroundColor = .white
            
            lbl1.textColor = .black
            lbl2.textColor = .black
            lbl3.textColor = .black
            lbl4.textColor = .black
            lbl5.textColor = .black
            lbl6.textColor = .black
            lbl7.textColor = .black
            lbl8.textColor = .black
            
            
            
            firstView.backgroundColor = .white
            SecondView.backgroundColor = .white
            thordview.backgroundColor = .white
            fourView.backgroundColor = .white
            fiveView.backgroundColor = .white
            sicVew.backgroundColor = .white
            sevenview.backgroundColor = .white
            eightView.backgroundColor = .white
            
            
            
            
            
                      // txtMenteeName.attributedPlaceholder = NSAttributedString(string: "Email",
                       //attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtMenteeName.textColor = .black
                       
                       
                       
                     //  tctAssignToMentee.attributedPlaceholder = NSAttributedString(string: "Email",
                   //    attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       tctAssignToMentee.textColor = .black
                       
                       
                     //  txtAddress.attributedPlaceholder = NSAttributedString(string: "Email",
                     //  attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtAddress.textColor = .black
                       
                       
                     //  txtSchoolLoc.attributedPlaceholder = NSAttributedString(string: "Email",
                     //  attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtSchoolLoc.textColor = .black
                       
                       
                      // txtDate.attributedPlaceholder = NSAttributedString(string: "Email",
                       //attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtDate.textColor = .black
                       
                       
                      // txtTimeFrom.attributedPlaceholder = NSAttributedString(string: "Email",
                      // attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtTimeFrom.textColor = .black
                       
                   //    txtSessionMethodlocation.attributedPlaceholder = NSAttributedString(string: "Email",
                   //    attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       txtSessionMethodlocation.textColor = .black
            
        }
    }
    
    
    func uiLoaddataForEdit(dicData : NSDictionary) {
        print("dicdata",dicData)
        var assignedMenteeName = ""
       
        idUseForMeetingEdit =  "\(dicData["id"] as? NSNumber ?? 0)"
        txtMenteeName.text = dicData["title"] as? String
        txtVDesc.text = dicData["description"] as? String
        txtAddress.text = dicData["school_name"] as? String
        txtSchoolLoc.text = dicData["school_location"] as? String
        txtDate.text = dicData["date"] as? String
        txtSessionMethodlocation.text = dicData["method_value"] as? String
        
        let time_from = dicData["time"] as? String
        let date = dicData["date"] as? String ?? ""
        let time = dicData["time"] as? String ?? ""
        editDateandTime = "\(date)\(" ")\(time)"
        let dateFormatter2 = DateFormatter()
        dateFormatter2.dateFormat = "HH:mm:ss"
        
        let dt = dateFormatter2.date(from: time_from!)
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        let strDate = dateFormatter.string(from: dt!)
        self.txtTimeFrom.text = strDate
        
        latString = (dicData["latitude"] as? String)!
        logString = (dicData["longitude"] as? String)!
        
        timeForServer = time_from!
        dateForServer = dicData["date"] as! String

        let arrOfMenteeList = dicData["mentees"] as? NSArray
        
        if let menteesList = arrOfMenteeList {
            
            if menteesList.count==1{
                
                
                let dicMenteeList = menteesList[0] as! NSDictionary
                
                let firstName = dicMenteeList["firstname"] as? String ?? ""
                
                let lastname = dicMenteeList["lastname"] as? String ?? ""
                
                let middlename = dicMenteeList["middlename"] as? String ?? ""

                assignedMenteeName = "\(firstName) \(middlename) \(lastname)"
                
                
                self.mentorIdForServer = "\(dicMenteeList["id"] as! NSNumber)" //(dicMenteeList["id"] as! NSNumber)
                
            } else {
                for mentee in menteesList {
                    
                    let dicMenteeList = mentee as! NSDictionary
                    
                    let firstName = dicMenteeList["firstname"] as? String ?? ""
                    
                    let lastname = dicMenteeList["lastname"] as? String ?? ""
                    
                    let middlename = dicMenteeList["middlename"] as? String ?? ""

                    assignedMenteeName += "\(firstName) \(middlename) \(lastname),"
                    
                    
                }
            }
            tctAssignToMentee?.text = assignedMenteeName
        } else {
            tctAssignToMentee?.text = totalName(dicData: dicData)
        }
    }
    
    override func totalName(dicData:NSDictionary) -> String {
        var TotalName = ""
        
        let firstName = dicData["firstname"] as? String
        let lastName = dicData["lastname"] as? String
        let middlename = dicData["middlename"] as? String
        
        if let fName = firstName{
            TotalName = fName
        }
        
        if let middleName = middlename{
            if middleName != ""{
                TotalName += " \(middleName)"
            }
        }
        
        if let LName = lastName{
            if LName != ""{
                TotalName += " \(LName)"
            }
        }
        
        return TotalName
    }
    @IBAction func btnAssignToMentee(_ sender: Any) {
        
        
        if self.connectedToNetwork() {
        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
               let mentorLoginVc = storyBoardMain.instantiateViewController(withIdentifier: "CustomAlertPicker") as! CustomAlertPicker
               mentorLoginVc.isIamFromDate = false
               mentorLoginVc.delegate = self
               mentorLoginVc.modalPresentationStyle = .overCurrentContext
               self.tabBarController?.present(mentorLoginVc, animated: true, completion: nil)
        }
        else {
            print("Not working")
        }
        // self.uiForShowMenteeList()
    }
    
    @IBAction func timeFromAction(_ sender: Any){
        
        if self.connectedToNetwork() {
            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
            let mentorLoginVc = storyBoardMain.instantiateViewController(withIdentifier: "CustomAlertPicker") as! CustomAlertPicker
            mentorLoginVc.isIamFromDate = true
            mentorLoginVc.delegate = self
            mentorLoginVc.modalPresentationStyle = .overCurrentContext
            
            self.tabBarController?.present(mentorLoginVc, animated: true, completion: nil)
        }
        else {
            print("Not working")
        }
    }
    
    
//    {
//
//        var timeZone = ""
//
//        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
//
//        if loginMode == "Mentor" {
//          timeZone = TakeStockInChildrenConstant.mentorTimeZone
//        }else{
//            timeZone = TakeStockInChildrenConstant.menteeTimeZone
//
//        }
//
//        print(timeZone)
//
//             let now: Date = Date()
//              let dateFormatter: DateFormatter = DateFormatter()
//              dateFormatter.dateStyle = .short
//              dateFormatter.timeStyle = .short
//
//              // Now in New York time
//              let nyTimeZone: TimeZone = TimeZone(identifier: timeZone)!
//              dateFormatter.timeZone = nyTimeZone
//              print(dateFormatter.string(from: now))
//
//             let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
//
//        print(nydate)
//
//
//        let alert = UIAlertController(style: .actionSheet, title: "Meeting", message: "Select Meeting time")
//        alert.addDatePicker(mode: .time, date: nydate, minimumDate: nydate, maximumDate: nil) { date in
//
//            let serverTime = self.changeTimeZoneToNewYark(now: date, dateFormate: "HH:mm:ss")
//            self.timeForServer = serverTime
//
//            let timeShowOnTheMobilePicker = self.changeTimeZoneToNewYark(now: date, dateFormate: "hh:mm a")
//
//            self.txtTimeFrom.text = timeShowOnTheMobilePicker
//
////            let dateFormatter2 = DateFormatter()
////            dateFormatter2.dateFormat = "HH:mm:ss"
////
////            let dt = dateFormatter2.string(from: date)
////            print(dt)
////
////            let dateFormatter = DateFormatter()
////            dateFormatter.dateFormat = "hh:mm a"
////            let strDate = dateFormatter.string(from: date)
////
//
//           // self.dateForServer = dt
//
//
//          //  print(strDateServer)
//            // self.dateForServer = strDateServer
//
//        }
//        alert.addAction(title: "Done", style: .cancel)
//        alert.show()
//    }

    
    func changeTimeZoneToNewYark(now: Date , dateFormate: String) -> String{
       // let now: Date = Date()
        let dateFormatter: DateFormatter = DateFormatter()
        dateFormatter.dateStyle = .short
        dateFormatter.timeStyle = .short
        var timeZone = ""
               
               let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                      
               if loginMode == "Mentor" {
                 timeZone = TakeStockInChildrenConstant.mentorTimeZone
               }else{
                   timeZone = TakeStockInChildrenConstant.menteeTimeZone

               }
        // Now in New York time
        let nyTimeZone: TimeZone = TimeZone(identifier: timeZone)!
        dateFormatter.timeZone = nyTimeZone
        dateFormatter.dateFormat = dateFormate
        return dateFormatter.string(from: now)
    }
    
    @IBAction func btnSelectDate(_ sender: Any){
        
//        let date = Date()
//        let result = changeTimeZoneToNewYark(now: date, dateFormate: "MM-dd-yyyy")
//        
//        self.txtDate.text = result
//        self.dateForServer = result
        
        
        
    }
    
//    {
//
//
//
//        let now: Date = Date()
//        let dateFormatter: DateFormatter = DateFormatter()
//        dateFormatter.dateStyle = .short
//        dateFormatter.timeStyle = .short
//        var timeZone = ""
//
//               let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
//
//               if loginMode == "Mentor" {
//                 timeZone = TakeStockInChildrenConstant.mentorTimeZone
//               }else{
//                   timeZone = TakeStockInChildrenConstant.menteeTimeZone
//
//               }
//
//        print(timeZone)
//        // Now in New York time
//        let nyTimeZone: TimeZone = TimeZone(identifier: timeZone)!
//        dateFormatter.timeZone = nyTimeZone
//        print(dateFormatter.string(from: now))
//
//        let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
//
//        let date = Date()
//        let result = changeTimeZoneToNewYark(now: date, dateFormate: "MM-dd-yyyy")
//
//        self.txtDate.text = result
//        self.dateForServer = result
//
//        let alert = UIAlertController(style: .actionSheet, title: "Meeting", message: "Select Meeting date")
//
//        var comps = DateComponents()
//        comps.year = 1
//        let maximumDate = Calendar(identifier: .gregorian).date(byAdding: comps, to: Date())
//
//        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: maximumDate) { date in
//
//            let result = self.changeTimeZoneToNewYark(now: date, dateFormate: "MM-dd-yyyy")
//
//            //  let dateFormatter = DateFormatter()
//            // dateFormatter.dateFormat = "MM-dd-yyyy"
//            // let strDate = dateFormatter.string(from: date)
//            self.txtDate.text = result
//
//            //  let strDateServer = dateFormatter.string(from: date)
//            self.dateForServer = result
//            print(result)
//
//        }
//        alert.addAction(title: "Done", style: .cancel)
//        alert.show()
//    }
    
    @IBAction func btnSearchLocation(_ sender: Any) {
        self.uiForShowSchoolList()
        /*let alert = UIAlertController(style: .alert)
        alert.addLocationPicker(completion: { (locationPrint) in

            self.latString = "\(locationPrint!.coordinate.latitude)"
            self.logString = "\(locationPrint!.coordinate.longitude)"
            self.txtAddress.text = locationPrint?.address
        })
        alert.addAction(title: "Cancel", style: .cancel)
        alert.show()*/
        
    }
    
    func uiForShowMenteeList() {
        if self.menteeIndex.count > 0 {
        self.txtAddress.isUserInteractionEnabled = true
        self.mentorIdForServer = self.menteeIndex[0]
        self.tctAssignToMentee.text = self.menteeName[0]
        self.exactMenteename = self.menteeName[0]
        
        let alert = UIAlertController(style: .alert, title: "Assign To", message: "Select  a mentee to assign")
        
        // let frameSizes: [String] = ["hello","niraj"]
        let pickerViewValues: [[String]] = [self.menteeName.map { String($0).description }]
        
        alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
            DispatchQueue.main.async {
                UIView.animate(withDuration: 1) {
                    self.mentorIdForServer = self.menteeIndex[index.row]
                    self.tctAssignToMentee.text = self.menteeName[index.row]
                    self.exactMenteename = self.menteeName[index.row]
                   // self.getMenteeList()
                }
            }
        }
        alert.addAction(image: nil, title: "Done", color: UIColor.blue, style: .default, isEnabled: true) { action in
            
            print("menteename------",self.exactMenteename)
                self.getMenteeList()
            }
       // alert.addAction(title: "Done", style: .cancel)
        alert.show()
        
    }
    }
    
    func uiForShowSchoolList() {
        //
        print("school index", self.schoolIdIndex)
        if menteeeSchoolCustom.count > 0 {  //arrMentorSchoolList.count
            self.schoolIdForServer = self.schoolIdIndex[0]
            self.txtAddress.text = self.menteeeSchoolCustom[0] //self.menteeIndex[0]//self.schoolName[0]
            self.setupUpdateconstrint()
         
            let alert = UIAlertController(style: .alert, title: "Name of School", message: "Select the Name of School")
            
            let pickerViewValues: [[String]] = [self.menteeeSchoolCustom.map { String($0).description }]
            
            alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
                DispatchQueue.main.async {
                    UIView.animate(withDuration: 1) {
                        print("pick")
                        /*
                        if self.menteeeSchoolCustom.last == "Affiliate office"
                        {
                            self.schoolIdForServer = "0" //self.menteeName[index.row]
                        }
                            */
                      //  else {
                            self.schoolIdForServer = self.schoolIdIndex[index.row]
                        
                        /*
                        
                        */
                     //   }
                       // self.schoolIdForServer = self.menteeName[index.row]//self.schoolIndex[index.row]
                        self.txtAddress.text = self.menteeeSchoolCustom[index.row]
                        print("set")
                        self.setupUpdateconstrint()
                        //self.menteeeSchoolCustom[index.row]
                        
                    }
                }
            }
          //  alert.set(title: "Done", font: .systemFont(ofSize: 20), color: .blue)
            alert.addAction(title: "Done", style: .cancel)
            alert.show()

        }
    }

    
    func setupUpdateconstrint() {
        print("func called")
        if self.schoolIdForServer == "0" {
            self.spaceviewHeightconstant.constant = 0
            self.lbl5.isHidden = true
            self.scrollView.layoutIfNeeded()
            print("0 work")
        }
        else if self.schoolIdForServer == "500" {
            self.spaceviewHeightconstant.constant = 0
            self.lbl5.isHidden = true
            self.scrollView.layoutIfNeeded()
            print("1 work")
        }
        else {
            self.spaceviewHeightconstant.constant = 80
            self.lbl5.isHidden = false
            self.scrollView.layoutIfNeeded()
            print("2 work")
        }
        
    }
    
    
    
    func validateTextFileBeforeSubmit() {
        
        let date = Date()
        let calendar = Calendar.current
        let hour = String(calendar.component(.hour, from: date))
        let minutes = String(calendar.component(.minute, from: date))
        let concateHT = "hour + minutes"
        
        print("Hour and minute",hour,minutes,concateHT)
        
        /*
        if (txtMenteeName.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Enter Agenda Name", controller: self) {}
        }
        */
        if (txtAddress.text!.isEmpty){
            self.showAlertView(title: "Alert!", msg: "Please Select Location", controller: self) {}
        }
            
        else if (tctAssignToMentee.text!.isEmpty){
            self.showAlertView(title: "Alert!", msg: "Missed assign to", controller: self) {}
        }
        else if (txtSessionMethodlocation.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please select Session Method", controller: self) {}
        }
//        else if (txtVDesc.text!.isEmpty){
//            self.showAlertView(title: "Alert!", msg: "Description is missing", controller: self) {}
//        }
        /*
        else if (txtSchoolLoc.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Enter Session space", controller: self) {}
        }
        */
        else if (txtDate.text!.isEmpty){
            self.showAlertView(title: "Alert!", msg: "Please select Session date", controller: self) {}
        } else if (txtTimeFrom.text!.isEmpty){
            self.showAlertView(title: "Alert!", msg: "Please select Session Time", controller: self) {}
        }
         else{
            
            if self.checkIamFrom == "editMeeting" {
                self.readEvents(editDateandTime.toDate())
                self.addMeetingServiceCall()
            }
            else if self.checkIamFrom == "editMeeting" {
                self.readEvents(editDateandTime.toDate())
                self.addMeetingServiceCall()
            }
            else {
                self.addMeetingServiceCall()
            }
            
        }
       
    }
    

    @IBAction func btnBackAction(_ sender: UIButton) {
        
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnAddSession(_ sender: UIButton) {
        
        validateTextFileBeforeSubmit()
    }
    
    
    func getSchoolListNew() {
        MentorApiManager.mentorSharedInstance.schoolListing(onSuccess: { json in
            DispatchQueue.main.async {
                print("PENDINGJSON\(json)")
                DispatchQueue.main.async(execute: {() -> Void in
                    self.arrSessionSpace.removeAll()
                   self.dummyArray = (json["data"] as! NSArray) as! [[String : Any]]
                    for item in self.dummyArray {
                        let name = item["name"] as? String ?? ""
                        self.arrSessionSpace.append(name)
                    }
                })
            }
        }, onFailure: { error in
            DispatchQueue.main.sync(execute: {() -> Void in
                self.stopActivityIndicator()
                self.logOutMentor()
                /*
                let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                self.present(alert, animated: true, completion: nil)
                */
                //self.actInd.stopAnimating()
                //                        self.viewBgLoading.isHidden = true
                
            })
        })
        
        
        
    }
    
    
    
    
    
    
    
    
    
    //MARK:- API Service Call
    func getMentorSchoolList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            MentorApiManager.mentorSharedInstance.getMentorSchoolList(onSuccess: { json in
                DispatchQueue.main.async {
                    print("MentorSchoolList json  sucess:: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        self.arrMentorSchoolList = json["data"] as! [[String : Any]]
                        print("MentorSchoolList Response\(self.arrMentorSchoolList)")
                        
                        
                        self.schoolName = []
                        self.schoolIndex = []
                        
                        if json["status"] as! Bool == true {
                            self.arrMentorSchoolList = json["data"] as! [[String : Any]]
                            for item in self.arrMentorSchoolList {
                                
                                self.schoolIndex.append("\(item["id"] ?? "nil")")
                                var TotalName = ""
                                
                                let strSchoolName = item["name"] as? String ?? ""
                                
                                
                                self.schoolName.append(strSchoolName)
                                self.arrSessionSpace.append(strSchoolName)
                                print("self.schoolName\(self.schoolName)")
                                
                                
                                //self.menteeName = [TotalName]
                                
                                //  print(item["firstname"] as! String)
                            }
                            //  print(self.menteeName)
                        }
                        self.stopActivityIndicator()
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.logOutMentor()
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    
    //set a calendar
    
    func insertEvent(store: EKEventStore) {
        
          print("insert event")
          let event:EKEvent = EKEvent(eventStore: store)
          let startDate = Date()
          // 2 hours
        _ = startDate.addingTimeInterval(2 * 60 * 60)
        //let reversed = String(str.reversed())

        event.title = self.txtMenteeName.text
        print("dates--",dateForServer.toDate())
        let concateDateAndtime = "\(dateForServer)\(" ")\(timeForServer)"
        print("concat",concateDateAndtime.toDate())
        event.startDate = concateDateAndtime.toDate()
        event.endDate = concateDateAndtime.toDate().addingTimeInterval(1 * 60 * 60)
        event.notes = self.txtVDesc.text
          event.calendar = store.defaultCalendarForNewEvents
          do {
              try store.save(event, span: .thisEvent)
          } catch let error as NSError {
          print("failed to save event with error : \(error)")
          }
          print("Saved Event")
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
    
    
    
    
    
    func getSessionMethod() {
        var parameter = [String:Any]()
        parameter["mentee_id"] = self.mentorIdForServer
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.getsession_method_Location)
            print("gesessionmethod\(url)")
            Alamofire.request(url, method:.post, parameters:parameter, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print("res--",response)
                    self.stopActivityIndicator()
                    // ApiUtillity.sharedInstance.dismissSVProgressHUD()
                    let dictVal = response.result.value
                    //  print("dictVal\(dictVal)")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    let dictdata: NSDictionary = dictMain["data"] as! NSDictionary
                     print("dictMain getsessionmethod\(dictdata)")
                    self.sessionLocation = []
                    self.sessionLocationId = []
                    
                    if dictMain["status"] as! Bool == true {
                        self.arrSessionMethod = (dictdata["session_method_location"] as! [[String : AnyObject]])
                        print("data----",self.arrSessionMethod)
                        
                        for item in self.arrSessionMethod {
                            self.sessionLocationId.append("\(item["id"] ?? "nil" as Any)")
                            let methodvalue = item["method_value"] as? String ?? ""
                            self.sessionLocation.append(methodvalue)
                            print("self.menteename\(self.sessionLocation)")
                            
                        }
                    }
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    self.logOutMentor()
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    

    
    
    
    func getMenteeList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeList)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print("get mentee list sucess",response)
                    self.stopActivityIndicator()
                    // ApiUtillity.sharedInstance.dismissSVProgressHUD()
                    let dictVal = response.result.value
                    //  print("dictVal\(dictVal)")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    // print("dictMain\(dictMain)")
                    self.menteeName = []
                    self.menteeIndex = []
                    self.schoolIdIndex = []
                    
                    if dictMain["status"] as! Bool == true {
                        self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                        print("data----",self.arrMenteeList)
                        
                        for item in self.arrMenteeList {
                            
                            
                            
                            self.menteeIndex.append("\(item["id"] ?? "nil")")
                            var TotalName = ""
                            
                            let firstName = item["firstname"] as? String
                            let lastName = item["lastname"] as? String
                            let middlename = item["middlename"] as? String
                            
                            if let fName = firstName {
                                TotalName = fName
                            }
                            
                            if let middleN = middlename {
                                if middleN != "" {
                                    
                                    TotalName += " \(middleN)"
                                    //TotalName = " \(LName)"
                                }
                            }
                            
                            if let LName = lastName {
                                if LName != "" {
                                    TotalName += " \(LName)"
                                    //TotalName = " \(LName)"
                                }
                            }
                            
                             self.menteeName.append(TotalName)
                            
                            if self.exactMenteename != "" {
                                
                                print("total name",TotalName)
                                print("exact name", self.exactMenteename)
                            
                            if TotalName == self.exactMenteename {
                                self.schoolIndex.removeAll()
                                self.menteeeSchoolCustom.removeAll()
                                self.schoolIdIndex.append("\(item["school_id"] ?? "nil")")
                                
                                let schoolname = item["school_name"] as? String ?? ""
                                
                                self.menteeeSchoolCustom.append(schoolname)
                                
                                print("mentee index",self.menteeIndex)
                                self.schoolIdIndex.append("0")
                                self.schoolIdIndex.append("500")
                                print("schoolid",self.schoolIdIndex)
                                self.menteeeSchoolCustom.append("Affiliate Office")
                                self.menteeeSchoolCustom.append("Virtual Session")
                                
                                print("self.menteename\(self.menteeName)")
                                
                            }
                            
                            }
                            else {
                                print("Not set")
                            }
                           
                            
                            
                        }
                        
                        
                        print("name0000",self.menteeeSchoolCustom)
                    }
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    self.logOutMentor()
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
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
        
    
    
    func addMeetingServiceCall() {
        var parameter = [String:String]()
        parameter["title"] = txtMenteeName.text
        parameter["description"] = txtVDesc.text
        parameter["date"] = dateForServer
        parameter["time"] = timeForServer
        parameter["school_space"] = txtSchoolLoc.text
        parameter["mentee_id"] = self.mentorIdForServer
        parameter["session_method_location_id"] = self.sessionLocationIdSeraver
        print("schollid---",self.schoolIdForServer)
        if self.schoolIdForServer == "0" {
            parameter["school_id"] = ""
            parameter["school_type"] = "Affiliate Office"
        }
        else if self.schoolIdForServer == "500" {
            parameter["school_id"] = ""
            parameter["school_type"] = "Virtual Session"
        }
        else {
            parameter["school_type"] = ""
            parameter["school_id"] = self.schoolIdForServer
        }
       
        
        
        if arrMyMenteeListForEdit.count != 0 {
            parameter["id"] = self.idUseForMeetingEdit
        }
        
        print("schedule param",parameter)
        if self.connectedToNetwork() {
        self.startActivityIndicator()
        self.view.isUserInteractionEnabled = false
        MentorApiManager().meetingCreation(parameter: parameter, completion: { (response) in
            self.stopActivityIndicator()
            let status = response["status"] as! Bool
            var style = ToastStyle()
            style.messageColor = .white
            
            if status == true {
                self.UI {
                    self.view.makeToast(response["message"] as? String ?? "", duration: 1.0, position: .center, title: nil, image: nil, style: style, completion: { (didTap) in
                        self.assignValueAfterPOP!()
                        
                        self.view.isUserInteractionEnabled = true
                        let myalert = UIAlertController(title: "Title", message: "Would you like to save this meeting to your calendar?", preferredStyle: UIAlertController.Style.alert)

                        myalert.addAction(UIAlertAction(title: "Yes", style: .default) { (action:UIAlertAction!) in

                                self.insertEvent(store: self.eventStore)

                            self.navigationController?.popViewController(animated: true)
                            })
                        
                        myalert.addAction(UIAlertAction(title: "No", style: .default) { (action:UIAlertAction!) in
                        self.navigationController?.popViewController(animated: true)
                        })
                        
                            self.present(myalert, animated: true)

                        
                        
                        
                    })
                }
            } else {
                self.UI {
                   // self.logOutMentor()
                    self.view.isUserInteractionEnabled = true
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                  
                }
            }
        })
        }
        else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }

}
extension MentorMeetingCreatorVC: DateAndTimeDelegate {
    func getDate(dateAndTimeforServer: String, dateAndTimeForTextField: String) {
        print("datess",dateAndTimeforServer)
        self.dateForServer = dateAndTimeforServer
        self.txtDate.text = dateAndTimeForTextField
    }
    
    func getTime(dateAndTimeforServer: String, dateAndTimeForTextField: String) {
        print("timess",dateAndTimeforServer)
        self.timeForServer = dateAndTimeforServer
        self.txtTimeFrom.text = dateAndTimeForTextField
    }
}

//MARK:-> Date Extention
/*
extension String {
  func toDate(withFormat format: String = "dd-MM-yyyy") -> Date {
    let dateFormatter = DateFormatter()
    dateFormatter.dateFormat = format
    guard let date = dateFormatter.date(from: self) else {
      preconditionFailure("Take a look to your format")
    }
    return date
  }
}
*/

extension String {
    func toDate(byFormat : String = "MM-dd-yyyy HH:mm:ss" , byZone : String = "America/New_York") -> Date {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = byFormat
        if byZone != "" {
            dateFormatter.timeZone = TimeZone(abbreviation: byZone) // "UTC"
        }
        let outdate = dateFormatter.date(from: self) ?? Date()
        return outdate
    }
}


extension String {
    func EditByDate(byFormat : String = "MM-dd-yyyy HH:mm:ss" , byZone : String = "UTC") -> Date {
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = byFormat
        if byZone != "" {
            dateFormatter.timeZone = TimeZone(abbreviation: byZone) // "UTC"
        }
        let outdate = dateFormatter.date(from: self) ?? Date()
        return outdate
    }
}


extension MentorMeetingCreatorVC: UITextFieldDelegate {
    
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        print("delegate called")
        let currentCharacterCount = textField.text?.count ?? 0
        if range.length + range.location > currentCharacterCount {
            return false
        }
        let newLength = currentCharacterCount + string.count - range.length
        return newLength <= 40
    }
    
    
}
