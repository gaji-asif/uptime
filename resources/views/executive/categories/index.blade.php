@extends('executive/layouts.app')


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
                        <a class="btn btn-theme margin-bottum-10" href="{{ route('categories.create') }}"> Create New Category</a>
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
                                           <!--  <td>id</td> -->
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

    var url = '<?php echo url('categories');?>';
    var durl = '<?php echo url('categories/delete');?>';
    var users_show = '<?php echo url('users');?>';
    $('#order-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo url('categories/categoriesdatatable');?>',
        "oLanguage": {
            "oPaginate": {
            "sFirst": "<i class='fa fa-chevron-left'></i><i class='fa fa-chevron-left'></i>",
            "sPrevious": "<i class='fa fa-chevron-left'></i>",
            "sNext": "<i class='fa fa-chevron-right'></i>",
            "sLast": "<i class='fa fa-chevron-right'></i><i class='fa fa-chevron-right'></i>" 
            }
        },
       
        columns: [
            /*{ data: 'id', name: 'id' },*/
            { data: 'category_name', name: 'category_name' },
            {"mRender": function ( data, type, row ) {
                return "<a href="+users_show+'/'+row['company_id']+" data-toggle='tooltip' data-placement='bottom' data-original-title='View company'>"+row['company_name']+"</a>"}
            },
            { data: 'created_at', name: 'created_at' },
            {"mRender": function ( data, type, row ) {
                return "<a class='btn action-btn btn-outline-primary' href="+url+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a> <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='category'><i class='fa fa-trash'></i></a>"}
            },
        ],
    });
}
</script>
@endsection
