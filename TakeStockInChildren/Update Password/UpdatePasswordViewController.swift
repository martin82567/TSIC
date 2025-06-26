//
//  UpdatePasswordViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/10/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class UpdatePasswordViewController: BaseViewController {

    @IBOutlet weak var confirmPassword: UITextField!
    @IBOutlet weak var oldPasswordTextfield: UITextField!
    @IBOutlet weak var newPasswordtextfield: UITextField!
    var trimmedNewPassword : String?
    var trimmedConfirmNewPassword: String?
    var iconClick = true
    var loginModeForResetPassword: String?
    override func viewDidLoad() {
        super.viewDidLoad()
    }
    
    //MARK:: Function Api Calling
    func getResetDetails () {
        
        let resetDetails:NSMutableDictionary = [
            "current_password"    : self.oldPasswordTextfield.text ?? "",
            "new_password" : self.trimmedNewPassword ?? "",
            "confirm_new_password"      : self.trimmedConfirmNewPassword ?? ""
        ]
        
        print("resetDetails :: \(resetDetails)")
        if self.connectedToNetwork() {
                self.startActivityIndicator()
            ApiManager.sharedInstance.UpdatePass(type: self.loginModeForResetPassword ?? "",resetDetails: resetDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            DispatchQueue.main.async(execute: {() -> Void in
                                self.navigationController?.popViewController(animated: true)
                                /*
                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                                let loginVC = storyBoard.instantiateViewController(w ithIdentifier: "LogInViewController") as! LogInViewC1ontroller
                                loginVC.fromResetPassword = true
                                self.present(loginVC, animated:true, completion:nil)
                                */
                            })
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        /*
                        if self.loginModeForResetPassword == "mentee"{
                            self.logOutMentee()
                        }
                        else {
                            self.logOutMentor()
                        }
                        */
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
        }else {
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
                            nav.navigationBar .isHidden = true;
                            nav.navigationBar.barStyle = .default
                             appDelegate.window? .rootViewController = nav
                            
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
                    // self.showA   lertAction(withTitle: "Alert", message: "Something is going wrong")
                }
            }
        } else
        {
            // showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    
    
    @IBAction func buttonHideUnhide(_ sender: Any) {
        
        if(iconClick == true) {
            newPasswordtextfield.isSecureTextEntry = false
        } else {
            newPasswordtextfield.isSecureTextEntry = true
        }
        iconClick = !iconClick
    }
    
    @IBAction func cancelBtn(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func changePasswordBtn(_ sender: Any) {
        
        let oldpassword = self.oldPasswordTextfield.text
        let newPassword = self.newPasswordtextfield.text
        let confirmnewpassword = self.confirmPassword.text
        trimmedNewPassword = newPassword?.trim()
        trimmedConfirmNewPassword = confirmnewpassword?.trim()
        
        // Validate the text fields
        if(oldpassword == ""){
            showAlert(_sourceController: self, _msg: AlertMessage.oldpassword)
        }
        else if(trimmedNewPassword == ""){
            showAlert(_sourceController: self, _msg: AlertMessage.reset_passwordBlank)
        }
        else if((trimmedNewPassword!.count < 6)){
            showAlert(_sourceController: self, _msg: AlertMessage.login_passwordMinimumLength)
        }
        else if(trimmedConfirmNewPassword == ""){
            showAlert(_sourceController: self, _msg: AlertMessage.reset_confirmPasswordBlank)
        }
        else if(!(trimmedNewPassword == trimmedConfirmNewPassword)){
            showAlert(_sourceController: self, _msg: AlertMessage.Reset_passwordMismatch)
        }
        else {
            self.getResetDetails()
        }
    }
}

// MARK:: UITextFieldDelegate
extension UpdatePasswordViewController: UITextFieldDelegate {
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
}
