//
//  MentorSideMenuTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 27/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MentorSideMenuTableViewCell: UITableViewCell {

    @IBOutlet weak var viewLineOne: UIView!
    @IBOutlet weak var viewLineTwo: UIView!
    @IBOutlet weak var versionLbl: UILabel!
    @IBOutlet weak var imageViewSideMenu: UIImageView!
    @IBOutlet weak var labelSideMenuName: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
