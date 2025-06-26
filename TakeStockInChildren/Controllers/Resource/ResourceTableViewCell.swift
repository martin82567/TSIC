//
//  ResourceTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 22/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ResourceTableViewCell: UITableViewCell {

    @IBOutlet weak var labelResourceName: UILabel!
    @IBOutlet weak var labelEmailCell: UILabel!
    @IBOutlet weak var labelPhoneCell: UILabel!
    @IBOutlet weak var labelAddressCell: UILabel!
    @IBOutlet weak var buttonSentMail: UIButton!
    @IBOutlet weak var buttonCall: UIButton!
    @IBOutlet weak var imageViewResource: UIImageView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
