//
//  TaskListTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 21/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class TaskListTableViewCell: UITableViewCell {

    @IBOutlet weak var lableTitle: UILabel!
    @IBOutlet weak var labelDescription: UILabel!
    @IBOutlet weak var labelStartDate: UILabel!
    @IBOutlet weak var labelEndDate: UILabel!
    @IBOutlet weak var buttonEditTask: UIButton!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
