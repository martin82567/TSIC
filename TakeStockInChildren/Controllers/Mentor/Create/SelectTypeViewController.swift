//
//  SelectTypeViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 19/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import CarbonKit

class SelectTypeViewController: BaseViewController, CarbonTabSwipeNavigationDelegate, UIPickerViewDelegate, UIPickerViewDataSource, UITextFieldDelegate, MentorMenuControllerDelegate {
    
    let MentorMeetingstoryBoard: UIStoryboard = UIStoryboard(name: "Mentor", bundle: nil)
    @IBOutlet weak var viewHeader: UIView!
    @IBOutlet weak var lableTitle: UILabel!
    @IBOutlet weak var textFieldSetType: UITextField!
    
    @IBOutlet weak var customHeaderView: UIView!
    @IBOutlet weak var buttonMenu: UIBarButtonItem!
    @IBOutlet weak var btnSideMenuOutlet: UIBarButtonItem!
    var picker = UIPickerView()
    var arrSetType = ["Goal", "Task"]//, "Challenge"
    var isTappedMenu : Bool = false
    
    override func viewDidLoad() {
        super.viewDidLoad()
        navigationController?.navigationBar.barTintColor = UIColor(red: 160.0/255.0, green: 166.0/255.0, blue: 40.0/255.0, alpha: 1.0)
        self.navigationController?.navigationBar.titleTextAttributes = [NSAttributedString.Key.foregroundColor: UIColor.white]
        
        let items = ["GOAL", "TASK"]//, "CHALLENGE"
        
        let carbonTabSwipeNavigation = CarbonTabSwipeNavigation(items: items, delegate: self)
        
        var frameRect: CGRect = (carbonTabSwipeNavigation.carbonSegmentedControl?.frame)!
        frameRect.size.width = UIScreen.main.bounds.size.width
        carbonTabSwipeNavigation.carbonSegmentedControl?.frame = frameRect
        
        //carbonTabSwipeNavigation.insertIntoRootViewController:self andTargetView:customHeaderView
        //carbonTabSwipeNavigation.insert(intoRootViewController: self, andTargetView: customHeaderView)
        carbonTabSwipeNavigation.insert(intoRootViewController: self)
        let setSelectedTextColour = UIColor.white
        carbonTabSwipeNavigation.setIndicatorColor(setSelectedTextColour)
        carbonTabSwipeNavigation.setSelectedColor(setSelectedTextColour, font: UIFont.systemFont(ofSize: 18))
        carbonTabSwipeNavigation.setNormalColor(setSelectedTextColour, font: UIFont.systemFont(ofSize: 18))
        carbonTabSwipeNavigation.carbonSegmentedControl?.backgroundColor = UIColor(red: 167.0/255.0, green: 174.0/255.0, blue: 59.0/255.0, alpha: 1.0)
        carbonTabSwipeNavigation.toolbar.barTintColor = UIColor.white
        carbonTabSwipeNavigation.toolbar.isTranslucent = true
        carbonTabSwipeNavigation.setTabExtraWidth(40)
        carbonTabSwipeNavigation.setTabBarHeight(60)
        
         self.navigationController?.isNavigationBarHidden = false
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.navigationController?.setNavigationBarHidden(false, animated: animated)
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        self.navigationController?.setNavigationBarHidden(true, animated: animated)
    }
    
    //MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- SideMenu
    func showHideMentorMenuController(_ isShown: Bool) {
        print(isShown)
        if isShown == true {
            isTappedMenu = true
        } else {
            isTappedMenu = false
        }
        btnSideMenuOutlet.isEnabled = !isShown
    }
    
    override func viewWillLayoutSubviews() {
        super.viewWillLayoutSubviews()
        
          //isTappedMenu = false
        
        if isTappedMenu == false {
            self.navigationController?.isNavigationBarHidden = false
        } else {
            self.navigationController?.isNavigationBarHidden = true
        }
       // self.navigationController?.isNavigationBarHidden = true
    }
    
    @IBAction func messageShowMentorSideMenu(_ sender: Any) {
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
    }
    
    //MARK:CarbonKitDelegate
    func carbonTabSwipeNavigation(_ carbonTabSwipeNavigation: CarbonTabSwipeNavigation, viewControllerAt index: UInt) -> UIViewController {
        switch index {
        case 0:
            let aircraftVC = self.storyboard!.instantiateViewController(withIdentifier: "CreateMentorGoalViewController") as! CreateMentorGoalViewController
            return aircraftVC
        default:
            // do something similar, instantiate view controller, set navigation property
            return self.storyboard!.instantiateViewController(withIdentifier: "CreateMentorTaskViewController") as! CreateMentorTaskViewController
        /*case 1:
            // do something similar, instantiate view controller, set navigation property
            return self.storyboard!.instantiateViewController(withIdentifier: "CreateMentorTaskViewController") as! CreateMentorTaskViewController
        default:
            // do something similar, instantiate view controller, set navigation property
            return self.storyboard!.instantiateViewController(withIdentifier: "CreateMentorChallengeViewController") as! CreateMentorChallengeViewController*/
        }
    }
    
    //MARK:-DonePicker
    @objc func donePicker() {
        self.view.endEditing(true)
    }
    
    //MARK:-CancelPicker
    @objc func cancelPicker() {
        self.view.endEditing(true)
    }
//
//    func setNavigation(){
//        if(isTappedMenu == true){
//            self.navigationController?.isNavigationBarHidden = true
//        }else{
//            self.navigationController?.isNavigationBarHidden = false
//        }
//    }
    
    @IBAction func buttonMenuAction(_ sender: Any) {
        self.navigationController?.isNavigationBarHidden = true
        print("Menu Tapped.")
        //isTappedMenu = true
         //setNavigation()
        let objSideMenu = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorSideMenuViewController") as! MentorSideMenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMentorMenuInController(self)
    }
    
    //MARK:-PickerDelegateAndDatasourceMethod
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        return arrSetType.count
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
        return arrSetType[row]
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        self.textFieldSetType.text = self.arrSetType[row]
    }

}

