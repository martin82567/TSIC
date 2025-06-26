//
//  PendingAddFilesViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 01/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class PendingAddFilesViewController: BaseViewController,UICollectionViewDelegate,UICollectionViewDataSource {
    
    @IBOutlet weak var collectionViewShowPendingFiles: UICollectionView!
    var arrValues = [[String: String]]()
    var arrNotes = [NSDictionary]()
    var dictItem = NSDictionary()
    var typeId = Int()
    var arrImgFiles = [NSDictionary]()
    var arrOfImages: [UIImage] = []
    var strAssignIdPending : String = ""
    var strGoalName : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    var isType: Int?
    var typeFiles: String = ""
    var imgCount : Int!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.dismiss(animated: true, completion:nil)
        self.tabBarController?.tabBar.isHidden = false
        //self.arrOfImages.removeAll()
        setUpPage()
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
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
    
    //MARK:- Api call
    func getAPIvalues() {
        let userDetails:NSMutableDictionary = [
            "type" : typeFiles,
            "assign_id" : strAssignIdPending,
        ]
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            ApiManager.sharedInstance.getgGoalDetails(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    print("jsongetgoaldetailsViewfiles\(json)")
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        let dataTemp = json["data"]! as! NSDictionary
                        let dataDict = dataTemp["datadetails"] as! NSDictionary
                        self.typeId = dataDict["id"] as! Int
                        self.dictItem = dataDict
                        self.arrValues.removeAll()
                        self.arrOfImages.removeAll()
                        self.arrImgFiles = (dataDict["useruploadedfile"] as! [NSDictionary])
                        print("arrImgFiles\(self.arrImgFiles)")
                        if self.arrImgFiles.count > 0 {
                            for i in 0..<self.arrImgFiles.count {
                                let dictImgTemp : NSDictionary = self.arrImgFiles[i] as! NSDictionary
                                let strImgURL = String(describing: dictImgTemp["file_name"]!)
                                print("strImgURL\(strImgURL)")
                                let imageUrlString =  TakeStockInChildrenConstant.downloadFileURL.appending(strImgURL)
                                print("imageUrlString\(imageUrlString)")
                                let imageUrl = URL(string: imageUrlString)!
                                print("imageUrl\(imageUrl)")
                                let image = try? UIImage(withContentsOfUrl: imageUrl)  // it's convert to UIImage
                                
                                self.arrOfImages.append(image!)
                            }
                        }
                        
                        DispatchQueue.main.asyncAfter(deadline: .now() + 0.1) {
                            if(self.arrImgFiles.count > 0){
                                self.collectionViewShowPendingFiles.reloadData()
                                self.stopActivityIndicator()
                            }else{
                                self.collectionViewShowPendingFiles.reloadData()
                                self.stopActivityIndicator()
                                let alert = UIAlertController(title: "TakeStockInChildren", message: "No attached file to show..", preferredStyle: UIAlertController.Style.alert)
                                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                    print("Action")
                                }))
                                self.present(alert, animated: true, completion: nil)
                            }
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK:-DeleteImage
    @IBAction func BtnDeleteImageDidTap(_ sender: Any) {
        let getbtnDeleteIndex = sender as! UIButton
        let index = (Int(getbtnDeleteIndex.tag))
        print("Clicked Index : \(String(describing: index))")
        print("(sender as AnyObject).tag \((sender as AnyObject).tag)")
        
        if (sender as AnyObject).tag == index {
            if self.connectedToNetwork() {
                self.startActivityIndicator()
                let dictTemp = self.arrImgFiles[index] as! NSDictionary
                print ("dicttempid\(String(describing: dictTemp["id"]))")
                ApiManager.sharedInstance.deleteImageForGoal(ImageID: String(dictTemp["id"] as! Int), completion: { (json) in
                    DispatchQueue.main.async {
                        print("afterdeletedimagelist\(json)")
                        
                        let status = json["status"] as! Bool
                        if status == true {
                            print ("iddeleted\(String(describing: dictTemp["id"]))")
                            self.arrOfImages.remove(at: index)
                            self.getAPIvalues()
                            print("arrofimagescount\(self.arrOfImages.count)")
                            self.showAlert(_sourceController: self, _msg: "File is deleted succesfully")
                            self.stopActivityIndicator()
                        } else {
                            self.stopActivityIndicator()
                            print ("idproblemtodeleted\(String(describing: dictTemp["id"]))")
                            self.collectionViewShowPendingFiles.reloadData()
                            self.showAlert(_sourceController: self, _msg: "Unable to delete")
                        }
                    }
                    guard let dataFromJson = json["data"] as? NSDictionary else{ return }
                })
            } else {
                self.showAlert(_sourceController: self, _msg: "Unable to connect")
            }
        }
    }
    
    // MARK:: UICollectionViewViewDatasource
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        //print("arrOfImages.count\(arrOfImages.count)")
        //print("imagefiles\(arrOfImages)")
        return arrOfImages.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        let imgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "PendingShowAddedFilesCollectionViewCell", for: indexPath) as! PendingShowAddedFilesCollectionViewCell
        if arrOfImages.count > 0 {
            let dictTemp = arrOfImages[indexPath.row]
            print(dictTemp)
            imgCell.imageViewFilesCell.image = dictTemp
        } else {
            print("nothing to show..")
        }
        imgCell.deleteImageCell.tag = indexPath.row
        print("imgCell.deleteImageCell.tag\(imgCell.deleteImageCell.tag)")
        imgCell.deleteImageCell.addTarget(self, action: #selector(BtnDeleteImageDidTap(_:)), for: .touchUpInside)
        return imgCell
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((collectionViewShowPendingFiles.frame.size.width - 50) / 4)
        return CGSize(width: width, height: 65)
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
}


