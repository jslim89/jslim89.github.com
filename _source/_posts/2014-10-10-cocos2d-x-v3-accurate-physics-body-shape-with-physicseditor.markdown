---
layout: post
title: "Cocos2d-x V3 accurate physics body shape with PhysicsEditor"
date: 2014-10-10 07:56:36 +0800
comments: true
categories: 
- cocos2d-x
---

I mention about [TexturePacker in my previous post](http://jslim.net/blog/2014/09/12/create-spritesheet-for-cocos2d-x-using-with-texturepacker/).
Now is [PhysicsEditor](https://www.codeandweb.com/physicseditor).

![PhysicsEditor](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/PhysicsEditor-logo.png)

## Create a physics file

First of all, create a physics file with PhysicsEditor. Just drag image(s) to the left pane.

![Drag images to PhysicsEditor](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/drag.png)

![Traverse body shape](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/select-shape.png)

1. Set the **Exporter** to `Chipmunk generic (PLIST) - BETA`
2. Set the **Relative** both values to `0.5` to move the anchor point to the center
3. Click on that icon

![Adjust tolerance](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/adjust-tolerance.png)

You will see the face is highlighted, PhysicsEditor helps you to generate vertices/points of the image.
You can change the tolerance value _(by default is `1`)_, of course, the lower the better, but will be slower _(theoretically)_.

![Image highlighted](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/highlighted.png)

Once you have done, the image is now highlighted with its shape.

![Publish physics](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/publish.png)

Publish it as `.plist` file. You have done the part.

## Add the physics file to Xcode project

![Add physics to Xcode](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/create-folder-references.png)

Drag the published file to Xcode file pane and select `Create folder references`.

![File inspector](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/file-pane.png)

It will looks like this.

## Test it out in code

Before actually use the physics file, let's try with the default API. Edit **HelloWorldScene.cpp**

```cpp HelloWorldScene.cpp
auto spriteBody = PhysicsBody::createBox(sprite->getContentSize());
sprite->setPhysicsBody(spriteBody);
```

![Rectangle shape](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/rectangle-shape.png)

It is rectangle.

Now, add [PEShapeCache_X3_0.h](https://raw.githubusercontent.com/jslim89/Cocos2d-x-PhysicsEditor-demo/master/Classes/PEShapeCache_X3_0.h) and [PEShapeCache_X3_0.cpp](https://raw.githubusercontent.com/jslim89/Cocos2d-x-PhysicsEditor-demo/master/Classes/PEShapeCache_X3_0.cpp) to project _(credit: https://github.com/baibai2013/PhysicsEditor-Loader-for-cocos2d-x-3.0)_.

Update **AppDelegate.cpp**, add the physics file you generated just now to cache

```cpp AppDelegate.cpp
#include "AppDelegate.h"
#include "HelloWorldScene.h"
#include "PEShapeCache_X3_0.h" // Make sure you include it

// ...

bool AppDelegate::applicationDidFinishLaunching() {
    // ...
    // after if (!glview) { ... }

    PEShapeCache::getInstance()->addBodysWithFile("physics/body.plist");

    // ...
}
```

Then in **HelloWorldScene.cpp**

```cpp HelloWorldScene.cpp
#include "HelloWorldScene.h"
#include "PEShapeCache_X3_0.h"

// ...

bool HelloWorld::init()
{
    // ...
    auto spriteBody = PEShapeCache::getInstance()->getPhysicsBodyByName("2dx"); // the name you put in PhysicsEditor
    sprite->setPhysicsBody(spriteBody);
}
```

Then finally you get

![Custom shape](http://jslim89.github.com/images/posts/2014-10-10-cocos2d-x-v3-accurate-physics-body-shape-with-physicseditor/custom-shape.png)

Although it is not perfect yet, but it basically solve the issue that I faced. Will continue to seek for solution to make it perfect :)

I created a demo project, please refer to [Cocos2d-x-PhysicsEditor-demo](https://github.com/jslim89/Cocos2d-x-PhysicsEditor-demo).

_References:_

- _[[Cocos3.0 Tutorial] Accurate hit testing with Cocos2d-x 3.0 integrated physics feature](http://discuss.cocos2d-x.org/t/cocos3-0-tutorial-accurate-hit-testing-with-cocos2d-x-3-0-integrated-physics-feature/13393)_
- _[cocos2d-x 3.0 PhysicsEditor 加载plist PEShapeCache_X3_0](http://www.58player.com/blog-2479-100819.html)_
