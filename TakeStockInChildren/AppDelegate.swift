//
//  AppDelegate.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 08/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import CoreLocation
import CoreData
import IQKeyboardManagerSwift
//import Firebase
//import FirebaseInstanceID
import FirebaseCore
import FirebaseMessaging
//import Fabric
//import Crashlytics
import UserNotifications
import Siren
import PushKit
import CallKit
import BackgroundTasks
import os
import ZoomVideoSDK

@UIApplicationMain
class AppDelegate: UIResponder, UIApplicationDelegate, UINavigationControllerDelegate {
    
    let provider = CXProvider(configuration: CXProviderConfiguration(localizedName: "TSIC"))
    var Voipdata: String?
    
     var window: UIWindow?
     var tabBarController : UITabBarController!
    
     var locationManager = CLLocationManager()
     //var nav : UINavigationController?
     let notificationCenter = UNUserNotificationCenter.current()

    func application(_ application: UIApplication, didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?) -> Bool {
    
        
        UIApplication.shared.setMinimumBackgroundFetchInterval(UIApplication.backgroundFetchIntervalMinimum)
        
        
        /*
        if #available(iOS 13.0, *) {
            BGTaskScheduler.shared.register(
                forTaskWithIdentifier: "com.example.tsicBackgroundapprefreshidentifier",
                using: nil) { (task) in
                print("Task handler")
                self.handleAppRefreshTask(task: task as! BGAppRefreshTask)
            }
        } else {
            print("Fallback on earlier versions")
            // Fallback on earlier versions
        }
        
        */
       
//
//        let now: Date = Date()
//        let dateFormatter: DateFormatter = DateFormatter()
//        dateFormatter.dateStyle = .short
//        dateFormatter.timeStyle = .short
//
//        // Now in New York time
//        let nyTimeZone: TimeZone = TimeZone(identifier: "America/New_York")!
//        dateFormatter.timeZone = nyTimeZone
//        dateFormatter.dateFormat = "hh:mm a"
//
//        print(dateFormatter.string(from: now))
        
        self.firebaseConfig(application: application)
        IQKeyboardManager.shared.enable = true
//        forceUpdateApp()
       
        //dateCompairTest()
        
//        enableLocalNotificationCenter()
        self.determineMyCurrentLocation()
        //testNavigation()
        createRootViewController()
        
        self.initializeZoom()
        
        Messaging.messaging().isAutoInitEnabled = false
//        registerForPushNotifications(application: application)
        
        // Configure firebase and then start the Notification Initialization
        FirebaseApp.configure()
        registerForPushNotifications(application: application)
        
        return true
    }
    
    
    func application(_ application: UIApplication, performFetchWithCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void) {
        self.getLatLogForGeofence { (result) in
            print("called geofence")
        }
    }
    
    @objc func getLatLogForGeofence(_ completion: @escaping (UIBackgroundFetchResult) -> Void) {
            print("lat lon fence called")
            MentorApiManager().getLatLogForGeofance(onSuccess: { (response) in
             //   print("geofence",response)
                let status = response["status"] as? Bool
                if let responseStatus = status {
                    if responseStatus == true {
                        completion(.newData)
                        let arrGeofenceData = response["data"] as? NSArray
                        if let arrGeofenceData = arrGeofenceData {
                                self.regionCheck(arrGeofences: arrGeofenceData)
                        } else {
                            print("NOt working")
                        }
                    }
                } else {
                    print("No Geofence data")
                }
            }, onFailure: { (response) in
                
            })
    }
    
    func checkWithimeteronrnot() {
        let center = UNUserNotificationCenter.current()
                     let content = UNMutableNotificationContent()
                     content.title = "Reminder"
                     content.body = "You are within 1km"
                    content.sound = .default
                    // let trigger = UNTimeIntervalNotificationTrigger(timeInterval: 10, repeats: false)
                     let request = UNNotificationRequest(identifier: "reminder", content: content, trigger: nil)
                 center.add(request) { (error) in
                     if error != nil {
                         print("Error notification",error?.localizedDescription)
                     }
                 }
    }

    func regionCheck(arrGeofences: NSArray) {
        if arrGeofences.count == 0 {
           // self.removeAllGeotifications()
        }
        
        for index in 0 ..< arrGeofences.count {
            let dictDetails : NSDictionary = arrGeofences.object(at: index) as! NSDictionary
            let strLat = dictDetails["latitude"] as? String
            let strLong = dictDetails["longitude"] as? String
            
            let doubleLat = Double(strLat ?? "") ?? 0.0
            let doublelon = Double(strLong ?? "") ?? 0.0
            
            let id = dictDetails["id"] as! NSNumber
            let title = dictDetails["title"] as? String ?? ""
            
            let savedlat = UserDefaults.standard.double(forKey: "latitude")
            let savedlon = UserDefaults.standard.double(forKey: "longitude")
           // print("savedlat and long",savedlat,savedlon)
            print("lat and lon",savedlat,savedlon,strLat,strLong)
            let coordinate₀ = CLLocation(latitude: savedlat, longitude: savedlon) //34.54545 // 56.64646
            let coordinate₁ = CLLocation(latitude: doubleLat, longitude: doublelon) //59.32635 //18.072310

            let distanceInMeters = coordinate₀.distance(from: coordinate₁) / 1000// result is in meters
            let kms = String(format:"%.02f", distanceInMeters)
            print("meter",kms)
            if kms <= "622" {
                checkWithimeteronrnot()
                print("Within kilometer")
            }
            else {
                print("Not within")
            }
        }
    }
    
