//
//  UIViewX.swift
//  DesignableX
//
//  Created by Mark Moeykens on 12/31/16.
//  Copyright © 2016 Mark Moeykens. All rights reserved.
//

import UIKit

@IBDesignable
class UIViewX: UIView {
    
    // MARK: - Gradient
    
    @IBInspectable var firstColor: UIColor = UIColor.white {
        didSet {
            updateView()
        }
    }
    
    @IBInspectable var secondColor: UIColor = UIColor.white {
        didSet {
            updateView()
        }
    }
    
    @IBInspectable var horizontalGradient: Bool = false {
        didSet {
            updateView()
        }
    }
    
    override class var layerClass: AnyClass {
        get {
            return CAGradientLayer.self
        }
    }
    
    func updateView() {
        let layer = self.layer as! CAGradientLayer
        layer.colors = [ firstColor.cgColor, secondColor.cgColor ]
        
        if (horizontalGradient) {
            layer.startPoint = CGPoint(x: 0.0, y: 0.5)
            layer.endPoint = CGPoint(x: 1.0, y: 0.5)
        } else {
            layer.startPoint = CGPoint(x: 0, y: 0)
            layer.endPoint = CGPoint(x: 0, y: 1)
        }
    }
    
    // MARK: - Border
    @IBInspectable public var borderColor2: UIColor = UIColor.clear {
        didSet {
            layer.borderColor = borderColor2.cgColor
        }
    }
    
    @IBInspectable public var borderWidth2: CGFloat = 0 {
        didSet {
            layer.borderWidth = borderWidth2
        }
    }
    
    @IBInspectable public var cornerRadius2: CGFloat = 0 {
        didSet {
            layer.cornerRadius = cornerRadius2
        }
    }
    
    // MARK: - Shadow
    @IBInspectable public var shadowOpacity2: CGFloat = 0 {
        didSet {
            layer.shadowOpacity = Float(shadowOpacity2)
        }
    }
    
    @IBInspectable public var shadowColor2: UIColor = UIColor.clear {
        didSet {
            layer.shadowColor = shadowColor2.cgColor
        }
    }
    
    @IBInspectable public var shadowRadius2: CGFloat = 0 {
        didSet {
            layer.shadowRadius = shadowRadius2
        }
    }
    
    @IBInspectable public var shadowOffsetY: CGFloat = 0 {
        didSet {
            layer.shadowOffset.height = shadowOffsetY
        }
    }
}
