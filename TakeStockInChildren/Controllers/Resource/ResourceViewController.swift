//
//  ResourceViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 22/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import CoreLocation
import AVFoundation
import AssetsLibrary
import Alamofire
class ResourceViewController: BaseViewController , CLLocationManagerDelegate ,UITextFieldDelegate{
    
    @IBOutlet weak var searchView: UIView!
    @IBOutlet weak var topBackgroundImage: UIImageView!
    @IBOutlet weak var backgroundimage: UIImageView!
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var textFieldResourceSearch: UITextField!
    @IBOutlet weak var tableViewResource: UITableView!
    
    //var arrNear = [NSDictionary]()
    var arrResource = [NSDictionary]()
    let appDelegate = AppDelegate()
    var tapGesture = UITapGestureRecognizer()
    var strState = ""
    var arrNeartest = NSMutableArray()
    var locationManager = CLLocationManager()
    var dicELearnList = [NSDictionary]()
    var valueMode : String?

    override func viewDidLoad() {
        super.viewDidLoad()
       // self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechanged()
        tapGesture = UITapGestureRecognizer(target: self, action: #selector(dismissKeyboard))
        tapGesture.cancelsTouchesInView = true
         self.getResourceList()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewResource.setContentOffset(.zero, animated:true)
        appDelegate.determineMyCurrentLocation()
       // setupELearningList()
       
    }
    
    func darkmodechanged() {
        if self.valueMode == "dark" {
            self.backgroundimage.image = UIImage(named: "BG4")
            self.mainView.backgroundColor = UIColor(hex: "#0E0F27")
            self.topBackgroundImage.image = UIImage(named: "BG2")
        }
        else if self.valueMode == "light"{
            self.backgroundimage.image = UIImage(named: "BackgroundImage")
            self.mainView.backgroundColor = .white
            self.topBackgroundImage.image = UIImage(named: "Arc")
        }
    }
    
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
//
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
                                self.appDelegate.window?.rootViewController = nav
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
                                self.appDelegate.window?.rootViewController = nav
                                
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
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:-API Calling
    func getResourceList(){
        print(textFieldResourceSearch.text!)
        var searchStr : String = textFieldResourceSearch.text!
        
        if searchStr == "" {
            searchStr = " "
        } else {
            searchStr = textFieldResourceSearch.text!
        }
        print("searchStr :: \(searchStr)")
        
        locationManager = CLLocationManager()
        locationManager.delegate = self
        locationManager.requestWhenInUseAuthorization()
        locationManager.desiredAccuracy = kCLLocationAccuracyBest
        locationManager.startUpdatingLocation()
        locationManager.startMonitoringSignificantLocationChanges()
        
        if CLLocationManager.locationServicesEnabled() {
            locationManager.startUpdatingLocation()
            //locationManager.startUpdatingHeading()
        }
        
        var lat = CLLocationDegrees()
        var long = CLLocationDegrees()
        if(locationManager.location?.coordinate.latitude != nil) && (locationManager.location?.coordinate.longitude != nil){
            searchView.isHidden = false

                    let latitude: CLLocationDegrees = (locationManager.location?.coordinate.latitude)!
                    let longitude: CLLocationDegrees = (locationManager.location?.coordinate.longitude)!
                    let location = CLLocation(latitude: latitude, longitude: longitude) //changed!!!
                    
                    lat = latitude
                    long = longitude
                
        if CLLocationManager.locationServicesEnabled() {
            switch(CLLocationManager.authorizationStatus()) {
            case .authorizedAlways, .authorizedWhenInUse:
                print("Authorize.")
                
      
           
               
             
                CLGeocoder().reverseGeocodeLocation(location, completionHandler: {(placemarks, error) -> Void in
                    if error != nil {
                        return
                    } else if let country = placemarks?.first?.country,
                        let city = placemarks?.first?.administrativeArea {
                        print(country)
                        self.strState = city
                        print("City\(self.strState)")
                        //    let searchDetails:NSMutableDictionary = [
                        //    "latitude" : "lat,//UserDefaults.standard.data(forKey: "latitude")!,
                        //    "longitude" : "long,//UserDefaults.standard.data(forKey: "longitude")!,
                        //    "state_code" : self.strState,//"IN",
                        //    "search_keyword" : searchStr
                        //    ]
                        
                        let searchDetails:NSMutableDictionary = [
//                            "latitude" : "27.994402",//UserDefaults.standard.data(forKey: "latitude")!,
//                            "longitude" : "-81.760254",//UserDefaults.standard.data(forKey: "longitude")!,
//                            "state_code" : "FL",//"IN",
                            "search_keyword" : searchStr
                        ]
                        print("Resource searchDetails :: \(searchDetails)")
                        if self.connectedToNetwork() {
                            self.startActivityIndicator()
                            
                            ApiManager.sharedInstance.getResourceList(postId: 1, searchDetails: searchDetails, onSuccess: { json in
                                DispatchQueue.main.async {
                                    DispatchQueue.main.async(execute: {() -> Void in
                                        let dataTemp = json["data"]! as! NSDictionary
                                        self.dicELearnList = dataTemp["e_learning_list"] as! [NSDictionary]
                                        
                                        if (self.dicELearnList.count > 0) {
                                            self.tableViewResource.reloadData()
                                            self.actInd.stopAnimating()
                                            self.stopActivityIndicator()
                                        } else {
                                        self.tableViewResource.reloadData()
                                            self.actInd.stopAnimating()
                                            self.stopActivityIndicator()
                                        }
                                    })
                                }
                            }, onFailure: { error in
                                //  alert.show(self, sender: self)
                                DispatchQueue.main.sync(execute: {() -> Void in
                                    self.stopActivityIndicator()
                                    self.logOutMentee()
                                    /*
                                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                                    self.present(alert, animated: true, completion: nil)
                                    */
                                    
                                    
                                })
                            })
                        } else {
                            self.showAlert(_sourceController: self,_msg: "Unable to connect")
                        }
                    } else {
                    }
                })
                break
                
            case .notDetermined:
                self.showAlertAction(withTitle: "Sorry", message: "Location services disable")
                print("Not determined.")
                break
                
            case .restricted:
                self.showAlertAction(withTitle: "Sorry", message: "Location services disable")
                print("Restricted.")
                break
                
            case .denied:
                self.showAlertAction(withTitle: "Sorry", message: "Location services disable")
                print("Denied.")
            }
        }
        } else {
            searchView.isHidden = true
            let msg = "Turn on Location Services To Show \"Resources\" To Determine your Location."
           
            let alertController = UIAlertController (title: msg, message: "", preferredStyle: .alert)

                let settingsAction = UIAlertAction(title: "Settings", style: .default) { (_) -> Void in

                    guard let settingsUrl = URL(string: UIApplication.openSettingsURLString) else {
                        return
                    }

                    if UIApplication.shared.canOpenURL(settingsUrl) {
                        UIApplication.shared.open(settingsUrl, completionHandler: { (success) in
                            print("Settings opened: \(success)") // Prints true
                        })
                    }
                }
                alertController.addAction(settingsAction)
                let cancelAction = UIAlertAction(title: "Cancel", style: .default, handler: nil)
                alertController.addAction(cancelAction)

                present(alertController, animated: true, completion: nil)
            //self.showAlertAction(withTitle: "Use Location?", message: "Location services disable")
        }
}
    
    // MARK:: UITextFieldDelegate
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        self.view.removeGestureRecognizer(tapGesture)
        textField.resignFirstResponder()
        self.getResourceList()
        tableViewResource.reloadData()
        return true
    }
    