    /*
    @available(iOS 13.0, *)
    func handleAppRefreshTask(task: BGAppRefreshTask) {
      print("Handling task")
      task.expirationHandler = {
        task.setTaskCompleted(success: false)
       // PokeManager.urlSession.invalidateAndCancel()
      }
            MentorApiManager().getTimeZoneMentor(onSuccess: { (response) in
                let status = response["status"] as? Bool
                if let responseStatus = status {
                    if responseStatus == true {
                     print("api called0000000======================================")
                        task.setTaskCompleted(success: true)
                    }
                } else {
                    print("No Geofence data")
                }
            }, onFailure: { (response) in
                
            })
        /*
      let randomPoke = (1...151).randomElement() ?? 1
      PokeManager.pokemon(id: randomPoke) { (pokemon) in
        NotificationCenter.default.post(name: .newPokemonFetched,
                                        object: self,
                                        userInfo: ["pokemon": pokemon])
        task.setTaskCompleted(success: true)
      }
      */
      
      scheduleBackgroundPokemonFetch()
    }
    
    @available(iOS 13.0, *)
    func scheduleBackgroundPokemonFetch() {
      let pokemonFetchTask = BGAppRefreshTaskRequest(identifier: "com.example.tsicBackgroundapprefreshidentifier")
      pokemonFetchTask.earliestBeginDate = Date(timeIntervalSinceNow: 30)
      do {
        try BGTaskScheduler.shared.submit(pokemonFetchTask)
        print("task scheduled")
      } catch {
        print("Unable to submit task: \(error.localizedDescription)")
      }
    }
    */
    
    //MARK:--> Voip Push registration
    
    func voipRegistration() {
        let mainQueue = DispatchQueue.main
        let voipRegistry: PKPushRegistry = PKPushRegistry(queue: mainQueue)
        voipRegistry.delegate = self
        voipRegistry.desiredPushTypes = [PKPushType.voIP]
    }
    
    //MARK:: Force Update
    func forceUpdateApp() {
        //Siren.shared.rulesManager(globalRules: .critical)
        let siren = Siren.shared
        siren.rulesManager = RulesManager(globalRules: .critical)
        
        siren.wail()
       /* siren.wail(performCheck: .onForeground) { (results, error) in
           // print(results!)
        }
        */
    }
    
    //MARK:: FireBase Messaging
    func firebaseConfig(application: UIApplication) {
//        FirebaseApp.configure()
//        Messaging.messaging().subscribe(toTopic: "test") { error in
//            print("Subscribed to test topic")
//        }
//        registerForPushNotifications(application: application)
    }
    
    func initializeZoom() {
        let initParams = ZoomVideoSDKInitParams()
        initParams.domain = "zoom.us"
        initParams.enableLog = true

        let sdkInitReturnStatus = ZoomVideoSDK.shareInstance()?.initialize(initParams)
        switch sdkInitReturnStatus {
            case .Errors_Success:
                print("SDK initialized successfully")
            default:
                if let error = sdkInitReturnStatus {
                    print("SDK failed to initialize: (error)")
            }
        }
    }
    
    func LoginViewController() {
        let loginVC = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "FirstViewController")
        let nav = UINavigationController(rootViewController: loginVC)
        nav.navigationBar.isHidden = true;
        nav.navigationBar.barStyle = .default
        self.window!.rootViewController = nav
    }
    
    func menteeRootViewController(){
        let homeController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "TabBarViewController")
        self.window!.rootViewController = homeController
    }
 
    func mentorRootViewController(){
        let homeController = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorTabBarViewController")
        self.window!.rootViewController = homeController
    }
    
    func createRootViewController() {
        let loginMode = UserDefaults.standard.value(forKey: "loginMode") as? String
        
        if let checkLoginMode = loginMode {
            if checkLoginMode == "Mentee" {
                self.menteeRootViewController()
            } else {
                self.mentorRootViewController()
            }
        } else {
            LoginViewController()
        }
    }
    
    func testNavigation() {
        let homeController = UIStoryboard(name: "Chat", bundle: nil).instantiateViewController(withIdentifier: "ChatVC")
        self.window!.rootViewController = homeController
    }
    
    func applicationWillResignActive(_ application: UIApplication) {
        // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
        // Use this method to pause ongoing tasks, disable timers, and invalidate graphics rendering callbacks. Games should use this method to pause the game.
        NotificationCenter.default.post(name: Notification.Name("UserRoomExitFromChat"), object: nil)
    }

    func applicationDidEnterBackground(_ application: UIApplication) {
        // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later.
        // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
        
        NotificationCenter.default.post(name: Notification.Name("UserRoomExitFromChat"), object: nil)
    }


    func applicationWillEnterForeground(_ application: UIApplication) {
        UIApplication.shared.applicationIconBadgeNumber = 0
        // Called as part of the transition from the background to the active state; here you can undo many of the changes made on entering the background.
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
        self.checkAppVersion()
    }

    func applicationWillTerminate(_ application: UIApplication) {
        // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
        // Saves changes in the application's managed object context before the application terminates.
        NotificationCenter.default.post(name: .terminateApp, object: nil)
        NotificationCenter.default.post(name: Notification.Name("UserRoomExitFromChat"), object: nil)
        self.saveContext()
    }

    // MARK: - Core Data stack

    lazy var persistentContainer: NSPersistentContainer = {
        /*
         The persistent container for the application. This implementation
         creates and returns a container, having loaded the store for the
         application to it. This property is optional since there are legitimate
         error conditions that could cause the creation of the store to fail.
        */
        let container = NSPersistentContainer(name: "TakeStockInChildren")
        container.loadPersistentStores(completionHandler: { (storeDescription, error) in
            if let error = error as NSError? {
                // Replace this implementation with code to handle the error appropriately.
                // fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                 
                /*
                 Typical reasons for an error here include:
                 * The parent directory does not exist, cannot be created, or disallows writing.
                 * The persistent store is not accessible, due to permissions or data protection when the device is locked.
                 * The device is out of space.
                 * The store could not be migrated to the current model version.
                 Check the error message to determine what the actual problem was.
                 */
                fatalError("Unresolved error \(error), \(error.userInfo)")
            }
        })
        return container
    }()

    // MARK: - Core Data Saving support

    func saveContext () {
        let context = persistentContainer.viewContext
         if context.hasChanges {
            do {
                try context.save()
            } catch {
                //Replace this implementation with code to handle the error appropriately.
                //fatalError() causes the application to generate a crash log and terminate. You should not use this function in a shipping application, although it may be useful during development.
                let nserror = error as NSError
                fatalError("Unresolved error \(nserror), \(nserror.userInfo)")
            }
        }
    }
}

