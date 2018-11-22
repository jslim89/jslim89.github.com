---
layout: post
title: "Create spritesheet for Cocos2d-x using with TexturePacker"
date: 2014-09-12 08:17:17 +0800
comments: true
tags: 
- cocos2d-x
---

## Create spritesheet

![Select Cocos2d](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/spritesheet-demo.png)

Have you seen this sort of image before? The first time I saw this I thought this is created using Photoshop, and I also
have no idea how to use this _BIG_ image in the game.

Until... I started to look in the game development, I only know that this is actually created by _spritesheet_ program/app.

Here I will be using [TexturePacker](https://www.codeandweb.com/texturepacker).

![TexturePacker logo](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/TP_logo_512.png)

Why? I have seen many game tutorials are talking about this, I tried it and found that it is very easy to use.

### How to use demo

![Select Cocos2d](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/texturepacker-start.png)

First of all, I'm going to do this in Cocos2d-x engine, so select **Cocos2d** here.

![Drag images to here](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/texturepacker-drag.png)

Now, drag images to the _box_

![Drag done](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/texturepacker-merged.png)

Here I drag 3 bird images, and you can see the left pane, there are `hero_01.png`, `hero_02.png`, `hero_03.png`, these are the original 
file name.

Once you've done, select **Publish sprite sheet**

![Publish spritesheet 1](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/publish-step-1.png)

It will prompt you twice, one is to save the plist file contains the meta data of the original images such as
`frame`, `offset`, `rotated`, etc. It may sound complicated, but no worry, Cocos2d-x will handle it.

![Publish spritesheet 2](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/publish-step-2.png)

The second prompt is to save the merged image.

![Publish spritesheet done](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/publish-step-done.png)

And you've done.

## Use in Cocos2d-x

![File structure](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/file-structure.png)

File structure

In **AppDelegate.cpp** `applicationDidFinishLaunching()` method, add

```cpp
// 1. fix the resolution
glview->setDesignResolutionSize(320, 480, ResolutionPolicy::EXACT_FIT);
director->setContentScaleFactor(1);

// 2. add search path for images
std::vector<std::string> searchPaths;

searchPaths.push_back("images");
FileUtils::getInstance()->setSearchPaths(searchPaths);

// 3. add sprite frame
SpriteFrameCache::getInstance()->addSpriteFramesWithFile("hero.plist", "hero.png");
SpriteFrameCache::getInstance()->addSpriteFramesWithFile("images.plist", "images.png");
```

1. I'm a iPhone user, thus I fix the resolution to _320 x 480_ for simplicity
2. In this case, I put the sprite sheet under **images** directory, thus I add the search path `images` to `FileUtils`
3. Add sprite sheet to cache

### Create a sprite object (bird in this case)

I just name it `Hero`. Edit **Hero.h**

```cpp
#ifndef __HERO_SCENE_H__
#define __HERO_SCENE_H__

#include "cocos2d.h"

class Hero : public cocos2d::Sprite
{
public:
    Hero();
    
private:
    cocos2d::RepeatForever *moving();
};

#endif // __HERO_SCENE_H__
```

and **Hero.cpp**

```cpp
#include "Hero.h"
#include "GameScene.h"

USING_NS_CC;

Hero::Hero()
{
    // 1. load a default image
    initWithSpriteFrameName("hero_01.png");
    
    // 2. run the move action
    this->runAction(moving());
}

RepeatForever* Hero::moving()
{
    // 3. repeat the frame
    int numFrame = 3;
    
    cocos2d::Vector<cocos2d::SpriteFrame *> frames;
    SpriteFrameCache *frameCache = SpriteFrameCache::getInstance();
    
    char file[100] = {0};
    
    for (int i = 0; i < numFrame; i++) {
        sprintf(file, "hero_%02d.png", i+1);
        SpriteFrame *frame = frameCache->getSpriteFrameByName(file);
        frames.pushBack(frame);
    }
    
    Animation *animation = Animation::createWithSpriteFrames(frames, 0.3);
    Animate *animate = Animate::create(animation);
    
    RepeatForever *repeat = RepeatForever::create(animate);
    return repeat;
}
```

1. Load an image for the Hero _(a.k.a bird)_ by default during object instantiation
2. Make the bird fly by running the move action
3. As we know that there are 3 images on **hero.plist**, thus fix it to 3. Then add all
these 3 images _(get from sprite frame cache)_ to an array, and then repeat it forever.

### Add hero to GameScene

Edit **GameScene.cpp**, under the `init()` method, add the code below right before `return` statement

```cpp
Hero *hero = new Hero();
hero->setPosition(Point(visibleSize.width / 2, visibleSize.height / 2));
this->addChild(hero);
```

This is to instantiate a **Hero** object, set the position to center of screen then add to current scene.

Run it and you will get the animated bird, see screenshot below.

![Animated bird](http://jslim89.github.com/images/posts/2014-09-12-create-spritesheet-for-cocos2d-x-using-with-texturepacker/animated-hero.gif)

Done.
