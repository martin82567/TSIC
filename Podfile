# Uncomment the next line to define a global platform for your project
 platform :ios, '11.2'

target 'TakeStockInChildren' do
  # Comment the next line if you don't want to use dynamic frameworks
  use_frameworks!

  pod 'ActionKit', '2.5.2'
  pod 'IQKeyboardManagerSwift', '6.4.1'
  pod 'FirebaseCore'
  pod 'FirebaseMessaging'
  pod 'FirebaseAnalytics'
  pod 'Alamofire', '4.8.2'
  pod 'SDWebImage', '3.8.3'
  pod 'FSCalendar'
  pod 'ImagePicker', :git => 'https://github.com/hyperoslo/ImagePicker.git'
  pod 'CarbonKit','2.3.0'
  pod 'NVActivityIndicatorView','4.7.0'
  pod 'IBAnimatable','6.0.0'
  #pod 'RLBAlertsPickers', :git => 'https://github.com/jbouaziz/Alerts-Pickers.git'
  pod 'Socket.IO-Client-Swift'
  pod 'Siren', :git => 'https://github.com/ArtSabintsev/Siren.git', :branch => 'swift5.0'
  pod 'ReverseExtension','0.6.0'
  pod 'ShimmerSwift','2.1.1'
  pod 'TwilioChatClient', '~> 5.0'
  pod 'FirebaseCrashlytics'
  pod 'ZoomVideoSDK'

  # Pods for TakeStockInChildren

  target 'TakeStockInChildrenTests' do
    inherit! :search_paths
    # Pods for testing
  end

  target 'TakeStockInChildrenUITests' do
    inherit! :search_paths
    # Pods for testing
  end
  
  post_install do |installer|
        installer.generated_projects.each do |project|
              project.targets.each do |target|
                  target.build_configurations.each do |config|
                      config.build_settings['IPHONEOS_DEPLOYMENT_TARGET'] = '12.0'
                   end
              end
       end
    end

end
