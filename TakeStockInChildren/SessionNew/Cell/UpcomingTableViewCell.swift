//
//  UpcomingTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/29/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit


protocol videoAction {
    func actionVideo(senderId: String,senderType: String,receiverid: String,recieverType: String,fromwheretag: String)
}

class UpcomingTableViewCell: UITableViewCell {

    @IBOutlet weak var placeholdertwo: UILabel!
    var uparr : NSMutableArray = []
    var videoDelegate: videoAction?
    @IBOutlet weak var collectionViewtwo: UICollectionView!
    override func awakeFromNib() {
        super.awakeFromNib()
        collectionViewtwo.delegate =  self
        collectionViewtwo.dataSource =  self
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
    
    @objc func videochat(sender : UIButton) {
        
        
        
        let objForEdit = uparr[sender.tag] as! NSDictionary
        
        let senderid = objForEdit["created_by"] as? Int
        print("senderid",senderid ?? "")
        
        var receiverId: String = ""
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("login mode",loginMode)
        var sendertype: String = ""
        var types: String = ""
        if loginMode == "Mentor"{
            sendertype = "mentor"
            types = "mentee"
        }
        else if loginMode == "Mentee" {
            sendertype = "mentee"
            types = "mentor"
        }
        print("types",types)
        receiverId = String(objForEdit["mentee_id"] as? Int ?? 0)
        
        
        
//        let arrOfMenteeList = objForEdit["mentees"] as? NSArray
//        
//        if let menteesList = arrOfMenteeList {
//            if menteesList.count == 1 {
//                let dicMenteeList = menteesList[0] as! NSDictionary
//                receiverId = String(dicMenteeList["id"] as? Int ?? 0)
//            } else {
//                for mentee in menteesList {
//                    let dicMenteeList = mentee as! NSDictionary
//                    receiverId = String(dicMenteeList["id"] as? Int ?? 0)
//                }
//            }
//        }
        print("recieevrid",receiverId)
        
        self.videoDelegate?.actionVideo(senderId: String(senderid ?? 0), senderType: sendertype, receiverid: receiverId, recieverType: types, fromwheretag: "viaChat")
        
        /*
        
       
        */

        
        
        
    }
    
    
}


extension UpcomingTableViewCell: UICollectionViewDelegate,UICollectionViewDataSource,UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return uparr.count
    }
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        var cell = collectionView.dequeueReusableCell(withReuseIdentifier: "UpcomingSessionCollectionViewCell", for: indexPath) as! UpcomingSessionCollectionViewCell
        
        
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("UpcomingSessionCollectionViewCell", owner: self, options: nil)!
            cell = (nib[0] as? UpcomingSessionCollectionViewCell)!
        }
        
        
        
        cell.videoBtn.addTarget(self, action: #selector(videochat(sender:)), for: .touchUpInside)
        cell.videoBtn.tag = indexPath.row
        
        
        
        if uparr.count > 0 {
            let dataDic = uparr[indexPath.row] as! NSDictionary
            cell.loadData(dicData: dataDic)
            
         let boolToCheckMentorCreated = dataDic["is_mentor_created"] as! Bool
          //  let boolToCheckMentorCreated = dataDic["created_from"] as! String
            
            if boolToCheckMentorCreated == true {
                cell.stackButton.isHidden = false
            } else {
                cell.stackButton.isHidden = true
            }
        }
        
        return cell
    }
    
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: 320, height: 270)
    }
}
