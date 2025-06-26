//
//  SessionisLoggedViewController.swift
//  TakeStockInChildren
//
//  Created by AquariousMnabook on 15/07/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import UIKit

class SessionisLoggedViewController: UIViewController {

    var arrMyJournalListing = [[String:Any]]()
    var take : Int = 0
    var pageList : Int = 10
    var isLoadMoreList : Bool = false
    var totalValue = Int()
    var tempMessagedata = [[String:Any]]()
    @IBOutlet weak var lblForNoMettigFound: UILabel!
    @IBOutlet weak var tableviewSession: UITableView!
    override func viewDidLoad() {
        super.viewDidLoad()  

        serviceCallTogetUpcommingMettings()
    }

    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        self.isLoadMoreList = false
    }
    
    
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
//        //8***********
//         func serviceCallToGetSessionListing() {
//             self.startActivityIndicator()
//             MentorApiManager().sessionListing(parameter: [:], completion: { (response) in
//                 let status = response["status"] as! Bool
//                 if status == true {
//                     self.stopActivityIndicator()
//                     let arrData = response["data"] as! NSArray
//                     print("arr",arrData)
//                     self.arrMyJournalListing.removeAllObjects()
//                     if arrData.count == 0 {
//                         self.UI {
//                             //self.lblForNoMettigFound.text = "No Session Found"
//                             self.tableVSessionList.isHidden = true
//                         }
//                     } else {
//                         self.arrMyJournalListing = arrData.mutableCopy() as! NSMutableArray
//                         self.UI {
//                             self.tableVSessionList.isHidden = false
//
//                             self.tableVSessionList.reloadData()
//                         }
//                     }
//                 } else {
//                     self.UI{
//                         /*
//                         Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//                         })
//                         */
//                         //self.logOutMentor()
//                     }
//                 }
//             })
//         }
    func serviceCallTogetUpcommingMettings() {
        var parameter = [String:String]()
        parameter["page"] = "\(self.take)"
        parameter["take"] = "\(self.pageList)"
        
        MentorApiManager().sessionListing(parameter: [:], completion: { (response) in
            let status = response["status"] as! Bool
            
            if status == true {
                self.totalValue = response["total_data"] as? Int ?? 0
                    self.UI {
                        self.lblForNoMettigFound.isHidden = true
                        self.tableviewSession.isHidden = false
                        
                        if self.isLoadMoreList {
                            self.tempMessagedata = response["data"] as? [[String : Any]] ?? []
                            
                            for item in self.tempMessagedata {
                                self.arrMyJournalListing.append(item)
                            }
                        }
                        else {
                            self.arrMyJournalListing = response["data"] as? [[String : Any]] ?? []
                        }
                        
                         if self.arrMyJournalListing.count > 0 {
                             
                             self.isLoadMoreList = false
                             
                            self.tableviewSession.reloadData()
                         }
                         else {
                            self.lblForNoMettigFound.text = "No Past Session"
                            self.lblForNoMettigFound.isHidden = false
                            self.tableviewSession.isHidden = true
                         }
                        
                    }
            } else {
                self.UI{
                    /*
                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    })
                    */
                    //self.logOutMentor()
                }
            }
        })
    }
    
//    func serviceCallTogetUpcommingMettings() {
//        var parameter = [String:String]()
//        parameter["page"] = "\(self.take)"
//        parameter["take"] = "\(self.pageList)"
//
//        MentorApiManager().loggedsessionListing(parameter: parameter, completion: { (response) in
//            let status = response["status"] as! Bool
//
//            if status == true {
//                self.totalValue = response["total_data"] as? Int ?? 0
//                    self.UI {
//                        self.lblForNoMettigFound.isHidden = true
//                        self.tableviewSession.isHidden = false
//
//                        if self.isLoadMoreList {
//                            self.tempMessagedata = response["data"] as? [[String : Any]] ?? []
//
//                            for item in self.tempMessagedata {
//                                self.arrMyJournalListing.append(item)
//                            }
//                        }
//                        else {
//                            self.arrMyJournalListing = response["data"] as? [[String : Any]] ?? []
//                        }
//
//                         if self.arrMyJournalListing.count > 0 {
//
//                             self.isLoadMoreList = false
//
//                            self.tableviewSession.reloadData()
//                         }
//                         else {
//                            self.lblForNoMettigFound.text = "No Past Session"
//                            self.lblForNoMettigFound.isHidden = false
//                            self.tableviewSession.isHidden = true
//                         }
//
//                    }
//            } else {
//                self.UI{
//                    /*
//                    Common().showAlertView(title: "", msg: (response["message"] as? String)!, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//                    })
//                    */
//                    //self.logOutMentor()
//                }
//            }
//        })
//    }
    

}


extension SessionisLoggedViewController: UITableViewDelegate,UITableViewDataSource {
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.arrMyJournalListing.count
    }
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier:String = "MentorTblCell2"
        var cell2:MentorTblCell2? = tableView.dequeueReusableCell(withIdentifier: cellIdentifier) as? MentorTblCell2
        if (cell2 == nil) {
            var nib:Array = Bundle.main.loadNibNamed("MentorTblCell2", owner: self, options: nil)!
            cell2 = nib[0] as? MentorTblCell2
        }
        
        if arrMyJournalListing.count > 0 {
            let dataDic = arrMyJournalListing[indexPath.row] as! NSDictionary
            
            cell2?.loadDataPast(dicData: dataDic)
        }
        cell2?.selectionStyle = .none
        return cell2!
    }
    
}

extension SessionisLoggedViewController: UIScrollViewDelegate {
    
    // MARK:- UIScrollViewDelegate
    func scrollViewDidEndDragging(_ scrollView: UIScrollView, willDecelerate decelerate: Bool) {
        if scrollView == self.tableviewSession {
            // calculates where the user is in the y-axis
            let offsetY = scrollView.contentOffset.y
            let contentHeight = scrollView.contentSize.height
            if offsetY > 0 && (offsetY > (contentHeight - scrollView.frame.size.height)) {
                if self.arrMyJournalListing.count > 0 && self.totalValue > self.arrMyJournalListing.count
                    && !self.isLoadMoreList {
                    self.pageList += 1
                    self.isLoadMoreList = true
                    self.serviceCallTogetUpcommingMettings()
                    
                }
                else
                {
                    print("No tab")
                }
            }
        }
    }
}

