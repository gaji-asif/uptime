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
                        <h4 class="theme-color">Manage Submission</h4>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-theme margin-bottum-10" href="{{ route('leader.employee.builds.createbuild') }}"> Create New Submission</a>
                        <button type="button" class="btn btn-theme margin-bottum-10 delete_build">Delete</button>
                    </div>
                </div> 

             
                <ul class="nav nav-tabs margin-tb" style="border-bottom: 0px solid #dee2e6;">
                                           
                </ul>
               
            </div>

            <div class="tab-content">
              <div id="home" class="tab-pane fade in active show">
                <!-- Data table start-->
                <div class="row">
                    <div class="col-12">
                        <div id="order-listing_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                      
                                    <table id="order-listing" class="table dataTable no-footer" role="grid" aria-describedby="order-listing_info">
                                        <thead>
                                            <tr>
                                                <td><input type="checkbox" name="select_all" value="1" id="example-select-all"></td>
                                                <td>Submission Name</td>                                               
                                                <td>Status</td>
                                                <td>Employee Name</td>
                                                <td>Challenge</td>
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
        </div>
</div>
<script>

function oderLinsting(){
    if ( $.fn.DataTable.isDataTable('#order-listing') ) {
        $('#order-listing').DataTable().destroy();
    }
    $('#order-listing tbody').empty();
    
    var ajax_url = '<?php echo url('leader/employee/build/buildsdatatable');?>';
    
   
    var url = '<?php echo url('leader/employee/buildshow');?>';
    var durl = '<?php echo url('leader/employee/builds/delete');?>';
    var eurl = '<?php echo url('leader/employee/edit-build');?>';
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
         
            { data: 'build_text', name: 'build_text' },
           
            { data: 'status', name: 'status' },
            { data: 'employee_name', name: 'employee_name' },
            { className: 'text-center',"mRender": function ( data, type, row ) {                
                return "<i class='"+row['challenge_check']+"'></i>" } },            
            { data: 'created_date', name: 'created_date' },        
            {"mRender": function ( data, type, row ) {
                emp = '';
                if (row['user_level'] == 1) {
                    var emp = 'employee1'
                }else{
                    var emp = 'employee'

                }

               /* return "<a class='btn action-btn btn-outline-info' href="+url+'/'+row['id']+"><i class='fa fa-eye'></i></a>  <a class='btn action-btn btn-outline-danger trash-button' href="+durl+'/'+row['id']+" data-att-name='"+emp+"'><i class='fa fa-trash'></i></a>"}
            },*/
            return "<a class='btn action-btn btn-outline-info' href="+url+'/'+row['id']+"><i class='fa fa-eye'></i></a>"}
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
$('.delete_build').on('click', function(e){
   
    e.preventDefault();
    var array = [];
    $("input[type=checkbox]:checked").each(function(i){
        array.push( this.value );

    });

    var ajaxurl = '<?php echo url('leader/employee/builds/builddelete');?>';
   
    if(array.length == 0){
        swal("Ahh", "Please Make Selection", "error");
    }

    else{
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

 $(document).ready(function() {
        oderLinsting();

 });
</script>
@endsection

