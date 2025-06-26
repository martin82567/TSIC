//
//  RequestingTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/29/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
protocol reschedule {
    func rescheduleAction(sender: UIButton, objEdit: NSDictionary)
}

protocol cancel {
    func cancelAction(canceldateTime: String,strMeetingID: String)
}


protocol deny {
    func denyAction(id: String)
}


class RequestingTableViewCell: UITableViewCell {

    @IBOutlet weak var placeholderOne: UILabel!
    var reqarr : NSMutableArray = []
    var rescheduleDelegate: reschedule?
    var cancelDelegate: cancel?
    var denydeleagate: deny?
    var commonData : NSMutableArray = []
    @IBOutlet weak var collectionViewone: UICollectionView!
    override func awakeFromNib() {
        super.awakeFromNib()
        collectionViewone.delegate =  self
        collectionViewone.dataSource =  self
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
    
    @objc func rescheduleAction(sender : UIButton) {
        print("in REquestingableviewCell rescheduleAction")
        let objarary = self.reqarr[sender.tag] as! NSDictionary
        self.rescheduleDelegate!.rescheduleAction(sender: sender, objEdit: objarary)
      
        
    }
    
    
    
    @objc func cancelAction(sender : UIButton) {
        
        
        let objForCancel = reqarr[sender.tag] as! NSDictionary
        let cancelDate = objForCancel["date"] as? String ?? ""
        let canceltime = objForCancel["time"] as? String ?? ""
        let canceldateTime: String = "\(cancelDate)\(" ")\(canceltime)"
        let strMeetingID: String =  "\(objForCancel["id"] as! NSNumber)"
        self.cancelDelegate!.cancelAction(canceldateTime: canceldateTime, strMeetingID: strMeetingID)
        
       
        print("cancel")
        
    }
    
    
    
    @objc func denyAction(sender : UIButton) {
        let objForCancel = reqarr[sender.tag] as! NSDictionary
        let strMeetingID =  "\(objForCancel["id"] as! NSNumber)"
        self.denydeleagate!.denyAction(id: strMeetingID)
        print("deny")
        
    }
    
    
    
    

}


extension RequestingTableViewCell : UICollectionViewDelegate,UICollectionViewDataSource, UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return reqarr.count
    }
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        var cell = collectionView.dequeueReusableCell(withReuseIdentifier: "PastSessionCollectionViewCell", for: indexPath) as! PastSessionCollectionViewCell
        if (cell == nil) {
            print("using PAST CELL for RequestingTableViewCell")
            var nib:Array = Bundle.main.loadNibNamed("PastSessionCollectionViewCell", owner: self, options: nil)!
            cell = ((nib[0] as? PastSessionCollectionViewCell)!)
        }
        
        cell.btnRescheduleOutlet.addTarget(self, action: #selector(rescheduleAction(sender:)), for: .touchUpInside)
        cell.btnRescheduleOutlet.tag = indexPath.row
        
        cell.btnCancelOutlet.addTarget(self, action: #selector(cancelAction(sender:)), for: .touchUpInside)
        cell.btnCancelOutlet.tag = indexPath.row
        
        cell.btnDenyOutlet.addTarget(self, action: #selector(denyAction(sender:)), for: .touchUpInside)
        cell.btnDenyOutlet.tag = indexPath.row
        if reqarr.count > 0 {
            let dataDic = reqarr[indexPath.row] as! NSDictionary
            
            cell.loadDataRequested(dicData: dataDic)
        }
        return cell
    }
    
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: self.collectionViewone.frame.size.width, height: self.collectionViewone.frame.size.height)
    }
}
