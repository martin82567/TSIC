//
//  EditCreatedGoalTaskChallengeViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 29/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class EditCreatedGoalTaskChallengeViewController: BaseViewController,UITextFieldDelegate,UITextViewDelegate {
    
    @IBOutlet weak var textViewDescription: UITextView!
    @IBOutlet weak var textFieldTitle: UITextField!
    @IBOutlet weak var textFieldStartDate: UITextField!
    @IBOutlet weak var textFieldEndDate: UITextField!
    
    var strTitle : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    var idCreate : Int?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        print("strTitle\(strTitle)")
        print("strDescription\(strDescription)")
        print("strStartDate\(strStartDate)")
        print("strEndDate\(strEndDate)")
        print("idCreate\(idCreate)")
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.navigationController?.setNavigationBarHidden(true, animated: animated)
        setUpValues()
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        //self.navigationController?.setNavigationBarHidden(false, animated: animated)
    }
    
    //MARK:-SetUpValues
    func setUpValues(){
        textFieldTitle.text = strTitle
        textViewDescription.text = strDescription
        textFieldStartDate.text = strStartDate
        textFieldEndDate.text = strEndDate
    }
    
    //MARK:-CheckingValidation
    func editCreation(){
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
            let editParameters:NSMutableDictionary = [
                "type" : "goal",
                "name" : textFieldTitle.text,
                "description" : textViewDescription.text,
                "start_date" : textFieldStartDate.text,
                "end_date" : textFieldEndDate.text,
                "id" : idCreate
            ]
            print("editParameters\(editParameters)")
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorCreateGoalTaskChallenge)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters: editParameters as! Parameters , headers: headers).responseJSON { response in
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
        } else
        {
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
    
    
    //MARK:-ButtonAction
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func buttonSaveAction(_ sender: Any) {
        if(textFieldTitle.text == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter a title")
        }else if(textViewDescription.text == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter a description")
        }else if(textFieldStartDate.text == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter start date")
        }else if(textFieldEndDate.text == ""){
            showAlertAction(withTitle: "Alert", message: "Please enter end date")
        }else{
            editCreation()
        }
    }
    
    @IBAction func buttonStartDateAction(_ sender: Any) {
        
        let now: Date = Date()
             let dateFormatter: DateFormatter = DateFormatter()
             dateFormatter.dateStyle = .short
             dateFormatter.timeStyle = .short
             
             // Now in New York time
             let nyTimeZone: TimeZone = TimeZone(identifier: "America/New_York")!
             dateFormatter.timeZone = nyTimeZone
             print(dateFormatter.string(from: now))
             
             let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
        let alert = UIAlertController(style: .actionSheet, title: "Edit Start Date", message: "Select start date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            self.textFieldStartDate.text = strDate
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    @IBAction func buttonEndDateAction(_ sender: Any) {
        let now: Date = Date()
             let dateFormatter: DateFormatter = DateFormatter()
             dateFormatter.dateStyle = .short
             dateFormatter.timeStyle = .short
             
             // Now in New York time
             let nyTimeZone: TimeZone = TimeZone(identifier: "America/New_York")!
             dateFormatter.timeZone = nyTimeZone
             print(dateFormatter.string(from: now))
             
             let nydate  = dateFormatter.date(from: dateFormatter.string(from: now))
        
        let alert = UIAlertController(style: .actionSheet, title: "Edit End Date", message: "Select end date")
        alert.addDatePicker(mode: .date, date: Date(), minimumDate: nydate, maximumDate: nil) { date in
            
            let dateFormatter = DateFormatter()
            dateFormatter.dateFormat = "MM-dd-yyyy"
            let strDate = dateFormatter.string(from: date)
            self.textFieldEndDate.text = strDate
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    //MARK:-TextFieldDelegate
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let maxLength = 30
        let currentString: NSString = textField.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if(newString.length <= maxLength) {
            return true
        } else {
            showAlert(_sourceController: self, _msg: "Title should not more than 30 characters")
            return false
        }
    }
}

