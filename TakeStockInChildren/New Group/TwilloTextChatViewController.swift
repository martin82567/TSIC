//
//  TwilloTextChatViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/7/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import IQKeyboardManagerSwift
class TwilloTextChatViewController: BaseViewController, QuickstartChatManagerDelegate,UITextFieldDelegate,UITextViewDelegate {
    @IBOutlet weak var videoBtn: UIButton!
    @IBOutlet var safearacolor: UIView!
    @IBOutlet weak var headerview: UIView!
    @IBOutlet weak var backgroundImage: UIImageView!
    @IBOutlet weak var tblChatMessage: UITableView!
    @IBOutlet weak var scrollView1: UIScrollView!
    @IBOutlet weak var vwChatTextContainer: UIView!
    @IBOutlet weak var constraintVwChatHeight: NSLayoutConstraint!
    @IBOutlet weak var constraintViewChatBottom: NSLayoutConstraint!
    
    @IBOutlet weak var btnChat: UIButton!
    @IBOutlet weak var txtViewChat: UITextView!
    
    
    @IBOutlet weak var imgViewProfile: UIImageView!
    @IBOutlet weak var lblUserName: UILabel!
    var maxHeightChat = 140
    var minHeightChat = 60

    var OtheruserId: String?
    var OtheruserName: String?
    var OtherchatCode: String?
    
    var firstname: String?
    var middleName: String?
    var lastName: String?
    var Id: String?
    var csid: String?
    var chatManager = QuickstartChatManager()
    var type: String?
    
    var checkStringDiiferentUser: String?
    
    var concate: String?
    
    var fromtag: String?
    
    var imageBaseUrl: String?
    var receiverTypeCheck: String?
    var receiverimageUrl: String?
    var strUrl: String?
    override func viewDidLoad() {
        super.viewDidLoad()

        print("OtherUserId",self.OtheruserId)
        chatManager.forChatType = fromtag ?? ""
        chatManager.forApiChatCode = OtherchatCode ?? ""
        if csid == "" {
            chatManager.uniqueChannelName = OtherchatCode ?? ""
            print("nul csid")
        }
        else if OtherchatCode == "" {
            chatManager.uniqueChannelName = csid ?? ""
            print("chat code null")
        }
        else if csid != "" && OtherchatCode != "" {
            chatManager.uniqueChannelName = csid ?? ""
            print("not null  sid and chatcode")
        }
        else {
            print("objcode and  sid")
            chatManager.uniqueChannelName = csid ?? ""
        }
        
        print("channelname",chatManager.uniqueChannelName)
        chatManager.delegate = self
        
        
        self.lblUserName.text = self.OtheruserName
        
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillShowNotification, object: nil)
        
        NotificationCenter.default.removeObserver(self, name: UIResponder.keyboardWillHideNotification, object: nil)
        
        URLSession.shared.invalidateAndCancel()
        IQKeyboardManager.shared.enable = true
        
        
        
        self.txtViewChat.text = "Enter your message"
        
        retrivetoken()
        
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("login mode",loginMode)
        var sendertype: String = ""
        if loginMode == "Mentor"{
            videoBtn.isHidden = false
        }
        else if loginMode == "Mentee" {
            videoBtn.isHidden = true
        }
        else {
            videoBtn.isHidden = true
        }

        let isFrom = UserDefaults.standard.object(forKey: "fromStaff")as? String
        
