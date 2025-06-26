//
//  UploadViewController.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 31/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class UploadViewController: BaseViewController,MenuControllerDelegate {
    
    @IBOutlet weak var headerView: UIView!
    
    
    
    @IBOutlet weak var bgimagr: UIImageView!
    @IBOutlet weak var buttonMenu: UIButton!
    @IBOutlet weak var tableViewUpload: UITableView!
    @IBOutlet weak var labelNoDataFound: UILabel!
    @IBOutlet weak var ViewZoomed: UIView!
    @IBOutlet weak var imageViewZoomed: UIImageView!
    var dicUploadReportList = [NSDictionary]()
    var arrUploadReportList = NSArray()
    var videoURLStr : String = ""
    var valueMode : String?
   // var dicData = [NSDictionary]()

    override func viewDidLoad() {
        super.viewDidLoad()
      //  self.valueMode = UserDefaults.standard.value(forKey: "mode") as? String
       // darkmodeChnaged()
        //NotificationCenter.default.addObserver(self, selector: #selector(Colorchnaged), name: .menteebackgroundColor, object: nil)
        ViewZoomed.isHidden = true
        // Do any additional setup after loading the view.
    }
    
    @objc func Colorchnaged() {
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
           bgimagr.image = UIImage(named: "BG4")
           tableViewUpload.backgroundColor = UIColor(hexString: "#0E0F27")
           headerView.backgroundColor = UIColor(hexString: "#0E0F27")
        }
        else if mode == "light" {
            bgimagr.image = UIImage(named: "BackgroundImage")
            tableViewUpload.backgroundColor = .white
            headerView.backgroundColor = UIColor(hexString: "#A7AE3B")
        }
        
        tableViewUpload.reloadData()
        
    }
    
    func darkmodeChnaged(){
        if self.valueMode == "dark" {
            bgimagr.image = UIImage(named: "BG4")
            tableViewUpload.backgroundColor = UIColor(hex: "#0E0F27")
            headerView.backgroundColor = UIColor(hexString: "#0E0F27")
        }
        else if self.valueMode == "light" {
            bgimagr.image = UIImage(named: "BackgroundImage")
                       tableViewUpload.backgroundColor = .white
                       headerView.backgroundColor = UIColor(hexString: "#A7AE3B")
        }
        
        
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        self.buttonMenu.addTarget(self, action: #selector(MeetingViewController.messageShowSideMenu), for: UIControl.Event.touchUpInside)
        getUploadReportList()
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

    //MARK:: Function Api Calling
    func getUploadReportList () {
        //    guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        //    print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getUploadReportList(userDetails: [:], onSuccess: { json in
                DispatchQueue.main.async {
                    print("ReportList json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        //self.dicUploadReportList = [json]
                        self.dicUploadReportList = [json]
                        print("self.dicUploadReportList\(self.dicUploadReportList)")
                        let status = json["status"] as! Bool
                        print("status\(status)")
                        
                        //self.arrUploadReportList = json
                       // self.dicUploadReportList = json["created_date"] as! NSDictionary
                      // print("uploadList\(self.dicUploadReportList)")
                        self.tableDatReload(dicData: json)
                        self.stopActivityIndicator()
                    })
                }
            }, onFailure: { error in
                
                DispatchQueue.main.sync(execute: {() -> Void in
                    self.stopActivityIndicator()
                    
                    let alert = UIAlertController(title: "Error", message: error["message"] as? String, preferredStyle: .alert)
                    alert.addAction(UIAlertAction(title: "Ok" , style: .default, handler: nil))
                    self.present(alert, animated: true, completion: nil)
                })
            })
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
    
    func tableDatReload(dicData:NSDictionary) {
        arrUploadReportList = (dicData["data"] as? NSArray)!
        print("arrUploadReportList\(arrUploadReportList)")
        self.tableViewUpload.reloadData()
    }
    
    @IBAction func buttonCloseAction(_ sender: Any) {
        ViewZoomed.isHidden = true
    }
    
    @IBAction func buttonZoomedImageAction(_ sender: UIButton) {
        ViewZoomed.isHidden = false
        
        let dic = arrUploadReportList[sender.tag] as! NSDictionary
        let imageUpload = dic["image"]
        
        videoURLStr = TakeStockInChildrenConstant.reportBaseURL.appending(imageUpload as! String)
        imageViewZoomed.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: ""))
    }
    
    //MARK:-ButtonAction
    @IBAction func buttonUploadAction(_ sender: Any) {
        let reportVc = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "AddReportViewController")
        self.navigationController?.pushViewController(reportVc, animated: true)
    }
}

// MARK:: UITableViewDatasource
extension UploadViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return arrUploadReportList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let uploadReportListCell = tableView.dequeueReusableCell(withIdentifier: "UploadTableViewCell") as! UploadTableViewCell
        let mode = UserDefaults.standard.value(forKey: "mode") as? String
        if mode == "dark" {
           // uploadReportListCell.contentView.backgroundColor = .black
            uploadReportListCell.viewMenteeCell.backgroundColor = UIColor(hexString: "#232137")
            uploadReportListCell.labeluploadate.textColor = .white
            uploadReportListCell.labelUploadTitle.textColor = .white
            uploadReportListCell.labeltitle.textColor = .white
            uploadReportListCell.labelUploadDate.textColor = .white
            uploadReportListCell.createdatelabel.textColor = .white
        }
        else if mode == "light" {
           // uploadReportListCell.contentView.backgroundColor = .white
            uploadReportListCell.viewMenteeCell.backgroundColor = .white
            uploadReportListCell.labeluploadate.textColor = .black
            uploadReportListCell.labelUploadTitle.textColor = .black
            uploadReportListCell.labeltitle.textColor = .black
            uploadReportListCell.labelUploadDate.textColor = .black
            uploadReportListCell.createdatelabel.textColor = .black
        }
        if (arrUploadReportList.count > 0) {
            print(arrUploadReportList[indexPath.row])
            let dic = arrUploadReportList[indexPath.row] as! NSDictionary
            let name = dic["name"]
            print(name!)
            let date = dic["created_date"]
            print(name!)
            let imageUpload = dic["image"]
            uploadReportListCell.labelUploadTitle.text = name as? String
            uploadReportListCell.labelUploadDate.text = date as? String
    
            videoURLStr = TakeStockInChildrenConstant.reportBaseURL.appending(imageUpload as! String)
            print("IMAGEURL\(videoURLStr)")
            uploadReportListCell.imageViewMyMentee.sd_setImage(with: URL(string: videoURLStr), placeholderImage: UIImage(named: ""))
            //var responseDateTiming = arrMyJournalListing[indexPath.row]["updated_at"] as? String
            if let dateTiming = date {
                let dateComponents = (dateTiming as AnyObject).components(separatedBy: " ")
                let splitDate = dateComponents[0]
                let splitTime = dateComponents[1]
                uploadReportListCell.labelUploadDate.text = splitDate as? String
            }
            
            uploadReportListCell.buttonZoomImage.tag = indexPath.row
            print(uploadReportListCell.buttonZoomImage.tag)
            uploadReportListCell.buttonZoomImage.addTarget(self, action: #selector(buttonZoomedImageAction(_:)), for: .touchUpInside)
        }
        return uploadReportListCell
    }
}

// MARK:: UITableViewDatasource
extension UploadViewController: UITableViewDelegate {
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        print("indexpath.row\(indexPath.row)")
    }
}
