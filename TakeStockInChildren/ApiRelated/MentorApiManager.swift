//
//  MentorApiManager.swift
//  TakeStockInChildren
//
//  Created by administrator on 28/08/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import Foundation
import NVActivityIndicatorView

class MentorApiManager: NSObject {
    
    static let mentorSharedInstance = MentorApiManager()
    
    //MARK: MentorLogin
    func mentorLogIn(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.Mentorlogin)
        let url = URL(string: urlString)!
        print("URL\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("Login jsonResponse:: \(String(describing: jsonResponse))")
                
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if(jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                                
                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                        //let dic : NSDictionary = ["message" : "Null data found"]
                        //onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    func unifiedLogIn(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UnifiedLogin)
        let url = URL(string: urlString)!
        print("URL\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if(jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                                
                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                        //let dic : NSDictionary = ["message" : "Null data found"]
                        //onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }

    
    
    
    //MARK: MentorResetPassword
    func mentorResetPass(postId: Int, resetDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorResetPassword)
        let url = URL(string: urlString)!
        print("reseturl\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: resetDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("ResetPassword jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //MARK:-MentorForgotPassword
    //MARK: ForgotPassword
    func mentorForgotPass(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorForgotPassword)
        let url = URL(string: urlString)!
        print("url\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("ForgotPassword jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }

    func getTimeZoneMentee(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
            let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.getTimeZoneMentee)
            
            let url = URL(string: urlString)!
           // print("getLatLogForGeofance url:: \(url)")
            
            guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "LatLogForGeofance Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    
                })
            }
            var request = URLRequest(url: url)
            request.httpMethod = "GET"
            request.addValue(token, forHTTPHeaderField: "Authorizations")
            request.addValue("application/json", forHTTPHeaderField: "Content-Type")
            request.timeoutInterval = 60
            request.addValue("ios", forHTTPHeaderField: "platform")
            request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            
            let session = URLSession.shared
            let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
                if let resData = data {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                   // print("GetLatLogForGeofance jsonResponse:: \(String(describing: jsonResponse))")
                    if (error != nil) {
                        onFailure(jsonResponse!)
                    } else {
                        if (jsonResponse != nil) {
                            if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                                if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                    onFailure(jsonResponse!)
                                } else {
                                    //                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                    onSuccess(jsonResponse!)
                                }
                            } else {
                                onFailure(jsonResponse!)
                            }
                        } else {
                            self.checkInternetConnectionPopUp()
                            
                        }
                    }
                }
            })
            task.resume()
        }
    
    func getTimeZoneMentor(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
            let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.getTimeZoneMentor)
            
            let url = URL(string: urlString)!
          //  print("getLatLogForGeofance url:: \(url)")
            
            guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "LatLogForGeofance Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    
                })
            }
            var request = URLRequest(url: url)
            request.httpMethod = "GET"
            request.addValue(token, forHTTPHeaderField: "Authorizations")
            request.addValue("application/json", forHTTPHeaderField: "Content-Type")
            request.timeoutInterval = 60
            request.addValue("ios", forHTTPHeaderField: "platform")
            request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            
            let session = URLSession.shared
            let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
                if let resData = data {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                   // print("GetLatLogForGeofance jsonResponse:: \(String(describing: jsonResponse))")
                    if (error != nil) {
                        onFailure(jsonResponse!)
                    } else {
                        if (jsonResponse != nil) {
                            if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                                if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                    onFailure(jsonResponse!)
                                } else {
                                    //                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                    onSuccess(jsonResponse!)
                                }
                            } else {
                                onFailure(jsonResponse!)
                            }
                        } else {
                            self.checkInternetConnectionPopUp()
                            
                        }
                    }
                }
            })
            task.resume()
        }
    
    func getLatLogForGeofance(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.getLatLogForGeofence)
        
        let url = URL(string: urlString)!
       // print("getLatLogForGeofance url:: \(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  
//            Common().showAlertView(title: "Alert!", msg: "LatLogForGeofance Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//                
//            })
        }
        