        if(isFrom == "IsStaff") {
            videoBtn.isHidden = true
            UserDefaults.standard.setValue("nil", forKey: "fromStaff")
        } else {
            videoBtn.isHidden = false

        }
        
    
    }
    
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        print("textfield did end editing")
        removeTextViewFromScreen()
    }
    
    // MARK: - TextView Delegates
    func textViewDidBeginEditing(_ textView: UITextView) {
        if textView.text == "Enter your message" {
            textView.text = ""
        }
    }
    
    func textViewDidChange(_ textView: UITextView) {
        var height = 0
        if textView.contentSize.height > textView.frame.size.height {
            height = Int(constraintVwChatHeight.constant + 20)
            
            constraintVwChatHeight.constant = CGFloat(height > maxHeightChat ? maxHeightChat : height)
            self.vwChatTextContainer.layoutIfNeeded()
           // self.tblChatMessage.scrollToBottom()
        } else if textView.contentSize.height < textView.frame.size.height - 20 {
            height = Int(constraintVwChatHeight.constant - 20)
            
            constraintVwChatHeight.constant = CGFloat(height < minHeightChat ? minHeightChat : height)
            self.vwChatTextContainer.layoutIfNeeded()
          //  self.tblChatMessage.scrollToBottom()
        } else {
            
        }
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        removeTextViewFromScreen()
       // self.tblChatMessage.scrollToBottom()
    }
   
    @IBAction func videoControllerPush(_ sender: Any) {
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        print("login mode",loginMode)
        var sendertype: String = ""
        if loginMode == "Mentor"{
            sendertype = "mentor"
           // videoBtn.isHidden = false
        }
        else if loginMode == "Mentee" {
            sendertype = "mentee"
           // videoBtn.isHidden = true
        }
        else {
           // videoBtn.isHidden = true
        }
        let sb: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
               let newViewController = sb.instantiateViewController(withIdentifier: "VideoViewController") as! VideoViewController
        newViewController.senderId = self.Id ?? ""
        newViewController.senderType = sendertype
        newViewController.receiverId = self.OtheruserId ?? ""
        newViewController.receiverType = self.receiverTypeCheck ?? ""
        newViewController.fromWhereTag = "viaChat"
        newViewController.receiverName = self.OtheruserName ?? ""
        newViewController.receiverImgUrl =  self.receiverimageUrl ?? ""
        newViewController.strurl = strUrl ?? ""
        
        self.navigationController?.pushViewController(newViewController, animated: true)
        
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    
    override func viewDidDisappear(_ animated: Bool) {
        super.viewDidDisappear(animated)
        chatManager.shutdown()
    }
    
    
    func retrivetoken() {
        self.startActivityIndicator()
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("Login Mode",loginMode)
        
        if loginMode == "Mentor" {
            self.type = "mentor"
        }
        else if loginMode == "Mentee" {
            self.type = "mentee"
        }
        
        var param = [String:String]()
        param["user_type"] = type
        param["user_id"] =  self.Id
   //     self.concate = "\(type ?? "")_\(self.Id ?? "")_\(self.firstname ?? "")"
   //     self.chatManager.myIdentity = self.concate ?? ""
        param["user_name"] = self.firstname
        print("Param",param)
        chatManager.login("hello", param: param,chatBy: self.type ?? "") { (success) in
                DispatchQueue.main.async {
                    if success {
                       print("Logged in =================")
                        
                        self.concate = UserCredential.shared.Identity//"\(self.type ?? "")_\(self.Id ?? "")_\(self.firstname ?? "")"
                        self.chatManager.myIdentity = self.concate ?? ""
                        
                        let profileImage = self.receiverimageUrl?.appending(self.strUrl ?? "")
                        self.imgViewProfile.sd_setImage(with: URL(string: profileImage ?? ""), placeholderImage: UIImage(named: "defaultProfileImage"))
                        
                        
                        DispatchQueue.main.asyncAfter(deadline: .now()+0.7) {
                            self.stopActivityIndicator()
                        }
                        
                    } else {
                        let msg = "Error Twiilo Chat"
                       self.displayErrorMessage(msg)
                    }
                }
            }
        
    }
    
    
    private func displayErrorMessage(_ errorMessage: String) {
        let alertController = UIAlertController(title: "Error",
                                                message: errorMessage,
                                                preferredStyle: .alert)
        let okAction = UIAlertAction(title: "OK", style: .default, handler: nil)
        alertController.addAction(okAction)
        present(alertController, animated: true, completion: nil)
    }
    
    
    func sendPushNotification(msg: String?) {
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        var type = ""
        var comesType = ""
        if loginMode == "Mentor" {
            type = "mentee"
            comesType = "mentor"
        }
        else if loginMode == "Mentee" {
            type = "mentor"
            comesType = "mentee"
        }
        var param = [String:String]()
        param["user_type"] = type
        param["user_id"] = self.OtheruserId
        param["message"] = msg ?? ""
        param["sender_name"] = self.OtheruserName
        param["comes_from"] = comesType
        print("Param for channel Update",param)
        MentorApiManager().sendPushTwillo(parameter: param) { (json) in
            print("Push Update--",json)
           
        }
    }
    
    func reloadMessages() {
        self.tblChatMessage.reloadData()
    }

    // Scroll to bottom of table view for messages
    func receivedNewMessage() {
        scrollTableViewToBottom()
    }
   
    func validate(textView: UITextView) -> Bool {
        guard let text = textView.text,
            !text.trimmingCharacters(in: CharacterSet.whitespacesAndNewlines).isEmpty else {

                return false
        }
        return true
    }

        
    func scrollTableViewToBottom() {
        if (self.tblChatMessage.contentSize.height > self.tblChatMessage.frame.size.height) {
            let offset = CGPoint(x: 0, y: self.tblChatMessage.contentSize.height - self.tblChatMessage.frame.size.height) as CGPoint
            self.tblChatMessage.setContentOffset(offset, animated: true)
        }
    }
    
    
    @objc func keyboardDidShow (notification:NSNotification) {
        if let keyboardFrame: NSValue = notification.userInfo?[UIResponder.keyboardFrameEndUserInfoKey] as? NSValue {
            let keyboardRectangle = keyboardFrame.cgRectValue
            let keyboardHeight = keyboardRectangle.height
            
            constraintViewChatBottom.constant = keyboardHeight
            //let tabBarHeight = self.tabBarController?.tabBar.frame.size.height
            //constraintViewChatBottom.constant = keyboardHeight - tabBarHeight!
            self.vwChatTextContainer.layoutIfNeeded()
           // self.tblChatMessage.scrollToBottom()
        }
    }
    
    
    @objc func keyboardWillBeHidden (notification:NSNotification) {
        constraintViewChatBottom.constant = 0;
    }
        
    func displayTextViewOnScreen() {
        txtViewChat.becomeFirstResponder()
    }
    
    
    func removeTextViewFromScreen() {
        if txtViewChat.text == "" {
            txtViewChat.text = "Enter your message"
        } else {
            txtViewChat.resignFirstResponder()
        }
        constraintVwChatHeight.constant = 60
        //[SLAnimUtil animEffect:SLA_EFFECT_POP_IN view:self.btnChatAreaDisplay time:0.5];
        let totalRows = 0 //[_session listAllMessages].count -1 as Int
        
        if (totalRows > 0) {
            self.tblChatMessage.setContentOffset(CGPoint(x: 0, y: CGFloat.greatestFiniteMagnitude), animated: true)
        }
    }
    
    
    @IBAction func btnSubmitChatAction(_ sender: Any) {
        print("btn submit")
        if txtViewChat.text == "Enter your message" {
            showAlert(_sourceController: self, _msg: "Can't send empty message")
        }
        else {
            if validate(textView: txtViewChat) {
                print("validate")
                let msg = txtViewChat.text!
                chatManager.sendMessage(txtViewChat.text!, completion: { (result, _) in
                    print("msg======",result.resultText,result.resultCode,self.chatManager.client?.isReachabilityEnabled())
                    self.sendPushNotification(msg: msg)
                    if result.isSuccessful() {
                        self.txtViewChat.text = ""
                        self.txtViewChat.resignFirstResponder()
                    } else {
                        self.displayErrorMessage("Unable to send message")
                    }
                })
                self.txtViewChat.text = ""
               DispatchQueue.main.asyncAfter(deadline: .now() + 0.2) {
                self.btnChat.isUserInteractionEnabled = true
               }
                
            }
            
        }
    }

}


