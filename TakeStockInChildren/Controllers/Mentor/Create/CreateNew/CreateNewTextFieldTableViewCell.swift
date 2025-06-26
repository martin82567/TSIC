//
//  CreateNewTextFieldTableViewCell.swift
//  TakeStockInChildren
//
//  Created by administrator on 20/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class CreateNewTextFieldTableViewCell: UITableViewCell {

    @IBOutlet weak var createTitleHead: UILabel!
    @IBOutlet weak var textFieldCreate: UITextField!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
//    func populateData(model: TextFieldAttribute) {
//       // textField.placeholder = model.placeHolder
//        textFieldCreate.text = model.textValue
//        //textField.isSecureTextEntry = model.isSecureTextEntry
//        textFieldCreate.returnKeyType = model.returnType
//        textFieldCreate.autocapitalizationType = model.capitalization
//        textFieldCreate.keyboardType = model.keyboardType
//    }

    public func configure(text: String?, placeholder: String) {
              textFieldCreate.text = text
             // textField.placeholder = placeholder
    
               textFieldCreate.accessibilityValue = text
        print("celltext\(textFieldCreate.text)")
             //   textField.accessibilityLabel = placeholder
           }

}
