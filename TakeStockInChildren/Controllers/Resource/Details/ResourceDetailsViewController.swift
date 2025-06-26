//
//  ResourceDetailsViewController.swift
//  TakeStockInChildren
//
//  Created by administrator on 06/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit

class ResourceDetailsViewController: BaseViewController {
    
    @IBOutlet weak var tableViewResourceDetails: UITableView!
    
    var arrResourceDetail: [String] = []
    var dicResourceDetail = NSDictionary()
    var idDetails : Int = 0
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.tabBarController?.tabBar.isHidden = false
        tableViewResourceDetails.setContentOffset(.zero, animated:true)
        getResourceDetails()
    }
    
    // MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:: Function Api Calling
    func getResourceDetails () {
        guard let token = UserDefaults.standard.value(forKey: "token") as? String else{return}
        print("token==\(token)")
        if self.connectedToNetwork() {
            self.startActivityIndicator()
            
            ApiManager.sharedInstance.getResourceDetails(ResourceID: String(idDetails), onSuccess: { json in
                DispatchQueue.main.async {
                    print("ResourceDetails json :: \(String(describing: json))")
                    DispatchQueue.main.async(execute: {() -> Void in
                        self.dicResourceDetail = json["resource_details"] as! NSDictionary
                        print("ResourceDetails Details :: \(self.dicResourceDetail)")
                        self.tableViewResourceDetails.reloadData()
                        self.stopActivityIndicator()
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
            self.showAlert(_sourceController: self,_msg: "Unable to connect")
        }
    }
    
    //MARK: Button Action
    @IBAction func buttonBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
}

// MARK:: UITableViewDatasource
extension ResourceDetailsViewController: UITableViewDataSource {
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return 1
        
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ResourceDetailsTableViewCell") as! ResourceDetailsTableViewCell
        
        if dicResourceDetail.count > 0 {
            cell.labelResourceName.text = dicResourceDetail["name"] as? String
            cell.labelType.text = dicResourceDetail["category"] as? String
            cell.labelLocation.text = dicResourceDetail["address"] as? String
            cell.labelDescription.text = dicResourceDetail["description"] as? String
            if (dicResourceDetail["work_phone"] as! String) == "" {
                cell.labelWorkPhone.text = "   "
            } else {
                cell.labelWorkPhone.text = (dicResourceDetail["work_phone"] as! String)
            }
        }
        return cell
    }
    
    
}



