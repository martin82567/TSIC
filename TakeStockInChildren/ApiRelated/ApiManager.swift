//
//  ApiManager.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 09/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import Foundation
import NVActivityIndicatorView

let APP_DELEGATE = UIApplication.shared.delegate as! AppDelegate
    

class ApiManager: NSObject {
    
    static let sharedInstance = ApiManager()
    
    func checkInternetConnectionPopUp(){
        DispatchQueue.main.async {
            Common().showAlertView(title: "TSIC", msg: "Error: The Take Stock App is experiencing technical difficulties due to issues with our server provider. Please try again later.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
    }

    //MARK: Login
    func logIn(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UnifiedLogin)
        let url = URL(string: urlString)!
        print("url---",url)
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
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as? NSDictionary
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

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    func unifiedLogIn(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UnifiedLogin)
        let url = URL(string: urlString)!
        print("url---",url)
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
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as? NSDictionary
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

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    func checkUserType(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.checkUserType)
        let url = URL(string: urlString)!
        print("url---",url)
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
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as? NSDictionary
                print("checkUserType:: \(String(describing: jsonResponse))")
                
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
                        }
                    } else {
                        self.checkInternetConnectionPopUp()
                    }
                }
            }
        })
        task.resume()
    }
    
    

//    //MARK:
//    func logOut(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.LogOut)
//        let url = URL(string: urlString)!
//        var request = URLRequest(url: url)
//        request.httpMethod = "GET"
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                //print("ELearningDetails jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                let logoutDic = jsonResponse!["data"] as! NSDictionary
//                                onSuccess(logoutDic)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//
//            }
//        })
//        task.resume()
//    }
//
//
    //MARK: ForgotPassword
    func forgotPass(postId: Int, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UnifiedForgotPassword)
        let url = URL(string: urlString)!
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
                        //onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
