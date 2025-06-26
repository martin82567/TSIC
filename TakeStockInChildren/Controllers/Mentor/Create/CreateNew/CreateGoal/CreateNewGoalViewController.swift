//
//  CreateNewViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 20/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//


import UIKit
import Alamofire

class CreateNewGoalViewController: BaseViewController,UITextViewDelegate,UITextFieldDelegate,UITableViewDelegate,UITableViewDataSource {
    
    @IBOutlet weak var tableViewCreateNew: UITableView!
    
    var strTypeOfNewCreation : String = ""
    var id : Int?
    var dict :[String:String] = ["type" : "goal", "name": "", "description" : "" , "start_date" : "" , "end_date" : ""]
    
    static var startDate : String = ""
    static var endDate: String?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        CreateNewGoalViewController.startDate = ""
        CreateNewGoalViewController.endDate = ""
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
    
    //MARK:: Function Api Calling
    func createGoal () {
        if self.connectedToNetwork() {
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
                    print("dictVal\(String(describing: dictVal))")
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    let status = dictMain["status"] as? Int
                    print("Status\(String(describing: status))")
                    if(status == 1) {
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
                    } else {
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
    
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:-CreateGoalSubmit
    @IBAction func buttonSubmitAction(_ sender: Any) {
        if (dict["name"] == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter a title")
        } else if(dict["description"] == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter description")
        } else if(dict["start_date"] == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter start date")
        } else if(dict["end_date"] == "") {
            showAlertAction(withTitle: "Alert", message: "Please enter end date")
        } else {
            print("dictionary\(dict)")
            createGoal()
        }
    }
    
    //MARK:-TextFieldDelegate
    // It is called when text field going to inactive
    func textFieldShouldEndEditing(_ textField: UITextField) -> Bool {
        //textField.backgroundColor = UIColor.whiteColor()
        if (textField.tag == 1) {
            print("OneTag\(String(describing: textField.text))")
            dict["name"] = textField.text
        } else if(textField.tag == 2) {
            print("TwoTag\(String(describing: textField.text))")
            dict["start_date"] = CreateNewGoalViewController.startDate
        } else if(textField.tag == 3) {
            print("ThreeTag\(String(describing: textField.text))")
            dict["end_date"] = CreateNewGoalViewController.endDate
        } else {}
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
        if (newString.length <= maxLength) {
            return true
        } else {
            showAlert(_sourceController: self, _msg: "Title should not more than 30 characters")
            return false
        }
    }
    
    //MARK:-TextViewDelegateMethod
    func textViewShouldEndEditing(_ textView: UITextView) -> Bool {
        if (textView.tag == 4) {
            dict["description"] = textView.text
        } else {
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
        let createNewGoalCell = tableView.dequeueReusableCell(withIdentifier: "CreateNewTypeTableViewCell") as! CreateNewTypeTableViewCell
        
        createNewGoalCell.textFieldDateFrom.delegate = self
        createNewGoalCell.textFieldDateTo.delegate = self
        createNewGoalCell.textFieldTitle.delegate = self
        createNewGoalCell.textViewDescription.delegate = self
        
        createNewGoalCell.textFieldDateFrom.text = CreateNewGoalViewController.startDate
        createNewGoalCell.textFieldDateTo.text = CreateNewGoalViewController.endDate

        createNewGoalCell.buttonStartDate.addTarget(self, action: #selector(buttonStartDatePressed), for: .touchUpInside)
        createNewGoalCell.buttonEndDate.addTarget(self, action: #selector(buttonEndDatePressed), for: .touchUpInside)
        
        return createNewGoalCell
    }
    
    @objc func buttonStartDatePressed() {
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
            CreateNewGoalViewController.startDate = strDate
            self.tableViewCreateNew.reloadData()
            self.dict["start_date"] = CreateNewGoalViewController.startDate
            print("CreateNewGoalViewController.startDate\(CreateNewGoalViewController.startDate )")
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
    
    @objc func buttonEndDatePressed() {
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
            CreateNewGoalViewController.endDate = strDate
            self.tableViewCreateNew.reloadData()
            self.dict["end_date"] = CreateNewGoalViewController.endDate
            print("CreateNewGoalViewController.startDate\(CreateNewGoalViewController.endDate )")
        }
        alert.addAction(title: "Done", style: .cancel)
        alert.show()
    }
}
