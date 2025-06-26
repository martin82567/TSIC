//
//  TakeStockInChildrenConstant.swift
//  TakeStockInChildren
//
//  Created by Aquarious Technology on 09/07/19.
//  Copyright © 2019 Aquarious Technology. All rights reserved.
//

import UIKit
import Foundation


struct PlatformUtils {
    static let isSimulator: Bool = {
        var isSim = false
        #if arch(i386) || arch(x86_64)
            isSim = true
        #endif
        return isSim
    }()
}
class TakeStockInChildrenConstant {
    static var mentorTimeZone: String = ""
    static var menteeTimeZone: String = ""
//    //set Url's 5August:-
////    PROD
    static let BaseURL = "https://tsicmentorapp.org/api/v1/"
    static let PREURL = "https://tsicmentorapp.org/api/"
    static let SocketURL = "https://tsicmentorapp.org:3000/"
    
    //UAT
//    static let BaseURL = "https://uat.tsicmentorapp.org/api/v1/"
//    static let PREURL = "https://uat.tsicmentorapp.org/api/"
//    static let SocketURL = "https://uat.tsicmentorapp.org:3000/"

    //dev staging
//    static let BaseURL = "https://test.tsicmentorapp.org/api/v1/"
//    static let PREURL = "https://test.tsicmentorapp.org/api/"
//    static let SocketURL = "https://test.tsicmentorapp.org:3000/"
    
    static let IMAGE_REPORT_BASEURL = "https://tsic.s3.us-east-2.amazonaws.com/"
//    static let HOST = "http://3.134.155.0/"
// //DEV
//       static let BaseURL = "https://mentorappdev.tsic.org/api/v1/"
//      static let PREURL = "https://mentorappdev.tsic.org/api/"
//      static let IMAGE_REPORT_BASEURL = "https://tsicdev.s3.us-east-2.amazonaws.com/"
//     static let HOST = "http://209.59.156.100/"
    
//MARK:-MenteeEndPoints
    static let PushnotifyTwillo = "\(PREURL)\("chat/send_nofication")"
    static let disclaimerUrl = "\(PREURL)\("disclaimer/index")"
    static let sidUpdateurl = "\(PREURL)\("chat/channel_id_update")"
    static let twiiloTokegeturl = "\(PREURL)\("chat/get_access_token")"
    static let UserImageBaseURL = "\(IMAGE_REPORT_BASEURL)\("userimage/")"
    static let reportBaseURL = "\(IMAGE_REPORT_BASEURL)\("report")"
    static let UserImageElearningBaseURL = "\(IMAGE_REPORT_BASEURL)\("e_learning/")"
    static let faqmentee = "\(BaseURL)\("faq/index")"
    static let faqmentor = "\(BaseURL)\("mentor/faq/index")"
    static let homemessagecentermentor = "\(BaseURL)\("mentor/message-center/index")"
    static let homemessagecentermentee = "\(BaseURL)\("message-center/index")"
    static let homemessagementor = "\(BaseURL)\("mentor/system-messaging/index")"
    static let homemessagementee = "\(BaseURL)\("system-messaging/index")"
    static let downloadFileURL = "\(IMAGE_REPORT_BASEURL)\("goaltask/")"
    static let MenteeImageBaseURL = "\(IMAGE_REPORT_BASEURL)\("userimage/")" // "http://209.59.156.100/~tsicdev/public/uploads/userimage/"
    
