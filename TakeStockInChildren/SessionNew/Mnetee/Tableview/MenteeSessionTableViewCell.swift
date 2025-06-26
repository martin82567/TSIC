//
//  MenteeSessionTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 7/1/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
protocol btnAcceptsession {
    func btnAccept(idUseForMeetingReschedule: String,sessionServerDateandTime: String,titleSet: String,describeText: String, currentTitle: String)
}

protocol btnReshuduleSession {
    func btnreschudle(idUseForMeetingReschedule: String,currentTitle: String)
}


protocol videoPushMenteefromBtn {
    func videopushSet(senderId: String, senderType: String, receiverId: String, receiverType: String, fromWhereTag: String)
}




class MenteeSessionTableViewCell: UITableViewCell {

    @IBOutlet weak var placholderOne: UILabel!
    var arrone : NSMutableArray = []
    
    var idUseForMeetingReschedule = ""
    var sessionServerDateandTime = ""
    var describeText = ""
    var titleSet = ""
    
    var acceptDelegate: btnAcceptsession?
    var reschuduledelegate: btnReshuduleSession?
    @IBOutlet weak var collectionviewMente: UICollectionView!
    override func awakeFromNib() {
        super.awakeFromNib()
        
       self.collectionviewMente.delegate = self
       self.collectionviewMente.dataSource =  self
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // ConfOigure the view for the selected state
    }
    
    @objc func btnAccept(sender: UIButton) {
        let objForAccept = arrone[sender.tag] as! NSDictionary
        idUseForMeetingReschedule =  "\(objForAccept["id"] as! NSNumber)"
        sessionServerDateandTime = objForAccept["schedule_time"] as? String ?? ""
        titleSet = objForAccept["title"] as? String ?? ""
        describeText = objForAccept["description"] as? String ?? ""
        print("date and time", sessionServerDateandTime)
        let currentTitle  = sender.currentTitle!
        

        self.acceptDelegate!.btnAccept(idUseForMeetingReschedule: idUseForMeetingReschedule, sessionServerDateandTime: sessionServerDateandTime, titleSet: titleSet, describeText: describeText, currentTitle: currentTitle)
    }
    
    @objc func btnReschedule(sender: UIButton) {
        
        let objForReschedule = self.arrone[sender.tag] as! NSDictionary
        idUseForMeetingReschedule =  "\(objForReschedule["id"] as! NSNumber)"
        let currentTitle  = sender.currentTitle!
       
        self.reschuduledelegate!.btnreschudle(idUseForMeetingReschedule: idUseForMeetingReschedule, currentTitle: currentTitle)
        
    }
}

extension MenteeSessionTableViewCell: UICollectionViewDelegate,UICollectionViewDataSource,UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return self.arrone.count
        
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        
        var cell:SessionMenteeCollectionViewCell = collectionView.dequeueReusableCell(withReuseIdentifier: "SessionMenteeCollectionViewCell", for: indexPath) as! SessionMenteeCollectionViewCell
        
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("SessionMenteeCollectionViewCell", owner: self, options: nil)!
            cell = nib[0] as! SessionMenteeCollectionViewCell
        }
        
       cell.btnAcceptOulet.addTarget(self, action: #selector(btnAccept(sender:)), for: UIControl.Event.touchUpInside)
       cell.btnAcceptOulet.tag = indexPath.row
            
       cell.btnRescheduleOulet.addTarget(self, action: #selector(btnReschedule(sender:)), for: UIControl.Event.touchUpInside)
       cell.btnRescheduleOulet.tag = indexPath.row
       cell.viewForAllButton.isHidden = false
       cell.videobtn.isHidden = true
            if arrone.count > 0 {
                let dataDic = arrone[indexPath.row] as! NSDictionary
                cell.loadDataRequested(dicData: dataDic)
            }
        return cell
        
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: self.collectionviewMente.frame.size.width, height: self.collectionviewMente.frame.size.height)
    }
}