//        Common().showAlertView(title: "Call getLatLogForGeofance", msg: "", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//        })
        
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(token, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 60
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
              //  print("GetLatLogForGeofance jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                //                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                        
                    }
                }
            }
        })
        task.resume()
    }
    //TODO:- MentorProfileModule
    //MARK: getUserDetails
    func getMentorUserDetails(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorUserDetails)
        let url = URL(string: urlString)!
       // print("mentorprofileurl:: \(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "MentorUserDetails Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(token, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
              //  print("UserDetails jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                print("Suceess response",error)
                                onSuccess(userDetailsDic)
                            }
                        } else {
                            print("Error response",error)
                            onFailure(jsonResponse!)
                        }
                    } else {
                        print("Error response================",error)
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //MARK:-SESSIONPIMANAGER
    func sessionCreation(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.Session_mentee)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "sessionCreation Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork()
        {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil{
                    return
                }
                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if(jsonResponse != nil){
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
                        {
                            print("response",jsonResponse)
                            completion(jsonResponse!)
                        }
                        else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    }
                    else {
                        return
                    }
                }
            }
            task.resume()
        }
            
        else
        {
            checkInternetConnectionPopUp()
        }
    }
    //MARK:-APPHELP:-
    func loadAppHelp(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        var urlString:String = ""
        var token:String = ""
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as? String
        if let checkLoginMode = loginMode {
            if checkLoginMode == "Mentee" {
                urlString = "https://tsicmentorapp.org/api/v1/faq/index"
                if(token != "") {
                    Common().showAlertView(title: "Alert!", msg:  "Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                      
                  })
                } else  {
                    token  =  UserDefaults.standard.value(forKey: "token") as! String

                }
                
            } else {
                urlString = "https://tsicmentorapp.org/api/v1/mentor/faq/index"
                if(token != "") {
                    Common().showAlertView(title: "Alert!", msg:  "Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                      
                  })
                } else  {
                    token  =  UserDefaults.standard.value(forKey: "mentorToken") as! String

                }
            }
        }
        
        
        if Common().connectedToNetwork()
        {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil{
                    return
                }
                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if(jsonResponse != nil){
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
                        {
                            print("response",jsonResponse)
                            completion(jsonResponse!)
                        }
                        else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    }
                    else {
                        return
                    }
                }
            }
            task.resume()
        }
            
        else
        {
            checkInternetConnectionPopUp()
        }
    }
    func sessionListing(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.Session_lising)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "sessionListing Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if(jsonResponse != nil){
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
                        {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    func meetingCreation(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.AddMeeting)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingCreation Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    func getAccessTokenVideochat(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.getAccestokenVideoChat
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.encodeParameters(parameters:parameter)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
   
    func initiateVideoCall(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.initiateVideocall
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.encodeParameters(parameters:parameter)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    func videoroomCreate(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.roomGenerateVideoCall
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.encodeParameters(parameters:parameter)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    func disconnectVideoCall(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.disconnectCall
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    func deniedVideoCall(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.deniedCall
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    func schoolListing(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.schoolList)
        let url = URL(string: urlString)!
        print("MentorSchoolList url----:: \(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "MentorUserDetails Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
            })
        }
        
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(token, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("MentorSchoolList------- jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                //let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                    }
                }
            }
        })
        task.resume()
    }
    
    
    
    func meetingListing(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.listmeeting)
        print("URl: ",urlString)
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingListing Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as? NSDictionary
                    print("MeetingListing:: \(String(describing: jsonResponse))")

                    if(jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    func loggedsessionListing(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.loggedSession)
        print("URl: ",urlString)
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingListing Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as? NSDictionary
                    print("MeetingListing:: \(String(describing: jsonResponse))")

                    if(jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    
    func AllmeetingListing(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.alllistmeeting)
        print("URl: ",urlString)
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingListing Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
           // urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as? NSDictionary
                    print("MeetingListing:: \(String(describing: jsonResponse))")

                    if(jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    

    func no_reschedule_meeting(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.no_reschedule_meeting)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "no_reschedule_meeting Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print(jsonResponse)
                    
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    func mentee_chat(parameter: [String:String] , chatBy: String ,pageList: Int, completion: @escaping(NSDictionary) -> Void ) {
        var tokenkeyIdentify = ""
        var urlString = ""
        var methodType = ""
        let page = "?page="
        let take = "&take="
        let pageList : Int = pageList
        let takevalue : Int = 15
        
        if chatBy=="mentee" {
           // "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.menteeChat)\(page)\(pageList)\(take)\(takevalue)"
            
            urlString = "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.menteeChat)\(page)\(pageList)\(take)\(takevalue)"//TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.menteeChat)
            tokenkeyIdentify = "token"
            methodType = "Post"//Get
        } else if chatBy=="mentor" {
            urlString = "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.mentor_chat)\(page)\(pageList)\(take)\(takevalue)"//TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.mentor_chat)
            tokenkeyIdentify = "mentorToken"
            methodType = "Post"
        } else if chatBy=="staff" {
            urlString = "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.staff_chat)\(page)\(pageList)\(take)\(takevalue)"//TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.staff_chat)
            tokenkeyIdentify = "mentorToken"
            methodType = "Post"
        } else if chatBy=="menteestaff" {
            urlString = "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.Mentee_staff_chat)\(page)\(pageList)\(take)\(takevalue)" //TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.Mentee_staff_chat)
            tokenkeyIdentify = "token"
            methodType = "Post"
        }
        
        print("final url",urlString)
        
        guard let token  =  UserDefaults.standard.value(forKey: tokenkeyIdentify) as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

            })
        }
        
        if Common().connectedToNetwork() {
           // startActivityIndicator()
            print("parameter",parameter)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            if chatBy=="mentee" {
                urlRequest.encodeParameters(parameters:parameter) //parameter
            } else {
                urlRequest.encodeParameters(parameters:parameter)

            }
            print("url request",urlRequest)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = methodType
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                print("data---",data)
               // self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print(jsonResponse)
                    
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    func retrieveTokenChat(parameter: [String:String], chatBy: String, completion: @escaping(NSDictionary) -> Void ) {
        var tokenkeyIdentify = ""
        var urlString = ""
        var methodType = ""
        
        
        if chatBy=="mentee" {
           // "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.menteeChat)\(page)\(pageList)\(take)\(takevalue)"
            urlString = TakeStockInChildrenConstant.twiiloTokegeturl
            tokenkeyIdentify = "token"
            methodType = "Post"//Get
        } else if chatBy=="mentor" {
            urlString = TakeStockInChildrenConstant.twiiloTokegeturl
            tokenkeyIdentify = "mentorToken"
            methodType = "Post"
        }
        
        print("final url",urlString)
        
        guard let token  =  UserDefaults.standard.value(forKey: tokenkeyIdentify) as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

            })
        }
        
        if Common().connectedToNetwork() {
           // startActivityIndicator()
            print("parameter",parameter)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            if chatBy=="mentee" {
                urlRequest.encodeParameters(parameters:parameter) //parameter
            } else {
                urlRequest.encodeParameters(parameters:parameter)

            }
            print("url request",urlRequest)
           // urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = methodType
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                print("data---",data)
               // self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("chat------",jsonResponse)
                    
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            self.stopActivityIndicator()
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    func channelSidUpdate(parameter: [String:String], completion: @escaping(NSDictionary) -> Void ) {
        var tokenkeyIdentify = ""
        var urlString = ""
        var methodType = ""
    
        urlString = TakeStockInChildrenConstant.sidUpdateurl
        tokenkeyIdentify = "token"
        methodType = "Post"
        
        print("final url",urlString)
        
        
        if Common().connectedToNetwork() {
           // startActivityIndicator()
            print("parameter",parameter)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter) //parameter
            print("url request",urlRequest)
           // urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = methodType
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                print("data---",data)
               // self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("chat==============response",jsonResponse)
                    
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    func sendPushTwillo(parameter: [String:String], completion: @escaping(NSDictionary) -> Void ) {
        var tokenkeyIdentify = ""
        var urlString = ""
        var methodType = ""
    
        urlString = TakeStockInChildrenConstant.PushnotifyTwillo
        tokenkeyIdentify = "token"
        methodType = "Post"
        
        print("final url",urlString)
        
        
        if Common().connectedToNetwork() {
           // startActivityIndicator()
            print("parameter",parameter)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter) //parameter
            print("url request",urlRequest)
           // urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = methodType
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                print("data---",data)
               // self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil {
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("chat==============response",jsonResponse)
                    
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            })
                        }
                    } else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    
    
    func meetingCancel(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.cancelMeeting)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingCancel Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil{
                    return
                }
                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print(jsonResponse)
                    
                    if(jsonResponse != nil){
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
                        {
                            completion(jsonResponse!)
                        }
                        else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    }
                    else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                        
                    }
                }
            }
            task.resume()
        }
            
        else
        {
            checkInternetConnectionPopUp()
        }
    }
    
    func menteeReport(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.menteeReport)
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "menteeReport Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.encodeParameters(parameters:parameter)
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
                self.stopActivityIndicator()
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                    
                }
                
                if error != nil{
                    return
                }
                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print(jsonResponse)
                    
                    if(jsonResponse != nil){
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
                        {
                            completion(jsonResponse!)
                        }
                        else {
                            
                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    }
                    else {
                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                            
                        })
                        
                    }
                }
            }
            task.resume()
        }
            
        else
        {
            checkInternetConnectionPopUp()
        }
    }
    
    func startActivityIndicator() {
        
        let activityData = ActivityData()
        NVActivityIndicatorView.DEFAULT_TYPE = .ballRotateChase
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now()) {
            NVActivityIndicatorPresenter.sharedInstance.startAnimating(activityData)
        }
    }
    
    func stopActivityIndicator() {
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now()) {
            NVActivityIndicatorPresenter.sharedInstance.stopAnimating()
        }
    }
    
    func checkInternetConnectionPopUp(){
        DispatchQueue.main.async {
            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
    }
    
    //MARK:-GetGoalTaskChallengeList
    func getMentorGoalList(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorShowGoalTaskChallengeList)
        let url = URL(string: urlString)!
        print("mentorlisting\(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "getMentorGoalList Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(token, forHTTPHeaderField: "Authorizations")
        request.httpMethod = "POST"
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.timeoutInterval = 20
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("GoalList jsonResponse:: \(String(describing: jsonResponse))")
                if(error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                                
                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        //self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //MARK:-CreateGoalTaskChallenge
    func createMentorGoalTaskChallenge(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorCreateGoalTaskChallenge)
        let url = URL(string: urlString)!
        print("mentorlisting\(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "createMentorGoalTaskChallenge Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(token, forHTTPHeaderField: "Authorizations")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("GoalList jsonResponse:: \(String(describing: jsonResponse))")
                if(error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                                
                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //MARK:-AssignedMenteeList
    func assignedMenteeListGoalTaskChallenge(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.AssignMenteeListGoalTaskChallenge)
        let url = URL(string: urlString)!
        print("mentorlisting\(url)")
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "assignedMenteeListGoalTaskChallenge Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(token, forHTTPHeaderField: "Authorizations")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("GoalList jsonResponse:: \(String(describing: jsonResponse))")
                if(error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                                
                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //MARK:-AssignGoalTaskChallenge
    func assignGoalTaskChallenge(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorAssignGoalTaskChallenge)
        let url = URL(string: urlString)!
        print("mentorlisting\(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "assignGoalTaskChallenge Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(token, forHTTPHeaderField: "Authorizations")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

        let jsonData = try? JSONSerialization.data(withJSONObject: userDetails , options: [])
        request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("GoalList jsonResponse:: \(String(describing: jsonResponse))")
                if(error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode{
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)

                                print(jsonResponse!)
                            } else {
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                            // onSuccess(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
    //TODO:- MentorSchoolList
    //MARK: getSchoolList
    func getMentorSchoolList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.getMentorSchoolList)
        let url = URL(string: urlString)!
        print("MentorSchoolList url:: \(url)")
        
        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "MentorUserDetails Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
            })
        }
        
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(token, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("MentorSchoolList jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                //let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(jsonResponse!)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                    }
                }
            }
        })
        task.resume()
    }
    
    
    
    
    func mentorhomemessageApi(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.homemessagementor//TakeStockInChildrenConstant.homemessagementor
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
          //  urlRequest.setValue(UserDefaults.standard.string(forKey: "mentorToken"), forHTTPHeaderField: "Authorizations")
           // urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
          //  urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    //MARK: --- Message Center API
    
    func messagecntermentee(parameter: [String: String], completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.homemessagecentermentee
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            
            
            guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

                })
            }
            print("token===========",token)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters: parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    func FAQListmentor(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.faqmentor
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            
            
            guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

                })
            }
            print("token===========",token)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
         //   urlRequest.encodeParameters(parameters: parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    
    
    
    func FAQListmentee(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.faqmentee
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            
            
            guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

                })
            }
            print("token===========",token)
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
          //  urlRequest.encodeParameters(parameters: parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
    func messagecntermentor(parameter: [String: String], completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.homemessagecentermentor
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            
            guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
                return  Common().showAlertView(title: "Alert!", msg: "mentee_chat Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

                })
            }
            print("token**********",token)
            
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "POST"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    func menteehomemessageApi(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.homemessagementee//TakeStockInChildrenConstant.homemessagementee
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
           // urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    func disclaimerApi(completion: @escaping(NSDictionary) -> Void ) {
        let urlString = TakeStockInChildrenConstant.disclaimerUrl
        
        if Common().connectedToNetwork() {
          //  startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
          //  urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.httpMethod = "GET"
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
           // urlRequest.encodeParameters(parameters:parameter)
            let session = URLSession.shared
            
            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
               // self.stopActivityIndicator()
                
                let httpResponse = response as? HTTPURLResponse
                if (httpResponse != nil) {
                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                
                if error != nil{
                    return
                } else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                } else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("response=========",jsonResponse)
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            completion(jsonResponse!)
                        } else {
                            
                            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                                
                            })
                        }
                    } else {
                        return
                    }
                }
            }
            task.resume()
        } else {
            checkInternetConnectionPopUp()
        }
    }
    
    