    func textFieldShouldBeginEditing(_ textField: UITextField) -> Bool {
        self.view.addGestureRecognizer(tapGesture)
        textFieldResourceSearch.text = ""
        return true
    }
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        self.view.removeGestureRecognizer(tapGesture)
        textFieldResourceSearch.resignFirstResponder()
        self.getResourceList()
    }
    
    @objc func dismissKeyboard() {
        self.view.removeGestureRecognizer(tapGesture)
        self.view.endEditing(true)
    }
    
    //MARK:-NearMeCall
    @IBAction func buttonCallAction(_ sender: Any) {
        let btnCall = sender as! UIButton
        let index = Int(btnCall.tag)
        print("Clicked Index : \(String(describing: btnCall.tag))")
        
        if (sender as AnyObject).tag == index {
            let phnoStr : String = (arrResource[index]["work_phone"])! as! String
            
            print("phnoStr:: \(phnoStr)")
            let unwantedChars = CharacterSet(charactersIn: "\"() -")
            let requiredString : NSString = phnoStr.components(separatedBy: unwantedChars).joined(separator: "") as NSString
            print("requiredString:: \(requiredString)")
            
            if let url = NSURL(string: "tel://\(requiredString))"), UIApplication.shared.canOpenURL(url as URL) {
                //UIApplication.shared.openURL(url as URL)
                UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)//(url, options: [:], completionHandler: nil)
            } else {
                let alert = UIAlertController(title: "Sorry", message: "Calling not supported by this device", preferredStyle: UIAlertController.Style.alert)
                
                let cancelAction = UIAlertAction(title: "Ok", style: .cancel) { (_) -> Void in
                }
                alert.addAction(cancelAction)
                self.present(alert, animated: true, completion: nil)
            }
        }
    }
    
