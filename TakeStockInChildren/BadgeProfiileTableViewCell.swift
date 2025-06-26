//
//  BadgeProfiileTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 2/24/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit
import ShimmerSwift
class BadgeProfiileTableViewCell: UITableViewCell {

    @IBOutlet weak var mainView: ShimmeringView!
    @IBOutlet weak var msgLabel: UILabel!
    @IBOutlet weak var startdateLabel: UILabel!
    @IBOutlet weak var enddataLabel: UILabel!
    @IBOutlet weak var sDatavalues: UILabel!
    @IBOutlet weak var eDatevalues: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
