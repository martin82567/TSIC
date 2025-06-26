//
//  ElearningDetailsVideoViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 18/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import WebKit
import Alamofire
class VideoPlayElearningTableViewCell: UITableViewCell,UITextViewDelegate {
    
    @IBOutlet weak var webVwELearn: WKWebView!
}
class ElearningDetailsVideoViewController: BaseViewController, WKUIDelegate {
    @IBOutlet weak var tableunderView: UIView!
    
    @IBOutlet weak var subview: UIView!
    @IBOutlet weak var backgroundimge: UIImageView!
    @IBOutlet weak var lableVideonotfound: UILabel!
    @IBOutlet weak var tableViewElearningVideo: UITableView!
    @IBOutlet weak var webViewVideoUpload: UIWebView!
    @IBOutlet weak var tableViewElearningURL: UITableView!
    
    private var isPlayVideo = false
    //var isPlayVideo = false
    var dicELearnDetails = NSDictionary()
    var idDetails : Int = 0
    var type : String = ""
    
    var cellVideo = VideoElearningTableViewCell()
    var cellUrl = UrlTableViewCell()
    var valuemode : String?
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        //self.valuemode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechnage()
        lableVideonotfound.isHidden = true
        // Do any additional setup after loading the view.
    }
    
    func darkmodechnage() {
        if self.valuemode == "dark" {
            backgroundimge.image = UIImage(named: "BG4")
            subview.backgroundColor = UIColor(hex: "#0E0F27")
        }
        else if self.valuemode == "light" {
            backgroundimge.image = UIImage(named: "BackgroundImage")
            subview.backgroundColor = .white
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewElearningVideo.setContentOffset(.zero, animated:true)
        tableViewElearningURL.setContentOffset(.zero, animated:true)
        if (type == "url") {
            tableViewElearningURL.isHidden = false
            tableViewElearningVideo.isHidden = true
        } else {
            tableViewElearningURL.isHidden = true
            tableViewElearningVideo.isHidden = false
        }
       // self.getELearningDetails()
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    @IBAction func buttonTapUrl(_ sender: Any) {
        
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:: Function Api Calling
    func getELearningDetails() {
        
       // guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getELearningDetails(ELearningID: String(idDetails), onSuccess: { json in
                DispatchQueue.main.async {
                    
                    print("ELearningDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        self.dicELearnDetails = json["e_learning_details"] as? NSDictionary ?? [:]
                        print("details data",self.dicELearnDetails)
                        if self.dicELearnDetails.count == 0 {
                            self.showAlert(_sourceController: self, _msg: "Session no longer to active")
                        }
                        if(self.type == "url"){
                            self.tableViewElearningURL.reloadData()
                            self.stopActivityIndicator()
                        }
                        else{
                            self.tableViewElearningVideo.reloadData()
                            self.stopActivityIndicator()
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                    if loginMode == "Mentor" {
                        self.logOutMentor()
                    }
                    else {
                        self.logOutMentee()
                    }
                
//                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
//                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
//                    self.present(alert, animated: true, completion: nil)
//
                })
            })
        } else {
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
                                nav.navigationBar.isHidden = true;
                                nav.navigationBar.barStyle = .default
                                appDelegate.window?.rootViewController = nav
                                
                            }
                        }else{
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
    
    
    //MARK:- WebView Delegate Method
    func webView(_ webView: UIWebView, didFailLoadWithError error: Error) {
        //labelURLLoadingFail.isHidden = false
        print("web view loading fail : ",error.localizedDescription)
    }
    
    @IBAction func buttonUrlTap(_ sender: Any) {
        let getIndex = sender as! UIButton
        let index = Int(getIndex.tag)
        print(index)
        //tableView is your table view object
        let cell = tableViewElearningURL.cellForRow(at: NSIndexPath(row: index, section: 0) as IndexPath) as! UrlTableViewCell
        
        let strURL = cell.labelURL.text!
        //if (sender as AnyObject).tag == index {
        if let url = URL(string: strURL) {
            UIApplication.shared.open(url, options: [:])
        }
    }
    
    //MARK:: Functions customize NSMutableAttributedString
    func convertToInlineImageFormat(htmlStrr:String, fontType: UIFont) -> NSMutableAttributedString {
        let htmlStringgg = htmlStrr as String
        let content = try! NSMutableAttributedString(
            data: htmlStringgg.data(using: String.Encoding.unicode, allowLossyConversion: true)!,
            options: [.documentType: NSAttributedString.DocumentType.html],
            documentAttributes: nil)
        //content.addAttribute(NSForegroundColorAttributeName, value: UIColor.white, range: NSRange(location: 0, length: htmlStrr.length))
        
        // Resizing Inline images
        content.enumerateAttribute(NSAttributedString.Key.attachment, in: NSMakeRange(0, content.length), options: NSAttributedString.EnumerationOptions.init(rawValue: 0), using: { (value, range, stop) -> Void in
            /*if let attachement = value as? NSTextAttachment {
             let image = attachement.image(forBounds: attachement.bounds, textContainer: NSTextContainer(), characterIndex: range.location)
             let screenSize: CGRect = UIScreen.main.bounds
             if (image?.size.width)! > screenSize.width - 2 {
             let size = (screenSize.width - 2)/((image?.size.width)!)
             let newImage = image?.resizableImage(withCapInsets: size)
             let newAttribut = NSTextAttachment()
             
             newAttribut.image = newImage
             content.addAttribute(NSAttributedString.Key.attachment, value: newAttribut, range: range)
             
             }
             }*/
            // ARTICLE DESCRIPTION FONT
            //let replacementFont = Contants.descFont
            
            let fontSizeDescribtion = UserDefaults.standard.float(forKey: "FontSize")
            var fontSize :Float = 0.0
            if fontSizeDescribtion == 0 {
                fontSize = 18
            } else {
                fontSize = Float(fontSizeDescribtion)
            }
            if fontType.fontName == "Helvetica Neue" {
                fontSize = 18
            }
            
            let fontDesc =  UIFont(name: fontType.fontName, size: CGFloat(Float(fontSize)))
            
            content.addAttribute(NSAttributedString.Key.font, value: fontDesc!, range: NSRange(location: 0, length: content.length))
            if type == "image" {
                content.addAttribute(NSAttributedString.Key.foregroundColor, value: UIColor.black, range: NSRange(location: 0, length: content.length))
            } else {
                content.addAttribute(NSAttributedString.Key.foregroundColor, value: UIColor.white, range: NSRange(location: 0, length: content.length))
            }
            
        }) // Content block
        
        return content
    }
    //    func textView(_ textView: UITextView, shouldInteractWith URL: URL, in characterRange: NSRange) -> Bool {
    //        if (URL.absoluteString == linkUrl) {
    //            UIApplication.shared.openURL(URL)
    //        }
    //        return true
    //    }
    //}
}

//// MARK:: UITableViewDatasource
//extension ElearningDetailsVideoViewController: UITableViewDataSource, UITableViewDelegate {
//    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
//        if (tableView == tableViewElearningVideo) {
//            return 2
//        }
//        return 1
//    }
//
//    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
//        if (tableView == tableViewElearningVideo) {
//            if indexPath.row == 0 {
//                let videoPlayCell = tableView.dequeueReusableCell(withIdentifier: "VideoPlayElearningTableViewCell") as! VideoPlayElearningTableViewCell
//                if dicELearnDetails.count > 0 {
//                    let mediaType = dicELearnDetails["type"] as? String
//
//                    var videoURLStr : String = ""
//                    if (dicELearnDetails["file"] == nil) {
//                        videoURLStr = dicELearnDetails["file"] as! String
//                    } else {
//                        videoURLStr = TakeStockInChildrenConstant.ELearningBaseURL.appending(dicELearnDetails["file"] as! String)
//                    }
//
//                    let videoURL = NSURL (string: videoURLStr)
//                    print("Elearn videoURL:: \(String(describing: videoURL)))")
//
//
//                    //videoPlayCell.webVwELearn.load(URLRequest(url: videoURL! as URL) as URLRequest)
//
//                   // videoPlayCell.webVwELearn.uiDelegate = self//requiresUserActionForMediaPlayback = false
//
//
//                    if self.isPlayVideo == true {
//                        // initialize the video player with the url
//                        videoPlayCell.webVwELearn.load(URLRequest(url: videoURL! as URL) as URLRequest)
//
//                        videoPlayCell.webVwELearn.uiDelegate = self
//                    } else {
//                        //lblElearnStatus.isHidden = false
//                    }
//                }
//                return videoPlayCell
//            } else {
//                let cell = tableView.dequeueReusableCell(withIdentifier: "VideoElearningTableViewCell") as! VideoElearningTableViewCell
//                if dicELearnDetails.count > 0 {
//                    cell.labelTitleCell.text  = self.dicELearnDetails["name"] as? String
//                    let htmlText = (dicELearnDetails["description"]  as? String)
//                    cell.labelDescriptionCell.text = htmlText?.htmlToString
//                }
//                return cell
//            }
//        } else {
//            let cell = tableView.dequeueReusableCell(withIdentifier: "UrlTableViewCell") as! UrlTableViewCell
//
//            if dicELearnDetails.count > 0 {
//                cell.labelTitle.text = self.dicELearnDetails["name"] as? String
//                cell.labelURL.text = self.dicELearnDetails["url"] as? String
//
//                let htmlText = String(describing: self.dicELearnDetails["description"]!)
//                cell.labelDescription.text = htmlText.htmlToString
//                //cell.webViewShow.loadHTMLString(htmlText, baseURL: nil)
//                //
//                //let webView2 = WKWebView()
//               // webView2.loadHTMLString("<html><body><p>Hello!</p></body></html>", baseURL: nil)
//
//            }
//            cell.buttonUrlTap.tag = indexPath.row
//            cell.buttonUrlTap.addTarget(self, action: #selector(buttonTapUrl(_:)), for: .touchUpInside)
//
//            return cell
//        }
//    }
//
//    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
//        if (tableView == tableViewElearningVideo) {
//            if indexPath.row == 0 {
//                return 200
//            }
//            return UITableView.automaticDimension
//        }
//        return 0
//    }
//
//    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
//        if (tableView == tableViewElearningVideo) {
//            if indexPath.row == 0 {
//                self.isPlayVideo = true
//                self.tableViewElearningVideo.reloadRows(at: [IndexPath.init(row: 0, section: 0)], with: .none)
//                //Rows(at: 0, with: UITableView.RowAnimation.none)
//            }
//        }
//    }
//
//}

// MARK:: UITableViewDatasource
extension ElearningDetailsVideoViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if (tableView == tableViewElearningVideo) {
            let cell = tableView.dequeueReusableCell(withIdentifier: "VideoElearningTableViewCell") as! VideoElearningTableViewCell
            if dicELearnDetails.count > 0 {
                cell.labelTitleCell.text  = self.dicELearnDetails["name"] as? String ?? ""
                let htmlText = (dicELearnDetails["description"]  as? String) ?? ""
                cell.labelDescriptionCell.text = htmlText.htmlToString
                
                //let mediaType = dicELearnDetails["type"] as? String ?? ""
                
                var videoURLStr : String = ""
                let imageURL = dicELearnDetails["file"] as? String
                print("imageURL\(String(describing: imageURL))")
                if (imageURL == "") {
                    print("NOVIDEOTOSHOW>>")
                    //videoURLStr = dicELearnDetails["file"] as! String
                    lableVideonotfound.isHidden = false
                } else {
                    videoURLStr = TakeStockInChildrenConstant.UserImageElearningBaseURL.appending(dicELearnDetails["file"] as! String)
                    let videoURL = NSURL (string: videoURLStr)
                    print("Elearn videoURL:: \(String(describing: videoURL)))")
                    webViewVideoUpload.loadRequest(URLRequest(url: videoURL! as URL) as URLRequest)
                }
            }
            return cell
        } else {
            let cell = tableView.dequeueReusableCell(withIdentifier: "UrlTableViewCell") as! UrlTableViewCell
            /*
            if self.valuemode == "dark" {
                cell.contentView.backgroundColor = UIColor(hex: "#0E0F27")
            }
            else if self.valuemode == "light" {
                cell.contentView.backgroundColor = .white
            }
            */
            if dicELearnDetails.count > 0 {
                cell.labelTitle.text = self.dicELearnDetails["name"] as? String ?? ""
                cell.labelURL.text = self.dicELearnDetails["url"] as? String ?? ""
                
                let htmlText = String(describing: self.dicELearnDetails["description"]!)
                print("htmltext\(htmlText)")
                cell.labelDescription.text = htmlText.htmlToString
            }
            cell.buttonUrlTap.tag = indexPath.row
            cell.buttonUrlTap.addTarget(self, action: #selector(buttonTapUrl(_:)), for: .touchUpInside)
            return cell
        }
    }
    
}

