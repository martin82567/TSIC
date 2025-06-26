
import UIKit
//import IBAnimatable
import ImagePicker
import CoreLocation
import Alamofire
//import RLBAlertsPickers

class ShowSessionViewController: BaseViewController, UITextFieldDelegate,UITextViewDelegate {
    var obj: NSDictionary!
    
    
    
    var mettingId: String?
    @IBOutlet weak var descriptionView: UIView!
    @IBOutlet weak var AgendaTextfield: TextFieldPadding!
    
    
    
    
    @IBOutlet weak var descriptionTesxtfield: TextViewPadding!
    @IBOutlet weak var agennameView: UIView!
    
    
    
    @IBOutlet weak var mainview: UIView!
    @IBOutlet weak var scroolsubview: UIView!
    @IBOutlet weak var scrollview: UIScrollView!
    @IBOutlet weak var firstlbl: UILabel!
    @IBOutlet weak var secondlabel: UILabel!
    @IBOutlet weak var fourlbl: UILabel!
    
    @IBOutlet weak var thirdlbl: UILabel!
    @IBOutlet weak var fiveview: UIView!
    @IBOutlet weak var sixlbl: UILabel!
    @IBOutlet weak var fivthlabel: UILabel!
    
    @IBOutlet weak var sevenview: UIView!
    @IBOutlet weak var sixview: UIView!
    @IBOutlet weak var fourview: UIView!
    @IBOutlet weak var thirdview: UIView!
    @IBOutlet weak var secondview: UIView!
    @IBOutlet weak var firstview: UIView!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var txtSessionType: TextFieldPadding!
    @IBOutlet weak var txtSessionMethod: TextFieldPadding!
    
    @IBOutlet weak var txtTitle: UITextField!
    @IBOutlet weak var txtMenteeName: TextFieldPadding!
    @IBOutlet weak var txtAddress: TextFieldPadding!
    @IBOutlet weak var txtDate: TextFieldPadding!
    @IBOutlet weak var txtSessionDuration: TextFieldPadding!
    @IBOutlet weak var txtVSessionNotes: TextViewPadding!
    @IBOutlet weak var noShowButton: UIButton!
    @IBOutlet weak var sessionDuration: UIButton!
    
    var arrMenteeList = [NSDictionary]()
    var arrSessionMethod = [[String: Any]]()
    var dateForServer = ""
    var menteeName: [String] = []
    var menteeIndex: [String] = []
    var sessionLocation: [String] = []
    var sessionLocationId: [String] = []
    var mentorIdForServer = ""
    var assignValueAfterPOP : (()-> Void)? = nil
    var timeFromMinute : Int?
    var timeToConvertedToMinute : Int?
    var timeFromConvertedToMinute : Int?
    var sessionLocationIdSeraver: String = ""
    var sessionTypeServer: String = ""
    var selectdate = Date()
    var arrSessionDuration: [String] = []
    var arrSessionType: [String] = []
    var pickerData : UIPickerView!
    let datepikcer = UIDatePicker()
    var valueMode: String?
    var noShowSelected : Bool = false

    //MARK:- UIView lifeCycle ☞🙂
    override func viewDidLoad() {
        super.viewDidLoad()
        print("Data----",self.obj)
        setvalues()
        txtVSessionNotes.delegate = self
     //   self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
     //   darkmodeChanged()
        createdatePicker()
        getMenteeList()
        getSessionMethod()
        arrSessionDuration = ["30 minutes", "35 minutes", "40 minutes", "45 minutes", "50 minutes", "55 minutes", "60 minutes", "65 minutes", "70 minutes", "75 minutes", "80 minutes", "85 minutes", "90 minutes"]
        arrSessionType = ["Individual","Group"]
        
        noShowButton.layer.cornerRadius = 2.0
        noShowButton.backgroundColor = .white
        noShowButton.layer.borderColor = UIColor.black.cgColor
        noShowButton.layer.borderWidth = 1.0
    }
    
    
    
