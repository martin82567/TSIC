//
//  DescriptionTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 30/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class DescriptionPendingTableViewCell: UITableViewCell {

    @IBOutlet weak var labelTitleCell: UILabel!
    @IBOutlet weak var labelDescriptionCell: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
