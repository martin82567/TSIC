//
//  JournalsViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class JournalsViewController: BaseViewController,UITextViewDelegate,UITextFieldDelegate {
    
    @IBOutlet weak var textFieldTitle: UITextField!
    @IBOutlet weak var textViewAddJournals: UITextView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        textViewAddJournals.text = "Note"
        textViewAddJournals.textColor = UIColor.lightGray
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Add journal service call called 😊
    func addJournalDataToServer(){
        self.startActivityIndicator()
        
        let title = self.textFieldTitle.text
        let details = self.textViewAddJournals.text
        
        let resetDetails:NSMutableDictionary = [
            "title"    : title!,
            "description" : details!,
        ]
        
        ApiManager().addJournalServiceCall(userDetails: resetDetails, onSuccess: { (resDic) in
            
            self.UI {
                self.stopActivityIndicator()
                // self.callBackCosure!()
                self.navigationController?.popViewController(animated: true)
            }
        }) { (resDicError) in
            
        }
    }
    
    //MARK:-AddJournals
    @IBAction func buttonAddJornals(_ sender: Any) {
        // Checking Validation of the text fields
        if(textFieldTitle.text == ""){
            showAlert(_sourceController: self, _msg: "Please enter a title")
        }
        else if (textFieldTitle.text == "") {
            showAlert(_sourceController: self, _msg: "Please enter details")
        } else {
            self.addJournalDataToServer()
        }
    }
    
    //MARK:ButtonBackAction
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    // MARK: - TextView Delegates
    func textViewDidBeginEditing(_ textView: UITextView) {
        if textViewAddJournals.textColor == UIColor.lightGray {
            textViewAddJournals.text = nil
            textViewAddJournals.textColor = UIColor.black
        }
    }
    
    func textViewDidChange(_ textView: UITextView) {
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        if textViewAddJournals.text.isEmpty {
            textViewAddJournals.text = "Note"
            textViewAddJournals.textColor = UIColor.lightGray
        }
    }
    
    //MARK:-TextFieldDelegate
    func textField(_ textField: UITextField, shouldChangeCharactersIn range: NSRange,
                   replacementString string: String) -> Bool
    {
        let maxLength = 30
        let currentString: NSString = textFieldTitle.text! as NSString
        let newString: NSString = currentString.replacingCharacters(in: range, with: string) as NSString
        if(newString.length <= maxLength){
            return true
        }else{
            showAlert(_sourceController: self, _msg: "Title should not more than 30 characters")
            return false
        }
    }
    
}


