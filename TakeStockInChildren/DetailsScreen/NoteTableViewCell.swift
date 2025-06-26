//
//  NoteTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 17/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class NoteTableViewCell: UITableViewCell {

    @IBOutlet weak var labelShowNote: UILabel!
    @IBOutlet weak var labelDate: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
