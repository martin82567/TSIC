//
//  MenteePastCollectionViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/6/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class MenteePastCollectionViewCell: UICollectionViewCell {
    
    @IBOutlet weak var videobtn: UIButton!
    //@IBOutlet weak var sevenview: UIView!
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
    
    @IBOutlet weak var lblScheduledByChecking: UILabel!
    @IBOutlet weak var lblMeetingTitle: UILabel!
    
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }
    
    
    
    
    func loadDataUpcomming(dicData : NSDictionary){

        lblScheduledByChecking.text = ""
        lblRequestedBy.backgroundColor = UIColor.white
        var totalDateTime = ""
        var assignedMenteeName = ""
        let description = dicData["description"] as? String
       // let is_request_sent = dicData["is_request_sent"] as? Bool
//        if let noteAlreadyWritten = is_request_sent {
//
//            if noteAlreadyWritten == true{
//                self.viewForAllButton.isHidden = true
//
//                btnRescheduleOulet.isHidden = true
//            }else{
//                self.viewForAllButton.isHidden = false
//
//                btnRescheduleOulet.isHidden = false
//            }
//
//        }
        
        if let desc = description {
            if desc == ""{
                self.iconDesciption.isHidden = true
                self.lblShowDescription.isHidden = true
            }else{
                self.iconDesciption.isHidden = false
                self.lblShowDescription.isHidden = false
                self.lblShowDescription.text = desc
            }
            
        }
        
        let eventDate = dicData["date"] as? String
        
        let time_from = dicData["time"] as? String
        
//        let dateFormatter2 = DateFormatter()
//        dateFormatter2.dateFormat = "HH:mm"
//
//        let dt = dateFormatter2.date(from: time_from!)
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        //let strDate = dateFormatter.string(from: dt!)
        totalDateTime += "\(eventDate!) \(time_from!) "
        lblShowdate.text = totalDateTime
        
        lblMeetingTitle?.text = String(describing: dicData["title"]!)
        
        let schoolname = dicData["school_name"] as? String ?? ""
        
        if schoolname == "" {
            lblShowLocation?.text = dicData["school_type"] as? String ?? ""//"Affiliate Office"
                   
        }
        else {
            lblShowLocation?.text = String(describing: dicData["school_name"]!)
        }
        
        lblSessionSpace?.text = String(describing: dicData["method_value"]!)
        lblRequestedBy?.text  = String(describing: dicData["creator_name"]!)

    }
    
    

}


 
