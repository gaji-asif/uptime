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
                        <h4 class="theme-color">Manage Timed Challenge</h4>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-theme margin-bottum-10" href="{{ route('leader.level-challenge.create') }}"> Create New Challenge</a>
                       
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
                                            <td>id</td>
                                            <td>Challenge Name</td>
                                            <td>Status</td>
                                            <td>Active</td>
                                            <td>Points</td>
                                            <td>Store</td>
                                            <td>AccessLevel</td>
                                            <td>Created At</td>
                                            <td>End Date</td>
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

    var url = '<?php echo url('leader/employee/level-challenge');?>';
    var durl = '<?php echo url('leader/employee/level-challenge/delete');?>';
    var table = $('#order-listing').DataTable({
        processing: true,
        // serverSide: true,
        ajax: '<?php echo url('leader/employee/level-challenge/challengedatatable');?>',
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
            { data: 'challenge_text', name: 'challenge_text' },
            { data: 'status', name: 'status' },
            { data: 'is_active', name: 'is_active' },
            { data: 'point', name: 'point' },
            { data: 'industry', name: 'industry' },
            { data: 'access_level', name: 'access_level' },
            { data: 'created_date', name: 'created_date' },
            { data: 'end_on', name: 'end_on' },
            {"mRender": function ( data, type, row ) {
                return "<a class='btn action-btn btn-outline-info' href="+url+'/'+row['id']+"><i class='fa fa-eye'></i></a> <a class='btn action-btn btn-outline-primary ' href="+url+'/'+row['id']+'/edit'+"><i class='fa fa-pencil'></i></a> <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='challenge'><i class='fa fa-trash'></i></a>"}
            },
        ],
         
    });
    
}

// Handle form submission event
$('.delete_challenge').on('click', function(e){

    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );
        
    });
    var ajaxurl = '<?php echo url('leader/employee/level-challenge/challangedelete');?>';
    if(array.length == 0){
        swal("Ahh", "Please Select Challenges", "error");
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
                        oderLinsting();
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