//
//    //MARK: ChangePassword
//    func changePass(postId: Int, changeDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ChangePassword)
//        let url = URL(string: urlString)!
//        var request = URLRequest(url: url)
//        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
//        request.httpMethod = "POST"
//        request.timeoutInterval = 20
//
//        let jsonData = try? JSONSerialization.data(withJSONObject: changeDetails , options: [])
//        request.httpBody = jsonData
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("ChangePassword jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                onSuccess(jsonResponse!)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
    //MARK: ResetPassword
    func resetPass(postId: Int, resetDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.unifiedResetpassword)
        let url = URL(string: urlString)!
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
    
    
    
    
    //MARK: UpdatePassword
    func UpdatePass(type: String, resetDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        
        var urlString = ""
        var tokenkeyIdentify = ""
        if type=="mentee" {
            print("type========================= mnetee")
           // "\(TakeStockInChildrenConstant.BaseURL)\(TakeStockInChildrenConstant.menteeChat)\(page)\(pageList)\(take)\(takevalue)"
            
            urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.menteeupdatepassword)
            tokenkeyIdentify = "token"

        } else if type=="mentor" {
            print("type========================= mnetor")
            print("")
            urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.mentorupdatepassword)
            tokenkeyIdentify = "mentorToken"
        }
        
        guard let token  =  UserDefaults.standard.value(forKey: tokenkeyIdentify) as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {

            })
        }
        print("token",token)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue(token, forHTTPHeaderField: "Authorizations")
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
                print("update password jsonResponse:: \(String(describing: jsonResponse))")
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
    

    //TODO:- Profile Module
    //MARK: getUserDetails
    func getUserDetails(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.UserDetails)
        let url = URL(string: urlString)!
        print("TOKEN\(UserDefaults.standard.string(forKey: "token"))")
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("UserDetails jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(userDetailsDic)
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

    //MARK: getUser'sMentorDetails
    func getMentorDetails(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.MentorProfileDetails)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let data = data, let dataString = String(data: data, encoding: .utf8) {
                print("Response data string----:\n \(dataString)")
            }
            
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
               // print("Mentor UserDetails jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(userDetailsDic)
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


//    //TODO:- Goal Module
//    //MARK: get goal task challenge lit
    func getgGoalList(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.GoalTaskChallengeList)
        print("PENDING Goal List for Mentee URL:: \(urlString)")
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
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

    func getCompleteGoalList(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.GoalTaskChallengeCompleteList)
        print("getCompleteGoalListByMentee URL:: \(urlString)")
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
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

    func goalTaskAction(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.GoalTaskChallengeAction)
        let url = URL(string: urlString)!
        print("url\(url)")
        print(UserDefaults.standard.string(forKey: "token"))
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
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

    //MARK:- get goal task challenge details
    func getgGoalDetails(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.GoalTaskChallengeDetails)
        let url = URL(string: urlString)!
        print("GoalDetailsURL\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
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

    func updateNote(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void){
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.updateNote)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
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

//    //TODO:- Resource Module
    //MARK: getResourceList
    func getResourceList(postId: Int, searchDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ResourceMentor)
        let url = URL(string: urlString)!
        print("REsourceURL\(url)")
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let jsonData = try? JSONSerialization.data(withJSONObject: searchDetails , options: [])
        request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
            
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
//
//    //MARK: getResourceDetails
    func getResourceDetails(ResourceID: String, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ResourceDetails).appending(ResourceID)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("ResourceDetails jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let resourceDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(resourceDetailsDic)
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
//
    //TODO:- ELearning Module
    //MARK: getELearningList
    func getELearningList(postId: Int, searchDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        var urlString = ""
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
             
        if loginMode == "Mentor" {
            urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ELearning)
        }else{
        urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ResourceMentor)
        }
      print(urlString)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        
        
        let loginMode2 = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode2 == "Mentor" {
            request.setValue(UserDefaults.standard.string(forKey: "mentorToken"), forHTTPHeaderField: "Authorizations")
        } else {
            request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
        }
        request.httpMethod = "POST"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

        let jsonData = try? JSONSerialization.data(withJSONObject: searchDetails , options: [])
        request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("ELearningList jsonResponse:: \(String(describing: jsonResponse))")
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
    
    
    
    
    
    //    //TODO:- Resource Module
        //MARK: Checkbox
        func disclaimerList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
            let urlString = TakeStockInChildrenConstant.disclaimerUrl
            let url = URL(string: urlString)!
            print("REsourceURL\(url)")
            var request = URLRequest(url: url)
            request.setValue("application/json", forHTTPHeaderField: "Content-Type")
            //request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
            request.httpMethod = "GET"
            request.timeoutInterval = 20
            request.addValue("ios", forHTTPHeaderField: "platform")
            request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
           // let jsonData = try? JSONSerialization.data(withJSONObject: searchDetails , options: [])
           // request.httpBody = jsonData

            let session = URLSession.shared
            let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

                if let resData = data {
                    let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                
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
    
    
    
    
    
    
    func FAQList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        var urlString = ""
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode == "Mentor" {
            urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.faqmentor)
        }else{
        urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.faqmentee)
        }
      print(urlString)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        
        
        let loginMode2 = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        if loginMode2 == "Mentor" {
            request.setValue(UserDefaults.standard.string(forKey: "mentorToken"), forHTTPHeaderField: "Authorizations")
        } else {
            request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
        }
        request.httpMethod = "GET"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

       // let jsonData = try? JSONSerialization.data(withJSONObject: searchDetails , options: [])
       // request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("ELearningList jsonResponse:: \(String(describing: jsonResponse))")
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
    
    
    
    
    func retrievechatToken(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        var urlString = "https://mentorappdev.tsic.org/api/chat/get_access_token"
         print(urlString)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        request.httpMethod = "GET"
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")

       // let jsonData = try? JSONSerialization.data(withJSONObject: searchDetails , options: [])
       // request.httpBody = jsonData

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("ELearningList jsonResponse:: \(String(describing: jsonResponse))")
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
    
    
    
    
    

    //MARK: getELearningDetails
    func getELearningDetails(ELearningID: String, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as! String
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ELearning).appending(ELearningID)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        
        if loginMode == "Mentor" {
            request.setValue(UserDefaults.standard.string(forKey: "mentorToken"), forHTTPHeaderField: "Authorizations")
        } else {
            request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
        }
      //  request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("ELearningDetails jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let eLearnDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(eLearnDetailsDic)
                            }
                        } else {
                            onFailure(jsonResponse!)
                        }
                    } else {
                        self.stopActivityIndicator()
                        self.checkInternetConnectionPopUp()

//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }

    func deleteImageForGoal(ImageID: String, completion: @escaping (NSDictionary)->Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.deleteImage).appending(ImageID)
        let url = URL(string: urlString)!
        print("deleteurl\(url)")
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.timeoutInterval = 20

        let task = URLSession.shared.dataTask(with: request as URLRequest) { (data, response, error) in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("jsonResponse:: \(String(describing: jsonResponse))")
                if error != nil {
                    print(error!)
                } else {
                    let json = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    completion(json!)
                }
            }
        }
        task.resume()
    }

    //TODO:- Job Module
    //MARK: getJobList
    func getJobList(SearchText: NSString, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.JobList)
        let url = NSURLComponents(string: urlString)!
        print("joblisturl\(url)")

        url.queryItems = [
            URLQueryItem(name: "search_text", value: SearchText as String)
        ]

        var request = URLRequest(url: url.url!)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.timeoutInterval = 20

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                //print("JobList jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                let eLearnDetailsDic = jsonResponse!["data"] as! NSDictionary
                                onSuccess(eLearnDetailsDic)
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
//
//    //MARK: getJobDetails
//    func getJobDetails(JobID: String, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.JobDeatils).appending(JobID)
//        let url = URL(string: urlString)!
//        var request = URLRequest(url: url)
//        request.httpMethod = "GET"
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("JobDetails jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                let resourceDetailsDic = jsonResponse!["data"] as! NSDictionary
//                                onSuccess(resourceDetailsDic)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
//    //MARK: getAppliedJobList
//    func getAppliedJobList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.AppliedJobList)
//        let url = URL(string: urlString)!
//        var request = URLRequest(url: url)
//        request.httpMethod = "GET"
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("AppliedJobList jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                let resourceDetailsDic = jsonResponse!["data"] as! NSDictionary
//                                onSuccess(resourceDetailsDic)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
//    /*//MARK:: getApplyJob
//     func getApplyJob(postId: Int, appliedUserDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//     let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ApplyJob)
//     let url = URL(string: urlString)!
//     var request = URLRequest(url: url)
//     request.setValue("application/json", forHTTPHeaderField: "Content-Type")
//     request.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
//     request.httpMethod = "POST"
//     request.timeoutInterval = 20
//
//     let jsonData = try? JSONSerialization.data(withJSONObject: appliedUserDetails , options: [])
//     request.httpBody = jsonData
//
//     let session = URLSession.shared
//     let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//     let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
//     print("ApplyJob jsonResponse:: \(String(describing: jsonResponse))")
//     if (error != nil) {
//     onFailure(jsonResponse!)
//     } else {
//     if (jsonResponse != nil) {
//     if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//     if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//     onFailure(jsonResponse!)
//     } else {
//     onSuccess(jsonResponse!)
//     }
//     } else {
//     onFailure(jsonResponse!)
//     }
//     } else {
//     let dic : NSDictionary = ["message" : "Null data found"]
//     onFailure(dic)
//     }
//     }
//     })
//     task.resume()
//     }*/
//
//    func apiAgencyResponseByPost(accessToken: String, userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.s3TipBaseURL.appending(MoreTooLifeServiceConstant.SubmitTipGetAgency)
//        let url = URL(string: urlString)!
//
//        let request: NSMutableURLRequest = NSMutableURLRequest(url:  url)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.setValue(accessToken, forHTTPHeaderField: "Authorizations")
//        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
//        request.httpBody = httpBody
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                if(jsonResponse != nil){
//                    if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                        if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                            onFailure(jsonResponse!)
//                        } else {
//                            onSuccess(jsonResponse!)
//                        }
//                        //onSuccess(jsonResponse!)
//                    } else {
//                        onFailure(jsonResponse!)
//                        // onSuccess(jsonResponse!)
//                    }
//                } else {
//                    let dic : NSDictionary = ["message" : "Null data found"]
//                    onFailure(dic)
//                }
//            }
//        })
//        task.resume()
//    }
//
//    func updateTokenToServerForPush(userDetails: NSDictionary, onSuccess: @escaping() -> Void, onFailure: @escaping() -> Void){
//
//        //        var username = userDetails["username"]
//        //        var password = userDetails["password"]
//        //        var email = userDetails["email"]
//        let url = MoreTooLifeServiceConstant.s3TipBaseURL.appending(MoreTooLifeServiceConstant.FCMTokenUpdateEndpoint)
//        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.setValue(UserDefaults.standard.string(forKey: "submited_tips_token")!, forHTTPHeaderField: "Authorizations")
//
//        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
//        request.httpBody = httpBody
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//            //            let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
//            if(error != nil){
//                onFailure()
//            } else{
//                onSuccess()
//
//            }
//        })
//        task.resume()
//
//    }
//
//    func getTip(strAuthorizationId: String, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(Error) -> Void) {
//        //        var username = userDetails["username"]
//        //        var password = userDetails["password"]
//        //        var email = userDetails["email"]
//
//        let url : String = MoreTooLifeServiceConstant.s3TipBaseURL.appending(MoreTooLifeServiceConstant.getTipEndpointNew)
//
//        print(url)
//
//        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.setValue(strAuthorizationId, forHTTPHeaderField: "Authorizations")
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            do {
//                // let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
//                if (error != nil) {
//                    onFailure(error!)
//                } else {
//                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: [])
//
//                    print(jsonResponse!)
//
//                    onSuccess(jsonResponse! as! NSDictionary)
//                }
//            } catch {
//                print("throw")
//            }
//        })
//        task.resume()
//    }
//
//
//    func updateMultiplePersonAndVehicle(strAuthorizationId: String, userDetails: NSDictionary, onSuccess: @escaping() -> Void, onFailure: @escaping() -> Void) {
//        //        var username = userDetails["username"]
//        //        var password = userDetails["password"]
//        //        var email = userDetails["email"]
//        let url : String = MoreTooLifeServiceConstant.s3TipBaseURL.appending(MoreTooLifeServiceConstant.addMultipleDataNew)
//        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.setValue(strAuthorizationId, forHTTPHeaderField: "Authorizations")
//
//        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
//        request.httpBody = httpBody
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//            //  let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
//            // print(jsonResponse)
//            if(error != nil){
//                onFailure()
//            } else{
//                onSuccess()
//
//            }
//        })
//        task.resume()
//    }
//
//    //TODO:- Chat Module
//    //MARK: getChatList
//    func getChatList(onSuccess: @escaping(NSArray) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ChatList)
//        let url = URL(string: urlString)!
//        var request = URLRequest(url: url)
//        request.httpMethod = "GET"
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                //print("ChatList jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                let chatListArr = jsonResponse!["data"] as! NSArray
//                                onSuccess(chatListArr)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
//
//    //MARK: getChatMessages
//    func getChat(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let url : String = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ChatMessages)
//        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//
//
//        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
//        request.httpBody = httpBody
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("ChatMessages jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                //                                let chatListArr = jsonResponse!["data"] as! NSArray
//                                onSuccess(jsonResponse!)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
//
//    func getChatCode(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let url : String = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ChatCode)
//        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
//        request.httpMethod = "POST"
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//
//        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
//        request.httpBody = httpBody
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("ChatMessages jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                //                                let chatListArr = jsonResponse!["data"] as! NSArray
//                                onSuccess(jsonResponse!)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        let dic : NSDictionary = ["message" : "Null data found"]
//                        onFailure(dic)
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
//
//    //    func getChatList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//    //        let urlString = MoreTooLifeServiceConstant.BaseURL.appending(MoreTooLifeServiceConstant.ChatList)
//    //        let url = NSURLComponents(string: urlString)!
//    //
//    //        var request = URLRequest(url: url.url!)
//    //        request.httpMethod = "GET"
//    //        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
//    //        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//    //        request.timeoutInterval = 20
//    //
//    //        let session = URLSession.shared
//    //        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//    //
//    //            if let resData = data {
//    //                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//    //                //print("JobList jsonResponse:: \(String(describing: jsonResponse))")
//    //                if (error != nil) {
//    //                    onFailure(jsonResponse!)
//    //                } else {
//    //                    if (jsonResponse != nil) {
//    //                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//    //                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//    //                                onFailure(jsonResponse!)
//    //                            } else {
//    //                                let eLearnDetailsDic = jsonResponse!["data"] as! NSDictionary
//    //                                onSuccess(eLearnDetailsDic)
//    //                            }
//    //                        } else {
//    //                            onFailure(jsonResponse!)
//    //                        }
//    //                    } else {
//    //                        let dic : NSDictionary = ["message" : "Null data found"]
//    //                        onFailure(dic)
//    //                    }
//    //                }
//    //            }
//    //        })
//    //        task.resume()
//    //    }
//

    //MARK: Version Code
    func getVersionCode(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.getVersionCode
        print(urlString)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue("application/json", forHTTPHeaderField: "Accept")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.timeoutInterval = 20

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as? NSDictionary
                print("My Journal jsonResponse:: \(String(describing: jsonResponse!))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil){
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
                        let dic : NSDictionary = ["message" : "Null data found"]
                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }
    
//    //MARK: My Journal
    func myJournalList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.journal_list)
        print(urlString)
        let url = URL(string: urlString)!
        var request = URLRequest(url: url)
        request.httpMethod = "GET"
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 20

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("My Journal jsonResponse:: \(String(describing: jsonResponse!))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil){
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
                        let dic : NSDictionary = ["message" : "Null data found"]
                        onFailure(dic)
                    }
                }
            }
        })
        task.resume()
    }

    func addJournalServiceCall(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let url : String = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.journal_add)
        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
        request.httpMethod = "POST"
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")

        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
        request.httpBody = httpBody
        request.timeoutInterval = 20

        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in

            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("ChatMessages jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                //                                let chatListArr = jsonResponse!["data"] as! NSArray
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
    
    
    func meetingListingForMentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.meetingMenteeListing)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingListingForMentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
            urlRequest.addValue("ios", forHTTPHeaderField: "platform")
            urlRequest.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
            urlRequest.httpMethod = "GET"
            // urlRequest.encodeParameters(parameters:parameter)
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
    
    
    
    
    
    func AllmeetingListingForMentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.allMenteemeetinglist)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "meetingListingForMentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        print("token",token)
        if Common().connectedToNetwork() {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
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
                
                if error != nil{
                    return
                }
                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
                }
                else{
                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
                    print("json mentee-=========",jsonResponse)
                    
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
    
    
    func upcommingMeetingmentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.upcommingMenteeMeeting)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "upcommingMeetingmentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork()
        {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
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
    func pastMeetingmentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.pastMenteeMeeting)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "pastMeetingmentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
            })
        }
        
        if Common().connectedToNetwork()
        {
            startActivityIndicator()
            let url = URL(string:urlString)
            let urlRequest = NSMutableURLRequest(url: url!)
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

    func serviceCallTOCheckMeetinParticipation(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.meetingParticipation)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "serviceCallTOCheckMeetinParticipation Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
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
    
    
    
    
    
    func serviceCallTOcencelMeetingMentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.cancelMeetingMentee)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "serviceCallTOCheckMeetinParticipation Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
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
    
    
    func acceptMeetingByMentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.acceptMeetingByMentee)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "acceptMeetingByMentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
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
    
    func rescheduleMeetingByMentee(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
        
        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.meetingReschedueRequestbyMentee)
        
        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
            return  Common().showAlertView(title: "Alert!", msg: "rescheduleMeetingByMentee Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                
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
    
    //MARK: getUploadReportList
    
    //MARK: getUploadReportList
//    func getUploadReportList(parameter: [String:String] , completion: @escaping(NSDictionary) -> Void ) {
//
//        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.listmeeting)
//
//        guard let token  =  UserDefaults.standard.value(forKey: "mentorToken") as? String else{
//            return  Common().showAlertView(title: "Alert!", msg: "Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//            })
//        }
//
//        if Common().connectedToNetwork()
//        {
//            startActivityIndicator()
//            let url = URL(string:urlString)
//            let urlRequest = NSMutableURLRequest(url: url!)
//            //            urlRequest.setValue(UserDefaults.standard.string(forKey: "token"), forHTTPHeaderField: "Authorizations")
//            urlRequest.addValue(token, forHTTPHeaderField: "Authorizations")
//            urlRequest.httpMethod = "POST"
//            // urlRequest.encodeParameters(parameters:parameter)
//            let session = URLSession.shared
//
//            let task = session.dataTask(with: urlRequest as URLRequest){ data,response,error in
//                self.stopActivityIndicator()
//                let httpResponse = response as? HTTPURLResponse
//                if (httpResponse != nil) {
//
//                    print("statusCode: \(httpResponse?.statusCode ?? 200)")
//
//                }
//
//                if error != nil{
//                    return
//                }
//                else if (httpResponse != nil) && httpResponse?.statusCode == 401 {
//                    // print("statusCode: \(httpResponse?.statusCode ?? 200)")
//                }
//                else{
//                    let jsonResponse = try? JSONSerialization.jsonObject(with: data!, options: []) as! NSDictionary
//                    print(jsonResponse)
//
//                    if(jsonResponse != nil){
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode
//                        {
//                            completion(jsonResponse!)
//                        }
//                        else {
//
//                            Common().showAlertView(title: "Alert!", msg: "something is wrong", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//                            })
//                        }
//                    }
//                    else {
//                        Common().showAlertView(title: "Alert!", msg: "null value from server", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//                        })
//
//                    }
//                }
//            }
//            task.resume()
//        }
//
//        else
//        {
//            checkInternetConnectionPopUp()
//        }
//    }
    
    func getUploadReportList(userDetails: NSDictionary, onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
        let url : String = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ListReport)
        let request: NSMutableURLRequest = NSMutableURLRequest(url: NSURL(string: url)! as URL)
        request.httpMethod = "POST"
        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
        request.addValue(UserDefaults.standard.string(forKey: "token")!, forHTTPHeaderField: "Authorizations")
        request.addValue("ios", forHTTPHeaderField: "platform")
        request.addValue(UIApplication.release, forHTTPHeaderField: "app_version")
        
        let httpBody = try? JSONSerialization.data(withJSONObject: userDetails, options: [])
        request.httpBody = httpBody
        request.timeoutInterval = 20
        
        let session = URLSession.shared
        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
            
            if let resData = data {
                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
                print("ChatMessages jsonResponse:: \(String(describing: jsonResponse))")
                if (error != nil) {
                    onFailure(jsonResponse!)
                } else {
                    if (jsonResponse != nil) {
                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
                                onFailure(jsonResponse!)
                            } else {
                                //                                let chatListArr = jsonResponse!["data"] as! NSArray
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
//    func getUploadReportList(onSuccess: @escaping(NSDictionary) -> Void, onFailure: @escaping(NSDictionary) -> Void) {
//        let urlString = TakeStockInChildrenConstant.BaseURL.appending(TakeStockInChildrenConstant.ListReport)
//        let url = URL(string: urlString)!
//        print("ShowUploadListUrl\(url)")
//
//        guard let token  =  UserDefaults.standard.value(forKey: "token") as? String else{
//            return  Common().showAlertView(title: "Alert!", msg: "Token missing", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//            })
//        }
//
//        var request = URLRequest(url: url)
//        request.httpMethod = "POST"
//        request.addValue(token, forHTTPHeaderField: "Authorizations")
//        request.addValue("application/json", forHTTPHeaderField: "Content-Type")
//        request.timeoutInterval = 20
//
//        let session = URLSession.shared
//        let task = session.dataTask(with: request as URLRequest, completionHandler: {data, response, error -> Void in
//
//            if let resData = data {
//                let jsonResponse = try? JSONSerialization.jsonObject(with: resData, options: []) as! NSDictionary
//                print("UserDetails jsonResponse:: \(String(describing: jsonResponse))")
//                if (error != nil) {
//                    onFailure(jsonResponse!)
//                } else {
//                    if (jsonResponse != nil) {
//                        if let response = response as? HTTPURLResponse, 200...299 ~= response.statusCode {
//                            if (jsonResponse?.value(forKey: "status") as! Bool == false) {
//                                onFailure(jsonResponse!)
//                            } else {
//                                let userDetailsDic = jsonResponse!["data"] as! NSDictionary
//                                onSuccess(userDetailsDic)
//                            }
//                        } else {
//                            onFailure(jsonResponse!)
//                        }
//                    } else {
//                        self.checkInternetConnectionPopUp()
//                    }
//                }
//            }
//        })
//        task.resume()
//    }
    
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
}

extension NSMutableURLRequest {
    
    private func percentEscapeString(string: String) -> String {
        let characterSet = CharacterSet(charactersIn: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._* ")
        
        
        return string.addingPercentEncoding(withAllowedCharacters: characterSet)!.replacingOccurrences(of: " ", with: "+")
        
    }
    
    func encodeParameters(parameters: [String : String]) {
        
        httpMethod = "POST"
        
        httpBody = parameters
            .map { "\(percentEscapeString(string: $0))=\(percentEscapeString(string: $1))" }
            .joined(separator: "&")
            .data(using: String.Encoding.utf8)
    }
}
