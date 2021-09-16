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
                        <h4 class="theme-color">Manage Challenge Requests</h4>
                    </div>
                    
                </div> 
            </div>
            <div class="tab-content">
              
              <div id="" class="tab-pane fade  in active show">
                <!-- Data table start-->
                <div class="row">
                    <div class="col-12">
                        <div id="request-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                      
                                    <table class="col-12" id="request-listing" class="table dataTable no-footer" role="grid" aria-describedby="request-listing_info">
                                        <thead>
                                            <tr>
                                                <td><input type="checkbox" name="select_all" value="1" id="example-select-all"></td>
                                               <!--  <td>id</td> -->
                                                <td>Request From</td>
                                                <td>Request Action</td>
                                                <!-- <td>Status</td> -->
                                                <td width="20%">Challenge Name</td>
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
                                    <div class="dataTables_paginate paging_simple_numbers" id="request-listing_paginate">
                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Data Table end-->
              </div>
       </div>
        </div>
    </div>

</div>
<script>


// Handle form submission event
$('.delete_challenge').on('click', function(e){
    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );
    });
    var ajaxurl = '<?php echo url('admin/challenge/challangedelete');?>';
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



/* request datatable  */
function requestLinsting(){ 
    if ( $.fn.DataTable.isDataTable('#request-listing') ) {
        $('#request-listing').DataTable().destroy();
    }
    $('#request-listing tbody').empty();
    var ajax_url2 = '<?php echo url('admin/employee/challenge requests/challengerequestdatatable');?>';
    /*if(call_from == '1'){
        var ajax_url = '<?php //echo url('builds/employee-data/'.$builds->employee_id);?>';
    }else if(call_from == '2'){
        var ajax_url = '<?php //echo url('builds/employeestatusajax/'.$builds->employee_id.'/'.$builds->employee_g_status);?>';
    }*/
   
    var url1 = '<?php echo url('admin/employee/buildshow');?>';
    var aurl = '<?php echo url('admin/employee/approve-request-challenge/');?>';
    var table1 = $('#request-listing').DataTable({
        processing: true,
        serverSide: true,
        ajax: ajax_url2,
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
            { data: 'from_user', name: 'from_user' },
            { data: 'request_type', name: 'request_type' },
            { data: 'challenge_text', name: 'challenge_text' },
            { data: 'created_at', name: 'created_at' },

            {"mRender": function ( data, type, row ) { 

            return "<a class='btn action-btn btn-outline-info' href="+aurl+'/'+row['id']+'/accept'+"><i class='fa fa-eye'></i> Approve</a> <a class='btn action-btn btn-outline-danger trash-button' href="+aurl+'/'+row['id']+'/reject'+" data-att-name='employee'><i class='fa fa-trash'></i> Reject</a>";

                }
            },
        ],
        initComplete: function(settings, json) {
            if($('.dt-body-center').hasClass('sorting_asc')){
                $('.dt-body-center').removeClass('sorting_asc');
            }
            $('#request-listing').addClass('table');
        }
    });
    setTimeout(function(){
        multilpleCheckbox(table1);
    }, 1000);
}


 $(document).ready(function() {
        //oderLinsting();
        requestLinsting();
    } );
</script>
@endsection


 