extension AppDelegate: CLLocationManagerDelegate {
    func determineMyCurrentLocation() {
        locationManager = CLLocationManager()
        locationManager.delegate = self
        locationManager.desiredAccuracy = kCLLocationAccuracyBest
        locationManager.requestAlwaysAuthorization()  //.requestAlwaysAuthorization()
        
        if CLLocationManager.locationServicesEnabled() {
            locationManager.startUpdatingLocation()
            //locationManager.startUpdatingHeading()
        }
    }
    
    func locationManager(_ manager: CLLocationManager, didUpdateLocations locations: [CLLocation]) {
        let userLocation:CLLocation = locations[0] as CLLocation
        
        // Call stopUpdatingLocation() to stop listening for location updates,
        // other wise this function will be called every time when user location changes.
        
        // manager.stopUpdatingLocation()
        UserDefaults.standard.set((userLocation.coordinate.latitude), forKey: "latitude")
        UserDefaults.standard.set((userLocation.coordinate.longitude), forKey: "longitude")
        
        let lat :String = String(describing: userLocation.coordinate.latitude)
        
//        Common().showAlertView(title: "update", msg: lat, controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
//
//        })
          //  print("user current latitude = \(userLocation.coordinate.latitude)")
           // print("user current longitude = \(userLocation.coordinate.longitude)")
    }
    
    func locationManager(_ manager: CLLocationManager, didFailWithError error: Error) {
        print("Error \(error)")
    }
}

//MARK: Firebase push notification
extension AppDelegate : UNUserNotificationCenterDelegate {
    
    // MARK: - Get Current View Controller
    
    public func getCurrentViewController() -> UIViewController? {
        if let rootViewController = self.tabBarController{
            if let viewControllers = rootViewController.viewControllers {
                if let navVC = viewControllers[rootViewController.selectedIndex] as? UINavigationController {
                    if let visibleVC = navVC.visibleViewController {
                        return visibleVC
                    }
                }
            }
        }
        return nil
    }
    
    
    
    
    //get error here
    func application(_ application: UIApplication, didFailToRegisterForRemoteNotificationsWithError error:
                     Error) {
        print("Registration failed!",error.localizedDescription)
    }
    
    //get Notification Here below ios 10
    func application(_ application: UIApplication, didReceiveRemoteNotification data: [AnyHashable : Any]) {
        // Print notification payload data
        print("Push notification received: \(String(describing: data["aps"]))")
        
        /*let aps = data["aps"] as? NSDictionary
         if aps != nil {
         if String(describing: aps!["category"]!) == "chat_activity" {
         let currentTab = menuVC.getCurrentTabController() as UITabBarController
         let currentNav = currentTab.selectedViewController as? UINavigationController
         let storyBoardChat: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
         let chatListVC = storyBoardChat.instantiateViewController(withIdentifier: "ChatListVC") as! ChatListVC
         chatListVC.isFromMenu = true
         currentNav?.pushViewController(chatListVC, animated: true)
         } else {
         let homeController = UIStoryboard(name: "Main", bundle: nil).instantiateViewController(withIdentifier: "TabBarViewController")
         UserDefaults.standard.set(true, forKey: "isHome")
         UserDefaults.standard.set(false, forKey: "isSearch")
         UserDefaults.standard.set(false, forKey: "isFilter")
         UserDefaults.standard.set(true, forKey: "isFirstTime")
         self.window!.rootViewController = homeController
         }
         }*/
        
        switch application.applicationState {
        case .active:
            // Pass push notification payload to the shared model
            print("Application is open, do not override")
        case .inactive, .background:
            break
        default:
            print("unrecognized application state")
        }
    }
    
    func application(_ application: UIApplication, didReceiveRemoteNotification userInfo: [AnyHashable: Any],
                     fetchCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void) {
        // If you are receiving a notification message while your app is in the background,
        // this callback will not be fired till the user taps on the notification launching the application.
        // TODO: Handle data of notification
        
        // With swizzling disabled you must let Messaging know about the message, for Analytics
        // Messaging.messaging().appDidReceiveMessage(userInfo)
        
        // Print message ID.
        print("Remote called")
        
        // Print full message.
        print(userInfo)
        
        completionHandler(UIBackgroundFetchResult.newData)
    }
    
