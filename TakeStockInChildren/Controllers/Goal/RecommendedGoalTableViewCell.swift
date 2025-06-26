//
//  RecommendedGoalTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 10/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class RecommendedGoalTableViewCell: UITableViewCell {

    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var imgInProgress: UIImageView!
    @IBOutlet weak var labelHeadCell: UILabel!
    @IBOutlet weak var labelDescriptionCell: UILabel!
    @IBOutlet weak var imgCompletedGoal : UIImageView!
    @IBOutlet weak var imgMenteeOwnerBadge: UIImageView!
    @IBOutlet weak var buttonCompletedDetails: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
