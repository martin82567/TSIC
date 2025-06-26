//
//  ResourceDetailsTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ResourceDetailsTableViewCell: UITableViewCell {

    @IBOutlet weak var labelResourceName: UILabel!
    @IBOutlet weak var labelLocation: UILabel!
    @IBOutlet weak var labelType: UILabel!
    @IBOutlet weak var labelDescription: UILabel!
    @IBOutlet weak var labelWorkPhone: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
