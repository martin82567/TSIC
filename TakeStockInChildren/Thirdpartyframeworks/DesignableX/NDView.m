//
//  NDView.m
//  Navigation-Delivery App
//
//  Created by Aquarious Technology on 20/04/17.
//  Copyright © 2017 Aquarious Technology. All rights reserved.
//

#import "NDView.h"

@implementation NDView


// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect {
    // Drawing code
    self.layer.cornerRadius =self.cornerRadius;
    self.clipsToBounds = YES;
    self.layer.masksToBounds = YES;
    self.layer.borderWidth= self.borderWidth;
    self.layer.borderColor= [self.borderColor CGColor];
    
    self.layer.shadowColor = [self.shadowColor CGColor];
    self.layer.shadowOffset = self.shadowOffset;
    self.layer.shadowOpacity = self.shadowOpacity;
    self.layer.shadowRadius = self.shadowRadius;

}
@end

