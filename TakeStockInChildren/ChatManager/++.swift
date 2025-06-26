//
//  Utilchat.swift
//  TakeStockInChildren
//
//  Created by Samir on 6/3/21.
//  Copyright © 2021 Aquarious Technology. All rights reserved.
//

import Foundation

// Helper to determine if we're running on simulator or device

struct TokenUtils {

    static func retrieveToken(url: String, completion: @escaping (String?, String?, Error?) -> Void) {
        if let requestURL = URL(string: url) {
            let session = URLSession(configuration: URLSessionConfiguration.default)
            let task = session.dataTask(with: requestURL, completionHandler: { (data, _, error) in
                if let data = data {
                    do {
                        print("dta==========---------",data)
                        let jsonResponse = try? JSONSerialization.jsonObject(with: data, options: []) as! NSDictionary
                       // let json = try JSONSerialization.jsonObject(with: data, options: [])
                        //print("json==========---------",jsonResponse)
                    
                        let dicData = jsonResponse?["data"] as! NSDictionary
                        let token = dicData["access_token"] as? String ?? ""
                        print("token============>>>>>>",token)
                        let identity = "samir"
                        completion(token, identity, error)
                        /*
                        if let tokenData = data as? AnyObject {
                            print("token===data========",tokenData)
                            let token = tokenData["access_token"] as? String ?? ""
                            let identity = "samir"
                            completion(token, identity, error)
                        }
                        */
                        /*else {
                            completion(nil, nil, nil)
                        }
                        */
                    } catch let error as NSError {
                        completion(nil, nil, error)
                    }
                } else {
                    completion(nil, nil, error)
                }
            })
            task.resume()
        }
    }
}
