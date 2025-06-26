//
//  TabBarViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 09/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

//protocol ViewControllerDelegate {
//    /**
//     This func should be fired when navigation bar should be refreshed according to visible UIViewController
//     */
//    func refreshNavigationBar(forViewController vc: UIViewController)
//}

class TabBarViewController: UITabBarController,UITabBarControllerDelegate,MenuControllerDelegate {
    func showHideMenuController(_ isShown: Bool) {
    
    }
    

    var selectedTabBarItem : Int?
    let firstVC = GoalViewController()
    let secondVC = MeetingViewController()
    let thirdVC = ResourceViewController()
    let fourthVC = ElearningViewController()
    var goTabbarIndex : Int?
    
    var valueMode : String?
    
    override func viewDidLoad() {
        super.viewDidLoad()

        print("mentee tab bar ")
        self.delegate = self
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
            UITabBar.appearance().backgroundColor = UIColor(hexString: "#0E0F27")

        } else if mode == "light" {
            UITabBar.appearance().backgroundColor = .white
        }
         print("goTabbarIndex\(goTabbarIndex)")
//        if(goTabbarIndex == 1){
//            selectedIndex = 1
//             print("selectedindex\(tabBarController?.selectedIndex)")
//        }
//        else{
//            print("selectedindex\(tabBarController?.selectedIndex)")
//            selectedIndex = 0
//
//        }
        //setTabBar()
        // Do any additional setup after loading the view.
    }
    
    
    override func viewWillAppear(_ animated: Bool) {
        //var selectedIndex = tabBarController!.selectedIndex
        //print("selectedindex\(tabBarController?.selectedIndex)")
       // print("Selected  \(String(describing: tabBarItem!.selectedImage))")
        //self.selectItemWithIndex(value: <#T##Int#>)
        
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    // UITabBarDelegate
    override func tabBar(_ tabBar: UITabBar, didSelect item: UITabBarItem) {
        print("Selected item")
    }
    
    // MARK:: UITabBarControllerDelegate
    func tabBarController(_ tabBarController: UITabBarController, didSelect viewController: UIViewController) {
        print("Selected \(tabBarController.selectedIndex)")
        
        if tabBarController.selectedIndex == 0 {
            //UserDefaults.standard.set(true, forKey: "isProfile")
//            UserDefaults.standard.set(false, forKey: "isChat")
//            UserDefaults.standard.set(false, forKey: "isMentor")
//            UserDefaults.standard.set(false, forKey: "isMeetings")
//            UserDefaults.standard.set(false, forKey: "isUpload")
           // UserDefaults.standard.set(false, forKey: "isCategory")
        } else if tabBarController.selectedIndex == 1 {
//            UserDefaults.standard.set(false, forKey: "isProfile")
//            UserDefaults.standard.set(false, forKey: "isChat")
//            UserDefaults.standard.set(true, forKey: "isMentor")
//            UserDefaults.standard.set(false, forKey: "isMeetings")
//            UserDefaults.standard.set(false, forKey: "isUpload")
           // UserDefaults.standard.set(true, forKey: "isCategory")
        } else if tabBarController.selectedIndex == 2{
//            UserDefaults.standard.set(false, forKey: "isProfile")
//            UserDefaults.standard.set(false, forKey: "isChat")
//            UserDefaults.standard.set(false, forKey: "isMentor")
//            UserDefaults.standard.set(true, forKey: "isMeetings")
//            UserDefaults.standard.set(false, forKey: "isUpload")
           // UserDefaults.standard.set(false, forKey: "isCategory")
        } else if tabBarController.selectedIndex == 3{
//            UserDefaults.standard.set(false, forKey: "isProfile")
//            UserDefaults.standard.set(true, forKey: "isChat")
//            UserDefaults.standard.set(false, forKey: "isMentor")
//            UserDefaults.standard.set(false, forKey: "isMeetings")
//            UserDefaults.standard.set(false, forKey: "isUpload")
            // UserDefaults.standard.set(false, forKey: "isCategory")
        } else{
//            UserDefaults.standard.set(false, forKey: "isProfile")
//            UserDefaults.standard.set(false, forKey: "isChat")
//            UserDefaults.standard.set(false, forKey: "isMentor")
//            UserDefaults.standard.set(false, forKey: "isMeetings")
//            UserDefaults.standard.set(true, forKey: "isUpload")
           // UserDefaults.standard.set(false, forKey: "isCategory")
        }
    }
    
    func selectItemWithIndex(value: Int) {
        self.selectedIndex = value;
        //self.tabBarController(self, didSelect: tabBarVC!)
    }
}

