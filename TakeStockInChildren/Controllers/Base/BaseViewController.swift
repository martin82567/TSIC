//
//  BaseViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 08/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import SystemConfiguration
import NVActivityIndicatorView

class BaseViewController: UIViewController {
    
    let actInd: UIActivityIndicatorView = UIActivityIndicatorView()
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        // Do any additional setup after loading the view.
    }
    
    func totalName(dicData:NSDictionary) -> String {
        var TotalName = ""
        
        let firstName = dicData["firstname"] as? String
        let lastName = dicData["lastname"] as? String
        let middlename = dicData["middlename"] as? String
        
        if let fName = firstName {
            TotalName = fName
        }
        
        if let middleName = middlename {
            if middleName != ""{
                TotalName += " \(middleName)"
            }
        }
        
        if let LName = lastName{
            if LName != ""{
                TotalName += " \(LName)"
            }
        }
        return TotalName
    }
    
    //MARK:- Show activity Indicator
    func showActivityIndicatory(uiView: UIView) {
        actInd.frame = CGRect(x: 0 , y: 0, width: 150, height: 150)
        actInd.center = uiView.center
        actInd.hidesWhenStopped = true
        
        actInd.style = UIActivityIndicatorView.Style.gray
        actInd.tintColor = UIColor.black
        uiView.addSubview(actInd)
        actInd.startAnimating()
    }
    
    func startActivityIndicator() { 
        let activityData = ActivityData()
        NVActivityIndicatorView.DEFAULT_TYPE = .ballRotateChase
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now()) {
            NVActivityIndicatorPresenter.sharedInstance.startAnimating(activityData)
        }
    }
    
    func stopActivityIndicator() {
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now()) {
            NVActivityIndicatorPresenter.sharedInstance.stopAnimating()
        }
    }
    
    func isValidEmail(emailStr: String) -> Bool {
        let emailRegEx = "(?:[a-z0-9!#$%\\&'*+/=?\\^_`{|}~-]+(?:\\.[a-z0-9!#$%\\&'*+/=?\\^_`{|}"+"~-]+)*|\"(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21\\x23-\\x5b\\x5d-\\"+"x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])*\")@(?:(?:[a-z0-9](?:[a-"+"z0-9-]*[a-z0-9])?\\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\\[(?:(?:25[0-5"+"]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-"+"9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x21"+"-\\x5a\\x53-\\x7f]|\\\\[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f])+)\\])"
        
        let emailTest = NSPredicate(format:"SELF MATCHES[c] %@", emailRegEx)
        return emailTest.evaluate(with: emailStr)
    }
    
    // MARK:- Show alert message
    func showAlert(_sourceController:UIViewController,_msg:String) {
        let alertController = UIAlertController(title: "Take Stock In Children", message: _msg, preferredStyle: .alert);
        let actionOk = UIAlertAction(title: "Ok", style: .cancel) { (action) in
        }
        alertController.addAction(actionOk)
        _sourceController.present(alertController, animated: true, completion: nil)
    }
    
    func showAlertView(title : String, msg :String, controller:UIViewController, okClicked : @escaping ()->()){
        let alertController = UIAlertController(title: title, message: msg, preferredStyle: UIAlertController.Style.alert)
        let okAction = UIAlertAction(title: "OK", style: UIAlertAction.Style.default) { (result : UIAlertAction) -> Void in
            print("OK")
            okClicked()
        }
        alertController.addAction(okAction);
        controller.present(alertController, animated: true, completion: nil)
    }
    
    func showAlertAction(withTitle title: String?, message: String?) {
        let alert = UIAlertController(title: title, message: message, preferredStyle: .alert)
        let action = UIAlertAction(title: "OK", style: .cancel, handler: nil)
        alert.addAction(action)
        present(alert, animated: true, completion: nil)
    }
    //    //MARK: Button Action
    //    @IBAction func buttonBackAction(_ sender: Any) {
    //        self.navigationController?.popViewController(animated: true)
    //        //self.dismiss(animated: true, completion:nil)
    //    }
    
    
}

