//
//  ReportCollectionVCell.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 9/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ReportCollectionVCell: UICollectionViewCell {

    @IBOutlet weak var imgV: UIImageView!
    @IBOutlet weak var lblTitle: UILabel!
    @IBOutlet weak var lblPostedDate: UILabel!
    

    override func awakeFromNib() {
        super.awakeFromNib()
    }
    func getDate(strDate: String) -> String {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd HH:mm:ss" //"MM-dd-yyyy HH:mm:ss"
        //"yyyy-MM-dd HH:mm:ss" //"2019-09-10 10:18:46"
        let dateDeadline = formatter.date(from: strDate)
        formatter.dateFormat = "MM-dd-yyyy"
        let strDateDeadline = formatter.string(from: dateDeadline!)
        return strDateDeadline
    }
    
    func loadData(dataDic : NSDictionary){
        
        let imgUrl = TakeStockInChildrenConstant.reportImage.appending(dataDic["image"] as? String ?? "sd")
        
        imgV.sd_setImage(with: URL(string: imgUrl), placeholderImage: UIImage(named: ""))
        
            lblTitle.text = dataDic["name"] as? String ?? ""
            lblPostedDate.text = getDate(strDate: dataDic["created_date"] as? String ?? "")

    }

}
