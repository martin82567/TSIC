//
//  SessionCancelViewController.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/29/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
protocol Aftercencelmeeting {
    func cancelMettingPOP(name: String)
}
class SessionCancelViewController: BaseViewController {

    @IBOutlet weak var cancelbutton: UIButton!
    @IBOutlet weak var cancelNote: TextViewPadding!
    var idUseForMeetingReschedule: String?
    var delgatecancelNote: Aftercencelmeeting?
    override func viewDidLoad() {
        super.viewDidLoad()
        cancelbutton.layer.cornerRadius = 10.0
        cancelbutton.clipsToBounds = true
    }
    
    @IBAction func backAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func cancelAction(_ sender: Any) {
        
        
        if cancelNote.text == "" {
            
            showAlert(_sourceController: self, _msg: "Please Add Note")
        }
        else {
            cancelMeeting()
        }
        
    }
    
    func cancelMeeting() {
        self.startActivityIndicator()
        print("service call yes or no")
        var parameter = [String:String]()
        parameter["meeting_id"] = idUseForMeetingReschedule
        parameter["note"] = self.cancelNote.text
        ApiManager().serviceCallTOcencelMeetingMentee(parameter: parameter, completion: { (response) in
            self.stopActivityIndicator()
            let status = response["status"] as! Bool
            self.UI {
                self.delgatecancelNote?.cancelMettingPOP(name: "Helo")
                self.navigationController?.popViewController(animated: true)
            }
            
            if status == true {
            } else {
                self.UI{
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                }
            }
        })
        
        
    }

}