    func setvalues() {
        let title = self.obj["title"] as? String ?? ""
        let des = self.obj["description"] as? String ?? ""
        let note = self.obj["note"] as? String ?? ""
       // let dataMentee =
           // self.obj["mentees"] as? NSArray
      //  print("data======",dataMentee)
        let date = self.obj["schedule_time"] as? String ?? ""
        
        let methodVlaue = self.obj["method_value"] as? String ?? ""
        
        
        AgendaTextfield.text =  title
        descriptionTesxtfield.text = des
       // let name = dataMentee?[0] as? NSDictionary
        txtMenteeName.text = self.obj["firstname"] as? String ?? ""
        self.mentorIdForServer = "\(self.obj["mentee_id"] as? NSNumber ?? 0)"
        print("mentee id",self.mentorIdForServer)
        txtDate.text = date
        txtSessionMethod.text = methodVlaue
        txtVSessionNotes.text = note
        self.mettingId = "\(self.obj["id"] as? NSNumber ?? 0)"
        print("Meeting id",self.mettingId as Any)
        self.dateForServer = date
        self.sessionLocationIdSeraver = "\(self.obj["session_method_location_id"] as? Int ?? 0)"
        
    }
    
    
    func darkmodeChanged() {
        if self.valueMode == "dark" {
            
            
            mainview.backgroundColor = .black
            
            
            scrollview.backgroundColor = UIColor(hexString: "#0E0F27")
            
            scroolsubview.backgroundColor = UIColor(hexString: "#0E0F27")
            
            headerView.backgroundColor = .black
            sevenview.backgroundColor = UIColor(hexString: "#0E0F27")
             sixview.backgroundColor = UIColor(hexString: "#0E0F27")
            fourview.backgroundColor = UIColor(hexString: "#0E0F27")
            thirdview.backgroundColor = UIColor(hexString: "#0E0F27")
            secondview.backgroundColor = UIColor(hexString: "#0E0F27")
            firstview.backgroundColor = UIColor(hexString: "#0E0F27")
            fiveview.backgroundColor = UIColor(hexString: "#0E0F27")
            
            
            txtMenteeName.textColor = .white
            txtDate.textColor = .white
            txtSessionDuration.textColor = .white
            txtVSessionNotes.textColor = .white
            txtSessionType.textColor = .white
            txtSessionMethod.textColor = .white
            
            
            firstlbl.textColor = .white
            secondlabel.textColor = .white
            thirdlbl.textColor = .white
            fourlbl.textColor = .white
            fivthlabel.textColor = .white
            sixlbl.textColor = .white
            
            
            
            
        }
        else if self.valueMode == "light" {
            
            mainview.backgroundColor = .white
            
            scrollview.backgroundColor = .white
            
            
            scroolsubview.backgroundColor = .white
            
            
            sevenview.backgroundColor = .white
             sixview.backgroundColor = .white
            fourview.backgroundColor = .white
            thirdview.backgroundColor = .white
            secondview.backgroundColor = .white
            firstview.backgroundColor = .white
            fiveview.backgroundColor = .white
            
            txtMenteeName.textColor = .black
            txtDate.textColor = .black
            txtSessionDuration.textColor = .black
            txtVSessionNotes.textColor = .black
            txtSessionType.textColor = .black
            txtSessionMethod.textColor = .black
            
            
            firstlbl.textColor = .black
            secondlabel.textColor = .black
            thirdlbl.textColor = .black
            fourlbl.textColor = .black
            fivthlabel.textColor = .black
            sixlbl.textColor = .black
            
            
            
            
           // headerView.backgroundColor = .black
        }
    }
    
