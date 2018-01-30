---
title: CefSharp listen to JavaScript event
date: 2018-01-30 15:23:45
tags:
- c#
- cefsharp
---

[CefSharp](https://github.com/cefsharp/CefSharp) is a Chromium browser embedded to an application.

In my case, I'm using WinForms _(CefSharp.WinForms v57.0.0)_.

### What I want to achieve here:

When user click on the `<a>` element, the C# code there need to perform some action.

### How it can be done

In **C#** code

```cs
...
public void InitBrowser()
{
    ...
    browser = new ChromiumWebBrowser("http://yoursite.com");
    ...

    var eventObject = new ScriptedMethodsBoundObject();
    eventObject.EventArrived += OnJavascriptEventArrived;
    browser.RegisterJsObject("boundEvent", eventObject, options: BindingOptions.DefaultBinder);
}
...
public static void OnJavascriptEventArrived(string eventName, object eventData)
{
    switch (eventName)
    {
        case "click":
        {
            var dataDictionary = eventData as Dictionary<string, object>;
            // do whatever you want here
            break;
        }
    }
}
```

In **JavaScript** code

```js
$('a').click(function(e) {
  if (!window.boundEvent) {
    console.log('window.boundEvent does not exist.');
    return;
  }   
  window.boundEvent.raiseEvent('click', {
    data1: 'foo',
    data2: 'bar'
  }); 
});
```
