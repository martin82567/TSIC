//
//  UploadTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 02/09/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class UploadTableViewCell: UITableViewCell {
    
    @IBOutlet weak var labeltitle: UILabel!
    
    @IBOutlet weak var labeluploadate: UILabel!
    @IBOutlet weak var viewMenteeCell: UIView!
    @IBOutlet weak var labelUploadTitle: UILabel!
    @IBOutlet weak var imageViewMyMentee: UIImageView!
    @IBOutlet weak var labelUploadDate: UILabel!
    
    @IBOutlet weak var createdatelabel: UILabel!
    @IBOutlet weak var buttonZoomImage: UIButton!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