//    //MARK:-StateWideCall
//    @IBAction func buttonStateWideCallAction(_ sender: Any) {
//        let btnCall = sender as! UIButton
//        let index = Int(btnCall.tag)
//        print("Clicked Index : \(String(describing: btnCall.tag))")
//
//        if (sender as AnyObject).tag == index {
//            let phnoStr : String = (arrResource[index]["work_phone"])! as! String
//
//            print("phnoStr:: \(phnoStr)")
//            if let url = NSURL(string: "tel://\(phnoStr))"), UIApplication.shared.canOpenURL(url as URL) {
//                //UIApplication.shared.openURL(url as URL)
//                UIApplication.shared.open(url as URL, options: [:], completionHandler: nil)//(url, options: [:], completionHandler: nil)
//            } else {
//                let alert = UIAlertController(title: "Sorry", message: "Calling not supported by this device", preferredStyle: UIAlertController.Style.alert)
//
//                let cancelAction = UIAlertAction(title: "Ok", style: .cancel) { (_) -> Void in
//
//                }
//                alert.addAction(cancelAction)
//                self.present(alert, animated: true, completion: nil)
//            }
//        }
//    }
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
    //MARK:-NearMeSentMail
    @IBAction func buttonSentMailAction(_ sender: Any) {
        let btnEmail = sender as! UIButton
        let index = Int(btnEmail.tag)
        print("Clicked Index : \(String(describing: btnEmail.tag))")
        if (sender as AnyObject).tag == index {
            let emailStr : String = (arrResource[index]["email"])! as! String
            
            print("emailStr:: \(emailStr)")
            // define allowed character set
            let `set` = CharacterSet.urlHostAllowed
            
            // create the URL
            let url = URL(string: "mailto:?to=\(emailStr.addingPercentEncoding(withAllowedCharacters: `set`) ?? "")")
            // load the URL
            if let url = url {
                if #available(iOS 10.0, *) {
                    UIApplication.shared.open(url)
                } else {
                    UIApplication.shared.openURL(url)
                }
            }
        }
        
    }
    
//    //MARK:-StateWideSentMail
//    @IBAction func buttonStateWideSentMailAction(_ sender: Any) {
//        let btnEmail = sender as! UIButton
//        let index = Int(btnEmail.tag)
//        print("Clicked Index : \(String(describing: btnEmail.tag))")
//        if (sender as AnyObject).tag == index {
//            let emailStr : String = (arrResource[index]["email"])! as! String
//
//            print("emailStr:: \(emailStr)")
//            // define allowed character set
//            let `set` = CharacterSet.urlHostAllowed
//
//            // create the URL
//            let url = URL(string: "mailto:?to=\(emailStr.addingPercentEncoding(withAllowedCharacters: `set`) ?? "")")
//            // load the URL
//            if let url = url {
//                if #available(iOS 10.0, *) {
//                    UIApplication.shared.open(url)
//                } else {
//                    UIApplication.shared.openURL(url)
//                }
//            }
//        }
//
//    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
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
                print("other",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as! String ?? ""
                self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            } else if(typeScreen == "video") {
                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                let eLearnDetailsVC = storyboardELearning.instantiateViewController(withIdentifier: "ElearningDetailsVideoViewController") as! ElearningDetailsVideoViewController
                eLearnDetailsVC.dicELearnDetails = dicELearnList[sender.tag]
                print("other",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as! String ?? ""
                self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            }
            else {
                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                let eLearnDetailsVC = storyboardELearning.instantiateViewController(withIdentifier: "ElearningDetailsVideoViewController") as! ElearningDetailsVideoViewController
                eLearnDetailsVC.dicELearnDetails = dicELearnList[sender.tag]
                print("other",dicELearnList[sender.tag])
                eLearnDetailsVC.idDetails = dicELearnList[index]["id"] as! Int
                eLearnDetailsVC.type = dicELearnList[index]["type"] as! String ?? ""
                self.navigationController?.pushViewController(eLearnDetailsVC, animated: true)
            }
        }
    }
}

// MARK:: UITableViewDatasource
extension ResourceViewController: UITableViewDataSource {
    
//    func numberOfSections(in tableView: UITableView) -> Int {
//
//        return 4
//    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return dicELearnList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ElearningTableViewCell") as! ElearningTableViewCell
        if self.valueMode == "dark" {
            cell.contentView.backgroundColor = .black
            cell.viewcell.backgroundColor = UIColor(hex: "#232137")
            cell.labelHeadCell.textColor = .white
            cell.labelDescription.textColor = .white
        }
        else if self.valueMode == "light" {
            cell.contentView.backgroundColor = .white
            cell.viewcell.backgroundColor = .white
            cell.labelHeadCell.textColor = .black
            cell.labelDescription.textColor = .black
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

// MARK:: UITableViewDelegate
extension ResourceViewController: UITableViewDelegate {
//    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
//            return 250
//    }
//
//    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
//        let storyboardResource = UIStoryboard(name: "Main", bundle: nil)
//        let resourceDetailsVC = storyboardResource.instantiateViewController(withIdentifier: "ResourceDetailsViewController") as! ResourceDetailsViewController
//        if arrResource.count > 0 {
//            resourceDetailsVC.idDetails = arrResource[indexPath.row]["id"] as! Int
//        }
//        self.navigationController?.pushViewController(resourceDetailsVC, animated: true)
//
//    }
}


