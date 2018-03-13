---
title: Angular 4 download file from server via http
date: 2018-03-13 13:17:18
tags:
- angular
- http
---

When want to download a file from server, usually just provide a `<a href...` will do. But what if the file only allow authorised user to access? Means you have to download first, in this case will have to use http

```ts
import { Http, ResponseContentType } from '@angular/http';
...

constructor(
  private http: Http,
) { }

downloadFile() {
  return this.http
    .get('https://jslim.net/path/to/file/download', {
      responseType: ResponseContentType.Blob,
      search: // query string if have
    })
    .map(res => {
      return {
        filename: 'filename.pdf',
        data: res.blob()
      };
    })
    .subscribe(res => {
        console.log('start download:',res);
        var url = window.URL.createObjectURL(res.data);
        var a = document.createElement('a');
        document.body.appendChild(a);
        a.setAttribute('style', 'display: none');
        a.href = url;
        a.download = res.filename;
        a.click();
        window.URL.revokeObjectURL(url);
        a.remove(); // remove the element
      }, error => {
        console.log('download error:', JSON.stringify(error));
      }, () => {
        console.log('Completed file download.')
      });
}
```

Make sure change the **responseType** to `ResponseContentType.Blob`.

Then, in the html file

```html
<button class="btn btn-primary" (click)="downloadFile()"><i class="fa fa-file-pdf-o"></i> Download</button>
```

Now, users are able to download the file when click on the button.

### References:

- [JavaScript blob filename without link](https://stackoverflow.com/questions/19327749/javascript-blob-filename-without-link/19328891#19328891)
- [How do I download a file with Angular2](https://stackoverflow.com/questions/35138424/how-do-i-download-a-file-with-angular2/35227885#35227885)
- [Angular 2/4 file download from web api](https://amitsethi0843.wordpress.com/2017/06/04/angular-24-file-download-from-web-api/)
