//
//  RequestSessionCollectionViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/30/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class RequestSessionCollectionViewCell: UICollectionViewCell {

    @IBOutlet weak var addLogButton: UIButton!
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
    @IBOutlet weak var lblMeetingTitle: UILabel!
    
    
    @IBOutlet weak var viewConcelled: UIViewX!
    
    
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }

    
    
    
    
    func loadDataPast(dicData : NSDictionary) {
//showHide add log button:-
//        let obj = self.dicData[indexPath.item]
        let islooged = dicData["is_logged"] as? Int
        print("id",islooged)
        if islooged == 1 {
            print("Not tap")
            addLogButton.isHidden = true
        }
        else {
   
            addLogButton.isHidden = false
        }
        
        var totalDateTime = ""
        var assignedMenteeName = ""

        
        let description = dicData["description"] as? String
        if let desc = description {
            if desc == "" {
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
        
        
        
        let status = dicData["status"] as! Int
        if status==3 {
            viewConcelled.isHidden = false
        }
        else {
            viewConcelled.isHidden = true
        }
        
        let strSchoolName = dicData["school_name"] as? String
        let strSchoolLoc  = dicData["method_value"] as? String
        
        lblShowLocation?.text = strSchoolLoc! + "," + strSchoolName!
        if strSchoolName == "" {
            lblSessionSpace?.text = dicData["school_type"] as? String
        }
        else {
            lblSessionSpace?.text = strSchoolName
        }
        
       // lblRequestedNote?.text = dicData["note"] as? String
        
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
    }
    
    @IBAction func addLogButtonTapped(_ sender: UIButton) {
    }
    
}


