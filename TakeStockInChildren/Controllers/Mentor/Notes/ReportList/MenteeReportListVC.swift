//
//  MenteeReportListVC.swift
//  TakeStockInChildren
//
//  Created by Niraj Paul on 9/11/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Alamofire
//import RLBAlertsPickers

class MenteeReportListVC: BaseViewController {

    @IBOutlet weak var btnSelectMenteeOutlet: UIButton!
    var menteeName: [String] = []
    var menteeIndex: [String] = []
    var menteeIdForServer = ""
    var arrMenteeList = [NSDictionary]()
    
    var arrReportList : NSMutableArray = []
    @IBOutlet weak var lblNoMenteeReport: UILabel!
    
    
    @IBOutlet weak var collectionV: UICollectionView!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()

        self.collectionV.register(UINib(nibName: "ReportCollectionVCell", bundle: nil), forCellWithReuseIdentifier: "ReportCollectionVCell")
        collectionV.delegate = self
        collectionV.dataSource = self
        
        getMenteeList()
    }
    
    //MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    
    @IBAction func btnSelectMentee(_ sender: Any) {
        if self.menteeIndex.count > 0 {
            self.menteeIdForServer = self.menteeIndex[0]
            self.btnSelectMenteeOutlet.setTitle(self.menteeName[0], for: .normal)
            
            self.uiForShowMenteeList()
        }
    }
    
    var menteeID : Int = 0
    var boolToCheckClicked : Bool = false
    
    func uiForShowMenteeList() {
        let alert = UIAlertController(style: .alert, title: "Mentee", message: "Select one mentee")
        
        let alertAction = UIAlertAction(title: "Done", style: .cancel) { (alert) in
            if self.boolToCheckClicked == true {
                self.menteeIdForServer =  self.menteeIndex[self.menteeID]
                self.btnSelectMenteeOutlet.setTitle(self.menteeName[self.menteeID], for: .normal)
                self.boolToCheckClicked = false
            } else {
                self.menteeIdForServer =  self.menteeIndex[0]
                self.btnSelectMenteeOutlet.setTitle(self.menteeName[0], for: .normal)
            }
            self.getMenteeReport()
        }
    
        let pickerViewValues: [[String]] = [self.menteeName.map { String($0).description }]

        alert.addPickerView(values: pickerViewValues, initialSelection: nil) { vc, picker, index, values in
            DispatchQueue.main.async {
                UIView.animate(withDuration: 1) {
                    self.boolToCheckClicked = true
                    self.menteeID = index.row
                    self.menteeIdForServer = self.menteeIndex[index.row]
                }
            }
        }
        alert.addAction(alertAction)
        alert.show()
    }
    
    
    func getMenteeReport() {
        var parameter = [String:String]()
        
        parameter["mentee_id"] = menteeIdForServer
        
        MentorApiManager().menteeReport(parameter: parameter, completion: { (response) in
            let status = response["status"] as! Bool

            if status == true {
                guard let arrData = response["data"] as? NSArray else{
                    return
                }
                self.arrReportList.removeAllObjects()
                if arrData.count == 0 {
                    self.UI {
                        self.lblNoMenteeReport.isHidden = false
                        self.collectionV.isHidden = true
                    }
                } else {
                    self.arrReportList = arrData.mutableCopy() as! NSMutableArray
                    self.UI {
                        self.lblNoMenteeReport.isHidden = true
                        self.collectionV.isHidden = false
                        
                       self.collectionV.reloadData()
                    }
                }
            } else {
                self.UI {
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                }
            }
        })
    }
    
    func getMenteeList() {
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            let token  = UserDefaults.standard.string(forKey: "mentorToken")!
            
            let headers = [
                "Authorizations": token,
                "Content-Type": "application/x-www-form-urlencoded"
            ]
            print(headers)
            
            let url = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MenteeList)
            print("MENTEELISTURL\(url)")
            Alamofire.request(url, method:.post, parameters:nil, headers: headers).responseJSON { response in
                switch response.result {
                case .success:
                    print(response)
                    self.stopActivityIndicator()
                    let dictVal = response.result.value
                    let dictMain:NSDictionary = dictVal as! NSDictionary
                    self.menteeName = []
                    self.menteeIndex = []
                    
                    if dictMain["status"] as! Bool == true {
                        self.arrMenteeList = dictMain["data"] as! [NSDictionary]
                        for item in self.arrMenteeList {
                            self.menteeIndex.append("\(item["id"] ?? "nil")")
                            var TotalName = ""
                            
                            let firstName = item["firstname"] as? String
                            let lastName = item["lastname"] as? String
                            let middlename = item["middlename"] as? String
                            
                            if let fName = firstName {
                                TotalName = fName
                            }
                            
                            if let middleN = middlename {
                                if middleN != "" {
                                    TotalName += " \(middleN)"
                                }
                            }
                            
                            if let LName = lastName {
                                if LName != "" {
                                    TotalName += " \(LName)"
                                }
                            }
                            self.menteeName.append(TotalName)
                        }
                    }
                    if self.menteeIndex.count > 0 {
                        self.menteeIdForServer =  self.menteeIndex[0]
                    }
                    if self.menteeName.count > 0 {
                        self.btnSelectMenteeOutlet.setTitle(self.menteeName[0], for: .normal)
                    }
                    self.getMenteeReport()
                case .failure(let error):
                    print(error)
                    self.stopActivityIndicator()
                }
            }
        } else {
            showAlert(_sourceController: self, _msg: "Unable to connect")
        }
    }
}

extension MenteeReportListVC : UICollectionViewDelegate , UICollectionViewDataSource ,UICollectionViewDelegateFlowLayout {
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        return arrReportList.count
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "ReportCollectionVCell", for: indexPath) as! ReportCollectionVCell
        if arrReportList.count > 0 {
            let dataDic = arrReportList[indexPath.row] as! NSDictionary
            cell.loadData(dataDic: dataDic)
        }
        cell.layer.cornerRadius = 10
        return cell
    }
    
    func collectionView(_ collectionView: UICollectionView, layout collectionViewLayout: UICollectionViewLayout, sizeForItemAt indexPath: IndexPath) -> CGSize {
        return CGSize(width: (self.collectionV.bounds.width-15)/2, height: 150)
    }
    
    func collectionView(_ collectionView: UICollectionView, didSelectItemAt indexPath: IndexPath) {
        let imgname : NSDictionary = arrReportList[indexPath.row] as! NSDictionary
        
        let goToShowViewController = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "showBigImageVC") as! showBigImageVC
        goToShowViewController.Bigphoto = imgname["image"] as? NSString
        self.navigationController?.pushViewController(goToShowViewController, animated: true)
    }
}
