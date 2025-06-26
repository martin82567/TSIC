//
//  EditMentorProfileViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 16/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class EditMentorProfileViewController: BaseViewController {
    let allowedCharacters : String = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    @IBOutlet weak var viewBGLoading: UIView!
    @IBOutlet weak var textFieldFirstname: UITextField!
    @IBOutlet weak var txtFstPhoneNumber: UITextField!
    @IBOutlet weak var txtMPhoneNumber: UITextField!
    @IBOutlet weak var txtLstPhoneNumber: UITextField!
    @IBOutlet weak var textFieldLastname: UITextField!
    @IBOutlet weak var textFieldMiddlename: UITextField!
    
    var dicJsonMentorResponse:NSDictionary? = [:]
    var firstName :String = ""
    var middleName :String = ""
    var lastName :String = ""
    var wordCountFullName : String = ""
    var phone : String = ""
    var videoURLStr : String = ""
    var profileImage : UIImage!
    
    override func viewDidLoad() {
        super.viewDidLoad()
          getUserDetails()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        //viewBGLoading.isHidden = true
      
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:-AddDoneKeyboard
    func addDoneButtonOnKeyboard() {
        let doneToolbar: UIToolbar = UIToolbar(frame: CGRect(x: 0, y: 0, width: 320, height: 50))
        doneToolbar.barStyle       = UIBarStyle.default
        let flexSpace              = UIBarButtonItem(barButtonSystemItem: UIBarButtonItem.SystemItem.flexibleSpace, target: nil, action: nil)
        let done: UIBarButtonItem  = UIBarButtonItem(title: "Done", style: UIBarButtonItem.Style.done, target: self, action: #selector(EditMentorProfileViewController.doneButtonAction))
        
        var items = [UIBarButtonItem]()
        items.append(flexSpace)
        items.append(done)
        
        doneToolbar.items = items
        doneToolbar.sizeToFit()
        
        //self.textFieldPhoneNumber.inputAccessoryView = doneToolbar
    }
    
    @objc func doneButtonAction() {
        //self.textFieldPhoneNumber.resignFirstResponder()
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func buttonSaveAction(_ sender: Any) {
        if (textFieldFirstname.text == "") {
            self.showAlertAction(withTitle: "Alert", message: "Please enter fristname")
        } else if (textFieldLastname.text == "") {
            self.showAlertAction(withTitle: "Alert", message: "Please enter lastname")
        } else if (txtFstPhoneNumber.text == "") || (txtMPhoneNumber.text == "") || (txtLstPhoneNumber.text == "") {
            self.showAlertAction(withTitle: "Alert", message: "Please enter phonenumber")
        } else {
            if self.connectedToNetwork() {
                self.startActivityIndicator()
                let token  = UserDefaults.standard.string(forKey: "mentorToken")!
                
                let headers = [
                    "Authorizations": token,
                    "Content-Type": "application/x-www-form-urlencoded"
                ]
                //let header
                print(headers)
                
                let strF: String = self.txtFstPhoneNumber.text!
                let strM: String = self.txtMPhoneNumber.text!
                let strL: String = self.txtLstPhoneNumber.text!
                print(strF , " + ", strM, " + ", strL)
                
                let strPh: String = "(" + strF + ") " + strM + "-" + strL
                print(strPh)
                var parameters = [String: String]()
                
                parameters["firstname"] = textFieldFirstname.text
                parameters["middlename"] = textFieldMiddlename.text
                parameters["lastname"] = textFieldLastname.text
                parameters["phone"] = strPh
                
                print(parameters)
                
                let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorUpdateUserDetails)
                print("MENTEELISTURL\(url)")
                Alamofire.request(url, method:.post, parameters: parameters , headers: headers).responseJSON { response in
                    switch response.result {
                    case .success:
                        print(response)
                        // ApiUtillity.sharedInstance.dismissSVProgressHUD()
                        let dictVal = response.result.value
                        print("dictVal\(String(describing: dictVal))")
                        //let dictMain:NSDictionary = dictVal as! NSDictionary
                        self.getUserDetails()
                        self.stopActivityIndicator()
                        self.navigationController?.popViewController(animated: true)
                        
                    case .failure(let error):
                        print(error)
                        self.stopActivityIndicator()
                    }
                }
            } else {
                showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        }
    }
    
    //MARK::ApiCalling
    func getUserDetails() {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
//            self.showActivityIndicatory(uiView: self.view)
//            viewBGLoading.isHidden = false
            self.startActivityIndicator()
            MentorApiManager.mentorSharedInstance.getMentorUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                 //   print("MentorDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        guard let jsonResponse = json as? NSDictionary else {
                            return
                        }
                        
                        self.dicJsonMentorResponse = jsonResponse as NSDictionary
 
                        self.setValues()
                        //self.tableViewProfileDisplay.reloadData()
                        self.stopActivityIndicator()
//                        self.actInd.stopAnimating()
//                        self.viewBGLoading.isHidden = true
                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
//                    self.actInd.stopAnimating()
//                    self.viewBGLoading.isHidden = true
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK:- SetValues
    func setValues() {
     //   print(dicJsonMentorResponse)
        
        if let dicMentorResponse = dicJsonMentorResponse {
            
            if dicMentorResponse.count > 0 {
                //TODO:firstname
                if String(describing: dicMentorResponse["firstname"]!) == "" {
                    firstName = ""
                } else {
                    firstName = String(describing: dicMentorResponse["firstname"]!)
                }
                textFieldFirstname.text = firstName
                
                //TODO:middleName
                if String(describing: dicMentorResponse["middlename"]!) == "" {
                    middleName = ""
                } else {
                    middleName = String(describing: dicMentorResponse["middlename"]!)
                }
                textFieldMiddlename.text = middleName
                
                //TODO:lastname
                if String(describing: dicMentorResponse["lastname"]!) == "" {
                    lastName = ""
                } else {
                    lastName = String(describing: dicMentorResponse["lastname"]!)
                }
                textFieldLastname.text = lastName
                
                //TODO:Phone
                if String(describing: dicMentorResponse["phone"]!) == "" {
                    txtFstPhoneNumber.text = ""
                    txtMPhoneNumber.text   = ""
                    txtLstPhoneNumber.text = ""
                } else {
                    let strPh = String(describing: dicMentorResponse["phone"]!).components(separatedBy: " ")
                    
                    if strPh[0].hasPrefix("(") || strPh[0].hasPrefix(")") == true {
                        
                        let strF = strPh[0].components(separatedBy: "(")
                        let strFst = strF[1].prefix(3)
                        print(strFst)
                        let strM = strPh[1].prefix(3)
                        print(strM)
                        let strLst = strPh[1].suffix(4)
                        print(strLst)
                        txtFstPhoneNumber.text = String(describing: strFst)
                        txtMPhoneNumber.text   = String(describing: strM)
                        txtLstPhoneNumber.text = String(describing: strLst)
                        
                    } else {
                        txtFstPhoneNumber.text = String(describing: strPh)

                    }
                }
            }
        }
        

    }
    
}

// MARK:: UITextFieldDelegate
extension EditMentorProfileViewController: UITextFieldDelegate {
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
    
    //MARK:: UITextFieldDelegate
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        if textField == txtFstPhoneNumber {
            let currentFString: NSString = txtFstPhoneNumber.text! as NSString
            let newString: NSString = currentFString.replacingCharacters(in: range, with: string) as NSString
            
            txtMPhoneNumber.resignFirstResponder()
            return newString.length <= 3
        } else if textField == txtMPhoneNumber {
            let currentFString: NSString = txtMPhoneNumber.text! as NSString
            let newString: NSString = currentFString.replacingCharacters(in: range, with: string) as NSString
            
            txtLstPhoneNumber.resignFirstResponder()
            return newString.length <= 3
        } else {//} if textField == txtLstPhoneNumber {
            let currentFString: NSString = txtLstPhoneNumber.text! as NSString
            let newString: NSString = currentFString.replacingCharacters(in: range, with: string) as NSString
            
            return newString.length <= 4
        }
    }
}


