//
//  MenTeeSessionTableViewCellTwo.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/2/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class MenTeeSessionTableViewCellTwo: UITableViewCell {
    @IBOutlet weak var upcomingSession: UILabel!
    
    var arrtwo : NSMutableArray = []
    
    var idUseForMeetingReschedule = ""
    var sessionServerDateandTime = ""
    var describeText = ""
    var titleSet = ""
    
    
    var videoDelegate: videoPushMenteefromBtn?
    @IBOutlet weak var menteeSessioncollectionviewtwo: UICollectionView!
    override func awakeFromNib() {
        super.awakeFromNib()
        self.menteeSessioncollectionviewtwo.delegate = self
        self.menteeSessioncollectionviewtwo.dataSource = self
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
    
    @objc func videopush(sender: UIButton) {
        
        
        
        let objForEdit = self.arrtwo[sender.tag] as! NSDictionary
        
        let senderid = objForEdit["mentee_id"] as? Int
        print("senderid",senderid ?? "")
    
        
        var receiverId: String = ""
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        print("login mode",loginMode)
        var sendertype: String = ""
        var types: String = ""
        if loginMode == "Mentor"{
            sendertype = "mentor"
            receiverId = String(objForEdit["mentee_id"] as? Int ?? 0)
            print("receiverId",receiverId)
    //        receiverId = String(objForEdit["created_by"] as? Int ?? 0)

            types = "mentee"
        }
        else if loginMode == "Mentee" {
            sendertype = "mentee"
            receiverId = String(objForEdit["mentor_id"] as? Int ?? 0)
            print("receiverId",receiverId)

            types = "mentor"
        }
        print("types",types)
//        receiverId = String(objForEdit["created_by"] as? Int ?? 0)
       
        self.videoDelegate!.videopushSet(senderId: String(senderid ?? 0), senderType: sendertype, receiverId: receiverId, receiverType: types, fromWhereTag: "viaChat")
        
        
        
        
        
        
    }
    
    
    

}



extension MenTeeSessionTableViewCellTwo: UICollectionViewDelegate,UICollectionViewDataSource,UICollectionViewDelegateFlowLayout{
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return self.arrtwo.count
        
    }
    
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        
        var cell:MenteePastCollectionViewCell = collectionView.dequeueReusableCell(withReuseIdentifier: "MenteePastCollectionViewCell", for: indexPath) as! MenteePastCollectionViewCell
        
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("MenteePastCollectionViewCell", owner: self, options: nil)!
            cell = (nib[0] as? MenteePastCollectionViewCell)!
        }
        
        
        cell.videobtn.isHidden = false

        cell.videobtn.addTarget(self, action: #selector(videopush(sender:)), for: UIControl.Event.touchUpInside)
        cell.videobtn.tag = indexPath.row
        
        if arrtwo.count > 0 {
            let dataDic = arrtwo[indexPath.row] as! NSDictionary
            cell.loadDataUpcomming(dicData: dataDic)
        }
        
        
        return cell
        
    }
    
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: self.menteeSessioncollectionviewtwo.frame.size.width, height: self.menteeSessioncollectionviewtwo.frame.size.height)
    }
}
