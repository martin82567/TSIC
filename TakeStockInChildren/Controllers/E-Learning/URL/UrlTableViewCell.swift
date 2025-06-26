//
//  UrlTableViewCell.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 25/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import  WebKit

class UrlTableViewCell: UITableViewCell {

    @IBOutlet weak var webViewShow: UIWebView!
    @IBOutlet weak var textViewDesc: UITextView!
    @IBOutlet weak var labelTitle: UILabel!
    @IBOutlet weak var labelURL: UILabel!
    @IBOutlet weak var labelDescription: UILabel!
    @IBOutlet weak var buttonUrlTap: UIButton!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
