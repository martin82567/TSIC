//
//  RegistrationViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 08/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class RegistrationViewController: BaseViewController {

    var iconClick = true
    @IBOutlet weak var textFieldUsername: UITextField!
    @IBOutlet weak var textFieldEmailAddress: UITextField!
    @IBOutlet weak var textFieldPassword: UITextField!
    @IBOutlet weak var textFieldConfirmPassword: UITextField!
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
    }
    @IBAction func buttonHideUnhideAction(_ sender: Any) {
        if(iconClick == true) {
            textFieldPassword.isSecureTextEntry = false
        } else {
            textFieldPassword.isSecureTextEntry = true
        }
        iconClick = !iconClick
    }
    
    @IBAction func buttonRegisterAction(_ sender: Any) {
        let username = textFieldUsername.text
        let email = textFieldEmailAddress.text
        let password = textFieldPassword.text
        let confirmPassword = textFieldConfirmPassword.text
    }
    @IBAction func buttonLogInAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
   

}
