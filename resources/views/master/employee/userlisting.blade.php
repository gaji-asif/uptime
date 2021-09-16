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
                        <h4 class="theme-color">Manage Employee</h4>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-theme margin-bottum-10" href="{{ route('admin.employee.employeecreate') }}"> Create New Employee</a>
                        <button type="button" class="btn btn-theme margin-bottum-10 delete_emp">Delete</button>
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
                                            <!-- <td>id</td> -->
                                            <td>Name</td>
                                            <td>Email</td>
                                            <td>company name</td>
                                            <td>Store</td>
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
    var ajax_url = '<?php echo url('admin/employee/useremployeedatatable');?>';
    var url = '<?php echo url('admin/employee/employeelist');?>';
    var urledit = '<?php echo url('admin/employee');?>';
    var url2 = '<?php echo url('admin/employee/showuser');?>';    
    var durl = '<?php echo url('admin/employee/delete');?>';
    var table = $('#order-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: ajax_url,
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
            { data: 'full_name', name: 'full_name' },
            { data: 'email', name: 'email' },
            { data: 'company_name', name: 'company_name' },
            { data: 'industry', name: 'industry' },
            { data: 'created_at', name: 'created_at' },
            {"mRender": function ( data, type, row ) {
                  return "<a class='btn action-btn btn-outline-info' href="+url2+'/'+row['id']+"><i class='fa fa-eye'></i></a>      <a class='btn action-btn btn-outline-primary' href="+urledit+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a>          <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='employee'><i class='fa fa-trash'></i></a>"
              }
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
$('.delete_emp').on('click', function(e){
    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );
    });
    var ajaxurl = '<?php echo url('admin/employee/employeedelete');?>';
    if(array.length == 0){
        swal("Ahh", "Please Select Employees", "error");
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