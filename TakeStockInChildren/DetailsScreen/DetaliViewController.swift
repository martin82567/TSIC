//
//  DetaliViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 17/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import ImagePicker
import Alamofire


class DetaliViewController: BaseViewController, UITextViewDelegate, ImagePickerDelegate, UINavigationControllerDelegate {//UIImagePickerControllerDelegate,
    
    
    @IBOutlet weak var imageViewIcon: UIImageView!
    @IBOutlet weak var viewBgLoading: UIView!
    @IBOutlet weak var collectionViewImageUpload: UICollectionView!
    @IBOutlet weak var labelAddFile: UILabel!
    @IBOutlet weak var buttonShowAttachedFile: UIButton!
    @IBOutlet weak var buttonAddNoteProperty: UIButton!
    @IBOutlet weak var buttonComplete: UIButton!
    @IBOutlet weak var textViewNote: UITextView!
    @IBOutlet weak var viewPopUp: UIView!
    @IBOutlet weak var tableViewNotes: UITableView!
    @IBOutlet weak var imageViewHeader: UIImageView!
    @IBOutlet weak var viewContainerHeightConstraint: NSLayoutConstraint!
    @IBOutlet weak var labelName: UILabel!
    @IBOutlet weak var labelHead: UILabel!
    @IBOutlet weak var labelStartDate: UILabel!
    @IBOutlet weak var labelEndDate: UILabel!
    @IBOutlet weak var labelDescription: UILabel!
    @IBOutlet weak var tableViewDescriptionDetails: UITableView!
    @IBOutlet weak var buttonLodeMore: UIButton!
    
