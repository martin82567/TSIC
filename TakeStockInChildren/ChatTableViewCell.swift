//
//  ChatTableViewCell.swift
//  MoreToLife
//
//  Created by Aquarious Technology on 22/02/19.
//  Copyright © 2019 Aquarious. All rights reserved.
//

import UIKit

class ChatTableViewCell: UITableViewCell {
    
    @IBOutlet weak var imgUser: UIImageView!
    @IBOutlet weak var imgOther: UIImageView!
    @IBOutlet weak var lblOther: UILabel!
    @IBOutlet weak var lblUser: UILabel!
    @IBOutlet weak var lblTime: UILabel!
    @IBOutlet weak var imgSeen: UIImageView!
    @IBOutlet weak var lblSenderName: UILabel!
    @IBOutlet weak var constraintHghtLblSenderName: NSLayoutConstraint!
    @IBOutlet weak var seenBtn: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }
    
    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)
        
        // Configure the view for the selected state
    }

}
