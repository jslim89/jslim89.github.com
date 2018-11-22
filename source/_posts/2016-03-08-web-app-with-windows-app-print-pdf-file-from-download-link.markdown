---
layout: post
title: "Web app with Windows app print pdf file from download link"
date: 2016-03-08 15:16:53 +0800
comments: true
tags: 
- windows-form-application
- javascript
---

## Scenario

A web app that allow user to click on a link/button, then trigger the selected printer and print out the document directly.

## Problem & Solution

Due to security reason, there is no way to trigger hardware via JavaScript.  Thus, I create a Windows Form Application _(WFA)_ using C#.

This application is actually just a wrapper that wrap a browser _(I'm using [CefSharp](https://github.com/cefsharp/CefSharp) browser)_, so that I can get the printer list by using C#, then pass to the browser.

### 1. Create a Windows Form Application

I assume you know how to do this. I'm using Visual Studio in this case.

Also, you need to add a few references that required for this project:

- System.configuration
- System.Management
- System.Web
- System.Net.Http

### 2. Download CefSharp browser

You can [install CefSharp via Nuget](https://www.nuget.org/packages/CefSharp.WinForms/)

### 3. Embed into your main form

**Foo.cs**

```csharp
using CefSharp;
using CefSharp.WinForms;

namespace FooSpace
{
    public partial class Foo : Form
    {
        ...
        private static string baseUrl = "http://yoursite.com";

        // session/cookie property
        public static string sessionCookieKey = "PHPSESSID";
        private string sessionCookie = null;

        public ChromiumWebBrowser browser;

        public Foo()
        {
            InitializeComponent();
            InitBrowser();
        }
        
        public void InitBrowser()
        {
            Cef.Initialize(new CefSettings());
            browser = new ChromiumWebBrowser(Foo.baseUrl);
            this.Controls.Add(browser);
            browser.Dock = DockStyle.Fill;
            browser.FrameLoadStart += OnFrameLoadStart;
            browser.FrameLoadEnd += OnFrameLoadEnd;
            browser.DownloadHandler = new FooDownloadHandler();
        }

        // Chromium delegate
        private void OnFrameLoadStart(object sender, FrameLoadStartEventArgs e)
        {
            // retrieve session cookie
            var visitor = new CookieVisitor(all_cookies => {
                var sb = new StringBuilder();
                foreach (var nameValue in all_cookies)
                {
                    // grab the session cookie
                    if (nameValue.Item1 == Foo.sessionCookieKey)
                    {
                        this.sessionCookie = nameValue.Item2;
                        // pass the session cookie to download handler
                        ((FooDownloadHandler)browser.DownloadHandler).setSessionCookie(this.sessionCookie);
                        break;
                    }
                }
            });
            Cef.GetGlobalCookieManager().VisitAllCookies(visitor);
        }
    }
}
```

### 4. Inject printer list into browser

Edit the same file **Foo.cs**, and add the following

```csharp
// after the page loaded, check if the url is setup printers page
private void OnFrameLoadEnd(object sender, FrameLoadEndEventArgs e)
{
    if (e.Url.Contains("setup/printers")) addPrinterOptionsToDropdown();
}

// Javascript
private void addPrinterOptionsToDropdown()
{
    // (Optional) Filter a few printer model
    Regex regex = new Regex(@"\b(EPSON|Canon)\b", RegexOptions.IgnoreCase);

    string js = "$('select').empty();";

    foreach (string sPrinters in System.Drawing.Printing.PrinterSettings.InstalledPrinters)
    {
        MatchCollection matches = regex.Matches(sPrinters);
        if (matches.Count < 1) continue;

        js += "\n$('select').append($('<option>').val('" + sPrinters + "').text('" + sPrinters + "'));";
    }

    js += "\n$('select').each(function(i) {"
        + "\n  $(this).val($(this).data('value'));"
        + "\n});";
    
    browser.ExecuteScriptAsync(js);
}
```

### 5. Create a download handler class

**FooDownloadHandler.cs**

```csharp
using System.Drawing.Printing;
using System.IO;

...

class FooDownloadHandler : IDownloadHandler
{
    private string sessionCookie = null;

    private string suggestedFilename = null;
    
    // without the session, the file cannot be downloaded
    public void setSessionCookie(string sessionCookie)
    {
        this.sessionCookie = sessionCookie;
    }

    public void OnBeforeDownload(IBrowser browser, DownloadItem downloadItem, IBeforeDownloadCallback callback)
    {
        if (!callback.IsDisposed)
        {
            using (callback)
            {
                callback.Continue(downloadItem.SuggestedFileName, showDialog: true);
            }
        }
    }

    // Webclient delegate
    private void client_DownloadProgressChanged(object sender, DownloadProgressChangedEventArgs e)
    {
        // download in progress
    }

    private void client_DownloadFileCompleted(object sender, AsyncCompletedEventArgs e)
    {
        // download completed
    }
}
```

Now if you try to download, it will prompt a "Save as" windows and you can choose where you want to save.

### 6. Install 3rd-party pdf reader

I'm using [Foxit](https://www.foxitsoftware.com/products/pdf-reader/) here, download it and install in the Windows machine _(client)_.

### 7. Pass the downloaded document to selected printer

Edit the file **FooDownloadHandler.cs**

```csharp
// create printer name property
private string printerName = null;

// add handler
public void OnBeforeDownload(IBrowser browser, DownloadItem downloadItem, IBeforeDownloadCallback callback)
{
    Directory.CreateDirectory(Path.GetTempPath());
    this.suggestedFilename = Path.Combine(Path.GetTempPath(), downloadItem.SuggestedFileName);
    
    // the printer name is passed from the web app thru query string
    string querystring = downloadItem.Url.Substring(downloadItem.Url.IndexOf('?'));
    System.Collections.Specialized.NameValueCollection queryDictionary = System.Web.HttpUtility.ParseQueryString(querystring);
    if (queryDictionary.Get("printer") != null && queryDictionary.Get("printer") != "")
    {
        this.printerName = queryDictionary.Get("printer");
    }
    else if (!callback.IsDisposed) // without printer name, will prompt "Save as" window
    {
        using (callback)
        {
            callback.Continue(downloadItem.SuggestedFileName, showDialog: true);
        }
        return;
    }

    // use WebClient to download
    WebClient client = new WebClient();
    client.Headers.Add(HttpRequestHeader.Cookie, Foo.sessionCookieKey + "=" + this.sessionCookie);
    client.DownloadProgressChanged += new DownloadProgressChangedEventHandler(client_DownloadProgressChanged);
    client.DownloadFileCompleted += new AsyncCompletedEventHandler(client_DownloadFileCompleted);
    client.DownloadFileAsync(new Uri(downloadItem.Url), this.suggestedFilename);
}

private void client_DownloadFileCompleted(object sender, AsyncCompletedEventArgs e)
{
    printPDF(this.suggestedFilename, this.printerName);
}

private void printPDF(string filename, string printerName)
{
    try
    {
        string exeFile = @"""C:\Program Files (x86)\Foxit Software\Foxit Reader\FoxitReader.exe""";
        ProcessStartInfo processInfo = new ProcessStartInfo();
        processInfo.FileName = "\"" + exeFile + "\"";
        processInfo.Arguments = "/t \"" + filename + "\" \"" + printerName + "\"";
        processInfo.CreateNoWindow = true;
        processInfo.UseShellExecute = false;
        processInfo.RedirectStandardError = true;
        processInfo.RedirectStandardOutput = true;

        var process = Process.Start(processInfo);

        process.OutputDataReceived += (object sender, DataReceivedEventArgs e) =>
            Console.WriteLine("output>>" + e.Data);
        process.BeginOutputReadLine();

        process.ErrorDataReceived += (object sender, DataReceivedEventArgs e) =>
            Console.WriteLine("error>>" + e.Data);
        process.BeginErrorReadLine();

        process.WaitForExit();
        process.Close();
    }
    catch (Exception ex)
    {
        Console.WriteLine(ex.Message);
    }
    finally
    {
        // after print, delete the temporary file
        File.Delete(filename);
    }
}
```

Enjoy it!
