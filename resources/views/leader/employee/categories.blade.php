@extends('leader/layouts.app')


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
                        <a class="btn btn-theme margin-bottum-10" href="{{ route('leader.employee.categories.create')  }}"> Create New Sub Category</a>
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
                                            <td>Count</td>
                                            <td>Main Category</td>
                                            <td>Sub Category</td>
                                            <td>Status</td>
                                            <td>Store</td>
                                            <td>Access Level</td>
                                            <td>Created On</td>
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

    var url = '<?php echo url('leader/employee/categories');?>';
    var durl = '<?php echo url('leader/employee/categories/delete');?>';
    var users_show = '<?php echo url('users');?>';
    $('#order-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo url('leader/employee/categories/categorydatatable');?>',
        "oLanguage": {
            "oPaginate": {
            "sFirst": "<i class='fa fa-chevron-left'></i><i class='fa fa-chevron-left'></i>",
            "sPrevious": "<i class='fa fa-chevron-left'></i>",
            "sNext": "<i class='fa fa-chevron-right'></i>",
            "sLast": "<i class='fa fa-chevron-right'></i><i class='fa fa-chevron-right'></i>" 
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'category_name', name: 'category_name' },
            { data: 'subcategory_name', name: 'subcategory_name' },
            { data: 'status', name: 'status' },
            { data: 'industry_name', name: 'industry_name' },
            { data: 'user_access_level', name: 'user_access_level' },
            { data: 'created_date', name: 'created_date' },
            

            {"mRender": function ( data, type, row ) {
                return "<a class='btn action-btn btn-outline-primary' href="+url+'/edit/'+row['id']+"><i class='fa fa-pencil'></i></a>"}
            },
        ],
    });
}
</script>
@endsection