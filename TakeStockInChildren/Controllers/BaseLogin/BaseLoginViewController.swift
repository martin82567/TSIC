//
//  EmailViewController.swift
//  TakeStockInChildren
//
//  Created by Divij Jindal on 06/07/22.
//  Copyright © 2022 Aquarious Technology. All rights reserved.
//

import UIKit

class BaseLoginViewController: BaseViewController {

    @IBOutlet weak var textFieldEmailId: UITextField!
    @IBOutlet weak var nextButton: UIButton!
    @IBOutlet weak var backgroundImage: UIImageView!
    @IBOutlet weak var firstview: UIView!
    @IBOutlet weak var topConstraintViewEmail: NSLayoutConstraint!
    @IBOutlet weak var welcomeLabel: UILabel!
    @IBOutlet weak var tolabel: UILabel!
    @IBOutlet weak var lblEdate: UILabel!
    @IBOutlet weak var lblSdate: UILabel!
    @IBOutlet weak var backButton: UIButton!
    
    var valueMode : String?

    override func viewDidLoad() {
        super.viewDidLoad()
        darkmodeChnage()
        backButton.setTitle("", for: .normal)
        
//        if let email = UserDefaults.standard.string(forKey: "username"), UserDefaults.standard.bool(forKey: "isFaceIdEnabled") == true {
//            BioMetricAuthentication.authenticateBiometrics(controller: self) { [weak self] in
//                self?.textFieldEmailId.text = email
//                self?.nextButtonPressed(UIButton())
//            }
//        }
    }
    var fromResetPassword: Bool = false
    
    @IBAction func backButtonPressed(_ sender: UIButton) {
        self.navigationController?.popViewController(animated: true)
    }
    @IBAction func nextButtonPressed(_ sender: UIButton) {
        let email = self.textFieldEmailId.text
        if(textFieldEmailId.text == ""){
            showAlert(_sourceController: self, _msg: "Error: You must provide an email address.Please try again with a valid email address. To validate your email address, please contact your local Take Stock in Children program.")
        }
        else if !isValidEmail(emailStr: email!) {
            showAlert(_sourceController: self, _msg: AlertMessage.login_InvalidEmail)
        }
        else {
            
            //let fkey = (UserDefaults.standard.value(forKey: "firebase_token") != nil) ?  UserDefaults.standard.value(forKey: "firebase_token")! : "zhdfggasthfhesytf764rtgycjt56tgryt4gycv648c"
        
            
            if self.connectedToNetwork() {
                let fcmToken = UserDefaults.standard.value(forKey: "firebase_token")
                var voip_token = UserDefaults.standard.value(forKey: "voip_token")
                if(voip_token == nil) {
                    voip_token = ""
                }
                let userDetails:NSMutableDictionary = [
                    "email" : email!,
                    "latitude" : "",//UserDefaults.standard.data(forKey: "latitude")!,
                    "longitude" : "",//UserDefaults.standard.data(forKey: "longitude")!,
                    "firebase_id" : fcmToken ?? "",
                    "voip_device_token": voip_token!,
                    "device_type": "iOS",
                ]

                ApiManager.sharedInstance.checkUserType(userDetails: userDetails) { json in
                    DispatchQueue.main.async {
                        self.stopActivityIndicator()
                        let jsonDic : NSDictionary = (json["data"] as? NSDictionary)!
                        print("JsonDic\(jsonDic)")
                        self.stopActivityIndicator()
                        print("Login json :: \(String(describing: jsonDic["user_type"]))")
                        
                        DispatchQueue.main.async(execute: {() -> Void in
                            
                            let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
                            let enterPasswordViewController = storyBoardMain.instantiateViewController(withIdentifier: "LogInViewController") as! LogInViewController
                            if jsonDic["user_type"] as! String == "mentee" {
                                enterPasswordViewController.loginMode = "Mentee"
                                enterPasswordViewController.emailValue = (jsonDic["email"] as? String)!
                            }else{
                                print("jsonDic as! String",jsonDic["user_type"] as! String)
                                enterPasswordViewController.loginMode = "Mentor"
                                enterPasswordViewController.emailValue = (jsonDic["email"] as? String)!
                            }
                            if(self.fromResetPassword == true){
                                let appDelegate = UIApplication.shared.delegate as? AppDelegate
                                let nav = UINavigationController(rootViewController: enterPasswordViewController)
                                nav.navigationBar.isHidden = true;
                                appDelegate?.window!.rootViewController = nav
                            }else{
                                self.navigationController?.pushViewController(enterPasswordViewController, animated: true)
                            }

                            print("navigate")
                        })
                    }
                } onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                }

                
                
        }
    }
    
    
    }
    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
    }
    */
//    func manageAutoLayOut(){
//        switch UIScreen.main.bounds.height{
//        case 480:
//            LoginMentorTopConstraint.constant = 264
//            loginMenteeTopConstraint.constant = 85
//        case 568:
//            LoginMentorTopConstraint.constant = 264
//            loginMenteeTopConstraint.constant = 85
//        case 667:
//            LoginMentorTopConstraint.constant = 312
//            loginMenteeTopConstraint.constant = 110
//        case 736:
//            LoginMentorTopConstraint.constant = 348
//            loginMenteeTopConstraint.constant = 124
//        case 812:
//            LoginMentorTopConstraint.constant = 386
//            loginMenteeTopConstraint.constant = 140
//        case 844:
//            LoginMentorTopConstraint.constant = 400
//            loginMenteeTopConstraint.constant = 150
//        case 896:
//            LoginMentorTopConstraint.constant = 428
//            loginMenteeTopConstraint.constant = 160
//        case 926:
//            LoginMentorTopConstraint.constant = 445
//            loginMenteeTopConstraint.constant = 170
//        default:
//            LoginMentorTopConstraint.constant = 50
//            loginMenteeTopConstraint.constant = 40
//        }
//    }

    func darkmodeChnage(){
        if self.valueMode == "dark" {
            textFieldEmailId.attributedPlaceholder = NSAttributedString(string: "Email",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldEmailId.textColor = UIColor(named: "MainTextColor")
        } else {
            textFieldEmailId.attributedPlaceholder = NSAttributedString(string: "Enter Email Here",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.darkGray])
            textFieldEmailId.textColor = UIColor(named: "MainTextColor")
        }
    }
}

extension BaseLoginViewController: UITextFieldDelegate {
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
}
