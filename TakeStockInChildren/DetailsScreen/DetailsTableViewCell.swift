//
//  DetailsTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 16/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class DetailsTableViewCell: UITableViewCell {

    @IBOutlet weak var labelHeadCell: UILabel!
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