    var strAssignId : String = ""
    var strGoalName : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    var isType: Int?
    var type: String = ""
    var completedTask: String = ""
    var arrValues = [[String: String]]()
    var arrNotes = [NSDictionary]()
    var arrUploadedImages = [NSDictionary]()
    var assignID = String()
    var lblHeader = UILabel()
    var dictItem = NSDictionary()
    var tapGesture = UITapGestureRecognizer()
    var imgIndex = Int()
    var typeId = Int()
    var uploadedImage : UIImage!
    var arrImageUpload = NSMutableArray()
    var arrImgFiles: NSArray!
    var imagePicker = UIImagePickerController()
    var arrOfImages: [UIImage] = []
    var imgCount : Int!
    var isCompletedAction: Bool = false
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
//        self.tableViewNotes.estimatedRowHeight = 68.0
//        self.tableViewNotes.rowHeight = UITableView.automaticDimension
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        viewBgLoading.isHidden = true
        viewPopUp.isHidden = true
        textViewNote.text = "You can add a small note.."
        textViewNote.textColor = UIColor.lightGray
        if(arrNotes.count > 0){
        buttonLodeMore.isHidden = false
        }else{
        buttonLodeMore.isHidden = true
        }
        setUpPage()
    }
    
    // MARK:: Function
    func openGallary() {
        let imagePickerController = ImagePickerController()
        imagePickerController.imageLimit = 5
        imagePickerController.delegate = self
        present(imagePickerController, animated: true, completion: nil)
    }
    
    //MARK:: ImagePickerDelegate
    func wrapperDidPress(_ imagePicker: ImagePickerController, images: [UIImage]) {
        
    }
    
    func doneButtonDidPress(_ imagePicker: ImagePickerController, images: [UIImage]) {
        self.arrOfImages.removeAll()
        for i in 0..<images.count {
            if self.arrOfImages.count < 6 {
                self.arrOfImages.append(images[i])
                imagePicker.dismiss(animated: true)
            } else {
                imagePicker.dismiss(animated: true)
                self.showAlert(_sourceController: self, _msg: "Your maximum limit of uploading images are over")
            }
        }
        //print(arrOfImages.count)
        self.uploadImages(arrImages: arrOfImages)
    }
    
    func cancelButtonDidPress(_ imagePicker: ImagePickerController) {
        imagePicker.dismiss(animated: true)
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    @objc func dismissKeyboard() {
        self.view.endEditing(true)
    }
    
    // MARK: - TextView Delegates
    func textViewDidBeginEditing(_ textView: UITextView) {
        if textViewNote.textColor == UIColor.lightGray {
            textViewNote.text = nil
            textViewNote.textColor = UIColor.black
        }
    }
    
    func textView(_ textView: UITextView, shouldChangeTextIn range: NSRange, replacementText text: String) -> Bool {
        let newText = (textView.text as NSString).replacingCharacters(in: range, with: text)
        let numberOfChars = newText.count
        return numberOfChars < 140    // 10 Limit Value
    }
    
    func textViewDidChange(_ textView: UITextView) {
        
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        if textViewNote.text.isEmpty {
            textViewNote.text = "You can add a small note.."
            textViewNote.textColor = UIColor.lightGray
        }
        self.dismissKeyboard()
    }
    
    func setUpPage() {
//        if(completedTask == "completed"){
//
//            switch isType {
//            case 1:
//                buttonComplete.isHidden = true
//                buttonAddNoteProperty.isHidden = true
//                buttonShowAttachedFile.isHidden = false
//                collectionViewImageUpload.isHidden = true
//                labelAddFile.isHidden = true
//                labelHead.text = "COMPLETED GOAL"
//                imageViewHeader.image = UIImage(named: "GoalDetails")
//                //labelDescription.text = strDescription
//                //print("label\(labelDescription.frame.height)")
//                //labelName.text = strGoalName
//                labelStartDate.text = strStartDate
//                labelEndDate.text = strEndDate
//                type = "goal"
//
//
//                break
//
//            case 2:
//                buttonComplete.isHidden = true
//                buttonAddNoteProperty.isHidden = true
//                buttonShowAttachedFile.isHidden = false
//                collectionViewImageUpload.isHidden = true
//                labelAddFile.isHidden = true
//                labelHead.text = "COMPLETED TASK"
//                imageViewHeader.image = UIImage(named: "TaskDetails")
//                //labelDescription.text = strDescription
//                //labelName.text = strGoalName
//                labelStartDate.text = strStartDate
//                labelEndDate.text = strEndDate
//                type = "task"
//                // buttonComplete.setTitle("Complete Task",for: .normal)
//
//                break
//
//            case 3:
//                buttonComplete.isHidden = true
//                buttonAddNoteProperty.isHidden = true
//                buttonShowAttachedFile.isHidden = false
//                collectionViewImageUpload.isHidden = true
//                labelAddFile.isHidden = true
//                labelHead.text = "COMPLETED CHALLENGE"
//                imageViewHeader.image = UIImage(named: "ChallengeDetails")
//                //labelDescription.text = strDescription
//                // labelName.text = strGoalName
//                labelStartDate.text = strStartDate
//                labelEndDate.text = strEndDate
//                type = "challenge"
//                //  buttonComplete.setTitle("Complete Challenge",for: .normal)
//
//                break
//            default:
//                labelHead.text = ""
//                break
//            }
//        }
//        else{
            switch isType {
            case 1:
                //labelHead.text = "COMPLETED GOAL"
                imageViewIcon.image = UIImage(named: "GoalIcon")
                //labelDescription.text = strDescription
                //labelName.text = strGoalName
                labelStartDate.text = strStartDate
                labelEndDate.text = strEndDate
                type = "goal"
                buttonComplete.setTitle("Complete Goal",for: .normal)
                buttonAddNoteProperty.isHidden = false
                buttonComplete.isHidden = false
                //buttonShowAttachedFile.isHidden = true
                //collectionViewImageUpload.isHidden = false
                labelAddFile.isHidden = false
                
                break
                
            case 2:
                //labelHead.text = "COMPLETED TASK"
                imageViewIcon.image = UIImage(named: "TaskIcon")
                //labelDescription.text = strDescription
                //labelName.text = strGoalName
                labelStartDate.text = strStartDate
                labelEndDate.text = strEndDate
                type = "task"
                buttonComplete.setTitle("Complete Task",for: .normal)
                buttonAddNoteProperty.isHidden = false
                buttonComplete.isHidden = false
                //buttonShowAttachedFile.isHidden = true
                //collectionViewImageUpload.isHidden = false
                labelAddFile.isHidden = false
                
                break
                
            case 3:
                //labelHead.text = "COMPLETED CHALLENGE"
                imageViewIcon.image = UIImage(named: "ChallengeIcon")
                //labelDescription.text = strDescription
                //labelName.text = strGoalName
                labelStartDate.text = strStartDate
                labelEndDate.text = strEndDate
                type = "challenge"
                buttonComplete.setTitle("Complete Challenge",for: .normal)
                buttonAddNoteProperty.isHidden = false
                buttonComplete.isHidden = false
                //buttonShowAttachedFile.isHidden = true
                //collectionViewImageUpload.isHidden = false
                labelAddFile.isHidden = false
                
                
                break
            default:
                labelHead.text = ""
                break
            }
        self.tableViewDescriptionDetails.reloadData()
        self.getAPIvalues()
    }
    
    
    //MARK:- Add Note
    @IBAction func buttonAddAction(_ sender: Any) {
        viewPopUp.isHidden = false
        self.tabBarController?.tabBar.isHidden = true
    }
    
    @IBAction func addNoteButtonActionInPopUpView(_ sender: Any) {
        viewPopUp.isHidden = true
        self.tabBarController?.tabBar.isHidden = false
        
        
        if textViewNote.text != "You can add a small note" && textViewNote.text != "" {
            let userDetails:NSMutableDictionary = [
                "type" : self.type,
                "id" : self.dictItem["id"]!,
                "note" : self.textViewNote.text!,
            ]
            if self.connectedToNetwork() {
                self.showActivityIndicatory(uiView: self.view)
                // self.viewBgLoading.isHidden = false
                ApiManager.sharedInstance.updateNote(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.actInd.stopAnimating()
                        //self.viewBgLoading.isHidden = true
                        // print("json\(json)")
                        //  self.dataView?.text = String(describing: json)
                        DispatchQueue.main.async(execute: {() -> Void in
                            let alert = UIAlertController(title: "", message: json["message"] as? String, preferredStyle: .alert)
                            alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                            self.present(alert, animated: true, completion: nil)
                            // self.window!.rootViewController = homeController
                            //23
                            self.getAPIvalues()
                        })
                    }
                }, onFailure: { error in
                    //  var alert = UIAlertController(title: "Error", message: "\(error)", preferredStyle: UIAlertControllerStyle.alert)
                    //  alert.show(self, sender: self)
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.actInd.stopAnimating()
                        //self.viewBgLoading.isHidden = true
                        /*
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                        */
                        self.logOutMentee()
                    })
                })
            } else {
                showAlert(_sourceController: self, _msg: "Unable to Connect")
            }
            
        }
    }
    
    //MARK:- Load More Notes
    @IBAction func buttonLoadMoreAction(_ sender: Any) {
        if(arrNotes.count != 0){
            let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
            let LoadeMoreNotesVC = storyboardELearning.instantiateViewController(withIdentifier: "NoteListViewController") as! NoteListViewController
            LoadeMoreNotesVC.arrNoteList = arrNotes
            self.navigationController?.pushViewController(LoadeMoreNotesVC, animated: true)
        }
        else{
            let alert = UIAlertController(title: "TakeStockInChildren", message: "No notes to show..", preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                print("Action")
            }))
            self.present(alert, animated: true, completion: nil)
            
        }
    }
    
