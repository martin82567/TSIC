//
//  TaskTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 15/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class TaskTableViewCell: UITableViewCell {

    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var imageViewInProgress: UIImageView!
    @IBOutlet weak var labelHeadCell: UILabel!
    @IBOutlet weak var labelDescriptionCell: UILabel!
    @IBOutlet weak var imageViewCompletedGoalCell: UIImageView!
    @IBOutlet weak var buttonCompleted: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
