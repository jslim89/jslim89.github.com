---
title: DataTable re-order rows
date: 2019-03-01 18:30:11
tags:
  - datatables
  - laravel
  - javascript
---

In CMS, often the navigation menu is editable in backend, and can change the order.
Let's see how to implement using [DataTable](https://datatables.net/).

### DB table

`menu`

| id  | seq | col_1 | col_2 | col_3 |
| --- | --- | ----- | ----- | ----- |
| 1   | 1   | val 1 | val 1 | val 1 |
| 2   | 2   | val 2 | val 2 | val 2 |
| 3   | 3   | val 3 | val 3 | val 3 |

### In HTML

```html
@extends('layout')
@section('content')
<table id="my-table" class="table table-pn table-striped">
    <thead>
    <tr>
        <th>Seq</th>
        <th>Col 1</th>
        <th>Col 2</th>
        <th>Col 3</th>
    </tr>
    </thead>
    <tbody></tbody>
</table>
@endsection

@push('css')
    <link href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/rowreorder/1.2.5/css/rowReorder.dataTables.min.css" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.2.5/js/dataTables.rowReorder.min.js"></script>
@endpush
```

### JavaScript

```js
$(function () {
    // declare a global variable
    var table;

    function getParams() {
        return {
            'q': $('#q').val(),
        };
    }

    function loadTable() {

        if (table) {
            table.ajax.reload(null, true);
            return;
        }
        table = $('#my-table').DataTable({
            'serverSide': true,
            'paging': true,
            'rowReorder': {
                'selector': 'tr',
                'dataSrc': 'seq', // follow the json data
            },
            'columnDefs': [
                { 'orderable': true, 'targets': 0, 'className': 'reorder' },
                { 'orderable': false, 'targets': '_all' }
            ],
            'columns': [
                { 'data': 'seq' },
                { 'data': 'col_1' },
                { 'data': 'col_2' },
                { 'data': 'col_3' },
            ],
            'ajax': {
                'url': '/get-menu-data',
                'type': 'GET',
                'data': function (d) { // do in this way, otherwise table.ajax.reload() not working
                    Object.assign(d, getParams());
                    return d;
                },
            },
        });

        // every time drag the row will send a request to server to update the `seq` column
        table.on( 'row-reorder', function ( e, diff, edit ) {
            var postData = [];
            for ( var i = 0, ien = diff.length ; i < ien ; i++ ) {
                var rowData = table.row(diff[i].node).data();
                postData.push({
                    'id': rowData.id,
                    'new_seq': diff[i].newData,
                });
            }

            $.ajax({
                url: '/update-menu-seq',
                dataType: 'json',
                type: 'post',
                async: false, // disable async, otherwise the table will reload before the db update
                data: {
                    seqs: postData,
                },
                success: function (data, textStatus, jqXHR) {
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                },
                complete: function (jqXHR, textStatus) {
                }
            });
        });
    }
    loadTable();
});
```

## In PHP

I show the example in Laravel

```php
<?php
class IndexController extends Controller
{
    public function getMenuData(Request $request)
    {
        $query = Menu::select([
            'id',
            'col_1',
            'col_2',
            'col_3',
            'seq',
        ]);

        $total = $query->count();

        if ($q = $request->input('q')) { // if search
            $query->where(function ($query) use ($q) {
                $query->orWhere('col_1', 'LIKE', '%' . $q . '%')
                    ->orWhere('col_2', 'LIKE', '%' . $q . '%')
                    ->orWhere('col_3', 'LIKE', '%' . $q . '%');
            });
        }

        $filtered = $query->count();

        $query->orderBy('seq');

        $results = $query->skip($request->input('start', 0))
            ->take($request->input('length', 50))
            ->get();

        return [
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $results,
        ];
    }

    public function updateMenuSequence(Request $request)
    {
        $rules = [
            'seqs' => 'required|array',
        ];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->messages()->first(),
            ], 400);
        }
        $sequences = $request->input('seqs', []);

        foreach ($sequences as $sequence) {
            $menu = Menu::where('id', $sequence['id'])->first();
            if (empty($menu)) {
                return response()->json([
                    'error' => sprintf('Menu %d not found.', $sequence['id']),
                ], 400);
            }
            $menu->seq = $sequence['new_seq'];
            $menu->save();
        }

        return [
            'success' => true,
            'message' => 'Menu sequence updated.',
        ];
    }
}
```

Now everytime dragged the table row, will trigger the update of the menu sequence, then reload the data table data.

## References

- [Reorder event](https://datatables.net/extensions/rowreorder/examples/initialisation/events.html)
- [DataTables ajax.reload() with parameters](https://stackoverflow.com/questions/42412845/datatables-ajax-reload-with-parameters/42435661#42435661)
