//
//  CustomView.swift
//  CarouselViewExample
//
//  Created by Matteo Tagliafico on 03/04/16.
//  Copyright © 2016 Matteo Tagliafico. All rights reserved.
//

import UIKit

class SessionListingCell2: UITableViewCell {
    
    @IBOutlet weak var topview: UIViewX!
    @IBOutlet weak var mainView: UIViewX!
    @IBOutlet weak var labelSessiontype: UILabel!
    @IBOutlet weak var labelSessionmethod: UILabel!
    @IBOutlet weak var lblTitleSession: UILabel!
    @IBOutlet weak var lblUserName: UILabel!
    @IBOutlet weak var lblSessionDate: UILabel!
    @IBOutlet weak var lblSessionLocation: UILabel!
    @IBOutlet weak var noShowLabel: UILabel!
    @IBOutlet weak var btnEditOutlet: UIButton!
    @IBOutlet weak var stackButton: UIStackView!
    
    @IBOutlet weak var lblDescription: UILabel!
    var valueMode : String?
    
    override func awakeFromNib() {
        super.awakeFromNib()
    }
    
    func loadDataSessionListing(dicData : NSDictionary) {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        var totalDateTime = ""
        //lblTitleSession.text = dicData["name"] as? String
        let eventDate = dicData["schedule_date"] as? String ?? ""
        let time_duration = dicData["time_duration"] as? String ?? ""
        totalDateTime += "\(eventDate) For \(time_duration) minutes"
        lblSessionDate.text = totalDateTime
        lblDescription?.text = dicData["name"] as? String
        if (dicData["no_show"] as! Int == 1) {
            self.noShowLabel.isHidden = false
        } else {
            self.noShowLabel.isHidden = true
        }
        
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
        
        let sessionType = dicData["type"] as? String ?? ""
        if sessionType == "1" {
            labelSessiontype.text = "Group"
        }
        else if sessionType == "2" {
            labelSessiontype.text = "Individual"
        }
        /*
        else if sessionType == "3" {
            labelSessiontype.text = "Virtual"
        }
        */
        
        //labelSessionmethod.text = "In-Person"
        let sesionMethod = dicData["session_method_location_id"] as? Int ?? 0
        
        if sesionMethod == 1 {
            labelSessionmethod.text = "In-Person"
        }
        if sesionMethod == 2 {
            labelSessionmethod.text = "Virtual"
        }
        if sesionMethod == 3 {
            labelSessionmethod.text = "App-Chat"
        }
        if sesionMethod == 4 {
            labelSessionmethod.text = "App-Chat"
        }
        
        if mode == "dark"{
            contentView.backgroundColor = .black
            mainView.backgroundColor = .gray
            labelSessiontype.textColor = .white
            labelSessionmethod.textColor = .white
            lblTitleSession.textColor = .white
            lblUserName.textColor = .black
            lblSessionDate.textColor = .white
            lblSessionLocation.textColor = .white
            lblDescription.textColor = .white
        }
        else if mode == "light" {
            contentView.backgroundColor = .white
            mainView.backgroundColor = .white
            labelSessiontype.textColor = .black
            labelSessionmethod.textColor = .black
            lblTitleSession.textColor = .black
            lblUserName.textColor = .black
            lblSessionDate.textColor = .black
            lblSessionLocation.textColor = .black
            lblDescription.textColor = .black
        }
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
    }
    
    
}
