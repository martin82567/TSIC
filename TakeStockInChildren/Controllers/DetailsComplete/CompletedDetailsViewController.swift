//
//  CompletedDetailsViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 29/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class CompletedDetailsViewController: BaseViewController {
    
    
    @IBOutlet weak var tableViewCompletedDetails: UITableView!
    @IBOutlet weak var imageViewIcon: UIImageView!
    
    var arrValues = [[String: String]]()
    var arrNotes = [NSDictionary]()
    var dictItem = NSDictionary()
    var typeId = Int()
    var arrImgFiles: NSArray?
    var arrOfImages: [UIImage] = []
    var strAssignId : String = ""
    var strGoalName : String = ""
    var strDescription : String = ""
    var strStartDate : String = ""
    var strEndDate : String = ""
    var isType: Int?
    var type: String = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewCompletedDetails.setContentOffset(.zero, animated:true)
        setUpPage()
    }
    
    //MARK:-StatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    func setUpPage() {
        switch isType {
        case 1:
            type = "goal"
            imageViewIcon.image = UIImage(named: "GoalIcon")
            break
            
        case 2:
            type = "task"
            imageViewIcon.image = UIImage(named: "TaskIcon")
            break
            
        case 3:
            type = "challenge"
            imageViewIcon.image = UIImage(named: "ChallengeIcon")
            break
            
        default:
            break
        }
        getAPIvalues()
        
    }
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    //MARK:- Api call
    func getAPIvalues() {
        let userDetails:NSMutableDictionary = [
            "type" : type,
            "assign_id" : strAssignId,
        ]
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            ApiManager.sharedInstance.getgGoalDetails(userDetails: userDetails, onSuccess: { json in
                DispatchQueue.main.async {
                    DispatchQueue.main.async(execute: {() -> Void in
                      
                        guard let dataTemp = json["data"] as? NSDictionary else{
                            return
                        }
                        guard let dataDict = dataTemp["datadetails"] as? NSDictionary else{
                            return
                        }
                        
                        if let id = dataDict["id"] as? Int {
                            self.typeId = id
                        }
                        self.dictItem = dataDict
                        self.arrValues.removeAll()
                        if let arrTemp = dataDict["notes"] as? NSArray {
                            self.arrNotes = arrTemp as! [NSDictionary]
                        }
                        
                        self.arrOfImages.removeAll()
                       
                        
                        //let arrTemp = dataDict["notes"] as! NSArray
//                        self.arrOfImages.removeAll()
                    //    self.arrImgFiles
                        let arrimgData = dataDict["useruploadedfile"] as? NSArray
                        
                        if let arrData = arrimgData{
                            if arrData.count != 0{
                                self.arrImgFiles = arrData
                               // print("self.arrImgFiles \(self.arrImgFiles.count)")
                            }
                        }
                        self.UI {
                            self.tableViewCompletedDetails.reloadData()
                            self.stopActivityIndicator()
                        }
                        
//                        self.arrNotes = arrTemp as! [NSDictionary]
                        
                       // DispatchQueue.main.asyncAfter(deadline: .now() + 0.1) {
                        
                       // }
                    })
                }
            }, onFailure: { error in
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Dismiss" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
        
    }
    
    //MARK:-ShowNotes
    @IBAction func buttonShowNotes(_ sender: Any) {
        if(arrNotes.count != 0){
            let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
            let LoadeMoreNotesVC = storyboardELearning.instantiateViewController(withIdentifier: "NoteListViewController") as! NoteListViewController
            LoadeMoreNotesVC.arrNoteList = arrNotes
            print("LoadeMoreNotesVC.arrNoteList \(LoadeMoreNotesVC.arrNoteList )")
            self.navigationController?.pushViewController(LoadeMoreNotesVC, animated: true)
        }
        else{
            let alert = UIAlertController(title: "TakeStockInChildren", message: "No notes to show..", preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                print("Action")
            }))
            self.present(alert, animated: true, completion: nil)
            
        }
        
    }
    
    //MARK:- ShowFiles
    @IBAction func buttonShowFiles(_ sender: Any) {
       
        if let arrImage = arrImgFiles {
            
            if(arrImage.count != 0){
                let storyboardELearning = UIStoryboard(name: "Main", bundle: nil)
                let LoadeAttachedFilesVC = storyboardELearning.instantiateViewController(withIdentifier: "AttachFileListViewController") as! AttachFileListViewController
                LoadeAttachedFilesVC.typeFiles = type
                LoadeAttachedFilesVC.strAssignIdCompleted = strAssignId
                //LoadeAttachedFilesVC.arrAttachFiles = arrOfImages
                self.navigationController?.pushViewController(LoadeAttachedFilesVC, animated: true)
                
            }else{
                let alert = UIAlertController(title: "TakeStockInChildren", message: "No attached file to show..", preferredStyle: UIAlertController.Style.alert)
                alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                    print("Action")
                }))
                self.present(alert, animated: true, completion: nil)
            }
        }else{
            let alert = UIAlertController(title: "TakeStockInChildren", message: "No attached file to show..", preferredStyle: UIAlertController.Style.alert)
            alert.addAction(UIAlertAction(title: "Ok", style: UIAlertAction.Style.default, handler: {(action:UIAlertAction!) in
                print("Action")
            }))
            self.present(alert, animated: true, completion: nil)
        }
        
     
    }
}

// MARK:: UITableViewDatasource
extension CompletedDetailsViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 3
        
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        if (indexPath.row == 0) {
            //MARK:-Descriptioncell
            let cell = tableView.dequeueReusableCell(withIdentifier: "DescriptionTableViewCell") as! DescriptionTableViewCell
            
            cell.labelDescriptionTitle.text = strGoalName
            cell.labelDescriptionDetails.text = strDescription
            return cell
        } else if (indexPath.row == 1) {
            //MARK:-Datecell
            let cell = tableView.dequeueReusableCell(withIdentifier: "DateTableViewCell") as! DateTableViewCell
            
            cell.labelStartDateCell.text = strStartDate
            cell.labelEndDateCell.text = strEndDate
            return cell
        } else {
            let cell = tableView.dequeueReusableCell(withIdentifier: "ShowNotesAttachedFilesTableViewCell") as! ShowNotesAttachedFilesTableViewCell
            cell.buttonShowNotes.tag = indexPath.row
            cell.buttonShowNotes.addTarget(self, action: #selector(buttonShowNotes(_:)), for: .touchUpInside)
            cell.buttonshowFiles.tag = indexPath.row
            cell.buttonshowFiles.addTarget(self, action: #selector(buttonShowFiles(_:)), for: .touchUpInside)
            //imgCell.buttonDeleteFile.tag = indexPath.row
            //imgCell.buttonDeleteFile.addTarget(self, action: #selector(BtnDeleteImageDidTap(_:)), for: .touchUpInside)
            return cell
        }
    }
}
// MARK:: UITextFieldDelegate
extension CompletedDetailsViewController: UITableViewDelegate {
    private func tableView(tableView: UITableView, heightForRowAtIndexPath indexPath: NSIndexPath) -> CGFloat {
        return UITableView.automaticDimension
    }
}


