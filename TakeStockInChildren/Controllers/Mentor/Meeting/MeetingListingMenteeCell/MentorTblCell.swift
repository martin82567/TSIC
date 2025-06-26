//
//  MentorTblCell.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 8/30/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import IBAnimatable
class MentorTblCell: UITableViewCell {
    
    @IBOutlet weak var sixview: UIView!
    
    @IBOutlet weak var videobtn: UIButton!
    @IBOutlet weak var sevenview: UIView!
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
    @IBOutlet weak var viewRequestedNotes: UIView!
    @IBOutlet weak var lblRequestedNotes: AnimatableLabel!
    
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
        /*
        self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
        
        if self.valueMode == "dark" {
            mainView.backgroundColor = .gray//UIColor(hex: "#232137")
            lblRequestedBy.textColor = .white
            lblShowLocation.textColor = .white
            lblSessionSpace.textColor = .white
            lblShowDescription.textColor = .white
            lblRequestedNotes.textColor = .white
            lblScheduledByChecking.textColor = .white
            lblMeetingTitle.textColor = .white
            lblShowdate.textColor = .white
            
            firstview.backgroundColor = UIColor(hex: "#232137")
            secondvIew.backgroundColor = UIColor(hex: "#232137")
            thirdview.backgroundColor = UIColor(hex: "#232137")
            fourview.backgroundColor = UIColor(hex: "#232137")
            fiveview.backgroundColor = UIColor(hex: "#232137")
            sixview.backgroundColor = UIColor(hex: "#232137")
            sevenview.backgroundColor = UIColor(hex: "#232137")
        }
        else if self.valueMode == "light" {
            mainView.backgroundColor = .white
            lblRequestedBy.textColor = .black
            lblShowLocation.textColor = .black
            lblSessionSpace.textColor = .black
            lblShowDescription.textColor = .black
            lblRequestedNotes.textColor = .black
            lblScheduledByChecking.textColor = .black
            lblMeetingTitle.textColor = .black
            lblShowdate.textColor = .black
            
            firstview.backgroundColor = .white
            secondvIew.backgroundColor = .white
            thirdview.backgroundColor = .white
            fourview.backgroundColor = .white
            fiveview.backgroundColor = .white
            sixview.backgroundColor = .white
            sevenview.backgroundColor = .white
            
        }
        */
        
        viewCanceled.isHidden = true
        lblScheduledByChecking.text = "Meeting requested by:"
        self.viewRequestedNotes.isHidden = true
        self.btnAcceptOulet.isHidden = false
        self.viewForAllButton.isHidden = false
       var totalDateTime = ""
       //var assignedMenteeName = ""
        
        print(dicData)
       let description = dicData["description"] as? String
       let is_request_sent = dicData["is_request_sent"] as? Bool
       let is_mentor_created = dicData["is_mentor_created"] as! Bool
        let created_from = dicData["created_from"] as? String
        
        let web_status = dicData["web_status"] as! Int
        if (created_from == "affiliate_portal") {
            self.btnRescheduleOulet.isHidden = true
        } else {
            self.btnRescheduleOulet.isHidden = false
        }
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