//    //MARK:- Show AttachEd Files Action
//    @IBAction func buttonShowAttachedFiles(_ sender: Any) {
//        if(arrOfImages.count != 0){
//            let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
//            let LoadeAttachedFilesVC = storyboardELearning.instantiateViewController(withIdentifier: "AttachFileListViewController") as! AttachFileListViewController
//            LoadeAttachedFilesVC.arrAttachFiles = arrOfImages
//            self.navigationController?.pushViewController(LoadeAttachedFilesVC, animated: true)
//
//        }else{
//            let alert = UIAlertController(title: "TakeStockInChildren", message: "No attached file to show..", preferredStyle: UIAlertController.Style.alert)
//            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
//                print("Action")
//            }))
//            self.present(alert, animated: true, completion: nil)
//        }
//    }
    
    //MARK:- View Pop Up Close Action
    @IBAction func buttonCloseAction(_ sender: Any) {
        viewPopUp.isHidden = true
        self.tabBarController?.tabBar.isHidden = false
        self.view.endEditing(true)
    }
    
    //MARK:- Complete Action
    @IBAction func buttonCompleteAction(_ sender: Any) {
        print("tapcompletebutton\(isCompletedAction)")
        //if(isCompletedAction == false){
        var strStatus = 0
        if dictItem["datastatus"] as! Int == 1 {
            strStatus = 2
        } else if dictItem["datastatus"] as! Int == 0 {
            strStatus = 1
        } else if dictItem["datastatus"] as! Int == 2 {
            strStatus = 3
        }
        
        let userDetails:NSMutableDictionary = [
            "type" : type,
            "id" : dictItem["id"],
            "status" : strStatus,
        ]
        if self.connectedToNetwork() {
            if(isCompletedAction == false){
                self.showActivityIndicatory(uiView: self.view)
                // self.viewBgLoading.isHidden = false
                
                ApiManager.sharedInstance.goalTaskAction(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        self.actInd.stopAnimating()
                        //self.viewBgLoading.isHidden = true
                        
                        // print("JSON\(json)")
                        //  self.dataView?.text = String(describing: json)
                        DispatchQueue.main.async(execute: { () -> Void in
                            let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: .alert)
                            //alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                            let dismissAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                                //23
                                self.getAPIvalues()
                                
                            }
                            alert.addAction(dismissAction)
                            self.present(alert, animated: true, completion: nil)
                        })
                        self.buttonComplete.isUserInteractionEnabled = false
                        self.isCompletedAction = true
                        print("isCompletedAction\(self.isCompletedAction)")
                    }
                }, onFailure: { error in
                    //  var alert = UIAlertController(title: "Error", message: "\(error)", preferredStyle: UIAlertControllerStyle.alert)
                    //  alert.show(self, sender: self)
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.actInd.stopAnimating()
                        //self.viewBgLoading.isHidden = true
                        /*
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                        */
                        self.logOutMentee()
                    })
                })
            }
            else{
                showAlert(_sourceController: self, _msg: "Completed already..")
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to Connect")
        }
        
        //        }else{
        //           showAlert(_sourceController: self, _msg: "Completed already..")
        //        }
        
    }
    
    
    //MARK:- Api call
    func getAPIvalues() {
        let userDetails:NSMutableDictionary = [
            "type" : type,
            "assign_id" : strAssignId,
        ]
        if self.connectedToNetwork() {
            self.showActivityIndicatory(uiView: self.view)
            //viewBgLoading.isHidden = false
            ApiManager.sharedInstance.getgGoalDetails(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    self.actInd.stopAnimating()
                    //self.viewBgLoading.isHidden = true
                    
                    //print("jsongetgoaldetails\(json)")
                    //  self.dataView?.text = String(describing: json)
                    DispatchQueue.main.async(execute: {() -> Void in
                        
                        let dataTemp = json["data"]! as! NSDictionary
                        let dataDict = dataTemp["datadetails"] as! NSDictionary
                        let populatedDictText = ["Key1": dataDict["name"] as! String, "Key2": dataDict["description"] as! String, "Key3":"Text"]
                        let populatedDictDate = ["Key1": dataDict["start_date"] as! String, "Key2": dataDict["end_date"] as! String, "Key3":"Date"]
                        self.typeId = dataDict["id"] as! Int
                        self.dictItem = dataDict
                        //print("self.dictItem\(self.dictItem)")
                        self.arrValues.removeAll()
                        self.arrValues.append(populatedDictText)
                        self.arrValues.append(populatedDictDate)
                        var arrTemp = dataDict["notes"] as! NSArray
                        if arrTemp.count > 0 {
                            self.arrNotes = arrTemp as! [NSDictionary]
                            //print("arrNotes\(self.arrNotes)")
                            self.buttonLodeMore.isHidden = false
                            //self.tableViewNotes.reloadData()
                        }
                        if dataDict["datastatus"] as! Int != 2{
                            
                        }
                        
                        if let arrFiles = dataDict["adminuploadedfile"] {
                            let arrF = dataDict["adminuploadedfile"] as! NSArray
                            if arrF.count > 0 {
                                for i in 0..<arrF.count {
                                    let dictTemp1 = NSMutableDictionary(dictionary: arrF[i] as! NSDictionary)
                                    let dictTemp = ["Key1": dictTemp1["file_name"] as! String, "Key2":"", "Key3":"File"]
                                    self.arrValues.append(dictTemp)
                                }
                            }
                        }
                        if dataDict["datastatus"] as! Int == 1 {
                            let populatedDictButton = ["Key1": "", "Key2": "Upload", "Key3":"UpButton"]
                            let populatedDictButton2 = ["Key1": "", "Key2": "Add Note", "Key3":"Button"]
                            self.arrValues.append(populatedDictButton2)
                            self.arrValues.append(populatedDictButton)
                        } else if dataDict["datastatus"] as! Int == 0 {
                            let populatedDictButton = ["Key1": "", "Key2": "Upload", "Key3":"UpButton"]
                            let populatedDictButton2 = ["Key1": "", "Key2": "Add Note", "Key3":"Button"]
                            self.arrValues.append(populatedDictButton2)
                            self.arrValues.append(populatedDictButton)
                        } else if dataDict["datastatus"] as! Int == 2 {
                        }
                        
                        self.arrOfImages.removeAll()
                        self.arrImgFiles = dataDict["useruploadedfile"] as? NSArray
                        //print("imageArray\(self.arrImgFiles)")
                        
                        if self.arrImgFiles.count > 0 {
                            for i in 0..<self.arrImgFiles.count {
                                let dictImgTemp : NSDictionary = self.arrImgFiles[i] as! NSDictionary
                                let strImgURL = String(describing: dictImgTemp["file_name"]!)
                                let imageUrlString =  TakeStockInChildrenConstant.downloadFileURL.appending(strImgURL)
                                let imageUrl = URL(string: imageUrlString)!
                                
                                let image = try? UIImage(withContentsOfUrl: imageUrl)  // it's convert to UIImage
                                
                                self.arrOfImages.append(image!)
                            }
                        }
                        //                        self.actInd.stopAnimating()
                        //                        self.viewBgLoading.isHidden = true
                        DispatchQueue.main.asyncAfter(deadline: .now() + 0.1) {
                            self.collectionViewImageUpload.reloadData()
                            //self.showActivityIndicatory(uiView: self.view)
                            //self.tableViewNotes.reloadData()
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.actInd.stopAnimating()
                    self.logOutMentee()
                    //self.viewBgLoading.isHidden = true
                    /*
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
 */
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to Connect")
        }
        
    }
    
    func uploadImages(arrImages: [UIImage]) {
        if self.connectedToNetwork() {
            //print("uploadImagesCall")
            
            let userDetails = UserDefaults.standard.string(forKey: "token")! as? NSDictionary
            let token  = UserDefaults.standard.string(forKey: "token")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.uploadImage)
            
            var parameters = [String: String]()
            
            //parameters["type"] = lblHeader.text?.lowercased()
            parameters["type"] = type
            parameters["id"] = String(self.typeId)
            self.showActivityIndicatory(uiView: self.view)
            
            Alamofire.upload(multipartFormData: { multipartFormData in
                for image in arrImages {
                    let imageData = image.jpegData(compressionQuality: 0.6)
                    multipartFormData.append(imageData!, withName: "files[]", fileName: "\(Date().timeIntervalSince1970).jpeg", mimeType: "image/jpeg")
                }
                for (key, value) in parameters {
                    multipartFormData.append(value.data(using: String.Encoding.utf8)!, withName: key)
                }
            }, to: url,
               method:.post,
               headers:headers as! HTTPHeaders,
               encodingCompletion: { encodingResult in
                switch encodingResult {
                case .success(let upload, _, _):
                    
                    upload.responseString(completionHandler: { (response) in
                        //print("Upload Image Response\(response)")
                        
                        self.actInd.stopAnimating()
                        if let result = response.result.value {
                            var dictonary:NSDictionary?
                            if let data = result.data(using: String.Encoding.utf8) {
                                do {
                                    dictonary = try JSONSerialization.jsonObject(with: data, options: []) as? NSDictionary
                                    
                                    if let myDictionary = dictonary {
                                        let dictTemp = myDictionary["data"] as! NSDictionary
                                        //idImage = dictTemp["id"]
                                        //print("DICTTEMP\(String(describing: dictTemp["useruploadedfile"]))")
                                        
                                        self.arrOfImages.removeAll()
                                        
                                        self.arrImgFiles = dictTemp["useruploadedfile"] as? NSArray
                                        
                                        if self.arrImgFiles.count > 0 {
                                            for i in 0..<self.arrImgFiles.count {
                                                let dictImgTemp : NSDictionary = self.arrImgFiles[i] as! NSDictionary
                                                let strImgURL = String(describing: dictImgTemp["file_name"]!)
                                                let imageUrlString =  TakeStockInChildrenConstant.downloadFileURL.appending(strImgURL)
                                                let imageUrl = URL(string: imageUrlString)!
                                                
                                                let image = try? UIImage(withContentsOfUrl: imageUrl)  // it's convert to UIImage
                                                
                                                self.arrOfImages.append(image!)
                                            }
                                        }
                                        
                                        self.collectionViewImageUpload.reloadData()
                                        self.actInd.stopAnimating()
                                    }
                                } catch let error as NSError {
                                    print(error)
                                }
                            }
                        }
                        self.collectionViewImageUpload.reloadData()
                        self.showAlert(_sourceController: self, _msg: "Files uploaded succesfully")
                        
                    })
                    
                case .failure(let error):
                    print(error)
                    self.actInd.stopAnimating()
                }
            })
        } else {
            self.actInd.stopAnimating()
            showAlert(_sourceController: self, _msg: "Unable to Connect")
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
    
    
    
    //    @objc func deleteImage(_ sender: UIButton){
    //        print("Button pressed :+1: ")
    //        print(arrOfImages.count)
    //        let deleteIndexPath = arrOfImages[sender.tag]
    //        arrOfImages.remove(at: sender.tag)
    //        print(deleteIndexPath)
    //        print(self.arrOfImages.count)
    //        if arrOfImages.count == 0 {
    //            self.viewContainerForMoreUploadImage.isHidden = true
    //            self.btnOutletUploadMoreImage.isHidden = true
    //            self.btnOutletUploadImage.isEnabled = true
    //            self.btnOutletUploadImage.isHidden = false
    //            self.imgPostAds.alpha = 0
    //            self.collectMorePostImg.isHidden = true
    //            self.imgCamera.isHidden = false
    //        }
    //        self.collectMorePostImg.reloadData()
    //    }
    //}
    //MARK:- Delete Images
    @IBAction func BtnDeleteImageDidTap(_ sender: Any) {
        let getbtnDeleteIndex = sender as! UIButton
        let index = Int(getbtnDeleteIndex.tag)
        //print("Clicked Index : \(String(describing: index))")
        
        if (sender as AnyObject).tag == index {
            if self.connectedToNetwork() {
                self.showActivityIndicatory(uiView: self.view)
                
                let dictTemp = self.arrImgFiles[index] as! NSDictionary
                
                ApiManager.sharedInstance.deleteImageForGoal(ImageID: String(dictTemp["id"] as! Int), completion: { (json) in
                    //print(json)
                    DispatchQueue.main.async {
                        self.actInd.stopAnimating()
                        let status = json["status"] as! Bool
                        if status == true {
                            self.arrOfImages.remove(at: index)
                            self.collectionViewImageUpload.reloadData()
                            self.showAlert(_sourceController: self, _msg: "File deleted succesfully")
                        } else {
                            self.showAlert(_sourceController: self, _msg: "Unable to Delete")
                            //print("nothing to display")
                        }
                    }
                    guard let dataFromJson = json["data"] as? NSDictionary else{ return }
                    //print(dataFromJson)
                })
            } else {
                self.showAlert(_sourceController: self, _msg: "Unable to Connect")
            }
        }
    }
    
}

// MARK:: UITableViewDatasource
extension DetaliViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
//        if(tableView == tableViewNotes){
//            return arrNotes.count
//        }else{
            return 1
        //}
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "DetailsTableViewCell") as! DetailsTableViewCell
        
        cell.labelHeadCell.text = strGoalName
        cell.labelDescriptionCell.text = strDescription
        return cell
    }
    
}

// MARK:: UITextFieldDelegate
extension DetaliViewController: UITableViewDelegate{
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
    }
    
}


