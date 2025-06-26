//
//  JobTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 18/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class JobTableViewCell: UITableViewCell {

    @IBOutlet weak var labelJobTitle: UILabel!
    @IBOutlet weak var labelJobDescription: UILabel!
    @IBOutlet weak var buttonApplyJobNow: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
