//
//  MyMenteeDetailsTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 12/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MyMenteeDetailsTableViewCell: UITableViewCell {

    @IBOutlet weak var labelMenteeEmailCell: UILabel!
    @IBOutlet weak var labelMenteePhoneCell: UILabel!
    @IBOutlet weak var labelMenteeAddressCell: UILabel!
    @IBOutlet weak var phoneImg: UIImageView!
    @IBOutlet weak var addressImg: UIImageView!
    
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
