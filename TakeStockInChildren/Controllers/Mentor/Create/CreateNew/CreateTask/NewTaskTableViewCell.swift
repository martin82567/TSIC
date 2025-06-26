//
//  NewTaskTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 28/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class NewTaskTableViewCell: UITableViewCell {

    @IBOutlet weak var buttonEndDate: UIButton!
    @IBOutlet weak var buttonStartDate: UIButton!
    @IBOutlet weak var textFieldTitle: TextFieldPadding!
    @IBOutlet weak var textViewDescription: TextViewPadding!
    @IBOutlet weak var textFieldDateFrom: TextFieldPadding!
    @IBOutlet weak var textFieldDateTo: TextFieldPadding!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