    //This is the two delegate method to get the notification in iOS 10..
    //First for foreground
    @available(iOS 10.0, *)
    internal func userNotificationCenter(_ center: UNUserNotificationCenter, willPresent notification: UNNotification, withCompletionHandler completionHandler: @escaping (_ options:UNNotificationPresentationOptions) -> Void) {
        
        print("Handle push from foreground")
        // custom code to handle push while app is in the foreground
        let userInfo = notification.request.content.userInfo
        
        var type_Video: String = ""
        var accesToken: String = ""
        var sendername: String = ""
        var uniquename: String = ""
        var roomSId: String = ""
        var remaintime: String = ""
        
        var dictionary_incomingcall: NSDictionary?
        let videochatdata = userInfo["meeting_data"]  as? String
        if let data = videochatdata?.data(using: String.Encoding.utf8) {
            print("after utf 8======",data)
            do {
                dictionary_incomingcall = try JSONSerialization.jsonObject(with: data, options: []) as? [String:AnyObject] as NSDictionary?
                type_Video = dictionary_incomingcall?["type"] as? String ?? ""
                accesToken = dictionary_incomingcall?["receiver_accesstoken"] as? String ?? ""
                sendername = dictionary_incomingcall?["sender_name"] as? String ?? ""
                uniquename = dictionary_incomingcall?["unique_name"] as? String ?? ""
                roomSId = dictionary_incomingcall?["room_sid"] as? String ?? ""
                remaintime = dictionary_incomingcall?["remaining_time"] as? String ?? ""
                
                CommonValue.shared.SRaccestoken = accesToken
                CommonValue.shared.roomname = uniquename
                CommonValue.shared.senderName = sendername
                CommonValue.shared.roomSid = roomSId
                CommonValue.shared.remainingTime = remaintime
                
                print("dicvideo",dictionary_incomingcall!)
                print("Overall data",CommonValue.shared.SRaccestoken,CommonValue.shared.roomname,CommonValue.shared.senderName,CommonValue.shared.roomSid,CommonValue.shared.remainingTime)
                
            }
            catch {
                print("Error",error.localizedDescription)
            }
        }
        
        let applicationState = UIApplication.shared.applicationState
        
        switch applicationState {
        case .background,.inactive:
            completionHandler([.alert, .sound, .badge])
        case .active:
            
            if type_Video == "video_chat" {
                rediectCallReceive()
            }
            else if type_Video == "miss_call"{
                pushtoincoming()
            }
            else if type_Video == "message_center" {
                completionHandler([.alert, .sound, .badge])
            }
            else {
                if let currentVC = UIViewController.topMostViewController() as? TwilloTextChatViewController{
                    print("Not alert")
                }
                else {
                    completionHandler([.alert, .sound, .badge])
                }
                
            }
            
        default:
            completionHandler([.alert, .sound, .badge])
        }
        
        /*
         switch applicationState {
         case .background,.inactive:
         break
         case .active:
         rediectCallReceive()
         default:
         print("Wrong")
         }
         */
        
        // Print full message.
        print(userInfo)
        print("will Present function called")
        
        // Change this to your preferred presentation option
        //completionHandler(UNNotificationPresentationOptions.alert)
    }
    
    /*
     private func userNotificationCenter(_ center: UNUserNotificationCenter, willPresent notification: UNNotification, withCompletionHandler completionHandler: @escaping (_ options:UNNotificationPresentationOptions) -> Void) {
     print("Handle push from foreground")
     // custom code to handle push while app is in the foreground
     print("\(notification.request.content.userInfo)")
     
     NotificationCenter.default.post(name: Notification.Name("newChatMessageAlert"), object: nil)
     
     let userInfo = notification.request.content.userInfo
     UIApplication.shared.applicationIconBadgeNumber = 0
     print(userInfo)
     
     if let aps = userInfo["aps"] as? NSDictionary {
     if let alert = aps["alert"] as? NSDictionary {
     if let message = alert["title"] as? NSString {
     self.setupAnnouncementAndDisplayWhwnAppInForgroung(title: message as String)
     }
     } else if (aps["alert"] as? NSString) != nil {
     
     }
     }
     // Change this to your preferred presentation option
     completionHandler(UNNotificationPresentationOptions.alert)
     }
     
     func convertToDictionary(text: String) -> [String: Any]? {
     if let data = text.data(using: .utf8) {
     do {
     return try JSONSerialization.jsonObject(with: data, options: []) as? [String: Any]
     } catch {
     print(error.localizedDescription)
     }
     }
     return nil
     }
     
     func setupAnnouncementAndDisplayWhwnAppInForgroung(title: String) {
     let alertController = UIAlertController(title: "Announcement", message:title , preferredStyle: .alert)
     let okAction = UIAlertAction(title: "OK", style: UIAlertAction.Style.default) {
     UIAlertAction in
     }
     alertController.addAction(okAction)
     self.window?.rootViewController?.present(alertController, animated: true, completion: nil)
     }
     */
    
    
    func pushtoincoming() {
        // NotificationCenter.default.post(name: .disconnectcall, object: nil)
        provider.reportCall(with: CommonValue.shared.uid, endedAt: nil, reason: .remoteEnded)
    }
    
