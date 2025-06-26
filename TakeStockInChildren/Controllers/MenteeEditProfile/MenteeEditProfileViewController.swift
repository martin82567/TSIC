//
//  MenteeEditProfileViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 19/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class MenteeEditProfileViewController: BaseViewController {
    
    let allowedCharacters : String = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    @IBOutlet weak var textFieldFirstname: UITextField!
    @IBOutlet weak var textFieldLastname: UITextField!
    @IBOutlet weak var textFieldMiddlename: UITextField!
    
    var dicUserDetails = NSDictionary()
    var firstName :String = ""
    var middleName :String = ""
    var lastName :String = ""
    var wordCountFullName : String = ""
    var phone : String = ""
    var videoURLStr : String = ""
    var profileImage : UIImage!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        getUserDetails()
    }
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func buttonSaveAction(_ sender: Any) {
        if(textFieldFirstname.text == ""){
            self.showAlertAction(withTitle: "Alert", message: "Please enter fristname")
        }else if(textFieldLastname.text == ""){
            self.showAlertAction(withTitle: "Alert", message: "Please enter lastname")
        }else{
            if self.connectedToNetwork()
            {
                self.startActivityIndicator()
                
                let token  = UserDefaults.standard.string(forKey: "token")!
                
                let headers = [
                    "Authorizations": token,
                    "Content-Type": "application/x-www-form-urlencoded"
                ]
                //let header
                print(headers)
                
                var parameters = [String: String]()
                parameters["firstname"] = textFieldFirstname.text
                parameters["middlename"] = textFieldMiddlename.text
                parameters["lastname"] = textFieldLastname.text
                print(parameters)
                
                let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UpdateUserDetails)
                print("MENTEELISTURL\(url)")
                Alamofire.request(url, method:.post, parameters: parameters , headers: headers).responseJSON { response in
                    switch response.result {
                    case .success:
                        print(response)
                        let dictVal = response.result.value
                        print("dictVal\(String(describing: dictVal))")
                        let dictMain:NSDictionary = dictVal as! NSDictionary
                        self.getUserDetails()
                        self.stopActivityIndicator()
                        self.navigationController?.popViewController(animated: true)
                    case .failure(let error):
                        print(error)
                        self.stopActivityIndicator()
                    }
                }
            } else
            {
                showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        }
    }
    
    //MARK:: Function Api Calling
    func getUserDetails () {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getUserDetails(onSuccess: { json in
                DispatchQueue.main.async {
                    print("UserDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        self.dicUserDetails = json["user_details"] as! NSDictionary
                        print("GetdicuserDetails\(self.dicUserDetails)")
                        UserDefaults.standard.set(json["user_details"], forKey: "userDetails")
                        self.setValues()
                        self.stopActivityIndicator()
                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    ////MARK:- SetValues
    func setValues() {
        firstName = (dicUserDetails["firstname"] as! String)
        middleName = (dicUserDetails["middlename"] as! String)
        lastName = (dicUserDetails["lastname"] as! String)
        textFieldFirstname.text = firstName
        textFieldMiddlename.text = middleName
        textFieldLastname.text = lastName
    }
}

// MARK:: UITextFieldDelegate
extension MenteeEditProfileViewController: UITextFieldDelegate {
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
    
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let cs = NSCharacterSet(charactersIn: allowedCharacters).inverted
        let filtered = string.components(separatedBy: cs).joined(separator: "")
        
        return (string == filtered)
    }
}




