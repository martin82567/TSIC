//
//  CreateNewTaskViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 22/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class CreateNewTaskViewController: BaseViewController,UITableViewDelegate,UITableViewDataSource,UITextFieldDelegate,UITextViewDelegate{

    @IBOutlet weak var tableViewCreateNew: UITableView!
    
    var strTypeOfNewCreation : String = ""
    var id : Int?
    var dict :[String:String] = ["type" : "task", "name": "", "description" : "" , "start_date" : "" , "end_date" : ""]
    
    static var startDate : String = ""
    static var endDate: String?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        CreateNewTaskViewController.startDate = ""
        CreateNewTaskViewController.endDate = ""
        
        // Do any additional setup after loading the view.
        self.navigationController?.setNavigationBarHidden(true, animated: true)
        self.tabBarController?.tabBar.isHidden = false
        tableViewCreateNew.setContentOffset(.zero, animated:true)
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        //self.navigationController?.setNavigationBarHidden(false, animated: animated)
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
//    //MARK:-TextFieldDelegate
//    // It is called when text field activated
//    func textFieldDidBeginEditing(_ textField: UITextField) {
//        let datePicker = UIDatePicker()
//        datePicker.datePickerMode = UIDatePicker.Mode.date
//        if(textField.tag == 2){
//            textField.inputView = datePicker
//            datePicker.addTarget(self, action: #selector(startDateChanged(caller:)), for: UIControl.Event.valueChanged)
//
//        }else if(textField.tag == 3){
//            textField.inputView = datePicker
//            datePicker.addTarget(self, action: #selector(endDateChanged(caller:)), for: UIControl.Event.valueChanged)
//        }else{
//        }
//    }
//
//    @objc func startDateChanged(caller: UIDatePicker){
//        let dateFormatter = DateFormatter()
//        dateFormatter.dateFormat = "MM-dd-YYYY"
//        CreateNewGoalViewController.startDate = dateFormatter.string(from: caller.date)
//        tableViewCreateNew.reloadData()
//    }
//
//    @objc func endDateChanged(caller: UIDatePicker){
//        let dateFormatter = DateFormatter()
//        dateFormatter.dateFormat = "MM-dd-YYYY"
//        CreateNewGoalViewController.endDate = dateFormatter.string(from: caller.date)
//        tableViewCreateNew.reloadData()
//    }
//
//    // It is called when text field going to inactive
//    func textFieldShouldEndEditing(_ textField: UITextField) -> Bool {
//        //textField.backgroundColor = UIColor.whiteColor()
//        if(textField.tag == 1){
//           // print("OneTag\(textField.text)")
//            dict["name"] = textField.text
//        }else if(textField.tag == 2){
//           // print("TwoTag\(textField.text)")
//            dict["start_date"] = CreateNewGoalViewController.startDate
//        }else if(textField.tag == 3){
//            //print("ThreeTag\(textField.text)")
//            dict["end_date"] = CreateNewGoalViewController.endDate
//        }else{}
//        //print("fulltextfielddata\(dict)")
//        return true
//    }
//
//    // It is called when text field is inactive
//    func textFieldDidEndEditing(_ textField: UITextField) {
//    }
//
//    // It is called each time user type a character by keyboard
//    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
//        return true
//    }
//
//    //MARK:-TextViewDelegateMethod
//    func textViewShouldEndEditing(_ textView: UITextView) -> Bool {
//        if(textView.tag == 4){
//            dict["description"] = textView.text
//        }else{
//        }
//        return true
//    }

    //MARK:: Function Api Calling
    func createTask () {
        if self.connectedToNetwork()
        {
            self.startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            //let header
            print(headers)
            print(dict)
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorCreateGoalTaskChallenge)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters: dict , headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    print("dictVal\(dictVal)")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    let status = dictMain["status"] as? Int
                    print("Status\(status)")
                    if(status == 1){
                        DispatchQueue.main.async{
                            
                            DispatchQueue.main.async(execute: { () -> Void in
                                self.stopActivityIndicator()
                                let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                    
                                    self.navigationController?.popViewController(animated: true)
                                    
                                }))
                                self.present(alert, animated: true, completion: nil)
                            })
                        }
                    }else{
                        self.stopActivityIndicator()
                        self.logOutMentor()
                        /*
                        let alert = UIAlertController(title: "Alert", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                        }))
                        self.present(alert, animated: true, completion: nil)
                        */
                        
                        
                    }
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    self.showAlertAction(withTitle: "Alert", message: "Something is going wrong")
                    // self.viewBGLoading.isHidden = true
                }
            }
        } else
        {
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
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:-CreateGoalSubmit
    @IBAction func buttonSubmitAction(_ sender: Any) {
        if(dict["name"] == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter a title")
        }else if(dict["description"] == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter description")
        }else if(dict["start_date"] == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter start date")
        }else if(dict["end_date"] == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter end date")
        }else{
            print("dictionary\(dict)")
            createTask()
            
        }
    }
    
    //MARK:-TextFieldDelegate
    // It is called when text field going to inactive
    func textFieldShouldEndEditing(_ textField: UITextField) -> Bool {
        //textField.backgroundColor = UIColor.whiteColor()
        if(textField.tag == 1){
            print("OneTag\(textField.text)")
            dict["name"] = textField.text
        }else if(textField.tag == 2){
            print("TwoTag\(textField.text)")
            dict["start_date"] = CreateNewGoalViewController.startDate
        }else if(textField.tag == 3){
            print("ThreeTag\(textField.text)")
            dict["end_date"] = CreateNewGoalViewController.endDate
        }else{}
        print("fulltextfielddata\(dict)")
        return true
    }
    
    // It is called when text field is inactive
    func textFieldDidEndEditing(_ textField: UITextField) {
    }
    
    // It is called each time user type a character by keyboard
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange,
                   replacementString string: String) -> Bool
    {
        let maxLength = 30
        let currentString: NSString = textField.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if(newString.length <= maxLength){
            return true
        }else{
            showAlert(_sourceController: self, _msg: "Title should not more than 30 characters")
            return false
        }
    }
    
    //MARK:-TextViewDelegateMethod
    func textViewShouldEndEditing(_ textView: UITextView) -> Bool {
        if(textView.tag == 4){
            dict["description"] = textView.text
        }else{
        }
        return true
    }
    
    //MARK:-TableViewDatasource&Delegate
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 460.0
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        
        let createNewTaskCell = tableView.dequeueReusableCell(withIdentifier: "NewTaskTableViewCell") as! NewTaskTableViewCell
        
        createNewTaskCell.textFieldDateFrom.delegate = self
        createNewTaskCell.textFieldDateTo.delegate = self
        createNewTaskCell.textFieldTitle.delegate = self
        createNewTaskCell.textViewDescription.delegate = self
        createNewTaskCell.textFieldDateFrom.text = CreateNewGoalViewController.startDate
        createNewTaskCell.textFieldDateTo.text = CreateNewGoalViewController.endDate
        
        createNewTaskCell.buttonStartDate.addTarget(self, action: #selector(buttonStartDatePressed), for: .touchUpInside)
        createNewTaskCell.buttonEndDate.addTarget(self, action: #selector(buttonEndDatePressed), for: .touchUpInside)
        
        return createNewTaskCell
    }
    
    @objc func buttonStartDatePressed(){
        print("Start Date Tapped")
        let now: Date = Date()
             let dateFormatter: DateFormatter = DateFormatter()
             dateFormatter.dateStyle = .short
             dateFormatter.timeStyle = .short
             
             // Now in New York time
             let nyTimeZone: TimeZone = TimeZone(identifier: "America/New_York")!
             dateFormatter.timeZone = nyTimeZone
             print(dateFormatter.string(from: now))
             
             let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
        let alert = UIAlertController(style: .actionSheet, title: "Create Task", message: "Select start date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            CreateNewGoalViewController.startDate = strDate
            self.dict["start_date"] = CreateNewGoalViewController.startDate
            print("CreateNewGoalViewController.startDate\(CreateNewGoalViewController.startDate )")
        }
        
        let doneAction = UIAlertAction(title: "Done", style: .cancel, handler: {(_ action: UIAlertAction) -> Void in
            let indexPath = IndexPath(item: 0, section: 0)
            self.tableViewCreateNew.reloadRows(at: [indexPath], with: UITableView.RowAnimation.none)
            
            self.tableViewCreateNew.setContentOffset(.zero, animated:true)
        })
        
        alert.addAction(doneAction)
        alert.show()
    }
    
    @objc func buttonEndDatePressed(){
        print("End Date Tapped")
        let now: Date = Date()
             let dateFormatter: DateFormatter = DateFormatter()
             dateFormatter.dateStyle = .short
             dateFormatter.timeStyle = .short
             
             // Now in New York time
             let nyTimeZone: TimeZone = TimeZone(identifier: "America/New_York")!
             dateFormatter.timeZone = nyTimeZone
             print(dateFormatter.string(from: now))
             
             let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
        let alert = UIAlertController(style: .actionSheet, title: "Create Task", message: "Select end date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            CreateNewGoalViewController.endDate = strDate
            self.dict["end_date"] = CreateNewGoalViewController.endDate
            print("CreateNewGoalViewController.startDate\(CreateNewGoalViewController.startDate )")
        }
        let doneAction = UIAlertAction(title: "Done", style: .cancel, handler: {(_ action: UIAlertAction) -> Void in
            let indexPath = IndexPath(item: 0, section: 0)
            self.tableViewCreateNew.reloadRows(at: [indexPath], with: UITableView.RowAnimation.none)
            self.tableViewCreateNew.setContentOffset(.zero, animated:true)
        })
        
        alert.addAction(doneAction)
        alert.show()
    }
}


