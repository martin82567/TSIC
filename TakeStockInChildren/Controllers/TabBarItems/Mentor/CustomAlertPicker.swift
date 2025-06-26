//
//  CustomAlertPicker.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 5/7/20.
//  Copyright © 2020 Aquarious Technology. All rights reserved.
//

import UIKit

protocol DateAndTimeDelegate {
    func getDate(dateAndTimeforServer: String, dateAndTimeForTextField: String)
    func getTime(dateAndTimeforServer: String, dateAndTimeForTextField: String)
}

class CustomAlertPicker: UIViewController {

    @IBOutlet weak var lblChooseDate: UILabel!
    @IBOutlet weak var datePicker: UIDatePicker!
    
    var isIamFromDate: Bool?
    var timeZone: String?

    var delegate: DateAndTimeDelegate?
    
    var showDateAndTimeForServer = ""
    var showDateAndTimeForLabel = ""
    var valeChanged: Bool?
    
    func modifyDateUI(){
        datePicker.datePickerMode = .date
         // Now in New York time
        guard let nyTimeZone: TimeZone = TimeZone(identifier: timeZone ?? "America/New_York") else {return}
        datePicker.timeZone = nyTimeZone
        datePicker.minimumDate = Date()
        
    }
   
    func modifyTimeUI(){
        datePicker.datePickerMode = .time
        
        // Now in New York time
        guard let nyTimeZone: TimeZone = TimeZone(identifier: timeZone ?? "America/New_York") else {return}
        datePicker.timeZone = nyTimeZone
        
    }
    func changeTimeZoneToNewYark(now: Date , dateFormate: String) -> String{
       // let now: Date = Date()
        let dateFormatter: DateFormatter = DateFormatter()
        dateFormatter.dateStyle = .short
        dateFormatter.timeStyle = .short
        var timeZone = ""

               let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String

               if loginMode == "Mentor" {
                 timeZone = TakeStockInChildrenConstant.mentorTimeZone
               }else{
                   timeZone = TakeStockInChildrenConstant.menteeTimeZone

               }
        // Now in New York time
        let nyTimeZone: TimeZone = TimeZone(identifier: timeZone)!
        dateFormatter.timeZone = nyTimeZone
        dateFormatter.dateFormat = dateFormate
        return dateFormatter.string(from: now)
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        self.valeChanged = false
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        if loginMode == "Mentor" {
            timeZone = TakeStockInChildrenConstant.mentorTimeZone
        }else{
            timeZone = TakeStockInChildrenConstant.menteeTimeZone
            
        }
        
        if isIamFromDate! {
            modifyDateUI()
        }else{
            modifyTimeUI()
        }
        datePicker.setValue(UIColor.white, forKeyPath: "textColor")

        datePicker.addTarget(self, action: #selector(dateChanged(_:)), for: .valueChanged)
        
        if #available(iOS 13.4, *) {
            datePicker.preferredDatePickerStyle = .wheels
        } else {
            // Fallback on earlier versions
        }
        
        // Do any additional setup after loading the view.
    }

    @objc func dateChanged(_ sender: UIDatePicker) {
        self.valeChanged = true

        let date =  sender.date
        print(date)
        if isIamFromDate! {
            let result = changeTimeZoneToNewYark(now: date, dateFormate: "MM-dd-yyyy") //"hh:mm a"
             print(result)
            self.showDateAndTimeForServer = result
            self.lblChooseDate.text = result
            self.showDateAndTimeForLabel = result
        }else{
            let result = changeTimeZoneToNewYark(now: date, dateFormate: "hh:mm a") //"HH:mm:ss"
              print(result)
            let resultForServer = changeTimeZoneToNewYark(now: date, dateFormate: "HH:mm:ss")
             print(resultForServer)
            self.showDateAndTimeForServer = resultForServer
            self.lblChooseDate.text = result
            self.showDateAndTimeForLabel = result
        }
    }
    
    func valueChanged(){
        if valeChanged! == false{
            if isIamFromDate! {
                let result = changeTimeZoneToNewYark(now: Date(), dateFormate: "MM-dd-yyyy") //"hh:mm a"
                print(result)
                self.showDateAndTimeForServer = result
                self.lblChooseDate.text = result
                self.showDateAndTimeForLabel = result
                
            }else{
                let result = changeTimeZoneToNewYark(now: Date(), dateFormate: "hh:mm a") //"HH:mm:ss"
                print(result)
                let resultForServer = changeTimeZoneToNewYark(now: Date(), dateFormate: "HH:mm:ss")
                print(resultForServer)
                self.showDateAndTimeForServer = resultForServer
                self.lblChooseDate.text = result
                self.showDateAndTimeForLabel = result
            }
            
        }
        
    }
    
    @IBAction func btnDoneAction(_ sender: Any) {
        
        if isIamFromDate! {
            self.valueChanged()
          self.delegate?.getDate(dateAndTimeforServer: self.showDateAndTimeForServer, dateAndTimeForTextField: self.showDateAndTimeForLabel)
        }else{
              self.valueChanged()
              self.delegate?.getTime(dateAndTimeforServer: self.showDateAndTimeForServer, dateAndTimeForTextField: self.showDateAndTimeForLabel)
        }
      
        self.dismiss(animated: true, completion: nil)
    }
    
    @IBAction func btnCancelAction(_ sender: Any) {
        self.dismiss(animated: true, completion: nil)
       }
    

    
    
}
