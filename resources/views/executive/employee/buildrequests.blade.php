@extends('executive/layouts.app')


@section('content')
<div class="content-wrapper">
   
    <div class="card">
        <div class="card-body">
            <div class="row">
             
                <ul class="nav nav-tabs margin-tb" style="border-bottom: 0px solid #dee2e6;">
                                                    
                </ul>
                 <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <h4 class="theme-color">Manage Build Requests</h4>
                    </div>
                    
                </div>
            </div>

            <div class="tab-content">
              
              <div id="" class="tab-pane fade in active show">
                <!-- Data table start-->
                <div class="row">
                    <div class="col-12">
                        <div id="request-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                      
                                    <table class="col-12" id="request-listing" class="table dataTable no-footer" role="grid" aria-describedby="request-listing_info">
                                        <thead>
                                            <tr>
                                                
                                                <td>Request From</td>
                                                <td>Request Action</td>
                                                <td width="20%">Build Name</td>
                                                
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
$('.delete_build').on('click', function(e){
    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );
    });
    var ajaxurl = '<?php echo url('builds/builddelete');?>';
    if(array.length == 0){
        swal("Ahh", "Please Select Builds", "error");
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
   
    var ajax_url2 = '<?php echo url('executive/employee/build requests/buildsrequestdatatable');?>';
    
   
    var url1 = '<?php echo url('executive/employee/buildshow');?>';
    var aurl = '<?php echo url('executive/employee/approve-request');?>';
    var viewurl = '<?php echo url('executive/employee/view-request');?>';
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
    
        columns: [
          
            { data: 'from_user', name: 'from_user' },
            { data: 'request_type', name: 'request_type' },
            { data: 'build_text', name: 'build_text' },
            { data: 'created_date', name: 'created_date' },
            {"mRender": function ( data, type, row ) { 

            return "<a class='btn action-btn btn-outline-info' href="+viewurl+'/'+row['id']+"><i class='fa fa-eye'></i> View</a> <a class='btn action-btn btn-outline-info' href="+aurl+'/'+row['id']+'/accept'+"><i class='fa fa-eye'></i> Approve</a> <a class='btn action-btn btn-outline-danger trash-button' href="+aurl+'/'+row['id']+'/reject'+" data-att-name='employee'><i class='fa fa-trash'></i> Reject</a>";

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
      
        requestLinsting();
 });
</script>
@endsection

