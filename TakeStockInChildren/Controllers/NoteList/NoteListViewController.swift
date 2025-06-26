//
//  NoteListViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 25/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class NoteListViewController: BaseViewController {
    
    @IBOutlet weak var labelNoNoteToShow: UILabel!
    @IBOutlet weak var tableViewNotes: UITableView!
    var arrNoteList = [NSDictionary]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
    }
    
    //MARK:-Staus Bar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
}

// MARK:: UITableViewDatasource
extension NoteListViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrNoteList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "NoteListTableViewCell") as! NoteListTableViewCell
        if (arrNoteList.count > 0) {
            let dicNote: NSDictionary = arrNoteList[indexPath.row]
            cell.labelNotesCreatedDate.text = dicNote["created_date"] as? String ?? ""
            cell.labelNotesDetails.text = dicNote["note"] as? String ?? ""
            cell.labelNotesTitle.text = dicNote["title"] as? String ?? ""
            //print("cell.labelNotesTitle.text\(cell.labelNotesTitle.text)")
        }  else {
            
        }
        return cell
    }
    
}


