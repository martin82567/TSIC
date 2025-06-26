//
//  MessageCenterTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 5/7/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class MessageCenterTableViewCell: UITableViewCell {

    @IBOutlet weak var edateLabel: UILabel!
    @IBOutlet weak var sDateLabel: UILabel!
    @IBOutlet weak var messageLbl: UILabel!
    @IBOutlet weak var mainView: UIView!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