    //MARK:-MentorEndPoints
    static let MentorImageBaseURL = "\(IMAGE_REPORT_BASEURL)\("mentor_pic/")"
    //MARK:-StaffEndPoints
    static let StaffImageBaseURL = "\(IMAGE_REPORT_BASEURL)\("agency_pic/")"
    //MARK:-ReportImageEndPoints
    static let reportImage = "\(IMAGE_REPORT_BASEURL)\("report/")"
    //MARK:- Video Chat
    static let getAccestokenVideoChat = "\(PREURL)\("videochat/get_access_token")"
    static let initiateVideocall = "\(PREURL)\("videochat/initiate_chat")"
    static let roomGenerateVideoCall = "\(PREURL)\("videochat/generate_room")"
    static let disconnectCall = "\(PREURL)\("videochat/disconnect_room")"
    static let deniedCall = "\(PREURL)\("videochat/denied_call")"
    static let GooglePlacesAPI = "https://maps.googleapis.com/maps/api/place/autocomplete/json?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&input="
    static let Login             = "user/login"
    static let ForgotPassword    = "user/forgotpassword"
    static let ResetPassword     = "user/resetpassword"
    static let mentorupdatepassword    = "mentor/update_password"
    static let menteeupdatepassword     = "user/update_password"
    static let ChangePassword    = "user/changepassword"
    static let MenteeLogOut      = "user/logout"
    //TODO:- Profile Module
    static let UserDetails       = "user/userdetails"
    static let UpdateUserDetails = "user/updateuserdetails"
    //TODO:- MentorProfile Module
    static let MentorProfileDetails = "user/mentordetails"
    //TODO:- ELearning Module
    static let ELearning        = "mentor/searchelearning" //"user/searchelearning"
    static let ResourceMentor        = "user/searchelearning" //"user/searchelearning"
    static let disclaimer        = "user/searchelearning"
    static let ELearningDetails = "user/elearningdetails/"
    //TODO:- Resource Module
    static let Resource        = "user/searchresource"
    static let ResourceDetails = "user/resourcedetails/"
    //TODO:- Job Module
    static let JobList         = "getjob"//tab
    static let JobDeatils      = "jobdetails/"
    static let AppliedJobList  = "getappliedjob"// Menu
    static let ApplyJob        = "job/apply"
        //TODO:- Goal Module
    static let GoalTaskChallengeList         = "user/getgoaltask"
    static let GoalTaskChallengeCompleteList = "user/goaltaskcompltelist"
    static let GoalTaskChallengeAction       = "user/actiongoaltask"
    static let GoalTaskChallengeDetails      = "user/getgoaltaskdetails"
    static let updateNote                    = "user/notesavegoaltask"
    static let deleteImage                   = "user/filedeletegoaltask/"
    static let uploadImage                   = "user/filesavegoaltask"
    static let MenteeCreateGoal              = "user/creategoaltask"
    //TODO:- Tip Module
    static let SubmitQuickTip     = "SubmittedTipsController/create_new"
    static let SubmitTipGetAgency = "BroadcastController/getagency"
    static let getTipEndpointNew  = "SubmittedTipsController/mytips_new"
    static let addMultipleDataNew = "SubmittedTipsController/multi_suspect_vehicle_new"
    //TODO:- Chat Module
    static let ChatList     = "chat/agencies"
    static let ChatMessages = "chat/my_chats"
    static let ChatCode = "chat/getchatcode"
    static let menteeChat = "chat/my_chats"
    static let staff_chat  = "mentor/staff_chat"
    //TODO:- My Journal
    static let journal_list     = "journal/list"
    static let journal_add = "journal/add"
    //TODO:- Firebase Module
    static let FCMTokenUpdateEndpoint = "UsersController/updatefirebase"
    static let meetingMenteeListing = "meeting/list"
   // static let meetingMenteeListing = "meeting/list"
    static let allMenteemeetinglist = "meeting/alllist"
    static let meetingReschedueRequestbyMentee = "meeting/saverequest"
    static let pastMenteeMeeting = "meeting/past"
    static let upcommingMenteeMeeting = "meeting/upcoming"
    static let acceptMeetingByMentee = "meeting/accept"
    static let meetingParticipation = "meeting/make_accept_web_meeting"
    static let cancelMeetingMentee = "meeting/saverequest"
    //TODO:- Add Report Module
    static let AddReport     = "createreport"
    //TODO:- Show Report List
    static let ListReport     = "listreport"
    //MARK:-MentorEndPoints
     /*00
     // Documents:- https://tsic.s3.us-east-2.amazonaws.com/documents/
     // Note:- https://tsic.s3.us-east-2.amazonaws.com/note/
      */
    //TODO:- MentorLogInModule
    static let Mentorlogin             = "mentor/login"
    //TODO:- MentorProfileModule
    static let MentorUserDetails       = "mentor/my-profile"
    static let MentorUpdateUserDetails = "mentor/save-profile"
    
    //TODO:- Unified LoginModule
    static let checkUserType = "check_user"
    static let UnifiedLogin = "unified/login"
    static let UnifiedForgotPassword = "unified/forgotpassword"
    static let unifiedResetpassword = "unified/resetpassword"
    
    //TODO:- MenteeListModule
    static let MenteeList       = "mentor/menteelist"
    static let StaffList       = "mentor/stafflist"
    static let MenteeStaffList       = "getstaffs"

    //static let MentorUpdateUserDetails = "mentor/save-profile"
    //TODO:- MentorForgotPassword
    static let MentorForgotPassword      = "mentor/forgotpassword"
    static let MentorResetPassword      = "mentor/resetpassword"
    
    //TODO:- MentorGoalTaskChallengeList
    static let MentorShowGoalTaskChallengeList      = "mentor/listgoaltaskchallenge"
    static let MentorCreateGoalTaskChallenge      = "mentor/creategoaltaskchallenge"
    static let MentorAssignGoalTaskChallenge      = "mentor/assigngoaltaskchallenge"
    static let AssignMenteeListGoalTaskChallenge = "mentor/listmenteegoaltaskchallenge"
    static let DeleteAssignedMenteeGoalTaskChallenge = "mentor/delmenteegoaltaskchallenge"
    
    //TODO:- Mentor SESSION
    static let Session_mentee  = "mentor/createsession"
    static let Session_lising  = "mentor/listsession"
    
    
    static let getsession_method_Location = "mentor/get_session_method_location"
    //TODO:- Mentor MEETING
    static let AddMeeting  = "mentor/addmeeting"
    static let listmeeting  = "mentor/listmeeting"
    static let loggedSession = "mentor/logged_meeting"
    static let alllistmeeting = "mentor/alllistmeeting"
    static let cancelMeeting  = "mentor/cancel_meeting"
    static let no_reschedule_meeting  = "mentor/no_reschedule_meeting"
    static let mentor_chat  = "mentor/mentee_chat"
    static let getTwiiloToken = "chat/get_access_token"
    static let twilloSidUpdate = "chat/channel_id_update"
    static let sendPushTwillo = "chat/send_nofication"
    static let Mentee_staff_chat  = "chat/staff_chat"
    
