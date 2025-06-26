//
//  PendingDetailsViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 30/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import ImagePicker
import Alamofire

class PendingDetailsViewController: BaseViewController, ImagePickerDelegate, UINavigationControllerDelegate,UITextViewDelegate,UITextFieldDelegate {
    
    @IBOutlet weak var textFieldNoteTitle: TextFieldPadding!
    @IBOutlet weak var tableViewDetailsPending: UITableView!
    @IBOutlet weak var buttonComplete: UIButton!
    @IBOutlet weak var imageViewIcon: UIImageView!
    @IBOutlet weak var viewPopUp: UIView!
    @IBOutlet weak var textViewNote: UITextView!
    
    var strAssignId : String = ""
    var strGoalName : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    var isType: Int?
    var type: String = ""
    var completedTask: String = ""
    var presentedWorkStatusOnDetailsScreen : Int?
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
        self.buttonComplete.isHidden = false
        self.buttonComplete.isUserInteractionEnabled = true
        // Do any additional setup after loading the view.
        //print("presentedWorkStatusOnDetailsScreen\(presentedWorkStatusOnDetailsScreen)")
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewDetailsPending.setContentOffset(.zero, animated:true)
        viewPopUp.isHidden = true
        textViewNote.text = "You can add a small note.."
        textViewNote.textColor = UIColor.lightGray
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
            if(presentedWorkStatusOnDetailsScreen == 1){
                buttonComplete.setTitle("Complete The Goal",for: .normal)
            }else{
                buttonComplete.setTitle("Begin The Goal",for: .normal)
            }
            imageViewIcon.image = UIImage(named: "GoalIcon")
            type = "goal"
            break
        case 2:
            if(presentedWorkStatusOnDetailsScreen == 1){
                buttonComplete.setTitle("Complete The Task",for: .normal)
            }else{
                buttonComplete.setTitle("Begin The Task",for: .normal)
            }
            imageViewIcon.image = UIImage(named: "TaskIcon")
            type = "task"
            break
        case 3:
            if(presentedWorkStatusOnDetailsScreen == 1){
                buttonComplete.setTitle("Complete The Challenge",for: .normal)
            }else{
                buttonComplete.setTitle("Begin The Challenge",for: .normal)
            }
            imageViewIcon.image = UIImage(named: "ChallengeIcon")
            type = "challenge"
            break
        default:
            break
        }
        self.getAPIvalues()
    }
    
    //MARK:- GetApicall
    func getAPIvalues() {
        let userDetails:NSMutableDictionary = [
            "type" : type,
            "assign_id" : strAssignId,
        ]
        if self.connectedToNetwork() {
            
            self.startActivityIndicator()
            ApiManager.sharedInstance.getgGoalDetails(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    DispatchQueue.main.async(execute: {() -> Void in
                        print("json\(json)")
                        self.stopActivityIndicator()
                        guard let dataTemp = json["data"] as? NSDictionary else{
                            return
                        }
                        guard let dataDict = dataTemp["datadetails"] as? NSDictionary else{
                            return
                        }
                        let populatedDictText = ["Key1": dataDict["name"] as! String, "Key2": dataDict["description"] as! String, "Key3":"Text"]
                        let populatedDictDate = ["Key1": dataDict["start_date"] as! String, "Key2": dataDict["end_date"] as! String, "Key3":"Date"]
                        self.typeId = dataDict["id"] as! Int
                        print("self.typeId\(self.typeId)")
                        self.dictItem = dataDict
                        self.arrValues.removeAll()
                        self.arrValues.append(populatedDictText)
                        self.arrValues.append(populatedDictDate)
                        let arrTemp = dataDict["notes"] as! NSArray
                        if arrTemp.count > 0 {
                            self.arrNotes = arrTemp as! [NSDictionary]
                        }
                        if dataDict["datastatus"] as! Int != 2{
                            
                        }
                        
                        if let arrFiles = dataDict["adminuploadedfile"] {
                            let arrF = dataDict["adminuploadedfile"] as! NSArray
                            if arrF.count > 0 {
                                for i in 0..<arrF.count {
                                    let dictTemp1 = NSMutableDictionary(dictionary: arrF[i] as! NSDictionary)
                                    let dictTemp = ["Key1": dictTemp1["file_name"] as! String, "Key2":"", "Key3":"File"]
                                    self.arrValues.append(dictTemp )
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
                        print("arrOfImages\(self.arrOfImages)")
                        //self.arrOfImages.removeAll()
                        self.arrImgFiles = dataDict["useruploadedfile"] as? NSArray
                        print("imageArray\(self.arrImgFiles.count)")
                        
                        //                        if self.arrImgFiles.count > 0 {
                        //                            for i in 0..<self.arrImgFiles.count {
                        //                                let dictImgTemp : NSDictionary = self.arrImgFiles[i] as! NSDictionary
                        //                                let strImgURL = String(describing: dictImgTemp["file_name"]!)
                        //                                let imageUrlString =  TakeStockInChildrenConstant.downloadFileURL.appending(strImgURL)
                        //                                let imageUrl = URL(string: imageUrlString)!
                        //
                        //                                let image = try? UIImage(withContentsOfUrl: imageUrl)  // it's convert to UIImage
                        //
                        //                                self.arrOfImages.append(image!)
                        //                            }
                        //                        }
                        //                        self.actInd.stopAnimating()
                        //                        self.viewBgLoading.isHidden = true
                        DispatchQueue.main.asyncAfter(deadline: .now() + 0.1) {
                            self.tableViewDetailsPending.reloadData()
                            self.tableViewDetailsPending.setContentOffset(.zero, animated:true)
                            //self.stopActivityIndicator()
                        }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let alertController = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    let actionOk = UIAlertAction(title: "Ok", style: .cancel) { (action) in
                        
                    }
                    alertController.addAction(actionOk)
                    self.present(alertController, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK:- Add Note
    @IBAction func buttonAddAction(_ sender: Any) {
        viewPopUp.isHidden = false
    }
    
    @IBAction func addNoteButtonActionInPopUpView(_ sender: Any) {
        self.tabBarController?.tabBar.isHidden = false
        
        print("textFieldNoteTitle.text\(String(describing: textFieldNoteTitle.text))")
        if textViewNote.text != "You can add a small note" && textViewNote.text != "" && textFieldNoteTitle.text != "" {
            print("id\(self.dictItem["id"])")
            let userDetails:NSMutableDictionary = [
                "type" : self.type,
                "id" : self.dictItem["id"]!,
                "note" : self.textViewNote.text!,
                "title" : self.textFieldNoteTitle.text!
            ]
            print("Dict\(userDetails)")
            if self.connectedToNetwork() {
                self.startActivityIndicator()
                ApiManager.sharedInstance.updateNote(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        print("json\(json)")
                        //  self.dataView?.text = String(describing: json)
                        DispatchQueue.main.async(execute: {() -> Void in
                            let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                self.textViewNote.text = "You can add a small note.."
                                self.textViewNote.textColor = UIColor.lightGray
                                self.viewPopUp.isHidden = true
                                self.dismissKeyboard()
                                self.getAPIvalues()
                            }))
                            
                            self.stopActivityIndicator()
                            self.present(alert, animated: true, completion: nil)
                            
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
    }
    
    //MARK:- ViewPopUpNotesCloseAction
    @IBAction func buttonCloseAction(_ sender: Any) {
        viewPopUp.isHidden = true
        self.tabBarController?.tabBar.isHidden = false
        dismissKeyboard()
        textViewNote.text = "You can add a small note.."
        textViewNote.textColor = UIColor.lightGray
    }
    
    //MARK:-DismissKeyboard
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
        let newText = (textViewNote.text as NSString).replacingCharacters(in: range, with: text)
        let numberOfChars = newText.count
        if numberOfChars <= 140
        {
            return true
        }
        else
        {
            showAlert(_sourceController: self, _msg: "You input maximum number of characters")
            return false
        }
    }
    
    func textViewDidChange(_ textView: UITextView) {
        
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        if textViewNote.text.isEmpty {
            textViewNote.text = "You can add a small note.."
            textViewNote.textColor = UIColor.lightGray
        }
    }
    
    //MARK:-ViewNotes
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
    
    //MARK:-AddFiles
    @IBAction func buttonAddFilesAction(_ sender: Any) {
        print("Addfilescount\(arrImgFiles.count)")
        if(arrImgFiles.count < 5){
            openGallary()
        }else{
            self.stopActivityIndicator()
            showAlert(_sourceController: self, _msg: "Your maximum limit of uploading images are over..You can show your attached files from View files")
        }
    }
    
    // MARK:: OpenGallery
    func openGallary() {
        let imagePickerController = ImagePickerController()
        imagePickerController.imageLimit = 1
        imagePickerController.delegate = self
        present(imagePickerController, animated: true, completion: nil)
    }
    
    //MARK:: ImagePickerDelegate
    func wrapperDidPress(_ imagePicker: ImagePickerController, images: [UIImage]) {
        
    }
    
    func doneButtonDidPress(_ imagePicker: ImagePickerController, images: [UIImage]) {
        self.arrOfImages.removeAll()
        //        if(arrImgFiles.count < 6){
        print("donepressedimagescount\(images.count)")
        for i in 0..<images.count {
            //if self.arrOfImages.count < 6 {
            self.arrOfImages.append(images[i])
            imagePicker.dismiss(animated: true)
            self.uploadImages(arrImages: arrOfImages)
            // } else {
            //                imagePicker.dismiss(animated: true)
            //                self.showAlert(_sourceController: self, _msg: "Your maximum limit of uploading images are over")
            // }
        }
        // print("selectimagedone\(arrOfImages.count)")
    }
    
    //MARK:-UploadImages
    func uploadImages(arrImages: [UIImage]) {
        if self.connectedToNetwork() {
            print("type\(type)")
            let userDetails = UserDefaults.standard.string(forKey: "token")! as? NSDictionary
            let token  = UserDefaults.standard.string(forKey: "token")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.uploadImage)
            
            var parameters = [String: String]()
            parameters["type"] = type
            parameters["id"] = String(self.typeId)
            self.startActivityIndicator()
            
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
                        print("Upload Image Response\(response)")
                        
                        
                        if let result = response.result.value {
                            var dictonary:NSDictionary?
                            if let data = result.data(using: String.Encoding.utf8) {
                                do {
                                    dictonary = try JSONSerialization.jsonObject(with: data, options: []) as? NSDictionary
                                    
                                    if let myDictionary = dictonary {
                                        let dictTemp = myDictionary["data"] as! NSDictionary
                                        
                                        self.arrOfImages.removeAll()
                                        // self.arrImgFiles.rem
                                        self.arrImgFiles = dictTemp["useruploadedfile"] as? NSArray
                                        print("arrImgFiles.count\(self.arrImgFiles.count)")
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
                                        print("afteruploadImage\(self.arrOfImages.count)")
                                        self.tableViewDetailsPending.reloadData()
                                        self.stopActivityIndicator()
                                    }
                                } catch let error as NSError {
                                    print(error)
                                }
                            }
                        }
                        let alert = UIAlertController(title: "TakeStockInChildren", message: "Files uploaded succesfully..", preferredStyle: UIAlertController.Style.alert)
                        alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                            print("Action")
                            self.stopActivityIndicator()
                            //                            let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                            //                                                    let LoadeAttachedFilesVC = storyboardELearning.instantiateViewController(withIdentifier: "PendingAddFilesViewController") as! PendingAddFilesViewController
                            //                                                    LoadeAttachedFilesVC.typeFiles = self.type
                            //                                                    LoadeAttachedFilesVC.strAssignIdPending = self.strAssignId
                            //                                                    self.navigationController?.pushViewController(LoadeAttachedFilesVC, animated: true)
                        }))
                        self.present(alert, animated: true, completion: nil)
                    })
                    
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            })
        } else {
            self.actInd.stopAnimating()
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    func cancelButtonDidPress(_ imagePicker: ImagePickerController) {
        imagePicker.dismiss(animated: true)
    }
         //MARK:-ViewFiles
    @IBAction func buttonShowFiles(_ sender: Any) {
        // if(arrOfImages.count != 0){
        let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
        let LoadeAttachedFilesVC = storyboardELearning.instantiateViewController(withIdentifier: "PendingAddFilesViewController") as! PendingAddFilesViewController
        LoadeAttachedFilesVC.typeFiles = type
        LoadeAttachedFilesVC.strAssignIdPending = strAssignId
        self.navigationController?.pushViewController(LoadeAttachedFilesVC, animated: true)
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
        //self.dismiss(animated: true, completion:nil)
    }
    
    //MARK:- Complete Action
    @IBAction func buttonCompleteAction(_ sender: Any) {
        print("tapcompletebutton\(isCompletedAction)")
        var strStatus = 0
        if dictItem["datastatus"] as! Int == 1 {
            strStatus = 2
        } else if dictItem["datastatus"] as! Int == 0 {
            strStatus = 1
        } else if dictItem["datastatus"] as! Int == 2 {
            strStatus = 3
        }
        print("dictItem[id]\(String(describing: dictItem["id"]))")
        print(strStatus)
        let userDetails:NSMutableDictionary = [
            "type" : type,
            "id" : dictItem["id"]!,
            "status" : strStatus,
        ]
        if self.connectedToNetwork() {
            if(isCompletedAction == false){
                self.startActivityIndicator()
                
                ApiManager.sharedInstance.goalTaskAction(userDetails: userDetails, onSuccess: { json in
                    DispatchQueue.main.async {
                        DispatchQueue.main.async(execute: { () -> Void in
                            
                            self.stopActivityIndicator()
                            let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: UIAlertController.Style.alert)
                            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                                print("CompleteAction")
                                
                                self.getAPIvalues()
                                
                                //                                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                                //                                let LoadeAttachedFilesVC = storyboardELearning.instantiateViewController(withIdentifier: "PendingAddFilesViewController") as! PendingAddFilesViewController
                                //                                LoadeAttachedFilesVC.typeFiles = self.type
                                //                                LoadeAttachedFilesVC.strAssignIdPending = self.strAssignId
                                //                                self.navigationController?.pushViewController(LoadeAttachedFilesVC, animated: true)
                            }))
                            self.present(alert, animated: true, completion: nil)
                            
                            //                            let alert = UIAlertController(title: "Success", message: json["message"] as? String, preferredStyle: .alert)
                            //                            let dismissAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            //                                self.getAPIvalues()
                            //                                self.actInd.stopAnimating()
                            //                                self.vwBgLoading.isHidden = true
                            //                            }
                            //                            alert.addAction(dismissAction)
                            //                            self.present(alert, animated: true, completion: nil)
                        })
                        self.buttonComplete.setTitle("Completed",for: .normal)
                        //self.buttonComplete.isHidden = true
                       // self.buttonComplete.isUserInteractionEnabled = false
                        //self.isCompletedAction = true
                       // print("isCompletedAction\(self.isCompletedAction)")
                    }
                }, onFailure: { error in
                    DispatchQueue.main.sync(execute: {() -> Void in
                        self.stopActivityIndicator()
                        let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                        alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                        self.present(alert, animated: true, completion: nil)
                    })
                })
            }
            else{
                showAlert(_sourceController: self, _msg: "Completed already..")
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    
    
    //    // MARK:: UICollectionViewViewDatasource
    //    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
    //        print(arrOfImages.count)
    //                if arrOfImages.count > 0 {
    //                    imgCount = Int(arrOfImages.count) + 1
    //                } else {
    //                    imgCount = 1
    //                }
    //        return imgCount
    //    }
    //
    //    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
    //                if Int(indexPath.row)+1 == imgCount {//Add new image
    //                    let addImgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "DetailsFileCollectionViewCell", for: indexPath) as! DetailsFileCollectionViewCell
    //
    //                    //addImgCell.imageViewFileCell.image = UIImage(named: arrImageUpload[indexPath.row] as! String )!
    //                    return addImgCell
    //                } else {//} if arrOfImages.count < imgCount {
    //                    let imgCell = collectionView.dequeueReusableCell(withReuseIdentifier: "AddFileCollectionViewCell", for: indexPath) as! AddFileCollectionViewCell
    //
    //                    if arrOfImages.count > 0 {
    //                        let dictTemp = arrOfImages[indexPath.row]
    //                        print(dictTemp)
    //                        imgCell.imageViewAddFile.image = dictTemp
    //                    }
    //                    imgCell.buttonDeleteFile.tag = indexPath.row
    //                    imgCell.buttonDeleteFile.addTarget(self, action: #selector(BtnDeleteImageDidTap(_:)), for: .touchUpInside)
    //
    //                    // self.actInd.stopAnimating()
    //                    return imgCell
    //                }
    //    }
    //
    //    // MARK:: UICollectionViewViewDelegate
    //    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
    //        let index = indexPath.item
    //        if(imgCount < 6) {
    //            if index == (imgCount-1) {
    //                //print("Add img")
    //                self.openGallary()
    //            }
    //        }
    //        showAlert(_sourceController: self, _msg: "Your maximum limit of uploading images are over")
    //
    //    }
    
    //    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
    //       // let width = ((collectionViewAttachFile.frame.size.width - 50) / 3)
    //
    //        return CGSize(width: 50, height: 65)
    //    }
    
    //else{
    //            let alert = UIAlertController(title: "TakeStockInChildren", message: "No attached file to show..", preferredStyle: UIAlertController.Style.alert)
    //            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
    //                print("Action")
    //            }))
    //            self.present(alert, animated: true, completion: nil)
    //        }
    // }
}

// MARK:: UITableViewDatasource
extension PendingDetailsViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if (indexPath.row == 0) {
            //MARK:-DescriptionCell
            let cell = tableView.dequeueReusableCell(withIdentifier: "DescriptionPendingTableViewCell") as! DescriptionPendingTableViewCell
            cell.labelTitleCell.text = strGoalName
            cell.labelDescriptionCell.text = strDescription
            return cell
        } else if (indexPath.row == 1) {
            //MARK:-DateCell
            let cell = tableView.dequeueReusableCell(withIdentifier: "DateTableViewCell") as! DateTableViewCell
            cell.labelStartDateCell.text = strStartDate
            cell.labelEndDateCell.text = strEndDate
            return cell
        } else if (indexPath.row == 2) {
            //MARK:-NotesCell
            let cell = tableView.dequeueReusableCell(withIdentifier: "AddNotesTableViewCell") as! AddNotesTableViewCell
            cell.buttonAddNote.tag = indexPath.row
            cell.buttonAddNote.addTarget(self, action: #selector(buttonAddAction(_:)), for: .touchUpInside)
            cell.buttonShowNote.tag = indexPath.row
            cell.buttonShowNote.addTarget(self, action: #selector(buttonLoadMoreAction(_:)), for: .touchUpInside)
            return cell
        } else {
            //MARK:-AttachFileCell
            let cell = tableView.dequeueReusableCell(withIdentifier: "AttachedFileTableViewCell") as! AttachedFileTableViewCell
            cell.buttonAddFiles.tag = indexPath.row
            cell.buttonAddFiles.addTarget(self, action: #selector(buttonAddFilesAction(_:)), for: .touchUpInside)
            cell.buttonViewFiles.tag = indexPath.row
            cell.buttonViewFiles.addTarget(self, action: #selector(buttonShowFiles(_:)), for: .touchUpInside)
            return cell
        }
    }
}

//MARK:-TableViewDelegate
extension PendingDetailsViewController: UITableViewDelegate {
    
    private func tableView(tableView: UITableView, heightForRowAtIndexPath indexPath: NSIndexPath) -> CGFloat {
        return UITableView.automaticDimension
    }
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 4
    }
}



