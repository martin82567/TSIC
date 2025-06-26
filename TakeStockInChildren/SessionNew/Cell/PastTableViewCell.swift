//
//  PastTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/29/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

protocol didSelectDelegate{
    func didSelect(data: NSDictionary)
}
class PastTableViewCell: UITableViewCell {

    var delegate: didSelectDelegate?
    @IBOutlet weak var placeholderthree: UILabel!
    var pastArr : NSMutableArray = []
    @IBOutlet weak var collectionViewThree: UICollectionView!
    override func awakeFromNib() {
        super.awakeFromNib()
        collectionViewThree.delegate = self
        collectionViewThree.dataSource =  self
        
        
        // Initialization code
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }


}

extension PastTableViewCell: UICollectionViewDelegate,UICollectionViewDataSource,UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return pastArr.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        var cell = collectionView.dequeueReusableCell(withReuseIdentifier: "RequestSessionCollectionViewCell", for: indexPath) as! RequestSessionCollectionViewCell
        
        if (cell == nil) {
            var nib:Array = Bundle.main.loadNibNamed("RequestSessionCollectionViewCell", owner: self, options: nil)!
            cell = (nib[0] as? RequestSessionCollectionViewCell)!
        }
        
        if pastArr.count > 0 {
            let dataDic = pastArr[indexPath.row] as! NSDictionary
            
            cell.loadDataPast(dicData: dataDic)
        }
        cell.addLogButton.tag = indexPath.row
        cell.addLogButton.addTarget(self, action: #selector(buttonPressed), for: .touchUpInside)

        return cell
    }
    @objc func buttonPressed(_ sender: UIButton) {
        print(sender.tag)
        let obj = self.pastArr[sender.tag]
        let resultNew = obj as? [String:Any]
            self.delegate?.didSelect(data: obj as! NSDictionary)
        
        
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: self.collectionViewThree.frame.size.width, height: self.collectionViewThree.frame.size.height)
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        let obj = self.pastArr[indexPath.item]
        let resultNew = obj as? [String:Any]

        let islooged = resultNew?["is_logged"] as? Int
        print("id",islooged)
        if islooged == 1 {
            print("Not tap")
        }
        else {
            self.delegate?.didSelect(data: obj as! NSDictionary)
        }
        
        /*
        let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
        let newViewController = MentorMeetingstoryBoard.instantiateViewController(withIdentifier: "ShowSessionViewController") as! ShowSessionViewController
        self.navigationController?.pushViewController(newViewController, animated: true)
        */
    }
    
    
    
    
}
