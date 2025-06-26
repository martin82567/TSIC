//
//  ToolKitViewController.swift
//  TakeStockInChildren
//
//  Created by AquariousMnabook on 03/08/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//
import UIKit
import WebKit

class ToolKitViewController: BaseViewController,WKNavigationDelegate, WKUIDelegate {
    @IBOutlet weak var backgroundimage: UIImageView!
    @IBOutlet weak var topheaderimage: UIImageView!
    
    var valueMode: String?
    @IBOutlet weak var webView: WKWebView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as? String
        
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechange()
        
        
        if let checkLoginMode = loginMode {
            if checkLoginMode == "Mentee" {
               guard let url = URL(string: "https://drive.google.com/file/d/14ZEN4PAIwPTlhdUv5j9XnXsbv6oZO8AY/view") else { return }
                       let request = URLRequest(url: url)
                       webView.load(request)
                //http://tsicmobileappfaq.s3-website-us-east-1.amazonaws.com/mentee.html
                //http://tsicmobileappfaq.s3-website-us-east-1.amazonaws.com/mentor.html
            } else {
                guard let url = URL(string: "https://drive.google.com/file/d/14ZEN4PAIwPTlhdUv5j9XnXsbv6oZO8AY/view") else { return }
                        let request = URLRequest(url: url)
                        webView.load(request)
            }
        }
        
     
        self.startActivityIndicator()
           // add activity
        self.webView.navigationDelegate = self
        // Do any additional setup after loading the view.
    }
    
    func darkmodechange() {
        if self.valueMode == "dark" {
            backgroundimage.image = UIImage(named: "BG4")
            topheaderimage.image = UIImage(named: "Arcdark11")
        }
        else if self.valueMode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            topheaderimage.image = UIImage(named: "Arc")
        }
    }
    
    
    func webView(_ webView: WKWebView, didFinish navigation: WKNavigation!) {
        self.stopActivityIndicator()
    }

    func webView(_ webView: WKWebView, didFail navigation: WKNavigation!, withError error: Error) {
        self.stopActivityIndicator()
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
}
