//
//  MentorProfileTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 08/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MentorProfileTableViewCell: UITableViewCell {
    
    @IBOutlet weak var mentorRIghticon: UIImageView!
    @IBOutlet weak var cellBadgeCount: UILabel!
    @IBOutlet weak var imageViewMentorIcon: UIImageView!
    
    @IBOutlet weak var mainView: UIView!
    @IBOutlet weak var labelMentorOptionsMennu: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