//    override func tabBar(_ tabBar: UITabBar, didSelect item: (UITabBarItem?)) {
//        if item == (self.tabBar.items as! [UITabBarItem])[0]{
//            //Do something if index is 0
//        }
//        else if item == (self.tabBar.items as! [UITabBarItem])[1]{
//            //Do something if index is 1
//        }
//    }

//    func setTabBar(){
//    switch selectedTabBarItem {
//    case 1:
//    let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//    let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "GoalViewController") as! GoalViewController
//   // goalVc.selectedTabBarItem = 1
//    goalVc.isFromMenu = true
//    self.navigationController?.pushViewController(goalVc, animated: true)
//
//
//    break
//    case 2:
//
//    let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//    let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "TabBarViewController") as! TabBarViewController
//   // goalVc.selectedTabBarItem = 1
//    // goalVc.isFromMenu = true
//    self.navigat ionController?.pushViewController(goalVc, animated: true)
//
//    break
//    case 3:
//
//    let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//    let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "TabBarViewController") as! TabBarViewController
//   // goalVc.selectedTabBarItem = 1
//    // goalVc.isFromMenu = true
//    self.navigationController?.pushViewController(goalVc, animated: true)
//    break
//    case 4:
//    let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//    let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "TabBarViewController") as! TabBarViewController
//    //goalVc.selectedTabBarItem = 1
//    // goalVc.isFromMenu = true
//    self.navigationController?.pushViewController(goalVc, animated: true)
//
//    break
//
//    default:
//        let storyBoardMain : UIStoryboard = UIStoryboard(name: "Main", bundle:nil)
//        let goalVc = storyBoardMain.instantiateViewController(withIdentifier: "TabBarViewController") as! TabBarViewController
//        //goalVc.selectedTabBarItem = 1
//        // goalVc.isFromMenu = true
//        self.navigationController?.pushViewController(goalVc, animated: true)
//    break
//    }
//    }
//    func setupTabbar(){
//        firstVC.delegate = self
//        //oneVC.tabBarItem = UITabBarItem(title: "One", image: #imageLiteral(resourceName: "ic_04"), tag: 0)
//
//        secondVC.delegate = self
//        //twoVC.tabBarItem = UITabBarItem(title: "Two", image: #imageLiteral(resourceName: "ic_01"), tag: 1)
//
//        thirdVC.delegate = self
//       // threeVC.tabBarItem = UITabBarItem(title: "Three", image: #imageLiteral(resourceName: "ic_02"), tag: 2)
//
//        fourthVC.delegate = self
//      //  fourVC.tabBarItem = UITabBarItem(title: "Four", image: #imageLiteral(resourceName: "ic_01"), tag: 3)
//
//        fourthVC.navigationItem.leftBarButtonItem = UIBarButtonItem(barButtonSystemItem: .add, target: self, action: #selector(addTapped))
//
//        viewControllers = [firstVC,secondVC,thirdVC,fourthVC]
//    }


//
//extension TabBarViewController: ViewControllerDelegate{
//    func refreshNavigationBar(forViewController vc: UIViewController) {
//        
//        switch vc {
//        case is OneViewController:
//            
//            
//            
//            break
//        case is TwoViewController:
//            
//            
//            break
//        case is ThreeViewController:
//            
//        CCX C
//            break
//        case is FourViewController:
//            
//            navigationItem.rightBarButtonItem = UIBarButtonItem(barButtonSystemItem: .add, target: self, action: #selector(addTapped))
//            
//            break
//            
//        default:
//            break
//        }
//        
//        
//        
//    }
//    
//    @objc fileprivate func addTapped() {
//        //Goto another view controller
//        self.navigationController?.pushViewController(ViewController(), animated: true)
//        
//        
//        
//    }
//}
