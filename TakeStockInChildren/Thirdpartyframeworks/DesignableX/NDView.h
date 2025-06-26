//
//  NDView.h
//  Navigation-Delivery App
//
//  Created by Aquarious Technology on 20/04/17.
//  Copyright © 2017 Aquarious Technology. All rights reserved.
//

#import <UIKit/UIKit.h>

IB_DESIGNABLE

@interface NDView : UIView

@property (nonatomic) IBInspectable float cornerRadius;
@property (nonatomic) IBInspectable float borderWidth;
@property (nonatomic) IBInspectable UIColor *borderColor;

@property (nonatomic) IBInspectable UIColor * shadowColor;
@property (nonatomic) IBInspectable CGSize shadowOffset;
@property (nonatomic) IBInspectable float shadowOpacity;
@property (nonatomic) IBInspectable float shadowRadius;
@end