    //MARK:- METHODS ☞🙂
    func selectLessTime(date:Date) -> Bool {
        let loginInterval = -date.timeIntervalSinceNow
        
        let formatter = DateComponentsFormatter()
        formatter.unitsStyle = .full
        formatter.includesApproximationPhrase = false
        formatter.includesTimeRemainingPhrase = false
        formatter.allowedUnits = [.minute] // [.hour, .minute]
        let userLoginTimeString = formatter.string(from: loginInterval) ?? ""
        let arrSplited = userLoginTimeString.split(separator: " ")
        let checkValue = arrSplited[0]
        let finalValue = checkValue.replacingOccurrences(of: ",", with: "")
        
        if Int(finalValue)! <= 0 {
         return false
        } else {
            return true
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
    
    
    
    
    func uiForShowMenteeList() {
        if self.connectedToNetwork() {
        self.mentorIdForServer = self.menteeIndex[0]
        self.txtMenteeName.text = self.menteeName[0]
        
        let alert = UIAlertController(style: .alert, title: "Mentee", message: "Select Mentee")
        
       // let frameSizes: [String] = ["hello","niraj"]
        let pickerViewValues: [[String]] = [self.menteeName.map { String($0).description }]

        alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
            DispatchQueue.main.async {
                UIView.animate(withDuration: 1) {
                    self.mentorIdForServer = self.menteeIndex[index.row]
                    self.txtMenteeName.text = self.menteeName[index.row]
                }
            }
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
        
        }
        else {
            print("Not working")
        }
        
    }
    
    
    func uiForSessionTypeList() {
        if self.connectedToNetwork() {
            self.txtSessionType.text = self.arrSessionType[0]
            self.sessionTypeServer = self.arrSessionType[0].lowercased()
        
        let alert = UIAlertController(style: .alert, title: "Session Type", message: "Select Session Type")
        
       // let frameSizes: [String] = ["hello","niraj"]
        let pickerViewValues: [[String]] = [self.arrSessionType.map { String($0).description }]

        alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
            DispatchQueue.main.async {
                UIView.animate(withDuration: 1) {
                    self.txtSessionType.text = self.arrSessionType[index.row]
                    self.sessionTypeServer = self.arrSessionType[index.row].lowercased()
                    print("type",self.sessionTypeServer)
                }
            }
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
        
        }
        else {
            print("Not working")
        }
        
    }
    
    func uiForSessionDuration() {
        // self.mentorIdForServer = self.menteeIndex[0]
        self.txtSessionDuration.text = self.arrSessionDuration[0]
        
        let alert = UIAlertController(style: .alert, title: "Session Duration", message: "Select Session Duration")
        
        // let frameSizes: [String] = ["hello","niraj"]
        let pickerViewValues: [[String]] = [self.arrSessionDuration.map { String($0).description }]
        
        alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
            DispatchQueue.main.async {
                UIView.animate(withDuration: 1) {
                    self.txtSessionDuration.text = self.arrSessionDuration[index.row]
                }
            }
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    
    @IBAction func getSessionMethodDropdown(_ sender: Any) {
        print("session method called")
        self.uiForsessionMethodList()
    }
    
    @IBAction func checkNoShow(_ sender: Any) {
        if !noShowSelected {
            noShowButton.setImage(UIImage(named: "checkmark")?.withRenderingMode(.alwaysTemplate), for: .normal)
            noShowSelected = true
            self.sessionDuration.isEnabled = false
            self.txtSessionDuration.isEnabled = false
            self.txtSessionDuration.text = nil
            self.txtSessionDuration.borderColor = UIColor(hexString: "dddddd")
        } else {
            noShowButton.setImage(UIImage(named: "")?.withRenderingMode(.alwaysTemplate), for: .normal)
            noShowSelected = false
            self.sessionDuration.isEnabled = true
            self.txtSessionDuration.isEnabled = true
            self.txtSessionDuration.borderColor = UIColor.lightGray
        }
        noShowButton.imageView?.contentMode = .scaleAspectFit
    }
    
    func uiForsessionMethodList() {
        if self.sessionLocationId.count > 0 {  //arrMentorSchoolList.count
            self.sessionLocationIdSeraver = self.sessionLocationId[0]
            self.txtSessionMethod.text = self.sessionLocation[0] //self.menteeIndex[0]//self.schoolName[0]
            
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
                        self.txtSessionMethod.text = self.sessionLocation[index.row] //self.menteeeSchoolCustom[index.row]
                    }
                }
            }
            alert.addAction(title: "Done", style: .cancel)
            alert.show()
        }
    }
    
    
    
