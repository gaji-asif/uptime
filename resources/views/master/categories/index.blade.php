@extends('master/layouts.app')


@section('content')
<div class="content-wrapper">

    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    @if ($message = Session::get('errors'))
    <div class="alert alert-danger">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h4 class="theme-color">Manage Category</h4>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-theme margin-bottum-10" href="{{ url('master/categories/create') }}"> Create New Category</a>
                          <button type="button" class="btn btn-theme margin-bottum-10 delete_cat">Delete</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">

                    <div id="order-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="order-listing" class="table dataTable no-footer" role="grid" aria-describedby="order-listing_info">
                                    <thead>
                                        <tr>
                                            <th style="border-top:1px solid #f3f3f3;"><input type="checkbox" name="select_all" value="1" id="example-select-all"></th>
                                            <td>Category Name</td>
                                            <td>Company Name</td>
                                            <td>Created At</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>

                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12 col-md-5"></div>
                            <div class="col-sm-12 col-md-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
$(function() {
    oderLinsting();
});
function oderLinsting(){
    if ( $.fn.DataTable.isDataTable('#order-listing') ) {
        $('#order-listing').DataTable().destroy();
    }
    $('#order-listing tbody').empty();

    var url = '<?php echo url('master/categories');?>';
    var durl = '<?php echo url('master/categories/delete');?>';
    var users_show = '<?php echo url('users');?>';
     var table = $('#order-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo url('master/categories/categoriesdatatable');?>',
        "oLanguage": {
            "oPaginate": {
            "sFirst": "<i class='fa fa-chevron-left'></i><i class='fa fa-chevron-left'></i>",
            "sPrevious": "<i class='fa fa-chevron-left'></i>",
            "sNext": "<i class='fa fa-chevron-right'></i>",
            "sLast": "<i class='fa fa-chevron-right'></i><i class='fa fa-chevron-right'></i>"
            }
        },
          columnDefs: [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'className': 'dt-body-center',
            'render': function (data, type, full, meta){
                return '<input type="checkbox" name="id[]" value="' +full['id'] + '">';
            }
        }],

        columns: [
             {"bSortable": false},
            { data: 'category_name', name: 'category_name' },
            {"mRender": function ( data, type, row ) {
                return "<a href="+users_show+'/'+row['company_id']+" data-toggle='tooltip' data-placement='bottom' data-original-title='View company'>"+row['company_name']+"</a>"}
            },
            { data: 'created_at', name: 'created_at' },
            {"mRender": function ( data, type, row ) {
                if (row['deleted_at']) {
                  return "<a class='btn action-btn btn-outline-info' href="+url+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a> <a class='btn action-btn btn-outline-primary' href="+url+ '/' + row['id']+'/restore/'+" data-att-name='category'><i class='fa fa-undo'></i></a>";
                }
                return "<a class='btn action-btn btn-outline-primary' href="+url+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a> <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='category'><i class='fa fa-trash'></i></a>"}
            },
        ],
         initComplete: function(settings, json) {
            if($('.dt-body-center').hasClass('sorting_asc')){
                $('.dt-body-center').removeClass('sorting_asc');
            }
        }
    });
         setTimeout(function(){
        multilpleCheckbox(table);
    }, 1000);

}

// Handle form submission event

$('.delete_cat').on('click', function(e){
    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );
    });
    var ajaxurl = '<?php echo url('master/categories/categorydelete');?>';
    if(array.length == 0){
        swal("Ahh", "Please Select Categories", "error");
    }else{
        swal({
            title: "Are you sure?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel please!",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: ajaxurl,
                    type:'POST',
                    dataType:'json',
                    data: {
                        ids:array,
                    },
                    success: function(result){
                        var table = $('#order-listing').DataTable();
                        table.ajax.reload( function ( json ) {
                            $('#myInput').val( json.lastInput );
                        } );
                        swal.close();
                        $("#example-select-all").prop("checked", false);
                    }
                });
            }else {
                swal.close();
            }
        });
    }
});

</script>
@endsection
