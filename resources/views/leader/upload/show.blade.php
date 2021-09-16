@extends('leader/layouts.app')

@section('content')
  <div class="content-wrapper">
    <div class="row profile-page">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            
            <div class="profile-body">
            <h4 class="card-title theme-color">Upload Details</h4>  
               <input type = "number" id="upload_id" value="{{$upload->id}}" hidden>                                    
                <div class= "row">
                <div class="col-6 form-group">
                        <h5 class="my-4">Upload Image</h5>
                        <div class="new-accounts upload-detail-image">
                          @if($upload->image != '')
                          <img src="<?php echo \Storage::disk('s3')->url('images/upload/'.$upload->image);?>" alt="Upload image" width="200px" height = "200px" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                          @else
                          <div class="btn btn-outline-danger file-icon">
                            <i class="mdi mdi-image-broken"></i>
                          </div>
                          @endif
                        </div>
                        <br>                          
                            <p>created At :<strong> {{ $upload->created_at }}</strong></p>
                </div>
                <div class = "col-6">                    
                   <div class="form-group">
                    <strong><label>Select Store *</label></strong>
                    <select class="form-control" name="industry" id="industry">                        
                        <option value="">Select Store</option>
                        @foreach ($upload_data['region'] as $item)
                            <option value="{{$item['id']}}">{{$item['industry_name']}}</option>
                        @endforeach
                    </select>
                  </div> 
                  <div class= "form-group" id= "total_empcount"> 
                         <div class="form-group"><strong><label>PhotoViews Count(total region)</label></strong></div>
                         <input type= "text" id= "totalregion_employee_count"  class = "form-control" value = "{{$upload_data['totalregion_count']}}" readonly> 
                   </div>    
                   <div class= "form-group hide"id= "employee_count">   
                         
                   </div>             
               </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
    $('#industry').change(function(e){
        e.preventDefault();
        var form = $(this).closest('form');
        var region = $(this).val();
        $('#employee_count').removeClass('hide').addClass('hide');
        $('#employee_count').html('');
        
        $('#total_empcount').addClass('hide');
        $('#total_empcount').html('');
        
        id = $('#upload_id').val();        
        if(region != ''){
                    $.ajax({
                      headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                       url: "{{ url('leader/upload/get_photoview_empcount') }}/"+region+"/"+id,
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                        if(result.status){

                          $('#employee_count').removeClass('hide');
                          $('#employee_count').html(result.html);
                        }
                        else{
		              swal("Oops!", result.message, "error");
		       }
		      }
		   });
	}
                             
    });
</script>
@endsection