---
title: Why Vue.js $emit not working
date: 2020-01-09 19:54:36
tags:
- vue
- vuejs
---

I'm working Vue with Laravel, creating a component _(nested view)_, and try to handle the user event in the root _(Laravel blade file)_.

But the problem is, in the root view, the event listener never get called.

See the code 👇

## Sample code

**MyFolder.vue** is a nested component

```vue
<template>
    <div :data-level="level" :style="level > 0 ? 'margin-left: 20px;' : ''">
        <div v-for="folder in folders">
            <div class="folder-item">
                <input :name="name"
                       type="checkbox"
                       :value="folder.id"
                       @change="folderChanged(folder)"
                       v-model="folder.checked">
            </div>
            <my-folder v-if="folder.children && folder.children.length"
                       type="checkbox"
                       :name="name"
                       :folders="folder.children"
                       v-on:checkboxToggled="folderChanged"
                       :level="level+1"></my-folder>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                required: true,
                type: String,
            },
            level: {
                required: true,
                type: Number,
            },
            folders: {
                required: true,
                type: Object,
            },
        },
        data: function() {
            return { }
        },
        methods: {
            folderChanged: function (folder) {
                console.log('in level ' + this.level + ', folder changed ' + folder.id);
                this.$emit('checkboxToggled', folder);
            },
        },
        computed: {
        }
    }
</script>
```

**index.html** Use the component, and pass the root folders

```html
<my-folder :folders="rootFolders"
           :name="'folders[]'"
           v-on:checkboxToggled="folderCheckboxHandler"
           :level="0"></my-folder>

<script src="/path/to/app.js"></script>
```

In **app.js**, define a handling method for folder checkbox toggle event.

```js
Vue.component('my-folder', require('./vue/MyFolder.vue'));

var app = new Vue({
    el: '#app',
    ...
    methods: {
        folderCheckboxHandler: function (folder) {
            console.log('in root level, folder changed ' + folder.id);
        },
    },
});
```

## Try it out

When check or uncheck the checkbox in level 2, the console output will be

```
in level 1, folder changed 15
in level 0, folder changed 15
```

The event from the inner child, passed to outer child, but never reach the root
event handler `folderCheckboxHandler`.

I was struggled for half day.

At the end, I figured out that the event name can't be camelCase _(see reference below)_,
then I change to **kebab-case**, it works just fine.

### Update

In **index.html**

```html
<my-folder :folders="rootFolders"
           :name="'folders[]'"
           v-on:checkbox-toggled="folderCheckboxHandler" // <-------- CHANGE THIS
           :level="0"></my-folder>
```

In **MyFolder.vue**

```vue
<template>
    ...
    <my-folder v-if="folder.children && folder.children.length"
               type="checkbox"
               :name="name"
               :folders="folder.children"
               v-on:checkbox-toggled="folderChanged" // <-------- CHANGE THIS
               :level="level+1"></my-folder>
   ...
</template>
...
methods: {
    folderChanged: function (folder) {
        console.log('in level ' + this.level + ', folder changed ' + folder.id);
        this.$emit('checkbox-toggled', folder); // <-------- CHANGE THIS
    },
},
```

## References:

- [Vue - Custom Events](https://vuejs.org/v2/guide/components-custom-events.html#Event-Names)
