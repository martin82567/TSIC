//
//  PastSessionCollectionViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/30/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class PastSessionCollectionViewCell: UICollectionViewCell {

    @IBOutlet weak var fourthviewtop: NSLayoutConstraint!
    
    @IBOutlet weak var topconstraint: NSLayoutConstraint!
    @IBOutlet weak var sevenview: UIView!
    @IBOutlet weak var sixview: UIView!
    @IBOutlet weak var fiveview: UIView!
    @IBOutlet weak var fourthview: UIView!
    @IBOutlet weak var thirdview: UIView!
    @IBOutlet weak var firstview: UIView!
    @IBOutlet weak var secondview: UIView!
    @IBOutlet weak var noteicon: UIImageView! {
        didSet {
            noteicon.isHidden = true
        }
    }
    @IBOutlet weak var mainView: UIViewX!
    @IBOutlet weak var lblRequestedBy: UILabel!
    @IBOutlet weak var lblScheduledByStatic: UILabel!
    @IBOutlet weak var lblShowLocation: UILabel!
    @IBOutlet weak var lblSessionSpace: UILabel!
    @IBOutlet weak var lblShowdate: UILabel!
    @IBOutlet weak var lblShowDescription: UILabel!
    @IBOutlet weak var viewRequestedNotes: UIView!
    @IBOutlet weak var viewAllButtons: UIView!
    
    @IBOutlet weak var btnRescheduleOutlet: UIButton!
    @IBOutlet weak var btnCancelOutlet: UIButton!
    @IBOutlet weak var btnDenyOutlet: UIButton!
    
    @IBOutlet weak var lblRequestedNote: UILabel!
    @IBOutlet weak var viewCanceled: UIViewX!
    
    @IBOutlet weak var lblMeetingTitle: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }
    
    
    
    
    
    func loadDataRequested(dicData : NSDictionary) {
    //    let mode = UserDefaults.standard.value(forKey:  "mode") as? String
        var assignedMenteeName = ""
        viewCanceled.isHidden=true
        viewAllButtons.isHidden = false
        viewRequestedNotes.isHidden = false
        var totalDateTime = ""
        
        let description = dicData["description"] as? String
        
        if let desc = description {
            if desc == "" {
              print("no note found")
                self.fiveview.isHidden = true
            } else {
                 noteicon.isHidden = false
                self.lblShowDescription.text = desc
            }
        }
        
        let eventDate = dicData["date"] as? String
        
        let time_from = dicData["time"] as? String
        
        let dateFormatter2 = DateFormatter()
        dateFormatter2.dateFormat = "HH:mm:ss"
        
        let dt = dateFormatter2.date(from: time_from!)
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        let strTime = dateFormatter.string(from: dt!)
        
        totalDateTime += "\(eventDate!) \(strTime) "
        lblShowdate.text = totalDateTime
        
         
        let schoolID = dicData["school_id"] as? Int
        if schoolID != 0 {
            print("Scholll---ID",schoolID)
            let strSchoolNametwo = dicData["school_name"] as? String ?? ""
            lblShowLocation?.text = strSchoolNametwo
        }
        else if schoolID == 0 {
            print("Scholll---ID-----",schoolID)
            let strSchoolName = dicData["school_type"] as? String ?? ""
            lblShowLocation?.text = strSchoolName
        }
        
        /*
        let strSchoolName = dicData["school_type"] as? String ?? ""
        let checkschool = dicData["school_name"] as? String ?? ""
        if strSchoolName == "" {
            let strSchoolNametwo = dicData["school_name"] as? String ?? ""
            lblShowLocation?.text = strSchoolNametwo
        }
        else if checkschool != "" && strSchoolName != "" {
            let strSchoolNametwo = dicData["school_name"] as? String ?? ""
            lblShowLocation?.text = strSchoolNametwo
        }
        else {
            lblShowLocation?.text = strSchoolName
        }
        print("schollname",strSchoolName)
        */
        
        let strSchoolLoc  = dicData["method_value"] as? String
        
        
        lblSessionSpace.text = strSchoolLoc!
        self.lblRequestedBy.text  = totalName(dicData: dicData)
        
        let Requestednote = dicData["note"] as? String
        print("noteuuuuu========",Requestednote)
        if let note = Requestednote {
            if note == "" {
                
                viewRequestedNotes.isHidden = true
                
                
               // heightcontriantsixview.constant = 0
               // self.layoutIfNeeded()
                
                
                lblScheduledByStatic.text = "Assign To"
                self.btnDenyOutlet.isHidden = true
                self.btnCancelOutlet.isHidden = true
                self.btnRescheduleOutlet.setTitle("EDIT", for: .normal)
                
                let arrOfMenteeList = dicData["mentees"] as? NSArray
                
                if let menteesList = arrOfMenteeList {
                    if menteesList.count == 1 {
                        let dicMenteeList = menteesList[0] as! NSDictionary
                        
                        let firstName = dicMenteeList["firstname"] as? String ?? ""
                        let lastname = dicMenteeList["lastname"] as? String ?? ""
                        let middlename = dicMenteeList["middlename"] as? String ?? ""
                        assignedMenteeName = "\(firstName) \(middlename) \(lastname)"
                    }
                    self.lblRequestedBy.text = assignedMenteeName
                }
            } else {
                self.btnRescheduleOutlet.setTitle("RESCHEDULE", for: .normal)
                //viewRequestedNotes.isHidden = false
                self.btnDenyOutlet.isHidden = false
                self.btnCancelOutlet.isHidden = false
                lblScheduledByStatic.text = "" //Rescheduled requested by
                self.lblRequestedNote.text = note
            }
        }
        
        let status = dicData["status"] as! Int
        
        
        if status == 0 {
        //meeting pending
        } else if status == 1 {
            // meeting Accepted
            self.btnDenyOutlet.isHidden = true
        } else if status == 3 {
            //Meeting Canceled
            viewAllButtons.isHidden = true
            viewCanceled.isHidden=false
        }
        lblMeetingTitle?.text = String(describing: dicData["title"]!)
        
        
        /*
        if mode == "dark" {
            
            
            contentView.backgroundColor = .black
            
            firstview.backgroundColor = .gray
            secondview.backgroundColor = .gray
            thirdview.backgroundColor = .gray
            fourthview.backgroundColor = .gray
            fiveview.backgroundColor = .gray
            sixview.backgroundColor = .gray
            sevenview.backgroundColor = .gray
            
            
            
            mainView.backgroundColor = .gray
            lblRequestedBy.textColor = .white
            lblScheduledByStatic.textColor = .white
            lblShowLocation.textColor = .white
            lblSessionSpace.textColor = .white
            lblShowdate.textColor = .white
            lblShowDescription.textColor = .white
            lblRequestedNote.textColor = .darkGray
            lblMeetingTitle.textColor = .white
        }
        else if mode == "light" {
            
            contentView.backgroundColor = .white
            
            
            firstview.backgroundColor = .white
            secondview.backgroundColor = .white
            thirdview.backgroundColor = .white
            fourthview.backgroundColor = .white
            fiveview.backgroundColor = .white
            sixview.backgroundColor = .white
            sevenview.backgroundColor = .white
            
            
            
            
            
            
            mainView.backgroundColor = .white
            lblRequestedBy.textColor = .black
            lblScheduledByStatic.textColor = .black
            lblShowLocation.textColor = .black
            lblSessionSpace.textColor = .black
            lblShowdate.textColor = .black
            lblShowDescription.textColor = .black
            lblRequestedNote.textColor = .darkGray
            lblMeetingTitle.textColor = .black
        }
        
        */
    }
    
    
    
    
    func totalName(dicData:NSDictionary) -> String {
        var TotalName = ""

        let firstName = dicData["firstname"] as? String
        let lastName = dicData["lastname"] as? String
        let middlename = dicData["middlename"] as? String
        
        if let fName = firstName {
            TotalName = fName
        }
        
        if let middleName = middlename {
            if middleName != "" {
                TotalName += " \(middleName)"
            }
        }
        
        if let LName = lastName {
            if LName != "" {
                TotalName += " \(LName)"
            }
        }
        return TotalName
    }
    
    
    
    
    
    func loadDataPast(dicData : NSDictionary) {
       // let mode = UserDefaults.standard.value(forKey: "mode") as? String
        viewAllButtons.isHidden = true
        viewRequestedNotes.isHidden = true
      //  viewCanceled.isHidden=true

        var totalDateTime = ""
        var assignedMenteeName = ""

        
        let description = dicData["description"] as? String
        if let desc = description {
            if desc == "" {
            } else {
                noteicon.isHidden = false
                self.lblShowDescription.text = desc
            }
        }
        
        let eventDate = dicData["date"] as? String
        
        let time_from = dicData["time"] as? String
        
        let dateFormatter2 = DateFormatter()
        dateFormatter2.dateFormat = "HH:mm:ss"
        
        let dt = dateFormatter2.date(from: time_from!)
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        let strTime = dateFormatter.string(from: dt!)
        
        totalDateTime += "\(eventDate!) \(strTime) "
        lblShowdate.text = totalDateTime
        
        
       
        
        
        
        
        
        let strSchoolName = dicData["school_name"] as? String
        let strSchoolLoc  = dicData["method_value"] as? String
        
        lblShowLocation?.text = strSchoolLoc! + "," + strSchoolName!
        if strSchoolName == "" {
            lblSessionSpace?.text = dicData["school_type"] as? String
        }
        else {
            lblSessionSpace?.text = strSchoolName
        }
        
        lblRequestedNote?.text = dicData["note"] as? String
        
        let arrOfMenteeList = dicData["mentees"] as? NSArray
        
        if let menteesList = arrOfMenteeList {
            if menteesList.count == 1 {
                let dicMenteeList = menteesList[0] as! NSDictionary
                
                let firstName = dicMenteeList["firstname"] as? String ?? ""
                let lastname = dicMenteeList["lastname"] as? String ?? ""
                let middlename = dicMenteeList["middlename"] as? String ?? ""
                
                assignedMenteeName = "\(firstName) \(middlename) \(lastname)"
            } else {
                for mentee in menteesList {
                    let dicMenteeList = mentee as! NSDictionary
                    
                    let firstName = dicMenteeList["firstname"] as? String ?? ""
                    let lastname = dicMenteeList["lastname"] as? String ?? ""
                    let middlename = dicMenteeList["middlename"] as? String ?? ""
                    
                    assignedMenteeName += "\(firstName), \(middlename) ,\(lastname)"
                }
            }
            lblRequestedBy.text = assignedMenteeName
        }
        lblScheduledByStatic.text = ""
        lblMeetingTitle?.text = String(describing: dicData["title"]!)
        /*
        if mode == "dark" {
            
            
            contentView.backgroundColor = .black
            
            
            firstview.backgroundColor = .gray
            secondview.backgroundColor = .gray
            thirdview.backgroundColor = .gray
            fourthview.backgroundColor = .gray
            fiveview.backgroundColor = .gray
            sixview.backgroundColor = .gray
            sevenview.backgroundColor = .gray
            
            
            
            mainView.backgroundColor = .gray
            lblRequestedBy.textColor = .white
            lblScheduledByStatic.textColor = .white
            lblShowLocation.textColor = .white
            lblSessionSpace.textColor = .white
            lblShowdate.textColor = .white
            lblShowDescription.textColor = .white
            lblRequestedNote.textColor = .darkGray
            lblMeetingTitle.textColor = .white
        }
        else if mode == "light" {
            
            contentView.backgroundColor = .white
            
            
            firstview.backgroundColor = .white
            secondview.backgroundColor = .white
            thirdview.backgroundColor = .white
            fourthview.backgroundColor = .white
            fiveview.backgroundColor = .white
            sixview.backgroundColor = .white
            sevenview.backgroundColor = .white
            
            
            
            mainView.backgroundColor = .white
            lblRequestedBy.textColor = .black
            lblScheduledByStatic.textColor = .black
            lblShowLocation.textColor = .black
            lblSessionSpace.textColor = .black
            lblShowdate.textColor = .black
            lblShowDescription.textColor = .black
            lblRequestedNote.textColor = .darkGray
            lblMeetingTitle.textColor = .black
        }
        */
    }

}

