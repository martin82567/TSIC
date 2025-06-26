//
//  UpcomingSessionCollectionViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/30/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class UpcomingSessionCollectionViewCell: UICollectionViewCell {

    
    @IBOutlet weak var videoBtn: UIButton!
    @IBOutlet weak var noteIcontwo: UIImageView! {
        didSet  {
            noteIcontwo.isHidden = true
        }
    }
    @IBOutlet weak var mainView: UIViewX!
    @IBOutlet weak var lblTitleSession: UILabel!
    @IBOutlet weak var lblUserName: UILabel!
    @IBOutlet weak var lblSessionDate: UILabel!
    @IBOutlet weak var lblSessionLocation: UILabel!
    @IBOutlet weak var btnEditOutlet: UIButton!
    @IBOutlet weak var stackButton: UIStackView!
    
    @IBOutlet weak var lblDescription: UILabel!
    @IBOutlet weak var viewCanceled: UIViewX!
    
    
    func loadData(dicData : NSDictionary) {
     //   let mode = UserDefaults.standard.value(forKey: "mode") as? String
        viewCanceled.isHidden = true
        var totalDateTime = ""
        var assignedMenteeName = ""

        
        
        let description = dicData["description"] as? String
        
        if let desc = description {
            if desc == "" {
            print("no note found")
            } else {
                 noteIcontwo.isHidden = false
                self.lblDescription.text = desc
            }
        }
        
        //lblDescription.text = dicData["description"] as? String

        lblTitleSession.text = dicData["title"] as? String
        
        let eventDate = dicData["date"] as? String ?? ""
        
        let time_from = dicData["time"] as? String
        
        let dateFormatter2 = DateFormatter()
        dateFormatter2.dateFormat = "HH:mm:ss"
        
        let dt = dateFormatter2.date(from: time_from!)
        
        let dateFormatter = DateFormatter()
        dateFormatter.dateFormat = "hh:mm a"
        let strTime = dateFormatter.string(from: dt!)
        totalDateTime += "\(eventDate) \(strTime) "
        lblSessionDate.text = totalDateTime
        
        
        var schoolID = dicData["school_id"] as? Int
        print("Scholll---ID-----",schoolID)
        
        var strSchoolName = dicData["school_name"] as? String
        if strSchoolName == "" {
            var strSchooltype = dicData["school_type"] as? String
            strSchoolName = strSchooltype
        }
        let strSchoolLoc  = dicData["school_location"] as? String
        
        lblSessionLocation?.text = strSchoolLoc! + "," + strSchoolName!
        //lblSessionLocation?.text = dicData["address"] as? String
        
        
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
                    assignedMenteeName += "\(firstName) \(middlename) \(lastname),"
                }
            }
            lblUserName?.text = assignedMenteeName
        }
        let status = dicData["status"] as! Int
        if status==3 {
            viewCanceled.isHidden = false
        }
        
        /*
        
        if mode == "dark" {
            
            contentView.backgroundColor = .black
            
            mainView.backgroundColor = .gray
            lblUserName.textColor = .white
            lblSessionLocation.textColor = .white
            lblSessionDate.textColor = .white
            lblTitleSession.textColor = .white
            lblDescription.textColor = .white
            
        }
        else if mode == "light" {
            
            contentView.backgroundColor = .white
            
            mainView.backgroundColor = .white
            lblUserName.textColor = .black
                       lblSessionLocation.textColor = .black
                       lblSessionDate.textColor = .black
                       lblTitleSession.textColor = .black
                       lblDescription.textColor = .black
        }
        */
        

    }
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

}

