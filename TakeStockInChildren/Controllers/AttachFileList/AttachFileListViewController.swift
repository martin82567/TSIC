//
//  AttachFileListViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 25/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
class AttachFileListViewController: BaseViewController {
    
    var arrAttachFiles: [UIImage] = []
    var typeFiles: String = ""
    var isType: Int?
    var strAssignIdCompleted:String = ""
    
    var arrValues = [[String: String]]()
    var arrNotes = [NSDictionary]()
    var dictItem = NSDictionary()
    var typeId = Int()
    var arrImgFiles = [[String: Any]]()
   // var arrImgFiles: NSArray!
    var arrOfImages: [UIImage] = []
    var strAssignId : String = ""
    var strGoalName : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    
    
    @IBOutlet weak var collectionViewFiles: UICollectionView!
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        setUpPage()
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
        //self.dismiss(animated: true, completion:nil)
    }
    //MARK:-SetUpPage
    func setUpPage() {
        switch isType {
        case 1:
            typeFiles = "goal"
            break
            
        case 2:
            typeFiles = "task"
            
            break
            
        case 3:
            
            typeFiles = "challenge"
            break
        default:
            
            break
        }
        getAPIvalues()
        
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
    //MARK:- Api call
    func getAPIvalues() {
        let userDetails:NSMutableDictionary = [
            "type" : typeFiles,
            "assign_id" : strAssignIdCompleted,
        ]
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            ApiManager.sharedInstance.getgGoalDetails(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    self.stopActivityIndicator()
                 //   DispatchQueue.main.async(execute: {() -> Void in
                        
                        print("showFilesjson\(json)")
                        let dataTemp = json["data"]! as! NSDictionary
                        let dataDict = dataTemp["datadetails"] as! NSDictionary
                        self.typeId = dataDict["id"] as! Int
                        self.dictItem = dataDict
                        self.arrValues.removeAll()
                        self.arrOfImages.removeAll()
                    self.arrImgFiles = dataDict["useruploadedfile"] as? [[String: Any]] ?? []
                        
                        if self.arrImgFiles.count > 0 {
                            
                            self.collectionViewFiles.reloadData()
                            
                            /*
                            for i in 0..<self.arrImgFiles.count {
                                let dictImgTemp : NSDictionary = self.arrImgFiles[i] as! NSDictionary
                                let strImgURL = dictImgTemp["file_name"] as? String ?? ""
                                let imageUrlString =  TakeStockInChildrenConstant.downloadFileURL.appending(strImgURL)
                                let imageUrl = URL(string: imageUrlString)
                                
                                if let custUrl = imageUrl {
                                    let image = try? UIImage(withContentsOfUrl: custUrl)
                                    if let imagecus = image {
                                        self.arrOfImages.append(imagecus)
                                    }
                                    
                                }
                                

                            // it's convert to UIImage
                                
                                
                            }
                            
                            self.collectionViewFiles.reloadData()
                            self.stopActivityIndicator()
                            
                            */
                        }
                   // })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    self.logOutMentee()
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
}

extension AttachFileListViewController: UICollectionViewDataSource, UICollectionViewDelegate, UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return arrImgFiles.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        let dict = self.arrImgFiles[indexPath.row]
        let addImgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "AttachFilesCollectionViewCell", for: indexPath) as! AttachFilesCollectionViewCell
        let strImgURL = dict["file_name"] as? String ?? ""
        addImgCell.imageViewFiles.sd_setImage(with: URL(string: TakeStockInChildrenConstant.downloadFileURL + strImgURL), placeholderImage: UIImage(named: "defaultProfileImage"))
       // imageViewProfile.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: "defaultProfileImage"))
        /*
        if arrOfImages.count > 0 {
            addImgCell.imageViewFiles.image = arrOfImages[indexPath.row]
        }
        */
        return addImgCell
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((self.collectionViewFiles.frame.size.width - 50) / 4)
        return CGSize(width: width, height: 65)
    }
}

