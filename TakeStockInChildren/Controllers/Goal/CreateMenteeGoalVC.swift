//
//  CreateMenteeGoalVC.swift
//  TakeStockInChildren
//
//  Created by Aquarious  on 20/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire


class CreateMenteeGoalVC: BaseViewController,UITextViewDelegate,UITextFieldDelegate {

    @IBOutlet weak var txtTitle: UITextField!
    @IBOutlet weak var txtVDescription: UITextView!
    @IBOutlet weak var txtFromDate: UITextField!
    @IBOutlet weak var txtToDate: UITextField!
    
    
    var strTypeOfNewCreation : String = ""
    var id : Int?
    var dict :[String:String] = ["id" : "","type" : "goal", "name": "", "description" : "" , "start_date" : "" , "end_date" : ""]
    
    static var startDate : String = ""
    static var endDate: String?
    
    
    //MARK:: UIView
    override func viewDidLoad() {
        super.viewDidLoad()
        CreateMenteeGoalVC.startDate = ""
        CreateMenteeGoalVC.endDate = ""
        // Do any additional setup after loading the view.
        
        self.navigationController?.setNavigationBarHidden(true, animated: true)
        self.tabBarController?.tabBar.isHidden = true
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        //tblCreateGoal.setContentOffset(.zero, animated:true)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:: Function Api Calling
    func userCreateGoal () {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "token")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            //let header
            print(headers)
            print(dict)
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeCreateGoal)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters: dict , headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    let dictVal = response.result.value
                    print("dictVal\(String(describing: dictVal))")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    let status = dictMain["status"] as? Int
                    print("Status\(String(describing: status))")
                    if(status == 1){
                        DispatchQueue.main.async {
                            DispatchQueue.main.async(execute: { () -> Void in
                                self.stopActivityIndicator()
                                let alert = UIAlertController(title: "Success", message: dictMain["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                    self.navigationController?.popViewController(animated: true)
                                }))
                                self.present(alert, animated: true, completion: nil)
                            })
                        }
                    } else {
                        self.stopActivityIndicator()
                        self.logOutMentee()
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
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
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
    
    
    //MARK: Button Action
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //TODO:-CreateGoalSubmit
    @IBAction func btnSubmitAction(_ sender: Any) {
        if (txtTitle.text == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter a title")
        } else if (txtVDescription.text == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter description")
        } else if (txtFromDate.text == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter start date")
        } else if (txtToDate.text == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter end date")
        } else {
            print("dictionary\(dict)")
            userCreateGoal()
        }
    }
    
    @IBAction func btnStartDateAction(_ sender: Any) {
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
        let alert = UIAlertController(style: .actionSheet, title: "Create Goal", message: "Select start date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            CreateMenteeGoalVC.startDate = strDate
            self.dict["start_date"] = CreateMenteeGoalVC.startDate
            print("CreateMenteeGoalVC.startDate: \(CreateMenteeGoalVC.startDate)")
            self.txtFromDate.text = strDate
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    @IBAction func btnEndDateAction(_ sender: Any) {
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
        let alert = UIAlertController(style: .actionSheet, title: "Create Goal", message: "Select end date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            CreateMenteeGoalVC.endDate = strDate
            self.dict["end_date"] = CreateMenteeGoalVC.endDate
            print("CreateMenteeGoalVC.endDate: \(String(describing: CreateMenteeGoalVC.endDate))")
            self.txtToDate.text = strDate
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    //MARK:-TextFieldDelegate
    // It is called when text field going to inactive
    func textFieldShouldEndEditing(_ textField: UITextField) -> Bool {
        //textField.backgroundColor = UIColor.whiteColor()
        if textField.tag == 1 {
            print("txtTitle:: \(String(describing: textField.text))")
            dict["name"] = textField.text
        } else if textField.tag == 2 {
            print("TwoTag\(String(describing: textField.text))")
            dict["start_date"] = CreateMenteeGoalVC.startDate
        } else if textField.tag == 3 {
            print("ThreeTag\(String(describing: textField.text))")
            dict["end_date"] = CreateMenteeGoalVC.endDate
        }  
        print("fulltextfielddata\(dict)")
        return true
    }
    
    // It is called when text field is inactive
    func textFieldDidEndEditing(_ textField: UITextField) {
    }
    
    //MARK:-TextFieldDelegate
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let maxLength = 30
        let currentString: NSString = textField.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if newString.length <= maxLength {
            return true
        } else {
            showAlert(_sourceController: self, _msg: "Title should not more than 30 characters")
            return false
        }
    }
    
    //MARK:-TextViewDelegateMethod
    func textViewShouldEndEditing(_ textView: UITextView) -> Bool {
        if textView.tag == 4 {
            dict["description"] = textView.text
        } else {
        }
        return true
    }
    
    /*//MARK:-TableViewDatasource&Delegate
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        return 460.0
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let menteeGoalCell = tableView.dequeueReusableCell(withIdentifier: "CreateNewTypeTableViewCell") as! CreateNewTypeTableViewCell
        
        menteeGoalCell.textFieldDateFrom.delegate = self
        menteeGoalCell.textFieldDateTo.delegate = self
        menteeGoalCell.textFieldTitle.delegate = self
        menteeGoalCell.textViewDescription.delegate = self
        
        menteeGoalCell.textFieldDateFrom.text = CreateMenteeGoalVC.startDate
        menteeGoalCell.textFieldDateTo.text = CreateMenteeGoalVC.endDate
        
        menteeGoalCell.buttonStartDate.addTarget(self, action: #selector(buttonStartDatePressed), for: .touchUpInside)
        menteeGoalCell.buttonEndDate.addTarget(self, action: #selector(buttonEndDatePressed), for: .touchUpInside)
        
        return menteeGoalCell
    }*/

}