    //Second for background and close
    @available(iOS 10.0, *)
    func userNotificationCenter(_ center: UNUserNotificationCenter, didReceive response:UNNotificationResponse, withCompletionHandler completionHandler: @escaping () -> Void) {
        print("did receive notification called")
        let userInfo = response.notification.request.content.userInfo
        let applicationState = UIApplication.shared.applicationState
        UIApplication.shared.applicationIconBadgeNumber = 0
        print(userInfo)
        // let chatUserTye: String?
        // let senderId: Int?
        // let firebaseId: String?
        
        switch applicationState {
        case .background,.inactive:
            print("inactive")
            var dictionary_video: NSDictionary?
            let videochatdata = userInfo["meeting_data"]  as? String
            if let data = videochatdata?.data(using: String.Encoding.utf8) {
                print("after utf 8 background video call data",data)
                do {
                    dictionary_video = try JSONSerialization.jsonObject(with: data, options: []) as? [String:AnyObject] as NSDictionary?
                    print("dic",dictionary_video!)
                }
                catch {
                    print("Error",error.localizedDescription)
                }
            }
            
            
            let type_Video = dictionary_video?["type"] as? String
            let receiver_Accesstoken = dictionary_video?["receiver_accesstoken"] as? String
            let sender_name = dictionary_video?["sender_name"] as? String
            let unique_name = dictionary_video?["unique_name"] as? String
            let remaintime = dictionary_video?["remaining_time"] as? String
            let roomSId = dictionary_video?["room_sid"] as? String ?? ""
            if type_Video == "video_chat" {
                CommonValue.shared.SRaccestoken = receiver_Accesstoken ?? ""
                CommonValue.shared.roomname = unique_name ?? ""
                CommonValue.shared.remainingTime = remaintime ?? ""
                CommonValue.shared.roomSid = roomSId
                print("did receive daata",CommonValue.shared.SRaccestoken,CommonValue.shared.roomname,CommonValue.shared.remainingTime,CommonValue.shared.roomSid)
                let chatSB: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                if  let videovc = chatSB.instantiateViewController(withIdentifier: "VideoViewController") as? VideoViewController,
                    let tabBarController = self.window?.rootViewController as? UITabBarController,
                    let navController = tabBarController.selectedViewController as? UINavigationController {
                    // tabBarController.tabBar.isHidden = true
                    navController.pushViewController(videovc, animated: true)
                }
            }
            
            
            
        case .active:
            break
            
            
        default:
            print("Wrong")
        }
        
        var dictonary:NSDictionary?
        let dataAnnouncement = userInfo["announcement_data"]  as? String
        if let data = dataAnnouncement?.data(using: String.Encoding.utf8) {
            print("after utf 8",data)
            do {
                dictonary = try JSONSerialization.jsonObject(with: data, options: []) as? [String:AnyObject] as NSDictionary?
            }
            catch {
                print("Error",error.localizedDescription)
            }
        }
        
        print("dataAnnouncement",dictonary ?? [:])
        let type = dictonary?["type"] as? String ?? ""
        let fromwhere = dictonary?["from_where"] as? String ?? ""
        print("chat type",type)
        print("from where",fromwhere)
        if type == "chat" {
            if fromwhere == "mentor" {
                print("mentor Current VC")
                let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Main", bundle: nil)
                if  let conversationVC = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MontorListFromMenteeVC") as? MontorListFromMenteeVC,
                    let tabBarController = self.window?.rootViewController as? UITabBarController,
                    let navController = tabBarController.selectedViewController as? UINavigationController {
                    navController.delegate = self
                    navController.pushViewController(conversationVC, animated: true)
                }
            }
            else if fromwhere == "mentee" {
                let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
                if  let conversationVC = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "MenteeListFromMontorVC") as? MenteeListFromMontorVC,
                    let tabBarController = self.window?.rootViewController as? UITabBarController,
                    let navController = tabBarController.selectedViewController as? UINavigationController {
                    navController.delegate = self
                    navController.pushViewController(conversationVC, animated: true)
                }
            }
            else {
                print("Not redirect")
            }
            // let data = userInfo["announcement_data"]  as? String
            // print("data---",data)
            /*
             var dictonary:NSDictionary?
             
             if let data = data?.data(using: String.Encoding.utf8) {
             
             do {
             dictonary = try JSONSerialization.jsonObject(with: data, options: []) as? [String:AnyObject] as NSDictionary?
             chatUserTye = dictonary?["from_where"] as? String ?? ""
             firebaseId = dictonary?["device_token"] as? String ?? ""
             senderId = dictonary?["sender_id"] as? Int ?? -1
             if chatUserTye == "mentor" {
             
             if let currentvc = getCurrentViewController() {
             if currentvc is ChatVC {
             print("Current VC")
             }
             else {
             
             
             let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
             let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
             newViewController.strIamfrom = ""//"staff"
             newViewController.senderIDForMentor = String(describing: senderId)
             newViewController.firebaseKeyReceiver = firebaseId
             newViewController.loginMenteeButMentorDicData = dictonary ?? [:]
             currentvc.navigationController?.pushViewController(newViewController, animated: true)
             
             
             
             }
             }
             
             }
             else if chatUserTye == "mentee"
             {
             
             if let currentvc = getCurrentViewController() {
             if currentvc is ChatVC {
             print("CurrentVC")
             }
             else {
             
             
             let MentorMeetingtoryBoard: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
             let newViewController = MentorMeetingtoryBoard.instantiateViewController(withIdentifier: "ChatVC") as! ChatVC
             newViewController.strIamfrom = "mentee"
             newViewController.loginMenteeButMentorDicData = dictonary ?? [:]
             newViewController.firebaseKeyReceiver = firebaseId
             currentvc.navigationController?.pushViewController(newViewController, animated: true)
             
             
             }
             }
             
             }
             else if chatUserTye == "staff" {
             
             if let currentvc = getCurrentViewController() {
             if currentvc is ChatVC {
             
             }
             else {
             
             
             }
             }
             
             }
             
             
             } catch let error as NSError {
             print(error)
             }
             }
             */
        }
        
        
        
        
        
        //if let aps = userInfo["announcement_data"] as? NSDictionary {
        //self.setupAnnouncementAndDisplay(dict: aps as NSDictionary)
        //}
        
        print("\(response.notification.request.content.userInfo)")
        completionHandler()
    }
    
    
    func rediectCallReceive() {
        let chatSB: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
        if  let incvc = chatSB.instantiateViewController(withIdentifier: "IncomgcallViewController") as? IncomgcallViewController,
            let tabBarController = self.window?.rootViewController as? UITabBarController,
            let navController = tabBarController.selectedViewController as? UINavigationController {
            navController.pushViewController(incvc, animated: true)
        }
    }
    
    
    
    // The callback to handle data message received via FCM for devices running iOS 10 or above.
    //    func application(received remoteMessage: MessagingRemoteMessage) {
    //        print(remoteMessage.appData)
    //    }
    
    
    
    
    func application(application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: NSData) {
        Messaging.messaging().apnsToken = deviceToken as Data
        let token = deviceToken.map { String(format: "%02.2hhx", $0) }.joined()
        print(token)
        print("Successfully register for remote notiifcation")
    }
    
    
    func registerForPushNotifications(application: UIApplication) {
        
        Messaging.messaging().delegate = self
        if #available(iOS 10.0, *) {
            // For iOS 10 display notification (sent via APNS)
            UNUserNotificationCenter.current().delegate = self
            
            let authOptions: UNAuthorizationOptions = [.alert, .badge, .sound]
            UNUserNotificationCenter.current().requestAuthorization(
                options: authOptions) { [weak self] (_, error) in
                    DispatchQueue.main.async {
                        application.registerForRemoteNotifications()
                        self?.voipRegistration()
                    }
                }
        } else {
            let settings: UIUserNotificationSettings =
            UIUserNotificationSettings(types: [.alert, .badge, .sound], categories: nil)
            application.registerUserNotificationSettings(settings)
        }
        
    }
    
    func getNotificationSettings() {
        UNUserNotificationCenter.current().getNotificationSettings { (settings) in
            print("Notification settings: \(settings)")
            guard settings.authorizationStatus == .authorized else { return }
            
            DispatchQueue.main.async(execute: {
                UIApplication.shared.registerForRemoteNotifications()
            })
        }
    }
    
    func application(_ application: UIApplication, didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data) {
        print("did register remote notifications called")
        let deviceTokenString = deviceToken.hexString
        print("device token",deviceTokenString)
        //let tokenParts = deviceToken.map { data -> String in
        //    return String(format: "%02.2hhx", data)
        //}
        
        //let token = tokenParts.joined()
        //  print("Device Token: \(token)")
        Messaging.messaging().apnsToken = deviceToken as Data
    }
    
    
    /*func registerFirebaseToken() {
     if let token = InstanceID.instanceID().token() {
     print("Device Token : \(token)")
     UserDefaults.standard.set(token, forKey: "firebase_token")
     }
     Messaging.messaging().shouldEstablishDirectChannel = true
     }
     
     
     func unregisterFirebaseToken(completion: @escaping (Bool)->()) {
     // Delete the Firebase instance ID
     InstanceID.instanceID().deleteID { (error) in
     if error != nil{
     print("FIREBASE: ", error.debugDescription);
     completion(false)
     } else {
     print("FIREBASE: Token Deleted");
     completion(true)
     }
     }
     }*/
    
}

