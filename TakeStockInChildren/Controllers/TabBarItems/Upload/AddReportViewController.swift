//
//  AddReportViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 02/09/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire

class AddReportViewController: BaseViewController, UITextFieldDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate {

    @IBOutlet weak var imageViewAddReport: UIImageView!
    @IBOutlet weak var textFieldUploadTitle: UITextField!
    
    @IBOutlet weak var headerview: UIView!
    @IBOutlet weak var backgroundimage: UIImageView!
    var profileImage : UIImage!
    var isImageSelected : Bool = false
    var valuemode : String?
    override func viewDidLoad() {
        super.viewDidLoad()
       // self.valuemode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodechnaged()
        
        let tapGestureRecognizer = UITapGestureRecognizer(target: self, action: #selector(ProfileViewController.cellTappedMethod(_:)))
        imageViewAddReport.isUserInteractionEnabled = true
        imageViewAddReport.addGestureRecognizer(tapGestureRecognizer)
        // Do any additional setup after loading the view.
    }
    
    
    
    func darkmodechnaged() {
        if self.valuemode == "dark" {
            headerview.backgroundColor = UIColor(hexString: "#0E0F27")
            backgroundimage.image = UIImage(named: "BG4")
            textFieldUploadTitle.attributedPlaceholder = NSAttributedString(string: "title",
            attributes: [NSAttributedString.Key.foregroundColor: UIColor.white])
            textFieldUploadTitle.textColor = .white
        }
        else if self.valuemode == "light" {
            backgroundimage.image = UIImage(named: "BackgroundImage")
            headerview.backgroundColor = UIColor(hexString: "#A7AE3B")
        }
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }

    //MARK:- Set Profile Image from PhotoLibrary or Camera
    @objc func cellTappedMethod(_ sender:AnyObject){
        actionSheetPopUpForSettingProfileImage()
    }
    
    func actionSheetPopUpForSettingProfileImage() {
        
        let alert:UIAlertController=UIAlertController(title: "Choose Image", message: nil, preferredStyle: UIAlertController.Style.actionSheet)
        
        let cameraAction = UIAlertAction(title: "Camera", style: UIAlertAction.Style.default)
        {
            UIAlertAction in
            self.selectImageFromCamera()
        }
        let gallaryAction = UIAlertAction(title: "Library", style: UIAlertAction.Style.default)
        {
            UIAlertAction in
            self.selectImageFromPhotoLibrary()
            
        }
        let cancelAction = UIAlertAction(title: "Cancel", style: UIAlertAction.Style.cancel)
        {
            UIAlertAction in
        }
        alert.addAction(cameraAction)
        alert.addAction(gallaryAction)
        alert.addAction(cancelAction)
        
        //------------------------
        // Present the controller
        //-------------------------
        if UIDevice.current.userInterfaceIdiom == .phone
        {
            self.present(alert, animated: true, completion: nil)
        }
        else
        {
            // popover=UIPopoverController(contentViewController: alert)
        }
    }
    
    func selectImageFromPhotoLibrary() {
        // UIImagePickerController is a view controller that lets a user pick media from their photo library.
        let imagePickerController = UIImagePickerController()
        
        // Only allow photos to be picked, not taken.
        imagePickerController.sourceType = .photoLibrary
        
        // Make sure ViewController is notified when the user picks an image.
        imagePickerController.delegate = self
        present(imagePickerController, animated: true, completion: nil)
    }
    
    func selectImageFromCamera() {
        // UIImagePickerController is a view controller that lets a user pick media from their photo library.
        let imagePickerController = UIImagePickerController()
        
        // Only allow photos to be taken.
        if (UIImagePickerController .isSourceTypeAvailable(UIImagePickerController.SourceType.camera)) {
            imagePickerController.delegate = self
            imagePickerController.allowsEditing = true
            imagePickerController.sourceType = UIImagePickerController.SourceType.camera
            imagePickerController.cameraCaptureMode = .photo
            present(imagePickerController, animated: true, completion: nil)
        } else {
            let alert = UIAlertController(title: "Camera Not Found", message: "This device has no Camera", preferredStyle: .alert)
            let ok = UIAlertAction(title: "OK", style:.default, handler: nil)
            alert.addAction(ok)
            present(alert, animated: true, completion: nil)
        }
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [UIImagePickerController.InfoKey : Any]) {
        // The info dictionary may contain multiple representations of the image. You want to use the original.
        guard let possibleImage = info[.originalImage] as? UIImage
            else {
                fatalError("Expected a dictionary containing an image, but was provided the following: \(info)")
                //return
        }
        // Set photoImageView to display the selected image.
        imageViewAddReport.image = possibleImage
        
        self.profileImage = possibleImage
        print("self.profileImage\(self.profileImage)")
        imageViewAddReport.clipsToBounds = true
        
        isImageSelected = true
        // Dismiss the picker.
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        // Dismiss the picker if the user canceled.
        dismiss(animated: true, completion: nil)
    }
    
    //MARK:- Update User Details
    func uploadFile() {
        if self.connectedToNetwork() {
           // let token  = UserDefaults.standard.string(forKey: "token")!
            
            guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "Upload Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    
                })
            }
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.AddReport)
            var parameters = [String: String]()
            self.startActivityIndicator()
        
            parameters["name"] = textFieldUploadTitle.text
            print(parameters)
            
            Alamofire.upload(multipartFormData: { multipartFormData in
                if self.profileImage != nil {
                    let imageData = self.profileImage.jpegData(compressionQuality: 6.0)
                    print("ImageData\(imageData)")
                    multipartFormData.append(imageData!, withName: "image", fileName: "\(Date().timeIntervalSince1970).jpeg", mimeType: "image/jpeg")
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
                        print("GotIMAGENAMEUPDATE\(response)")
                        self.stopActivityIndicator()

                        let alert = UIAlertController(title: "Success", message: "Uploaded succesfully" as? String, preferredStyle: UIAlertController.Style.alert)
                        let acceptAction = UIAlertAction(title: "Ok", style: .default) { (_) -> Void in
                            self.navigationController?.popViewController(animated: true)
                        }
                        alert.addAction(acceptAction)
                        self.present(alert, animated: true, completion: nil)
                    })
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                    self.showAlertAction(withTitle: "Alert", message: "Uploaded is not done")
                }
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func buttonUploadAction(_ sender: Any) {
        if (textFieldUploadTitle.text == "") {
            self.showAlertAction(withTitle: "Alert", message: "Add Title")
        } else if (isImageSelected == false) {
            self.showAlertAction(withTitle: "Alert", message: "Upload image")
        } else {
            uploadFile()
        }
    }
    
    //MARK:: UITextFieldDelegate
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange, replacementString string: String) -> Bool {
        let currentString: NSString = textField.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if newString.length > 25 {
            let alert = UIAlertController(title: "Alert", message: "Title must be under 25 characters", preferredStyle: .alert)
            alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
            self.present(alert, animated: true, completion: nil)
        }
        return newString.length <= 25
    }
}