extension UIViewController {
    
    //check if network connection can be made
    func connectedToNetwork() -> Bool {
        var zeroAddress = sockaddr_in()
        zeroAddress.sin_len = UInt8(MemoryLayout<sockaddr_in>.size)
        zeroAddress.sin_family = sa_family_t(AF_INET)
        
        guard let defaultRouteReachability = withUnsafePointer(to: &zeroAddress, {
            $0.withMemoryRebound(to: sockaddr.self, capacity: 1) {
                SCNetworkReachabilityCreateWithAddress(nil, $0)
            }
        }) else {
            return false
        }
        
        var flags: SCNetworkReachabilityFlags = []
        if !SCNetworkReachabilityGetFlags(defaultRouteReachability, &flags) {
            return false
        }
        
        let isReachable = flags.contains(.reachable)
        let needsConnection = flags.contains(.connectionRequired)
        
        return (isReachable && !needsConnection)
    }
    
    func showAlertForInternet (viewController:UIViewController) {
        let alert = UIAlertController(title: "Error",
                                      message: "Please check your internet connection",
                                      preferredStyle: .alert)
        let settingAction = UIAlertAction(title: NSLocalizedString("SETTING", comment: "setting action"), style: .default, handler: {(_ action: UIAlertAction) -> Void in
            
            guard let settingsUrl = URL(string: UIApplication.openSettingsURLString) else {
                return
            }
            
            if UIApplication.shared.canOpenURL(settingsUrl) {
                UIApplication.shared.open(settingsUrl, completionHandler: { (success) in
                    print("Settings opened: \(success)") // Prints true
                })
            }
        })
        let cancelAction = UIAlertAction(title: "CANCEL", style: .cancel, handler: nil)
        
        alert.addAction(settingAction)
        alert.addAction(cancelAction)
        viewController.present(alert, animated: true, completion: nil)
    }
    
    // Function for background thread
    func BG(_ block: @escaping ()->Void) {
        DispatchQueue.global(qos: .default).async(execute: block)
    }
    
    // Function for main thread (For user interface(UI))
    func UI(_ block: @escaping ()->Void) {
        DispatchQueue.main.async(execute: block)
    }
    
}

//MARK:- customize NSMutableAttributedString
extension String {
    func trim() -> String {
        return self.trimmingCharacters(in: CharacterSet.whitespacesAndNewlines);
    }
    
    var htmlToAttributedString: NSAttributedString? {
        guard let data = data(using: .utf8) else { return NSAttributedString() }
        do {
            return try NSAttributedString(data: data, options: [.documentType: NSAttributedString.DocumentType.html, .characterEncoding:String.Encoding.utf8.rawValue], documentAttributes: nil)
        } catch {
            return NSAttributedString()
        }
    }
    var htmlToString: String {
        return htmlToAttributedString?.string ?? ""
    }
}

extension UIImageView {
    func downloaded(from url: URL, contentMode mode: UIView.ContentMode = .scaleAspectFit) {  // for swift 4.2 syntax just use ===> mode: UIView.ContentMode
        contentMode = mode
        URLSession.shared.dataTask(with: url) { data, response, error in
            guard
                let httpURLResponse = response as? HTTPURLResponse, httpURLResponse.statusCode == 200,
                let mimeType = response?.mimeType, mimeType.hasPrefix("image"),
                let data = data, error == nil,
                let image = UIImage(data: data)
                else { return }
            DispatchQueue.main.async() {
                self.image = image
            }
            }.resume()
    }
    func downloaded(from link: String, contentMode mode: UIView.ContentMode = .scaleAspectFit) {  // for swift 4.2 syntax just use ===> mode: UIView.ContentMode
        guard let url = URL(string: link) else { return }
        downloaded(from: url, contentMode: mode)
    }
}


extension String {
    func toImage() -> UIImage? {
        if let data = Data(base64Encoded: self, options: .ignoreUnknownCharacters){
            return UIImage(data: data)
        }
        return nil
    }
}

