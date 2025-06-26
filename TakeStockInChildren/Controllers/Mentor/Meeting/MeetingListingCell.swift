//
//  CustomView.swift
//  CarouselViewExample
//
//  Created by Matteo Tagliafico on 03/04/16.
//  Copyright © 2016 Matteo Tagliafico. All rights reserved.
//

import UIKit

class MeetingListingCell: UITableViewCell {
    
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
    
    var valueMode : String?
    
    override func awakeFromNib() {
        super.awakeFromNib()

    }
    
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
    
    
    func loadDataSessionListing(dicData : NSDictionary) {
      //  let mode = UserDefaults.standard.value(forKey: "mode") as? String
        viewCanceled.isHidden = true

        var totalDateTime = ""
        //lblTitleSession.text = dicData["name"] as? String
        let eventDate = dicData["date"] as? String ?? ""
        let time_from = dicData["time_from"] as? String ?? ""
        let time_to = dicData["time_to"] as? String ?? ""
        totalDateTime += "\(eventDate) From \(time_from) To \(time_to)"
        lblSessionDate.text = totalDateTime
        
        lblSessionLocation?.text = dicData["name"] as? String
        
        var TotalName = ""
        
        let firstName = dicData["firstname"] as? String
        let lastName = dicData["lastname"] as? String
        let middlename = dicData["middlename"] as? String
        
        if let fName = firstName {
            TotalName = fName
        }
        
        if let middleN = middlename {
            if middleN != "" {
                TotalName += " \(middleN)"
            }
        }
        
        if let LName = lastName {
            if LName != "" {
                TotalName += " \(LName)"
            }
        }
        lblUserName?.text = TotalName
        
        /*
        if mode == "dark" {
            
            contentView.backgroundColor = .black
            
            lblUserName.textColor = .white
            lblSessionLocation.textColor = .white
            lblSessionDate.textColor = .white
            lblTitleSession.textColor = .white
            lblDescription.textColor = .white
            
        }
        else if mode == "light" {
            
            contentView.backgroundColor = .white
            
            lblUserName.textColor = .black
                       lblSessionLocation.textColor = .black
                       lblSessionDate.textColor = .black
                       lblTitleSession.textColor = .black
                       lblDescription.textColor = .black
        }
        */
        
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
    }
    
    
}

