@extends('leader/layouts.app')

@section('content')
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            
            <div class="profile-body">
            <h4 class="card-title theme-color">Reward Details</h4>
             
                   <div class="row">
                     <div class="col-md-3">
                       <div class="form-group">

                        <h5 class="my-4">Reward Image</h5>

                        <div class="new-accounts reward-detail-image">
                          @if($reward->image != '')
                          <img src="<?php echo \Storage::disk('s3')->url('images/reward/'.$reward->image);?>" alt="Reward image" width="200px" height = "200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                          @else
                          <div class="btn btn-outline-danger file-icon">
                            <i class="mdi mdi-image-broken"></i>
                          </div>
                          @endif
                        </div>
                        <br>
                            <p>created At :<strong> {{ $reward->created_at }}</strong></p>
                              <p>created At :<strong> {{ $reward->description }}</strong></p>
                      </div>

                     </div>




                     <!-- list view  -->
                     <div class="col-md-9">
                           <div class="row">
                              <div class="col-lg-12 margin-tb">
                                <div class="pull-left">
                                    <h5 class="title">Employee List</h5>
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
                                                             
                                                            <td>Name</td>
                                                            <td>Image</td>
                                                            <td>Purchase Date</td>
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
                     <!-- end of list view  -->
              </div>
              <!-- end of row -->
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  .title{
      margin-top:27%;
      margin-bottom: 25%;

  }
</style>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script>
  
$(function() {
  
   oderLinsting();
});

function oderLinsting(){

    if ( $.fn.DataTable.isDataTable('#order-listing') ) {
        $('#order-listing').DataTable().destroy();
    }
    $('#order-listing tbody').empty();        
    var ajax_url = '<?php echo url('executive/reward/useremployeedatatable');?>';
   
    var url2 = '<?php echo url('executive/reward/showemployee');?>';    
   
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
      
        columns: [
            
            { data: 'full_name', name: 'full_name' },                      
            { data: 'image', name: 'image' },
            { data: 'created_at',name:'created_at' },
            {"mRender": function ( data, type, row ) {
                  return "<a class='btn action-btn btn-outline-info' href="+url2+'/'+row['id']+"><i class='fa fa-eye'></i></a>"
              }
            },
        ],
        
    });
   
    
}

</script>
@endsection