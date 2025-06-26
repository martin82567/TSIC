//
//  ResetPasswordViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 08/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ResetPasswordViewController: BaseViewController {
    
    @IBOutlet weak var tolabel: UILabel!
    @IBOutlet weak var welcomeLabel: UILabel!
    @IBOutlet weak var textFieldEmailAddress: UITextField!
    @IBOutlet weak var textFieldCode: UITextField!
    @IBOutlet weak var textFieldNewPassword: UITextField!
    @IBOutlet weak var textFieldConfirmPassword: UITextField!
    @IBOutlet weak var firstView: UIView!
    @IBOutlet weak var topHeightViewEmailConstraint: NSLayoutConstraint!
    
    @IBOutlet weak var cancelbtn: UIButton!
    @IBOutlet weak var fourthview: UIView!
    @IBOutlet weak var thirdvIew: UIView!
    @IBOutlet weak var secondView: UIView!
    @IBOutlet weak var backgroundimage: UIImageView!
    var loginModeForResetPassword : String = ""
    var StrEmail : String?
    var email : String?
    var password : String?
    var confPassword : String?
    var code : String?
    var trimmedNewPassword : String?
    var iconClick = true
    var valueMode : String?
    override func viewDidLoad() {
        super.viewDidLoad()
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechanged()
        
        self.textFieldEmailAddress.text = StrEmail
        manageAutoLayOut()
    }
    
    func darkmodechanged() {
        if self.valueMode == "dark" {
            backgroundimage.image = UIImage(named: "BG4")
            welcomeLabel.textColor = UIColor(hex: "#0E0F27")
            tolabel.textColor = UIColor(hex: "#0E0F27")
            firstView.backgroundColor = UIColor(hex: "#0E0F27")
            secondView.backgroundColor = UIColor(hex: "#0E0F27")
            thirdvIew.backgroundColor = UIColor(hex: "#0E0F27")
            fourthview.backgroundColor = UIColor(hex: "#0E0F27")
            cancelbtn.titleLabel?.textColor = .white
            
            
            
            textFieldEmailAddress.attributedPlaceholder = NSAttributedString(string: "Email",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldEmailAddress.textColor = .white
            
            textFieldCode.attributedPlaceholder = NSAttributedString(string: "Code",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldCode.textColor = .white
            
            
            textFieldNewPassword.attributedPlaceholder = NSAttributedString(string: "New Password",
                       attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       textFieldNewPassword.textColor = .white
                       
                       textFieldConfirmPassword.attributedPlaceholder = NSAttributedString(string: "Confirm Password",
                       attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
                       textFieldConfirmPassword.textColor = .white
            
        }
        else if self.valueMode == "light" {
            backgroundimage.image = UIImage(named: "BaseLogInBackground")
            welcomeLabel.textColor = .white
            tolabel.textColor = .white
            firstView.backgroundColor = .white
            secondView.backgroundColor = .white
            thirdvIew.backgroundColor = .white
            fourthview.backgroundColor = .white
            cancelbtn.titleLabel?.textColor = .black
            
            
            textFieldEmailAddress.attributedPlaceholder = NSAttributedString(string: "Email",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldEmailAddress.textColor = .black
            
            textFieldCode.attributedPlaceholder = NSAttributedString(string: "Code",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldCode.textColor = .black
            
            
            textFieldNewPassword.attributedPlaceholder = NSAttributedString(string: "New Password",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldNewPassword.textColor = .black
            
            textFieldConfirmPassword.attributedPlaceholder = NSAttributedString(string: "Confirm Password",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.black])
            textFieldConfirmPassword.textColor = .black
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        //        dismiss(animated: true, completion: nil)
    }
    
    //MARK:- Manage Autolayout
    func manageAutoLayOut(){
        switch UIScreen.main.bounds.height{
        case 480:
            topHeightViewEmailConstraint.constant = 26
        case 568:
            topHeightViewEmailConstraint.constant = 26
        case 667:
            topHeightViewEmailConstraint.constant = 26
        case 736:
            topHeightViewEmailConstraint.constant = 56
        case 812:
            topHeightViewEmailConstraint.constant = 100
        case 896:
            topHeightViewEmailConstraint.constant = 100
        default:
            topHeightViewEmailConstraint.constant = 100
        }
    }
    
    @IBAction func buttonChangePasswordAction(_ sender: Any) {
        let newPassword = self.textFieldNewPassword.text
        let newConfirmPassword = self.textFieldConfirmPassword.text
        trimmedNewPassword = newPassword?.trim()
        let trimmedConfirmNewPassword = newConfirmPassword?.trim()
        
        // Validate the text fields
        if(textFieldCode.text == ""){
            showAlert(_sourceController: self, _msg: AlertMessage.reset_otpBlank)
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
    
    @IBAction func buttonCancelAction(_ sender: Any) {
        self.dismiss(animated: false, completion: nil)
    }
    
    @IBAction func buttonHideUnhideAction(_ sender: Any) {
        if(iconClick == true) {
            textFieldNewPassword.isSecureTextEntry = false
        } else {
            textFieldNewPassword.isSecureTextEntry = true
        }
        iconClick = !iconClick
    }
    
    //MARK:: Function Api Calling
    func getResetDetails () {
        email = StrEmail!.trim()
        code = self.textFieldCode.text
        password = trimmedNewPassword
        
        let resetDetails:NSMutableDictionary = [
            "email"    : email!,
            "password" : password!,
            "otp"      : code!
        ]
        
        print("resetDetails :: \(resetDetails)")
        if self.connectedToNetwork() {
            if(loginModeForResetPassword == "Mentee"){
                self.startActivityIndicator()
                print("Coming to mentee Reset password controller")
                ApiManager.sharedInstance.resetPass(postId: 1, resetDetails: resetDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            DispatchQueue.main.async(execute: {() -> Void in
                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                                let loginVC = storyBoard.instantiateViewController(withIdentifier: "BaseLoginViewController") as! BaseLoginViewController
//                                loginVC.emailValue = ""
                                loginVC.fromResetPassword = true
//                                loginVC.loginMode = "Mentee"
                                self.present(loginVC, animated:true, completion:nil)
                            })
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
            }
            else{
                self.startActivityIndicator()
                print("Coming to mentor Reset password controller")

                ApiManager.sharedInstance.resetPass(postId: 1, resetDetails: resetDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.stopActivityIndicator()
                        print("ResetPassword\(String(describing: json))")
                        let alert = UIAlertController(title: "Take Stock In Children", message: json["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            DispatchQueue.main.async(execute: {() -> Void in
//                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//                                let loginVC = storyBoard.instantiateViewController(withIdentifier: "LogInViewController") as! LogInViewController
//                                loginVC.loginMode = "Mentor"
//                                loginVC.fromResetPassword = true
                                
                                let storyBoard : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                                let loginVC = storyBoard.instantiateViewController(withIdentifier: "BaseLoginViewController") as! BaseLoginViewController
                                loginVC.fromResetPassword = true
                                loginVC.modalPresentationStyle = .fullScreen

                                self.present(loginVC, animated:true, completion:nil)
                            })
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "OK" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
            }
        }else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
}

// MARK:: UITextFieldDelegate
extension ResetPasswordViewController: UITextFieldDelegate {
    
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
}


