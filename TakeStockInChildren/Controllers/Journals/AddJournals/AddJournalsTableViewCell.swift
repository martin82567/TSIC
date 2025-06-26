//
//  AddJournalsTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class AddJournalsTableViewCell: UITableViewCell {

    @IBOutlet weak var textFieldTitleJournals: UITextField!
    @IBOutlet weak var buttonSaveJournals: UIButton!
    @IBOutlet weak var textViewJournals: UITextView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