extension DetaliViewController: UICollectionViewDataSource, UICollectionViewDelegate, UICollectionViewDelegateFlowLayout {
    // MARK:: UICollectionViewViewDatasource
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        if arrOfImages.count > 0 {
            imgCount = Int(arrOfImages.count) + 1
        } else {
            imgCount = 1
        }
        return imgCount
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        if Int(indexPath.row)+1 == imgCount {//Add new image
            let addImgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "DetailsFileCollectionViewCell", for: indexPath) as! DetailsFileCollectionViewCell
            
            //addImgCell.imageViewFileCell.image = UIImage(named: arrImageUpload[indexPath.row] as! String )!
            return addImgCell
        } else {//} if arrOfImages.count < imgCount {
            let imgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "AddFileCollectionViewCell", for: indexPath) as! AddFileCollectionViewCell
            
            if arrOfImages.count > 0 {
                let dictTemp = arrOfImages[indexPath.row]
                //print(dictTemp)
                imgCell.imageViewAddFile.image = dictTemp
            }
            imgCell.buttonDeleteFile.tag = indexPath.row
            imgCell.buttonDeleteFile.addTarget(self, action: #selector(BtnDeleteImageDidTap(_:)), for: .touchUpInside)
            // self.actInd.stopAnimating()
            return imgCell
        }
    }
    
    // MARK:: UICollectionViewViewDelegate
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        let index = indexPath.item
        if (imgCount < 6) {
            if index == (imgCount-1) {
                //print("Add img")
                self.openGallary()
            }
        }
        showAlert(_sourceController: self, _msg: "Your maximum limit of uploading images are over")
        
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((self.collectionViewImageUpload.frame.size.width - 50) / 3)
        return CGSize(width: width, height: 65)
    }
}

extension UIImage {
    convenience init?(withContentsOfUrl url: URL) throws {
        let imageData = try Data(contentsOf: url)
        
        self.init(data: imageData)
    }
    
}

