//
//  FirstViewController.swift
//  TakeStockInChildren
//
//  Created by Divij Jindal on 29/07/22.
//  Copyright © 2022 Aquarious Technology. All rights reserved.
//

import UIKit

class FirstViewController: UIViewController {

    @IBOutlet weak var loginAsMentee: UIButton!
    @IBOutlet weak var loginAsMentor: UIButton!
    @IBOutlet weak var backButton: UIButton!
    let mainStoryboard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
    override func viewDidLoad() {
        super.viewDidLoad()
        backButton.isHidden = true
    }
    
    @IBAction func loginAsMentor(_ sender: UIButton) {
        let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "BaseLoginViewController") as!BaseLoginViewController
        self.navigationController?.pushViewController(newViewController, animated: true)

    }
    @IBAction func loginAsMenteePressed(_ sender: UIButton) {
        let newViewController = mainStoryboard.instantiateViewController(withIdentifier: "BaseLoginViewController") as!BaseLoginViewController
        self.navigationController?.pushViewController(newViewController, animated: true)
    }
    
}
