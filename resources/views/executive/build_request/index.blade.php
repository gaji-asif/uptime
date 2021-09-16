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
                        <h4 class="theme-color">Manage Submission Requests</h4>
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
                                                <td width="20%">Employee Name</td>
                                                <td width="20%">Request Action</td> 
                                                <td width="20%">Submission IDs</td>                                                                                      
                                                <td width="20%">Created At</td>
                                                <td width="20%">Action</td> 
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

 $(function() {     
        requestLinsting();
 });
/* request datatable  */
function requestLinsting(){ 
    if ( $.fn.DataTable.isDataTable('#request-listing') ) {
        $('#request-listing').DataTable().destroy();
    }
    $('#request-listing tbody').empty();
   
    var ajax_url = '<?php echo url('executive/build_request/buildrequestdatatable');?>';
    
   
    var url = '<?php echo url('executive/build_request/show');?>';
    var appr_url = '<?php echo url('executive/build_request/approve');?>';
    var reject_url = '<?php echo url('executive/build_request/reject');?>';
    var table1 = $('#request-listing').DataTable({
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
    
        columns: [
          
            { data: 'employee_name', name: 'employee_name' },
            { data: 'request_type', name: 'request_type' },
            { data: 'data', name: 'data' },           
            { data: 'created_at', name: 'created_at' },
            {"mRender": function ( data, type, row ) { 

            return "<a class='btn action-btn btn-outline-info' href="+appr_url+'/'+row['id']+"><i class='fa fa-eye'></i> Approve</a> <a class='btn action-btn btn-outline-danger trash-button' href="+reject_url+'/'+row['id']+" data-att-name='employee'><i class='fa fa-trash'></i> Reject</a>";

                }
            },
        ],
         
    });
   }  
</script>
@endsection

