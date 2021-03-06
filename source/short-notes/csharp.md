---
layout: page
title: C#
permalink: /short-notes/csharp/
date: 2020-05-23 21:13:51
comments: false
sharing: true
footer: true
---

https://docs.microsoft.com/en-us/dotnet/csharp/

#### Read, Modify, Write XML

```csharp
// read from file
XmlDocument xml = new XmlDocument();
xml.Load("input.xml");

// Make changes to the document.
XmlNode node = xml.DocumentElement.SelectSingleNode("/settings");
...

// write to file
using(XmlTextWriter xtw = new XmlTextWriter("output.xml", Encoding.UTF8)) {
    xtw.Formatting = Formatting.Indented; // optional, if you want it to look nice
    xml.WriteContentTo(xtw);
}
```

##### Reference:

- [Best way to read, modify, and write XML](http://stackoverflow.com/questions/3736516/best-way-to-read-modify-and-write-xml/3736580#3736580)
