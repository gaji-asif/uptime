@extends('rep/layouts.app')


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
                        <h4 class="theme-color">Manage Regions</h4>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-theme margin-bottum-10" href="{{ url('industry/create') }}"> Create New Region</a>
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
                                            <td>Region Name</td>
                                           <!-- <td>Access Level</td>
                                            <td>Category </td>
                                        -->
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

    var url = '<?php echo url('industry');?>';
    var durl = '<?php echo url('industry/delete');?>';
    var users_show = '<?php echo url('users');?>';
    $('#order-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?php echo url('industry/industrydatatable');?>',
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
            { data: 'industry_name', name: 'industry_name' },
          //  { data: 'access_level', name: 'access_level' },
          //  { data: 'category_name', name: 'category_name' },

            {"mRender": function ( data, type, row ) {
                return "<a class='btn action-btn btn-outline-primary' href="+url+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a> <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='industry'><i class='fa fa-trash'></i></a>"}
            },
        ],
    });
}
</script>
@endsection