//
//  JournalListingTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class JournalListingTableViewCell: UITableViewCell {

    @IBOutlet weak var journalsTitle: UILabel!
    @IBOutlet weak var journalsDescription: UILabel!
    @IBOutlet weak var journalsCreatedDate: UILabel!
    @IBOutlet weak var journalsCreatedTime: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
