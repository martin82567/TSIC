//
//  AssignedMenteeListTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 26/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class AssignedMenteeListTableViewCell: UITableViewCell {

    @IBOutlet weak var buttonDeleteMentee: UIButton!
    @IBOutlet weak var imageViewAssignedMentee: UIImageView!
    @IBOutlet weak var lableAssignedMenteeName: UILabel!
    @IBOutlet weak var labelStausAssignedMentee: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