extension AppDelegate : MessagingDelegate {
    // [START refresh_token]
    func messaging(_ messaging: Messaging, didReceiveRegistrationToken fcmToken: String?) {
        UNUserNotificationCenter.current().getNotificationSettings(completionHandler: { permission in
            switch permission.authorizationStatus  {
            case .authorized:
                print("User granted permission for notification")
                print("Firebase registration token: \(fcmToken ?? "")")
                UserDefaults.standard.set(fcmToken, forKey: "firebase_token")
            case .denied:
                print("User denied notification permission")
            case .notDetermined:
                print("Notification permission haven't been asked yet")
            case .provisional:
                // @available(iOS 12.0, *)
                print("The application is authorized to post non-interruptive user notifications.")
            case .ephemeral:
                // @available(iOS 14.0, *)
                print("The application is temporarily authorized to post notifications. Only available to app clips.")
            @unknown default:
                print("Unknow Status")
            }
        })
        Messaging.messaging().subscribe(toTopic: "/topics/nutriewell_live")
//        Messaging.messaging().shouldEstablishDirectChannel = true
        // TODO: If necessary send token to application server.
        // Note: This callback is fired at each app startup and whenever a new token is generated.
    }
    
    // [END refresh_token]
    // [START ios_10_data_message]
    // Receive data messages on iOS 10+ directly from FCM (bypassing APNs) when the app is in the foreground.
    // To enable direct data messages, you can set Messaging.messaging().shouldEstablishDirectChannel to true.
//    func messaging(_ messaging: Messaging, didReceive remoteMessage: MessagingRemoteMessage) {
//        print("message remote",remoteMessage.appData)
     //   let stringDetails = String(describing: remoteMessage.appData["meeting_data"]!)
        //remoteMessage.appData["announcement_data"] as! String
        // let dictDetails = convertToDictionary(text: stringDetails)
        //scheduleNotification(notificationType: "You have a session in next 30 minutes. Remember to log it after completion.")
      //  print(stringDetails)
//    }
    // [END ios_10_data_message]
}

extension AppDelegate {
    
    func handleEvent(forRegion region: CLRegion!) {
        
        if UIApplication.shared.applicationState == .active {
            // show popup
            DispatchQueue.main.async {
                Common().showAlertView(title: "Session Notification", msg: "You make sure to session log", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
                    self.navToSessionPage()
                })
            }
        } else {
            scheduleNotification(notificationType: "Make sure for the session log")
            
            // Otherwise present a local notification
            //            let notification = UILocalNotification()
            //            notification.alertBody = "Make sure for the session log"
            //            notification.soundName = "Default"
            //            UIApplication.shared.presentLocalNotificationNow(notification)
            
        }
    }
    
    
    func locationManager(_ manager: CLLocationManager, didEnterRegion region: CLRegion) {
        if region is CLCircularRegion {
            print("Helloo")
            handleEvent(forRegion: region)
        }
    }
    
    func locationManager(_ manager: CLLocationManager, didExitRegion region: CLRegion) {
        if region is CLCircularRegion {
            handleEvent(forRegion: region)
        }
    }
}

extension AppDelegate {
    
    func checkAppVersion() {
        ApiManager.sharedInstance.getVersionCode { dict in
            let jsonDic : NSDictionary = (dict["data"] as? NSDictionary)!
            let appVersionDict : NSDictionary = (jsonDic["app_version"] as? NSDictionary)!
            let platform = appVersionDict["platform"] as? String
            let version = appVersionDict["version_code"] as? Double ?? 0
            let currentVersion = Double(UIApplication.release)
            if platform == "ios" && currentVersion < version {
                DispatchQueue.main.async {
                    let alert = UIAlertController(title: "Update Available", message: "A new version available. Please update now.", preferredStyle: UIAlertController.Style.alert)
                    alert.addAction(UIAlertAction(title: "Update from App Store", style: UIAlertAction.Style.default, handler: { action in
                        let url = URL(string: "https://apps.apple.com/in/app/tsic/id1476056526")!
                        UIApplication.shared.open(url)
                    }))
                    let topController = self.window?.rootViewController?.view.topMostController()
                    topController?.present(alert, animated: true, completion: nil)
                }
            }
            print("App Version Success:\(dict)")
        } onFailure: { errorDict in
            print("App version Error Dict: \(errorDict)")
        }

    }
}

extension AppDelegate {
    
    //Enable Notification center
    func enableLocalNotificationCenter() {
        
        let options: UNAuthorizationOptions = [.alert, .sound, .badge]
        
        notificationCenter.requestAuthorization(options: options) {
            (didAllow, error) in
            if !didAllow {
                
                print("User has declined notifications")
            }
        }
        
    }
    
