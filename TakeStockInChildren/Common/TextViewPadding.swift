//
//  TextViewPadding.swift
//  TakeStockInChildren
//
//  Created by administrator on 21/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import Foundation
import UIKit

class TextViewPadding : UITextView {
    required init?(coder aDecoder: NSCoder) {
        super.init(coder: aDecoder)
        textContainerInset = UIEdgeInsets(top: 8, left: 6, bottom: 8, right: 4)
    }
}
