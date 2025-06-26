//
//  NDTextField.m
//  Navigation-Delivery App
//
//  Created by Aquarious Technology on 20/04/17.
//  Copyright © 2017 Aquarious Technology. All rights reserved.
//

#import "NDTextField.h"

@implementation NDTextField

- (id)initWithCoder:(NSCoder *)aDecoder {
    self = [super initWithCoder:aDecoder];
    if(self != nil) {
        NSAttributedString *str = [[NSAttributedString alloc] initWithString:self.placeholder attributes:@{ NSForegroundColorAttributeName : [UIColor grayColor] }];
        self.attributedPlaceholder = str;
 
    }
    return self;
}

/*
// Only override drawRect: if you perform custom drawing.
// An empty implementation adversely affects performance during animation.
- (void)drawRect:(CGRect)rect {
    // Drawing code
}
*/

@end