// MARK: UITableViewDataSource Delegate
extension TwilloTextChatViewController: UITableViewDataSource,UITableViewDelegate {

    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }

    // Return number of rows in the table
    func tableView(_ tableView: UITableView,
                   numberOfRowsInSection section: Int) -> Int {
        return chatManager.dummymessage.count
    }

    // Create table view rows
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath)
        -> UITableViewCell {
        var cell = ChatTableViewCell()
        if chatManager.messages.count > 0 {
            let obj = chatManager.dummymessage[indexPath.row]
            print("msg=============",obj.author.lowercased(), self.concate?.lowercased())
            if obj.author.lowercased() == self.concate?.lowercased() {
                cell = tableView.dequeueReusableCell(withIdentifier: "User",
                                                             for: indexPath) as! ChatTableViewCell
               // let trimmedStr = (obj.body).trimmingCharacters(in: .whitespaces)
              //  print("trimmed string",trimmedStr)
                cell.lblUser.text = obj.body
                cell.lblUser.numberOfLines = 0
                
                _ = Date()
                let formatter = DateFormatter()
                formatter.dateFormat = "MM-dd-yyyy"   //"MM-dd-yyyy"
                let result = formatter.date(from: obj.dateCreated ?? "")
                
                cell.lblTime.text = result?.dateString()
                
                
                /*
                let date = obj.dateCreated ?? ""
                let dateFormatter = DateFormatter()
                dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
                dateFormatter.timeZone = TimeZone(abbreviation: "UTC")
                let outdate = dateFormatter.date(from: date) ?? Date()

                let format = DateFormatter()
                format.dateFormat = "dd-MM-yyyy"
                let stringdate = format.string(from: outdate)
                */
                
                
                let date = NSDate.dateWithISO8601String(dateString: obj.dateCreated)
                let timestamp = DateTodayFormatter().stringFromDate(date: date)
                
                cell.lblTime.text = timestamp
                
                if obj.isread {
                    print("msg seen")
                    cell.imgSeen.image = UIImage(named: "MessageSeen")
                }
                else {
                    print("msg unseen")
                    cell.imgSeen.image = UIImage(named: "MessageUnSeen")
                }
                
                
                
            }
            else {
                cell = (tableView.dequeueReusableCell(withIdentifier: "Other", for: indexPath) as! ChatTableViewCell)
           // let trimmedStr = (obj.body).trimmingCharacters(in: .whitespaces)
                cell.lblOther.text = obj.body
                cell.lblOther.numberOfLines = 0
             //   print("trimmed string",trimmedStr)
               
                
                /*
                let date = obj.dateCreated ?? ""
                let dateFormatter = DateFormatter()
                dateFormatter.dateFormat = "yyyy-MM-dd HH:mm:ss"
                dateFormatter.timeZone = TimeZone(abbreviation: "UTC")
                let outdate = dateFormatter.date(from: date) ?? Date()
                
                let format = DateFormatter()
                format.dateFormat = "dd-MM-yyyy"
                let stringdate = format.string(from: outdate)
                */
                
                let date = NSDate.dateWithISO8601String(dateString: obj.dateCreated)
                let timestamp = DateTodayFormatter().stringFromDate(date: date)
                
                cell.lblTime.text = timestamp
                
            }
        }
        
        
        
        cell.selectionStyle = .none
        return cell
    }
    
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        var height : CGFloat = 0.0
        
        if self.chatManager.dummymessage.count > 0 {
            let dictChatData = self.chatManager.dummymessage[indexPath.row]
            let lblText = UILabel()
            lblText.numberOfLines = 0
            lblText.lineBreakMode = .byWordWrapping//.byWordWrapping
            let trimmedStr = (dictChatData.body).trimmingCharacters(in: .whitespaces)//replacingOccurrences(of: "^\\s*+$", with: "", options: .regularExpression)// (dictChatData["message"] as? String)?.replacingOccurrences(of: "\\s+$", with: "", options: .regularExpression)
            lblText.text = trimmedStr
            
            let attributedText = NSAttributedString(string:trimmedStr, attributes:  [NSAttributedString.Key.font : lblText.font!])
            
            //print("Text messages :: \(String(describing: dictChatData["message"]))")
            let widthText = (self.view.frame.size.width-180)
            //print("widthText :: \(widthText)")
            
            let rect = attributedText.boundingRect(with: CGSize(width: widthText, height: CGFloat.greatestFiniteMagnitude), options: .usesLineFragmentOrigin, context: nil)
            print(rect.size.height)
            height = (rect.size.height + 40) > 65 ? (rect.size.height + 40) : 65
        }
        
        return ceil(height+20)
    
    }
    
    
    
}
