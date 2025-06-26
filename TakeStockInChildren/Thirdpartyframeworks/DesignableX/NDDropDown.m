//
//  NDDropDown.m
//  Navigation-Delivery App
//
//  Created by Aquarious Technology on 27/05/17.
//  Copyright © 2017 Aquarious Technology. All rights reserved.
//

#import "NDDropDown.h"

@implementation NDDropDown


- (id)initWithCoder:(NSCoder *)aDecoder {
    self = [super initWithCoder:aDecoder];
    if(self != nil) {
        self.cornerRadius = 0;
        
    }
    return self;
}

- (void)drawRect:(CGRect)rect {
    self.clipsToBounds = YES;
    self.layer.masksToBounds = YES;
    self.layer.cornerRadius = self.cornerRadius;
    
    self.layer.shadowColor = [[UIColor grayColor] CGColor];
  //  self.layer
    
}
 

@end
