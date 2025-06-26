//
//  SettingsViewController.swift
//  TakeStockInChildren
//
//  Created by Nikhil Batra on 12/07/23.
//  Copyright © 2023 Aquarious Technology. All rights reserved.
//

import UIKit

class SettingsViewController: UIViewController {
    
    @IBOutlet weak var faceIdToggle: UISwitch!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.faceIdToggle.isOn = UserDefaults.standard.bool(forKey: "isFaceIdEnabled")
    }
    
    @IBAction func onSwitchChange(_ sender: UISwitch) {
        UserDefaults.standard.set(sender.isOn, forKey: "isFaceIdEnabled")
    }
    
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
}
