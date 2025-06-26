//
//  MentorTabBarViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 14/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class MentorTabBarViewController: UITabBarController {

    override func viewDidLoad() {
        super.viewDidLoad()
        print("mentor tab bar")
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            UITabBar.appearance().backgroundColor = UIColor(hexString: "#0E0F27")

        } else if mode == "light" {
            UITabBar.appearance().backgroundColor = .white
        }

//        if let tabItems = tabBarController?.tabBar.items {
//            // In this case we want to modify the badge number of the third tab:
//            let tabItem = tabItems[3]
//            tabItem.badgeValue = "4"
//        }
        // Do any additional setup after loading the view.
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)
        
    }

}
