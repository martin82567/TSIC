//
//  showBigImageVC.swift
//  SageAutomation
//
//  Created by niraj paul on 06/12/17.
//  Copyright © 2017 Aquarious Technology. All rights reserved.
//

import UIKit
import SDWebImage

class showBigImageVC: BaseViewController, UIScrollViewDelegate {

//    @IBOutlet var imgVBAckImg: UIImageView!
    @IBOutlet var imgV: UIImageView!
    @IBOutlet weak var scrollV: UIScrollView!
    //@IBOutlet weak var imageYPositionConstraint: NSLayoutConstraint!
    
    var imageView: UIImageView?
    
    var fileData: (data:Data, filename: String)?
    
    var Bigphoto: NSString!
    var iAmfromAnnouncement : NSString = ""

    var url : URL? = nil
    
    
    //MARK:: VIEW CONTROLLER LIFE CYCLE ☞🙂
    override func viewDidLoad() {
        super.viewDidLoad()
       
        //headerView2()
        let imgUrl =  URL(string: "\(TakeStockInChildrenConstant.reportImage)\(Bigphoto!)")
        
        imgV.sd_setImage(with: imgUrl, placeholderImage: UIImage(named: ""))
        
        scrollV.minimumZoomScale = 1.0
        scrollV.maximumZoomScale = 6.0//maximum zoom scale you want
        scrollV.zoomScale = 1.0
        scrollV.delegate = self
        // Do any additional setup after loading the view.
    }
    
    override func viewWillLayoutSubviews() {
        super.viewWillLayoutSubviews()
        
        var frame = self.view.bounds
        frame.size.width = frame.size.width - 40
        frame.size.height = frame.size.height - 40 - 64
        frame.origin.x = 20
        frame.origin.y = 20
        
        imageView?.frame = frame
    }
    
    //MARK:: UIStatusBar
    override var preferredStatusBarStyle: UIStatusBarStyle {
        return .lightContent
    }
    
    //MARK:: IBAction
    @IBAction func btnBackAction(_ sender: Any) {
        self.navigationController?.popViewController(animated: true)
    }
    
    @IBAction func btnDownloadAction(_ sender: Any) {
        guard let image = imgV!.image else { return }

        UIImageWriteToSavedPhotosAlbum(image, self, #selector(image(_:didFinishSavingWithError:contextInfo:)), nil)
    }
    
    @IBAction func selectedDoneBarButton(_ sender: Any) {
        self.navigationController?.dismiss(animated: true, completion: nil)
    }
    
    @IBAction func selectedActionBarButton(_ sender: Any) {
        if self.fileData != nil {
           // self.shareFile(withData: imageData.data, filename: imageData.filename)
        }
    }
    
    //MARK:: Function
    @objc func image(_ image: UIImage, didFinishSavingWithError error: Error?, contextInfo: UnsafeRawPointer) {
        if let error = error {
            // we got back an error!
            let ac = UIAlertController(title: "Save error", message: error.localizedDescription, preferredStyle: .alert)
            ac.addAction(UIAlertAction(title: "OK", style: .default))
            present(ac, animated: true)
        } else {
            let ac = UIAlertController(title: "Saved!", message: "Your altered image has been saved to your photos.", preferredStyle: .alert)
            ac.addAction(UIAlertAction(title: "OK", style: .default))
            present(ac, animated: true)
        }
    }
    
    func viewForZooming(in scrollView: UIScrollView) -> UIView? {
       // return self.imageView
        return imgV
    }
}


