//
//  SessionMenteeCollectionViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/1/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import IBAnimatable
class SessionMenteeCollectionViewCell: UICollectionViewCell {

    
    @IBOutlet weak var videobtn: UIButton!
    //@IBOutlet weak var sevenview: UIView!
    @IBOutlet weak var fiveview: UIView!
    @IBOutlet weak var fourview: UIView!
    @IBOutlet weak var thirdview: UIView!
    @IBOutlet weak var secondvIew: UIView!
    @IBOutlet weak var firstview: UIView!
    @IBOutlet weak var mainView: UIViewX!
    @IBOutlet weak var iconDesciption: UIImageView!
    
    @IBOutlet weak var lblRequestedBy: UILabel!
    @IBOutlet weak var lblShowLocation: UILabel!
    @IBOutlet weak var lblSessionSpace: UILabel!
    @IBOutlet weak var lblShowdate: UILabel!
    @IBOutlet weak var lblShowDescription: UILabel!
    
    @IBOutlet weak var btnAcceptOulet: UIButton!
    @IBOutlet weak var btnRescheduleOulet: UIButton!
    
    @IBOutlet weak var viewForAllButton: UIView!
    @IBOutlet weak var lblScheduledByChecking: UILabel!
    @IBOutlet weak var lblMeetingTitle: UILabel!
    @IBOutlet weak var viewCanceled: UIViewX!
    var valueMode : String?
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }
    
    func loadDataRequested(dicData : NSDictionary){
        lblRequestedBy.backgroundColor = UIColor.white
        viewCanceled.isHidden = true
        lblScheduledByChecking.text = "Meeting requested by:"
        self.btnAcceptOulet.isHidden = false
        self.viewForAllButton.isHidden = false
       var totalDateTime = ""
       //var assignedMenteeName = ""
        
        print(dicData)
       let description = dicData["description"] as? String
       let is_request_sent = dicData["is_request_sent"] as? Bool
       let is_mentor_created = dicData["is_mentor_created"] as! Bool
        let web_status = dicData["web_status"] as! Int
        let created_from = dicData["created_from"] as? String

        if is_mentor_created == true {
            if let noteAlreadyWritten = is_request_sent {
                if noteAlreadyWritten == true {
                    btnRescheduleOulet.isHidden = true
                    btnAcceptOulet.isHidden = true
                    let status = dicData["status"] as! Int
                    
                    if status == 3 {
                        viewCanceled.isHidden = false
                        self.viewForAllButton.isHidden = true
                    } else if status == 1 {
                        self.viewForAllButton.isHidden = true
                    }
                } else {
                    btnRescheduleOulet.isHidden = false
                }
            }
        } else {
            if web_status == 1 {
                self.btnAcceptOulet.setTitle("Yes", for: .normal)
                self.btnRescheduleOulet.setTitle("No", for: .normal)
            } else {
                self.viewForAllButton.isHidden = true
            }
        }
        if (created_from == "affiliate_portal") {
            self.btnRescheduleOulet.isHidden = true
        } else {
            self.btnRescheduleOulet.isHidden = false
        }
        
        if let desc = description {
            if desc == "" {
                self.iconDesciption.isHidden = true
                self.lblShowDescription.isHidden = true
            } else {
                self.iconDesciption.isHidden = false
                self.lblShowDescription.isHidden = false
                self.lblShowDescription.text = desc
            }
        }

        let eventDate = dicData["date"] as? String

        let time_from = dicData["time"] as? String

        /*let dateFormatter2 = DateFormatter()
        dateFormatter2.dateFormat = "HH:mm:ss"

4        let dt = dateFormatter2.date(from: time_from!)

        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        let strDate = dateFormatter.string(from: dt!)*/
        
        totalDateTime += "\(eventDate!) \(time_from!) "
        lblShowdate.text = totalDateTime
        lblMeetingTitle?.text = String(describing: dicData["title"]!)
        let scNAME = dicData["school_name"] as? String ?? ""
        if scNAME != "" {
            lblShowLocation?.text = dicData["school_name"] as? String ?? ""
        }
        else {
            let scType = dicData["school_type"] as? String ?? ""
            lblShowLocation?.text = scType
        }
        
        
      //  lblShowLocation?.text = "fghfhfh"//dicData["school_name"] as? String ?? "Affiliate office"//String(describing: dicData["school_name"] ?? "Affiliate office")
        lblSessionSpace?.text = String(describing: dicData["method_value"]!)  //school_location
        lblRequestedBy?.text  = String(describing: dicData["creator_name"]!)
        
    }

}

