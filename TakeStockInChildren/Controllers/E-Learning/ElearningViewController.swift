//
//  ElearningViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 17/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import AVFoundation
import AssetsLibrary
import Alamofire
class ElearningViewController: BaseViewController {
    
    @IBOutlet weak var topheaderimage: UIImageView!
    @IBOutlet weak var tblElearning: UITableView!
    
    @IBOutlet weak var backgroundimage: UIImageView!
    var dicELearnList = [NSDictionary]()
    var idDetails     : Int = 0
    var type          : String = ""
    var valuemode: String?
    
    override func viewDidLoad() {
        super.viewDidLoad()
        //self.valuemode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechnage()
        self.tabBarController?.tabBar.isHidden = false
        tblElearning.setContentOffset(.zero, animated:true)
        setupELearningList()
    }
    
    
    func darkmodechnage() {
        if self.valuemode == "dark" {
            topheaderimage.image = UIImage(named: "Arcdark11")
            backgroundimage.image = UIImage(named: "BG4")
        }
        else if self.valuemode == "light" {
            topheaderimage.image = UIImage(named: "Arc")
            backgroundimage.image = UIImage(named: "BackgroundImage")
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        let loginMode2 = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode2 == "Mentor" {
            
        }else{
            
        }
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:: Function Api Calling
    func setupELearningList () {
        var searchStr : String = ""
        if searchStr == "" {
            searchStr = " "
        } else {
            searchStr = ""
        }
        print("searchStr :: \(searchStr)")
        
        let searchDetails:NSMutableDictionary = [
            "search_keyword" : searchStr
        ]
        
        //print("ELearning searchDetails :: \(searchDetails)")
        if self.connectedToNetwork() {
            //self.startActivityIndicator()
            self.showActivityIndicatory(uiView: self.view)
            
            ApiManager.sharedInstance.getELearningList(postId: 1, searchDetails: searchDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    DispatchQueue.main.async(execute: {() -> Void in
                        let dataTemp = json["data"]! as! NSDictionary
                        print("elearninglist\(dataTemp)")
                        self.dicELearnList = dataTemp["e_learning_list"] as! [NSDictionary]
                        
                        if (self.dicELearnList.count > 0) {
                            self.tblElearning.reloadData()
                            self.actInd.stopAnimating()
                            self.stopActivityIndicator()
                        } else {
                            self.actInd.stopAnimating()
                            self.stopActivityIndicator()
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.actInd.stopAnimating()
                    let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
                         
                    if loginMode == "Mentor" {
                        self.logOutMentor()
                    }
                    else {
                        self.logOutMentee()
                    }
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                    */
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
    
    //MARK:: Function  
    func videoPreviewUIImage(moviePath: URL) -> UIImage? {
        let asset = AVURLAsset(url: moviePath)
        let generator = AVAssetImageGenerator(asset: asset)
        generator.appliesPreferredTrackTransform = true
        let timestamp = CMTime(seconds: 2, preferredTimescale: 60)
        if let imageRef = try? generator.copyCGImage(at: timestamp, actualTime: nil) {
            return UIImage(cgImage: imageRef)
        } else {
            return nil
        }
    }
    
    @objc func BtnELearnDetailDidTap(sender: UIButton) {
        let btnDetail = sender as! UIButton
        let index = Int(btnDetail.tag)
        //print("Clicked Index : \(String(describing: btnDetail.tag))")
        let typeScreen = dicELearnList[index]["type"] as! String
        if (sender as AnyObject).tag == index {
            if (typeScreen == "image") {
                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                let eLearnDetailsVC = storyboardELearning.instantiateViewController(withIdentifier: "ImageElearningViewController") as! ImageElearningViewController
                eLearnDetailsVC.dicELearnDetails = dicELearnList[sender.tag]
                print("FFFFFFFF",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as? String ?? ""
            self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            } else if(typeScreen == "video") {
                let storyboardELearning = UIStoryboard(name: "Main", bundle
                                                        
                                                        : nil)
                let eLearnDetailsVC = storyboardELearning.instantiateViewController(withIdentifier: "ElearningDetailsVideoViewController") as! ElearningDetailsVideoViewController
                eLearnDetailsVC.dicELearnDetails = dicELearnList[sender.tag]
                print("FFFFFFFF",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as? String ?? "";      self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            } else {
                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                let eLearnDetailsVC = storyboardELearning.instantiateViewController(withIdentifier: "ElearningDetailsVideoViewController") as! ElearningDetailsVideoViewController
                eLearnDetailsVC.dicELearnDetails = dicELearnList[sender.tag]
                print("other",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as? String ?? ""
                self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            }
        }
    }
}

// MARK:: UITableViewDatasource
extension ElearningViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return dicELearnList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ElearningTableViewCell") as! ElearningTableViewCell
        
        if self.valuemode == "dark" {
            cell.mainvIew.backgroundColor = .gray
        }
        else if self.valuemode == "light" {
            cell.mainvIew.backgroundColor = .white
        }
        if dicELearnList.count > 0 {
            let dicElearn = dicELearnList[indexPath.item]
            
            cell.labelHeadCell.text    = dicElearn["name"] as? String
            let htmlText               = (dicElearn["description"]  as? String)!
            cell.labelDescription.text = htmlText.htmlToString
            
            let mediaType = dicElearn["type"] as? String
            
            if let mediaTyp = mediaType {
                if mediaTyp == "image" {
                    cell.imageViewVideoSign.isHidden = true
                    let mediaType = dicElearn["file"] as? String
                    
                    let imgurl = TakeStockInChildrenConstant.UserImageElearningBaseURL+mediaType!
                    print("url000000",imgurl)
                    // image url
                    cell.imageViewCell.sd_setImage(with: URL(string: imgurl), placeholderImage: UIImage(named: "VedioImage"))
                    cell.imageViewCell.layer.cornerRadius = (cell.imageViewCell.frame.size.height)/2
                    cell.imageViewCell.clipsToBounds = true
                } else if (mediaTyp == "video") {
                    // video url
                    cell.imageViewVideoSign.isHidden = false
                    let mediaType = dicElearn["file"] as? String
                    
                    let imgurl = TakeStockInChildrenConstant.UserImageElearningBaseURL+mediaType!
                    print("IMAGEURLELEARNING\(imgurl)")
                    if imgurl.lowercased().range(of:".mp4") != nil {
                        DispatchQueue.global(qos: .background).async {
                            let image = self.videoPreviewUIImage(moviePath: URL(string: imgurl)!)
                            DispatchQueue.main.async {
                                cell.imageViewCell.image              = image
                                cell.imageViewCell.layer.cornerRadius = (cell.imageViewCell.frame.size.height)/2
                                cell.imageViewCell.clipsToBounds      = true
                            }
                        }
                    } else {
                        cell.imageViewCell.image = UIImage(named: "videoImage")
                    }
                } else {
                    cell.imageViewVideoSign.isHidden      = true
                    cell.imageViewCell.image              = UIImage(named: "UrlLogo")
                    cell.imageViewCell.layer.cornerRadius = (cell.imageViewCell.frame.size.height)/2
                    cell.imageViewCell.clipsToBounds      = true
                }
            }
            cell.buttonDetailstap.tag = indexPath.row
            cell.buttonDetailstap.addTarget(self, action: #selector(BtnELearnDetailDidTap), for: .touchUpInside)
        }
        
        return cell
    }
}

// MARK:: UITextFieldDelegate
extension ElearningViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
    }
}