//        //MARK:
//        func logOut(onSuccess: @escaping(NSArray) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//            let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.mentorLogOut)
//            let url = URL(string: urlString)!
//            print("MentorLogOutURL\(url)")
//            var request = URLRequest(url: url)
//            request.httpMethod = "POST"
//            request.addValue(UserDefaults.standard.string(forKey: "mentorToken")!, forHTTPHeaderField: "Authorizations")
//            request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//            request.timeoutInterval = 20
//
//            let session = URLSession.shared
//            let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//                if let resData = data {
//                    let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                    //print("ELearningDetails jsonResponse:: \(String(describing: jsonResponse))")
//                    if (error != nil) {
//                        onFailure(jsonResponse!)
//                    } else {
//                        if (jsonResponse != nil) {
//                            if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                                if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                    onFailure(jsonResponse!)
//                                } else {
//                                  let logoutMessage = jsonResponse!["message"] as! String
//                                   onSuccess(logoutMessage)
//                                }
//                            } else {
//                                onFailure(jsonResponse!)
//                            }
//                        } else {
//                            let dic : NSDictionary = ["message" : "Null data found"]
//                            onFailure(dic)
//                        }
//                    }
//
//                }
//            })
//            task.resume()
//        }
}

/*
 NSOperation advantages over GCD:
 i. Control On Operation
 you can Pause, Cancel, Resume an NSOperation
 
 ii. Dependencies
 you can set up a dependency between two NSOperations
 operation will not started until all of its dependencies return true for finished.
 
 iii. State of Operation
 can monitor the state of an operation or operation queue. ready ,executing or finished. Can also compute pending operations.
 
 iv. Max Number of Operation
 you can specify the maximum number of queued operations that can run simultaneously
 
 v. Observable
 The NSOperation and NSOperationQueue classes have a number of properties that can be observed, using KVO (Key Value Observing). This is another important benefit if you want to monitor the state of an operation or operation queue.
 
 When to Go for GCD or NSOperation
 when you want more control over queue (all above mentioned) use NSOperation and for simple cases where you want less overhead (you just want to do some work "into the background" with very little additional work) use GCD
 
 */