        let dt = dateFormatter2.date(from: time_from!)

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
    
    
    func loadDataUpcomming(dicData : NSDictionary){
        
        self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
        
        if self.valueMode == "dark" {
            mainView.backgroundColor = .gray//UIColor(hex: "#232137")
            lblRequestedBy.textColor = .white
            lblShowLocation.textColor = .white
            lblSessionSpace.textColor = .white
            lblShowDescription.textColor = .white
            lblRequestedNotes.textColor = .white
            lblScheduledByChecking.textColor = .white
            lblMeetingTitle.textColor = .white
            
            
            firstview.backgroundColor = UIColor(hex: "#232137")
            secondvIew.backgroundColor = UIColor(hex: "#232137")
            thirdview.backgroundColor = UIColor(hex: "#232137")
            fourview.backgroundColor = UIColor(hex: "#232137")
            fiveview.backgroundColor = UIColor(hex: "#232137")
            sixview.backgroundColor = UIColor(hex: "#232137")
            sevenview.backgroundColor = UIColor(hex: "#232137")
        }
        else if self.valueMode == "light" {
            mainView.backgroundColor = .white
            lblRequestedBy.textColor = .black
            lblShowLocation.textColor = .black
            lblSessionSpace.textColor = .black
            lblShowDescription.textColor = .black
            lblRequestedNotes.textColor = .black
            lblScheduledByChecking.textColor = .black
            lblMeetingTitle.textColor = .black
            
            
            firstview.backgroundColor = .white
                       secondvIew.backgroundColor = .white
                       thirdview.backgroundColor = .white
                       fourview.backgroundColor = .white
                       fiveview.backgroundColor = .white
                       sixview.backgroundColor = .white
                       sevenview.backgroundColor = .white
        }
        
        
        viewCanceled.isHidden = true

        lblScheduledByChecking.text = ""
        self.viewRequestedNotes.isHidden = true
        self.btnAcceptOulet.isHidden = true

        var totalDateTime = ""
        var assignedMenteeName = ""
        
        let description = dicData["description"] as? String
       // let is_request_sent = dicData["is_request_sent"] as? Bool
          self.viewForAllButton.isHidden = true
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
    
    func loadDataPast(dicData : NSDictionary){
        /*
        self.valueMode =  UserDefaults.standard.value(forKey: "mode") as? String
        
        if self.valueMode == "dark" {
            mainView.backgroundColor = .gray//UIColor(hex: "#232137")
            lblRequestedBy.textColor = .white
            lblShowLocation.textColor = .white
            lblSessionSpace.textColor = .white
            lblShowDescription.textColor = .white
            lblRequestedNotes.textColor = .white
            lblScheduledByChecking.textColor = .white
            lblMeetingTitle.textColor = .white
            
            
            firstview.backgroundColor = UIColor(hex: "#232137")
            secondvIew.backgroundColor = UIColor(hex: "#232137")
            thirdview.backgroundColor = UIColor(hex: "#232137")
            fourview.backgroundColor = UIColor(hex: "#232137")
            fiveview.backgroundColor = UIColor(hex: "#232137")
            sixview.backgroundColor = UIColor(hex: "#232137")
            sevenview.backgroundColor = UIColor(hex: "#232137")
        }
        else if self.valueMode == "light" {
            mainView.backgroundColor = .white
            lblRequestedBy.textColor = .black
            lblShowLocation.textColor = .black
            lblSessionSpace.textColor = .black
            lblShowDescription.textColor = .black
            lblRequestedNotes.textColor = .black
            lblScheduledByChecking.textColor = .black
            lblMeetingTitle.textColor = .black
            
            
            firstview.backgroundColor = .white
                       secondvIew.backgroundColor = .white
                       thirdview.backgroundColor = .white
                       fourview.backgroundColor = .white
                       fiveview.backgroundColor = .white
                       sixview.backgroundColor = .white
                       sevenview.backgroundColor = .white
        }
        */
        viewCanceled.isHidden = true

        lblScheduledByChecking.text = "" //Meeting requested by:
        self.viewRequestedNotes.isHidden = true
        
        var totalDateTime = ""
        var assignedMenteeName = ""
        
        let description = dicData["description"] as? String
        
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
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        totalDateTime += "\(eventDate!) \(time_from!) "
        lblShowdate.text = totalDateTime
        
        lblMeetingTitle?.text = String(describing: dicData["title"]!)
        
        let schoolname = dicData["school_name"] as? String ?? ""
        if schoolname == "" {
            lblShowLocation?.text = "Affiliate Office"
        }
        else {
            lblShowLocation?.text = String(describing: dicData["school_name"]!)
        }
    
        
        
        lblSessionSpace?.text = String(describing: dicData["method_value"]!) //school_location
        lblRequestedBy?.text  = String(describing: dicData["creator_name"]!)
        
        let status = dicData["status"] as! Int
        
        if status==3{
            viewCanceled.isHidden = false
        }
        self.viewForAllButton.isHidden = true
    }
    
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

    }
    
}

