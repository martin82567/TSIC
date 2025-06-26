//
//  ElearningTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 18/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ElearningTableViewCell: UITableViewCell {

    @IBOutlet weak var mainvIew: UIView!
    @IBOutlet weak var viewcell: UIView!
    @IBOutlet weak var labelHeadCell: UILabel!
    @IBOutlet weak var imageViewCell: UIImageView!
    @IBOutlet weak var labelDescription: UILabel!
    @IBOutlet weak var buttonDetailstap: UIButton!
    @IBOutlet weak var imageViewVideoSign: UIImageView!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