    func isUserAllowForLocalNotification(){
        notificationCenter.getNotificationSettings {(settings) in
            if settings.authorizationStatus != .authorized {
                self.enableNotification()
            }
        }
    }
    
    func enableNotification(){
        DispatchQueue.main.async {
            Common().showAlertView(title: "Alert!", msg: "Please allow the notification service so i can send you push notification.", controller: (APP_DELEGATE.window?.rootViewController)!, okClicked: {
            })
        }
    }
    func navToSessionPage(){
        let homeController = UIStoryboard(name: "Mentor", bundle: nil).instantiateViewController(withIdentifier: "MentorTabBarViewController") as! MentorTabBarViewController
        homeController.selectedIndex = 1
        self.window!.rootViewController = homeController
    }
    
    func scheduleNotification(notificationType: String) {
        // 1
        print("caledd")
        let content = UNMutableNotificationContent()
        content.title = notificationType
        // content.subtitle = "After complete the session , make sure to session log"
        // content.body = "Notification triggered"
        
        // 2
        let imageName = "logo"
        guard let imageURL = Bundle.main.url(forResource: imageName, withExtension: "png") else { return }
        
        let attachment = try! UNNotificationAttachment(identifier: imageName, url: imageURL, options: .none)
        
        content.attachments = [attachment]
        
        // 3
        let trigger = UNTimeIntervalNotificationTrigger(timeInterval: 1, repeats: false)
        let request = UNNotificationRequest(identifier: "notification.id.01", content: content, trigger: trigger)
        
        // 4
        UNUserNotificationCenter.current().add(request, withCompletionHandler: nil)
        
    }
}


extension AppDelegate: PKPushRegistryDelegate {
    func pushRegistry(_ registry: PKPushRegistry, didUpdate pushCredentials: PKPushCredentials, for type: PKPushType) {
        print("credential token",pushCredentials.token.hexString)
        print("push type",type.rawValue)
        let deviceToken = pushCredentials.token.map{ String(format: "%02x", $0)}.joined()
        UserDefaults.standard.set(deviceToken, forKey: "voip_token")
        print("deviceToken push registry---",deviceToken)
        
        Messaging.messaging().token { [weak self] token, error in
          if let error = error {
              print("Error fetching FCM registration token: \(error)")
          } else if let token = token {
              print("FCM registration token: \(token)")
              print("Firebase registration token: \(token)")
              UserDefaults.standard.set(token, forKey: "firebase_token")
              UNUserNotificationCenter.current().requestAuthorization(
                options: [.alert, .badge, .sound]) { [weak self] (_, error) in
                    Messaging.messaging().delegate = self
                    DispatchQueue.main.async {
                        UIApplication.shared.registerForRemoteNotifications()
                    }
                }
          }
        }
    }
    
    func pushRegistry(_ registry: PKPushRegistry, didInvalidatePushTokenFor type: PKPushType) {
        print("invalud token")
    }

    func pushRegistry(_ registry: PKPushRegistry, didReceiveIncomingPushWith payload: PKPushPayload, for type: PKPushType, completion: @escaping () -> Void) {
       
        var type_Video: String = ""
        var accesToken: String = ""
        var sendername: String = ""
        var uniquename: String = ""
        var roomSId: String = ""
        var remaintime: String = ""
        var createat: String = ""
        
        //Web needed credential
        var senderId: String = ""
        var senderType: String = ""
        var receiverId: String = ""
        var receiverType: String = ""
        
        var arrTemp = [AnyHashable: Any]()
        arrTemp = payload.dictionaryPayload
        
        let dict : Dictionary <String, AnyObject> = arrTemp["aps"] as! Dictionary<String, AnyObject>
        let vData : Dictionary <String, AnyObject> = dict["data"] as? Dictionary <String, AnyObject> ?? [:]
        let meetingData : Dictionary<String, AnyObject> = vData["meeting_data"] as? Dictionary <String, AnyObject> ?? [:]
        
        
        type_Video = meetingData["type"] as? String ?? ""
        accesToken = meetingData["receiver_accesstoken"] as? String ?? ""
        sendername = meetingData["sender_name"] as? String ?? ""
        uniquename = meetingData["unique_name"] as? String ?? ""
        roomSId = meetingData["room_sid"] as? String ?? ""
        remaintime = meetingData["remaining_time"] as? String ?? ""
        createat = meetingData["created_at"] as? String ?? ""
        senderId = meetingData["sender_id"] as? String ?? ""
        senderType = meetingData["sender_type"] as? String ?? ""
        receiverId = meetingData["receiver_id"] as? String ?? ""
        receiverType = meetingData["receiver_type"] as? String ?? ""
        CommonValue.shared.SRaccestoken = accesToken
        CommonValue.shared.roomname = uniquename
        CommonValue.shared.senderName = sendername
        CommonValue.shared.roomSid = roomSId
        CommonValue.shared.remainingTime = remaintime
        CommonValue.shared.createAt = createat
        CommonValue.shared.receiverId = receiverId
        CommonValue.shared.receiverType = receiverType
        CommonValue.shared.senderId = senderId
        CommonValue.shared.senderType = senderType
        
        print("dicvideo voip",meetingData)
        os_log("Here at after push")
       // print("Overall data voip",CommonValue.shared.SRaccestoken,CommonValue.shared.roomname,CommonValue.shared.senderName,CommonValue.shared.roomSid,CommonValue.shared.remainingTime)
       // print("type video",type_Video)
        if type_Video == "miss_call" {
             
            denyCall()
        }
        else {
            provider.setDelegate(self, queue: nil)
            let update = CXCallUpdate()
            update.hasVideo = true
            update.supportsHolding = false
            update.remoteHandle = CXHandle(type: .generic, value: "Calling...")
            let uid = UUID()
            CommonValue.shared.uid = uid
            print("uid",CommonValue.shared.uid)
            os_log("Reported incoming call")
            provider.reportNewIncomingCall(with: uid, update: update, completion: { error in
                completion()
            })
//            incomingCall()
        }
        
       // print("again",payload.dictionaryPayload)
    }
    
    
}


