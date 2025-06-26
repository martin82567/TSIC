//
//  BioMetricAuthentication.swift
//  TakeStockInChildren
//
//  Created by Nikhil Batra on 12/07/23.
//  Copyright © 2023 Aquarious Technology. All rights reserved.
//

import UIKit
import LocalAuthentication

class BioMetricAuthentication {
    
    class func authenticateBiometrics(controller: UIViewController, isAuthenticated:(()->(Void))? = nil) {
        let context = LAContext()
        var error: NSError?
        
        if context.canEvaluatePolicy(.deviceOwnerAuthenticationWithBiometrics, error: &error) {
            let reason = "Identify yourself!"
            
            context.evaluatePolicy(.deviceOwnerAuthenticationWithBiometrics, localizedReason: reason) { success, authenticationError in
                DispatchQueue.main.async {
                    if success {
                        isAuthenticated?()
                    } else {
                        let alert = UIAlertController(title: "Error", message: "Biometrics could not bee veerified", preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        controller.present(alert, animated: true, completion: nil)
                    }
                }
            }
        } else {
            // no biometry
        }
    }
}
