//
//  AttachedFileTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 30/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class AttachedFileTableViewCell: UITableViewCell {

    @IBOutlet weak var collectionViewAttachFile: UICollectionView!
    @IBOutlet weak var buttonAddFiles: UIButton!
    @IBOutlet weak var buttonViewFiles: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        collectionViewAttachFile.isHidden = true
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        let width = ((collectionViewAttachFile.frame.size.width - 50) / 3)
        
        return CGSize(width: 50, height: 65)
    }
}