    @IBAction func ActionGetSessionType(_ sender: Any) {
        self.uiForSessionTypeList()
    }
    
    
    
    func getSessionMethod() {
        var param = [String: Any]()
        param["mentee_id"] =  self.mentorIdForServer
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
            Alamofire.request(url, method:.post, parameters:param, headers: headers).responseJSON { response in
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
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    
    
    
    
    
    func validateTextFileBeforeSubmit() {
        if (AgendaTextfield.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Set Agenda Name", controller: self) {}
        }
//        if (descriptionTesxtfield.text.isEmpty) {
//            self.showAlertView(title: "Alert!", msg: "Please Enter Description", controller: self) {}
//        }
        if (txtMenteeName.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Select Mentee Name", controller: self) {}
        }
        else if (txtDate.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Select Date", controller: self) {}
        }
        else if (txtSessionDuration.text!.isEmpty && !self.noShowSelected) {
            self.showAlertView(title: "Alert!", msg: "Please Select Session Duration", controller: self) {}
        }
        else if (txtSessionMethod.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Select Session Method", controller: self) {}
        }
        else if (txtSessionType.text!.isEmpty) {
            self.showAlertView(title: "Alert!", msg: "Please Select Session Type", controller: self) {}
        }
        else if (txtVSessionNotes.text!.isEmpty) {
                self.showAlertView(title: "Alert!", msg: "Please enter short description", controller: self) {}
        }
        else {
            self.addSessionServiceCall()
        }
    }
    
    
    
    func createdatePicker() {
        let toolbar = UIToolbar()
        toolbar.sizeToFit()
        toolbar.backgroundColor = UIColor(hexString: "889300")
        let dontBtn = UIBarButtonItem(barButtonSystemItem: .done, target: nil, action: #selector(donePresed))
        let flexSpace = UIBarButtonItem(barButtonSystemItem: .flexibleSpace, target: nil, action: nil)
        let canclBtn = UIBarButtonItem(barButtonSystemItem: .cancel, target: nil, action: #selector(cancellPressed))
        toolbar.setItems([dontBtn,flexSpace,canclBtn], animated: true)
        
        txtDate.inputAccessoryView = toolbar
        txtDate.inputView = datepikcer
        
        if #available(iOS 13.4, *) {
            datepikcer.preferredDatePickerStyle = .wheels
        } else {
            // Fallback on earlier versions
        }
        
        datepikcer.datePickerMode = .date
        datepikcer.maximumDate = Date()
        
        datepikcer.backgroundColor = UIColor(hexString: "889300")
    }
    
    @objc func cancellPressed() {
        self.view.endEditing(true)
    }
    
    @objc func donePresed() {
        let dateFormatter = DateFormatter()
        //dateFormatter.timeZone = TimeZone(identifier: "UTC")
        dateFormatter.dateFormat = "MM-dd-yyyy"
        let strDate = dateFormatter.string(from: datepikcer.date)
        print("strdate",strDate)
        self.txtDate.text = strDate
        
        let strDateServer = dateFormatter.string(from: datepikcer.date)
        self.dateForServer = strDateServer
        
        self.view.endEditing(true)
    }
    
    
    //MARK:: UIButton IBAction ☞🙂
    @IBAction func btnSelectDate(_ sender: Any){
        /*
        let date = Date()
        let formatter = DateFormatter()
        formatter.dateFormat = "MM-dd-yyyy" //"yyyy-MM-dd HH:mm:ss"
       let result = formatter.string(from: date)
        self.txtDate.text = result
        self.dateForServer = result
        
        let alert = UIAlertController(style: .actionSheet, title: "Session", message: "Select session date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate:nil , maximumDate: Date()) { date in
            print("date---",date)
            self.selectdate = date
            let dateFormatter = DateFormatter()
            dateFormatter.timeZone = TimeZone(identifier: "UTC")
            dateFormatter.dateFormat = "MM-dd-yyyy HH:mm:ss'Z'"
            let strDate = dateFormatter.string(from: date)
            print("strdate",strDate)
            self.txtDate.text = strDate
            
            let strDateServer = dateFormatter.string(from: date)
            self.dateForServer = strDateServer
            print(strDateServer)
        }
        alert.addAction(title: "Done", style: .cancel)

        /*
        alert.addAction(UIAlertAction(title: "Done", style: .cancel) { (action:UIAlertAction!) in
            if self.dateForServer == "" {
                let date = Date()
                let formatter = DateFormatter()
                formatter.timeZone = TimeZone(identifier: "UTC")
                formatter.dateFormat = "MM-dd-yyyy HH:mm:ss'Z'" //"yyyy-MM-dd HH:mm:ss"
                let result = formatter.string(from: date)
                self.txtDate.text = result
                self.dateForServer = result
                print("block date",result)
            }
            else {
                print("Already set")
            }
        })
        */
        
        //alert.addAction(title: "Done", style: .cancel)
        /*
         let alertAction = UIAlertAction(title: "Done", style: .default) { (alert) in
         if !self.isScrollThePicker{
             self.txtSchoolLoc.text = self.arrSessionSpace[0]
          }
         }
         */
        alert.show()
        
        */
    }
    
    
//    {
//
//        let now: Date = Date()
//        let dateFormatter: DateFormatter = DateFormatter()
//        dateFormatter.dateStyle = .short
//        dateFormatter.timeStyle = .short
//
//
//        var timeZone = ""
//
//        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
//
//        if loginMode == "Mentor" {
//            timeZone = TakeStockInChildrenConstant.mentorTimeZone
//        }else{
//            timeZone = TakeStockInChildrenConstant.menteeTimeZone
//
//        }
//        // Now in New York time
//        let nyTimeZone: TimeZone = TimeZone(identifier: timeZone)!
//        dateFormatter.timeZone = nyTimeZone
//        print(dateFormatter.string(from: now))
//
//        let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
//
//
//        let date = Date()
//        let formatter = DateFormatter()
//        formatter.dateFormat = "MM-dd-yyyy" //"yyyy-MM-dd HH:mm:ss"
//        let result = formatter.string(from: date)
//        self.txtDate.text = result
//        self.dateForServer = result
//
//        let alert = UIAlertController(style: .actionSheet, title: "Session", message: "Select session date")
//
//
//        var comps = DateComponents()
//        comps.year = -1
//        let minimumDate = Calendar(identifier: .gregorian).date(byAdding: comps, to: Date())
//
//        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nil, maximumDate: Date()) { date in
//
//            self.selectdate = date
//            let dateFormatter = DateFormatter()
//            dateFormatter.dateFormat = "MM-dd-yyyy"
//            let strDate = dateFormatter.string(from: date)
//            self.txtDate.text = strDate
//
//            let strDateServer = dateFormatter.string(from: date)
//            self.dateForServer = strDateServer
//            print(strDateServer)
//        }
//         alert.addAction(title: "Done", style: .cancel)
//         alert.show()
//    }
    
    @IBAction func btnMenteeName(_ sender: Any) {
        //  getMenteeList()
        self.uiForShowMenteeList()
    }
    
    @IBAction func sessionDurationAction(_ sender: Any) {
        self.uiForSessionDuration()
    }
    @IBAction func btnSearchLocation(_ sender: Any) {
        
        let alert = UIAlertController(style: .alert)
        alert.addLocationPicker(completion: { (locationPrint) in
            
            self.txtAddress.text = locationPrint?.address
        })
        alert.addAction(title: "Cancel", style: .cancel)
        alert.show()
    }
    
    @IBAction func btnBackAction(_ sender: UIButton) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnAddSession(_ sender: UIButton) {
        validateTextFileBeforeSubmit()
    }
    
    //MARK:: UITextFieldDelegate ☞🙂
    func textFieldDidBeginEditing(_ textField: UITextField) {
        if textField == txtSessionDuration {
            self.txtSessionDuration.text = arrSessionDuration[0]
            //self.pickUp(txtSessionDuration)
        }
    }
    
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let currentString: NSString = textField.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if newString.length > 30 {
            let alert = UIAlertController(title: "Alert", message: "Title should not more than 30 characters", preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
            self.present(alert, animated: true, completion: nil)
        }
        return newString.length <= 30
    }
    
    
    func textView(_ textView: UITextView, shouldChangeTextIn range: NSRange, replacementText text: String) -> Bool {
        // get the current text, or use an empty string if that failed
        let currentText = textView.text ?? ""

        // attempt to read the range they are trying to change, or exit if we can't
        guard let stringRange = Range(range, in: currentText) else { return false }

        // add their new text to the existing text
        let updatedText = currentText.replacingCharacters(in: stringRange, with: text)

        print("update text count",updatedText.count)
        // make sure the result is under 16 characters
        return updatedText.count <= 1024
    }
    
    //MARK:- API Service Call ☞🙂
    func addSessionServiceCall() {
       
        let arrDuration = txtSessionDuration.text?.components(separatedBy: " ")
        
        let total = ("\(String(describing: timeToConvertedToMinute)) - \(String(describing: timeFromConvertedToMinute))")
        print("total\(total)")
        var parameter = [String:String]()
        parameter["name"] = txtVSessionNotes.text
        // parameter["address"] = txtVSessionNotes.text
        parameter["schedule_date"] = dateForServer
        parameter["time_duration"] = self.noShowSelected ? "0" : arrDuration![0]
        parameter["mentee_id"] = self.mentorIdForServer
        parameter["session_method_location_id"] = self.sessionLocationIdSeraver
        parameter["meeting_id"] = self.mettingId ?? ""
        parameter["no_show"] = self.noShowSelected ? "1" : "0"
        if self.sessionTypeServer == "individual" {
            parameter["type"] = "2"
        }
        else if self.sessionTypeServer == "group" {
            parameter["type"] = "1"
        }
        /*
        else if self.sessionTypeServer == "virtual" {
            parameter["type"] = "3"
        }
        */
       // parameter["type"] = self.sessionTypeServer

        print("param",parameter)
        
        MentorApiManager().sessionCreation(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool
            var style = ToastStyle()
            style.messageColor = .white
            
            if status == true {
                self.UI {
                    
                    self.view.makeToast(response["message"] as? String, duration: 1.0, position: .center, title: nil, image: nil, style: style, completion: { (didTap) in
                         //  self.assignValueAfterPOP!()
                       self.navigationController?.popViewController(animated: true)
                    })
                
                }
            } else {
                self.UI {
                    self.logOutMentor()
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                }
            }
        })
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
                    self.stopActivityIndicator()
                    
                    print(response)
                    // ApiUtillity.sharedInstance.dismissSVProgressHUD()
                    let dictVal = response.result.value
                  //  print("dictVal\(dictVal)")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                   // print("dictMain\(dictMain)")
                    self.menteeName = []
                    self.menteeIndex = []
                    
                    if dictMain["status"] as! Bool == true {
                        self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                        for item in self.arrMenteeList{
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
                            print("self.menteename\(self.menteeName)")
                            //self.menteeName = [TotalName]
                            
                          //  print(item["firstname"] as! String)
                        }
                      //  print(self.menteeName)
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
}
