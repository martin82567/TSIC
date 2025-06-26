//
//  ChatViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 31/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//+


import UIKit

class ChatViewController: BaseViewController,MenuControllerDelegate {

    @IBOutlet weak var buttonMenu: UIButton!
    
    var isChat      : Bool = true
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // Do any additional setup after loading the view.
    }
    
    @IBAction func btnChatAction(_ sender: Any) {
        let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
        self.UI {
            let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
            newViewController.strIamfrom = "staff"
            self.navigationController?.pushViewController(newViewController, animated: true)
        }
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        self.buttonMenu.addTarget(self, action: #selector(ChatViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
    }

    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:- SideMenu
    func showHideMenuController(_ isShown: Bool) {
        buttonMenu.isUserInteractionEnabled = !isShown
    }
    
    @objc func messageShowSideMenu() {
        let objSideMenu = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "MenuViewController") as! MenuViewController
        objSideMenu.delegate = self
        objSideMenu.showMenuInController(self)
    }
}
