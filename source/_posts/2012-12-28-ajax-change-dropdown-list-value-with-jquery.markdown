---
layout: post
title: "Ajax change dropdown list value with jQuery"
date: 2012-12-28 12:29
comments: true
categories: 
- javascript
---

A typical example for this is **Country** and **State/Province**.

You may have seen some website that provide such a dropdown list. When you change the **Country**, the **State** will be reloaded.

Now, I want to show this example using jQuery.

```js
$(function() {
    $('#country').change(function() {
        $.ajax({
            url: '/ajax/state',
            dataType: 'json',
            type: 'GET',
            // This is query string i.e. country_id=123
            data: {country_id : $('#country').val()},
            success: function(data) {
                $('#state').empty(); // clear the current elements in select box
                for (row in data) {
                    $('#state').append($('<option></option>').attr('value', data[row].stateId).text(data[row].stateName));
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
});
```

_Reference: [How to change options of \<select \> with jQuery?](http://stackoverflow.com/questions/1801499/how-to-change-options-of-select-with-jquery#answers)_
