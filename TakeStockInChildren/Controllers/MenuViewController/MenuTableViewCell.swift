//
//  MenuTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 10/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MenuTableViewCell: UITableViewCell {

    @IBOutlet weak var imageViewMenuIcon: UIImageView!
    @IBOutlet weak var labelMenuIcon: UILabel!
    @IBOutlet weak var viewLineOne: UIView!
    @IBOutlet weak var viewLineTwo: UIView!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
