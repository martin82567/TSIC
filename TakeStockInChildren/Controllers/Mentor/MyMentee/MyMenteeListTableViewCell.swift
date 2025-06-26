//
//  MyMenteeListTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 12/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MyMenteeListTableViewCell: UITableViewCell {

    @IBOutlet weak var badgeLabel: UILabel!
    @IBOutlet weak var badgeImage: UIImageView!
    @IBOutlet weak var labelMyMentee: UILabel!
    @IBOutlet weak var labelLastSessionsLogged: UILabel!
    @IBOutlet weak var viewMenteeImageBorder: UIView!
    @IBOutlet weak var labelLowerLine: UILabel!
    @IBOutlet weak var labelUpperLine: UILabel!
    @IBOutlet weak var viewMenteeCell: UIView!
    @IBOutlet weak var labelMyMenteeName: UILabel!
    @IBOutlet weak var lblMenteeSchool: UILabel!
    @IBOutlet weak var lblMenteeUpcomingDate: UILabel!
    @IBOutlet weak var TallulahFallsSchoolServiceConstant: UILabel!
    @IBOutlet weak var buttonShowMenteeDetailsTap: UIButton!
    @IBOutlet weak var imageViewMyMentee: UIImageView!
    @IBOutlet weak var imageViewMyMenteeName: UILabel!
    
    @IBOutlet weak var numberofSessionlabel: UILabel!
    @IBOutlet weak var lastSessionLabel: UILabel!
    @IBOutlet weak var lblNewChatCount: UILabel!
    @IBOutlet weak var btnMenteeCall: UIButton!
     
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