    //TODO:- UserData
    static let menteeUserData = "userDetails"
    static let mentorUserData = ""
    
    //TODO:-LogOut
    static let mentorLogOut = "mentor/logout"
    static let menteeReport  = "mentor/menteereports"
    
//    static let getLatLogForGeofence  = "mentor/upcoming_accepted_meeting"
    static let getLatLogForGeofence  = "mentor/todaymeeting"
    static var getTimeZoneMentor  = "mentor/get_timezone"
    static var getTimeZoneMentee  = "user/get_timezone"

    //MARK: App Version
    static var getVersionCode  = "\(PREURL)getVersionCode"
    
    //TODO:Mentor School List
    static let getMentorSchoolList  = "mentor/schoollist"
    static let schoolList = "mentor/schoollist"
}

class TakeStockInChildrenConstantVariables {
    static var accessToken = ""
    //static var userDetailsData : UserDetailsData?
    //static let IndicatorSize = UIDevice.current.iPhone == true ? CGSize(width: 30, height:30) : CGSize(width: 70, height:70)
    //static let IndicatorValue = 14
}


extension UIDevice {
    var iPhoneX: Bool {
        return UIScreen.main.nativeBounds.height == 2436
    }
    var iPhone: Bool {
        return UIDevice.current.userInterfaceIdiom == .phone
    }
    enum ScreenType: String {
        case iPhone4_4S = "iPhone 4 or iPhone 4S"
        case iPhones_5_5s_5c_SE = "iPhone 5, iPhone 5s, iPhone 5c or iPhone SE"
        case iPhones_6_6s_7_8 = "iPhone 6, iPhone 6S, iPhone 7 or iPhone 8"
        case iPhones_6Plus_6sPlus_7Plus_8Plus = "iPhone 6 Plus, iPhone 6S Plus, iPhone 7 Plus or iPhone 8 Plus"
        case iPhoneX_XS = "iPhone X"
        case iPhone_XSMax = "iPhone XS Max"
        case iPhone_XR = "iPhone XR"
        case unknown
    }
    var screenType: ScreenType {
        switch UIScreen.main.nativeBounds.height {
        case 960:
            return .iPhone4_4S
        case 1136:
            return .iPhones_5_5s_5c_SE
        case 1334:
            return .iPhones_6_6s_7_8
        case 1920, 2208:
            return .iPhones_6Plus_6sPlus_7Plus_8Plus
        case 2436:
            return .iPhoneX_XS
        case 2688:
            return .iPhone_XSMax
        case 1792:
            return .iPhone_XR
        default:
            return .unknown
        }
    }
}

extension UIColor {
    public convenience init?(hex: String) {
        let r, g, b, a: CGFloat

        if hex.hasPrefix("#") {
            let start = hex.index(hex.startIndex, offsetBy: 1)
            let hexColor = String(hex[start...])

            if hexColor.count == 8 {
                let scanner = Scanner(string: hexColor)
                var hexNumber: UInt64 = 0
                if scanner.scanHexInt64(&hexNumber) {
                    r = CGFloat((hexNumber & 0xff000000) >> 24) / 255
                    g = CGFloat((hexNumber & 0x00ff0000) >> 16) / 255
                    b = CGFloat((hexNumber & 0x0000ff00) >> 8) / 255
                    a = CGFloat(hexNumber & 0x000000ff) / 255

                    self.init(red: r, green: g, blue: b, alpha: a)
                    return
                }
            }
        }

        return nil
    }
}


extension UIColor {
    convenience init(hexString: String, alpha: CGFloat = 1.0) {
        let hexString: String = hexString.trimmingCharacters(in: CharacterSet.whitespacesAndNewlines)
        let scanner = Scanner(string: hexString)

        if (hexString.hasPrefix("#")) {
            scanner.scanLocation = 1
        }

        var color: UInt32 = 0
        scanner.scanHexInt32(&color)
        let mask = 0x000000FF
        let r = Int(color >> 16) & mask
        let g = Int(color >> 8) & mask
        let b = Int(color) & mask

        let red   = CGFloat(r) / 255.0
        let green = CGFloat(g) / 255.0
        let blue  = CGFloat(b) / 255.0

        self.init(red:red, green:green, blue:blue, alpha:alpha)
    }

    func toHexString() -> String {
        var r:CGFloat = 0
        var g:CGFloat = 0
        var b:CGFloat = 0
        var a:CGFloat = 0

        getRed(&r, green: &g, blue: &b, alpha: &a)

        let rgb:Int = (Int)(r*255)<<16 | (Int)(g*255)<<8 | (Int)(b*255)<<0

        return String(format:"#%06x", rgb)
    }
}
