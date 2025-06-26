//
//  HelpTabViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 02/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
// http://takestockinchildren.org/mentorapp/home

import UIKit
import WebKit

class HelpTabViewController: BaseViewController,WKNavigationDelegate, WKUIDelegate {
    @IBOutlet weak var backgroundimage: UIImageView!
    @IBOutlet weak var topheaderimage: UIImageView!
    var getUrl:URL? = nil
    var valueMode: String?
    @IBOutlet weak var webView: WKWebView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        callToGetApi()
        
        
       // let loginMode = UserDefaults.standard.value(forKey: "loginMode") as? String
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
      //  darkmodechange()
//        if let checkLoginMode = loginMode {
//            if checkLoginMode == "Mentee" {
//               guard let url = URL(string: "http://tsicmobileappfaq.s3-website-us-east-1.amazonaws.com/mentor.html") else { return }
//                let request = URLRequest(url: getUrl!)
//                       webView.load(request)
//                //http://tsicmobileappfaq.s3-website-us-east-1.amazonaws.com/mentee.html
//                //http://tsicmobileappfaq.s3-website-us-east-1.amazonaws.com/mentor.html
//            } else {
//                guard let url = URL(string: "http://tsicmobileapp-faq.s3-website-us-east-1.amazonaws.com/mentee.html") else { return }
//                let request = URLRequest(url: getUrl! )
//                        webView.load(request)
//            }
//        }
        
     
        self.startActivityIndicator()
           // add activity
        self.webView.navigationDelegate = self
        // Do any additional setup after loading the view.
    }
    //Mark:- Get Api Call:-
    func callToGetApi() {
        var str:String = ""
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as? String
        MentorApiManager().loadAppHelp(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            if status == true {
               print("RESPONSE",response)
                let Data = response["data"] as! NSDictionary
                if loginMode == "Mentee" {
                    str = Data["mentee_faq"] as! String
                } else {
                    str = Data["mentor_faq"] as! String
                }
               
                self.getUrl = URL(string: str)
                self.UI{
                    let request = URLRequest(url: self.getUrl!)
                    self.webView.load(request)
                }
            } else {
               
            }
        })
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
