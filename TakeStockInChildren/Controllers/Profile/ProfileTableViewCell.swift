//
//  ProfileTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 12/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ProfileTableViewCell: UITableViewCell {

    
    @IBOutlet weak var labelCount: UILabel!
    @IBOutlet weak var ViewOfBackground: UIView!
    @IBOutlet weak var imageListCell: UIImageView!
    @IBOutlet weak var labelSideMenuCell: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
