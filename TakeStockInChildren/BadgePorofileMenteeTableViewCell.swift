//
//  BadgePorofileMenteeTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Samir on 2/25/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//
    
import UIKit
import ShimmerSwift
class BadgePorofileMenteeTableViewCell: UITableViewCell {

    @IBOutlet weak var mainView: ShimmeringView!
    @IBOutlet weak var msgLabel: UILabel!
    @IBOutlet weak var sdateLabel: UILabel!
    @IBOutlet weak var edateLabel: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
