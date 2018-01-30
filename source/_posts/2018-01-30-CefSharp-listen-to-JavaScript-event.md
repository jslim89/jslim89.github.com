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
    var jsonString = eventData.ToString();
    var serializer = new System.Web.Script.Serialization.JavaScriptSerializer();
    var dataDict = serializer.Deserialize<Dictionary<string, object>>(jsonString);

    Console.WriteLine("Event arrived: {0}", eventName); // output 'click'

    switch (eventName)
    {
        case "click":
        {
            // do whatever you want here
            Console.WriteLine(dataDict["data1"]); // output 'foo'
            Console.WriteLine(dataDict["data2"]); // output 'bar'
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
  window.boundEvent.raiseEvent('click', JSON.stringify({
    data1: 'foo',
    data2: 'bar'
  }));
});
```