extension AppDelegate: CXProviderDelegate {
    
    
    func denyCall() {
        print("uid----deny",CommonValue.shared.uid)
        os_log("Reported missed call")
        provider.reportCall(with: CommonValue.shared.uid, endedAt: nil, reason: .remoteEnded)
        NotificationCenter.default.post(name: .notanswered, object: nil)
        CommonValue.shared.roomname = ""
        CommonValue.shared.SRaccestoken = ""
        CommonValue.shared.senderName = ""
        CommonValue.shared.roomSid = ""
        CommonValue.shared.remainingTime = ""
        CommonValue.shared.createAt = ""
        
        CommonValue.shared.receiverId = ""
        CommonValue.shared.receiverType = ""
        CommonValue.shared.senderId = ""
        CommonValue.shared.senderType = ""
    }
    
    func incomingCall() {
        
        
    }
    
    func providerDidReset(_ provider: CXProvider) {
        print("provider did reset")
    }
    
    func provider(_ provider: CXProvider, perform action: CXAnswerCallAction) {
        action.fulfill()
        
        DispatchQueue.main.asyncAfter(deadline: .now() + 0.5) {
            let keyWindow = UIApplication.shared.windows.first { $0.isKeyWindow }
            let chatSB: UIStoryboard = UIStoryboard(name: "Chat", bundle: nil)
            if  let videovc = chatSB.instantiateViewController(withIdentifier: "VideoViewController") as? VideoViewController,
                let tabBarController = keyWindow?.rootViewController as? UITabBarController,
                let navController = tabBarController.selectedViewController as? UINavigationController {
                // tabBarController.tabBar.isHidden = true
                navController.pushViewController(videovc, animated: true)
            }
        }
    }
    
    
    func provider(_ provider: CXProvider, perform action: CXEndCallAction) {
        print("provider did reset----")
        provider.reportCall(with: CommonValue.shared.uid, endedAt: nil, reason: .remoteEnded)
//        disconnectVideocall()
        disconnectDenyCall()
        action.fulfill()
    }
    
    func disconnectVideocall() {
        
        var parameter: [String: String] = [:]
        parameter["room_sid"] = CommonValue.shared.roomSid
        parameter["disconnect_type"] = "miss_call"
        parameter["unique_name"] = CommonValue.shared.roomname
        print("roomsid____",CommonValue.shared.roomSid)
        MentorApiManager().disconnectVideoCall(parameter: parameter) { (json) in
            print("access called-------",json)
            let status = json["status"] as! Bool
            if status == true {
                DispatchQueue.main.async {
                    CommonValue.shared.roomname = ""
                    CommonValue.shared.SRaccestoken = ""
                    CommonValue.shared.senderName = ""
                    CommonValue.shared.roomSid = ""
                    CommonValue.shared.remainingTime = ""
                    CommonValue.shared.createAt = ""
                    
                    CommonValue.shared.receiverId = ""
                    CommonValue.shared.receiverType = ""
                    CommonValue.shared.senderId = ""
                    CommonValue.shared.senderType = ""
                }
            } else {
                print("Error discoonect")
            }
        }
        
    }
    
    func disconnectDenyCall() {
        
        var parameter: [String: String] = [:]
        let dicUserDetails = UserDefaults.standard.value(forKey: "userDetails") as! NSDictionary
        parameter["unique_name"] = CommonValue.shared.roomname
        parameter["denied_by"] = "\(dicUserDetails["id"] as? Int ?? 0)"
        parameter["denied_by_type"] = UserDefaults.standard.value(forKey: "loginMode") as? String ?? ""
        MentorApiManager().deniedVideoCall(parameter: parameter) { (json) in
            
            let status = json["status"] as! Bool
            if status == true {
                DispatchQueue.main.async {
                    CommonValue.shared.roomname = ""
                    CommonValue.shared.SRaccestoken = ""
                    CommonValue.shared.senderName = ""
                    CommonValue.shared.roomSid = ""
                    CommonValue.shared.remainingTime = ""
                    CommonValue.shared.createAt = ""
                    
                    CommonValue.shared.receiverId = ""
                    CommonValue.shared.receiverType = ""
                    CommonValue.shared.senderId = ""
                    CommonValue.shared.senderType = ""
                }
            } else {
                print("Error discoonect")
            }
        }
        
    }
    
    
}





extension Data {
    var hexString: String {
        let hexString = map { String(format: "%02.2hhx", $0) }.joined()
        return hexString
    }
}

extension Notification.Name {
    static let disconnectcall = Notification.Name("disconnect")
    static let callEnd = Notification.Name("callEnd")
    static let terminateApp =  Notification.Name("terminate")
    static let notanswered = Notification.Name("notanswer")
}



extension UIViewController {
    static func topMostViewController() -> UIViewController? {
        if #available(iOS 13.0, *) {
            let keyWindow = UIApplication.shared.windows.filter {$0.isKeyWindow}.first
            return keyWindow?.rootViewController?.topMostViewController()
        }
        
        return UIApplication.shared.keyWindow?.rootViewController?.topMostViewController()
    }
    
    func topMostViewController() -> UIViewController? {
        if let navigationController = self as? UINavigationController {
            return navigationController.topViewController?.topMostViewController()
        }
        else if let tabBarController = self as? UITabBarController {
            if let selectedViewController = tabBarController.selectedViewController {
                return selectedViewController.topMostViewController()
            }
            return tabBarController.topMostViewController()
        }
            
        else if let presentedViewController = self.presentedViewController {
            return presentedViewController.topMostViewController()
        }
        
        else {
            return self
        }
    }
}

extension OSLog {

    /// Logs the view cycles like viewDidLoad.
    static let notification = OSLog(subsystem: "TakeStockInChildren", category: "TakeStockInChildren")
}
