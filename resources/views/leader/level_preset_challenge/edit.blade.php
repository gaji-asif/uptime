@extends('leader/layouts.app')


@section('content')
<div class="content-wrapper">
@if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                <li>{{ $message }}</li>
            </ul>
        </div>
    @endif
    

    <div class="card col-md-8">
        <div class="card-body">
        <h4 class="card-title theme-color">Edit Preset Challenge</h4>
        @if(isset($challenge))
            <form action="{{ url('leader/employee/level-preset-challenge/update',$challenge->id) }}" method="post" class="forms-sample"  enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <strong><label for="challenge_text">New Challenge Name *</label> </strong>
                    <input type="text" name="challenge_text" id="challenge_text" class="form-control" value="{{$challenge->challenge_text}}" placeholder="Challenge Title" required>
                </div>

                <div class="form-group {{($challenge_data['categories'] ? '' : 'hide')}}">
                    <strong><label for="category">Main Category *</label></strong>
                    <select class="form-control" name="category" id="category" required>
                        <option value="">Select Category</option>
                        @foreach ($challenge_data['categories'] as $cat)
                            <option value="{{$cat['id']}}" {{($challenge->category_id == $cat['id'] ? "selected" : "")}}>{{$cat['category_name']}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group sub_cat ">
                    <strong><label for="category">SubCategory *</label></strong>
                    <select class="form-control" name="subcategory" id="subcategory">
                        <option value="">select Subcategory</option>  
                        @foreach($challenge_data['subcategory'] as $item)
                           <option value="{{$item['id']}}" {{$challenge->subcategory_id == $item['id'] ?"selected":""}}>{{$item['subcategory_name']}}</option>  
                        @endforeach 
                    </select>
                </div>

                <div class="form-group row" hidden>
                    <strong class="col-sm-3"><label class="col-form-label">Status *</label> </strong>
                    <div class="col-sm-3">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="status" id="status-1" value="-1" {{$challenge->status == '-1' ? "checked" : ""}} required>
                            -1 &nbsp;(Waiting)
                        <i class="input-helper"></i></label>
                        </div>
                    </div>
                   
                </div>
             
                <div class="form-group row">
                    <strong class="col-sm-3"><label class="col-form-label">Active *</label> </strong>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="active1" value="0" {{$challenge->is_active == '0' ? "checked" : ""}} required>
                            0
                        <i class="input-helper"></i></label>
                        <br>
                        <label>In Active</label>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-radio">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="is_active" id="active2" value="1" {{$challenge->is_active == '1' ? "checked" : ""}} required>
                            1
                        <i class="input-helper"></i></label>
                         <br>
                         <label>Active</label>
                        </div>
                    </div>          
                </div>
                <div class="form-group">
                    <strong><label for="point">Points</label></strong>
                    <input type="number" class="form-control" name="point" id="point" min="0" value="{{ $challenge->point }}">
                </div>
                
                <div class="form-group">
                    <strong><label>New Challenge Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                    <input type="hidden" id="delete_image" name="delete_image" value='0'>
                </div>
                @if($challenge->image != '')
                    <div class="form-group form-group-image">
                    <strong><label>Profile Pic</label></strong>
                        <div class="relativeDiv">
                            <div id="removeImage" class="removeImage" title="Remove Pic"><i class="fa fa-close"></i></div>
                            <img src="<?php echo \Storage::disk('s3')->url('images/challenge/'.$challenge->image);?>" onerror="this.src='<?php echo url('images/no_image.png'); ?>'">
                        </div>
                    </div>
                @endif     
 		<div class="form-group hide">
                   <input type="text" class="form-control" name="empcount" id="empcount" hidden>
               </div>  
                 
               
		<div class="form-group">
                    <strong><label for="sent_to">Sent to</label></strong>
                    <select class="form-control select_target" name="sent_to" id="sent_to">
                        <option value="">Select</option>  
                        <option value="1">Employee</option>
                        <option value="2">Region</option>
                        <option value="3">All</option>
                    </select>
                </div>
               
               <div id="employee_industry" class="form-group hide">
                </div>                 
               <div id="accesslevel" class="form-group hide">
                </div>               
               <div id="employee-list" class="form-group hide">                    
                </div>  
                
               <input type="text" class="form-control" name="emp_industry" id="emp_industry" hidden>
               <input type="text" class="form-control" name="emp_accesslevel" id="emp_accesslevel" hidden>
                 <!-- <div class="form-group">
                    <strong><label for="end_date">Expiry date</label></strong>
                    <input type="text" value="{{ $challenge->end_on }}" class="form-control" name="end_date" id="datetimepicker">
                </div>  -->

                <button type="submit" class="btn btn-theme mr-2">Submit</button>
            </form>
        @else
            <div class="text-center"><h3><i class="fa fa-warning size-40"></i></br> This challenge is in use or its over.</h3></div>
        @endif
        </div>
    </div>
        <!-- </div>
    </div> -->

</div>

<style type="text/css">
    input#datetimepicker{
        border: 1px solid #f3f3f3;
        font-family: "Open Sans", sans-serif;
        font-size: 0.75rem;
        padding: 0.56rem 0.75rem;
        line-height: 14px;
        width: 100%;
    }
       dt {
    font-weight: 300;
}
</style>

<script type="text/javascript">

  var count = 0;
  var id_arr = []; 

  function getSelectedValue(id) {
     
    return $("#" + id).find("dt a span.value").html();
  }
  function removeElement(arr,index){

    var result = [];
    var j = 0 ;
    for(var i = 0 ; i < arr.length ; i++){
      if(index != i){
         result[j] = arr[i];
         j++;
      }
    }
    return result;
  }
  
    $('#datetimepicker_mask').datetimepicker({
        mask:'9999/19/39 29:59',
    });
    $('#datetimepicker').datetimepicker();
    $('#datetimepicker1').datetimepicker({
        datepicker:false,
        format:'H:i',
        step:5
    });

    $( document ).ready(function() {
           $("#company_id").change(function(){
            $('.sub_cat').addClass("hide");
        });

	
 $('.select_target').change(function(e) {

		
                 e.preventDefault();
                 var form = $(this).closest('form');
                 var key = $(this).val();
                       
                 form.find('#employee_industry').removeClass('hide').addClass('hide');
                 form.find('#employee_industry').html('');
                 form.find('#accesslevel').removeClass('hide').addClass('hide');
                 form.find('#accesslevel').html('');
                 form.find('#employee-list').removeClass('hide').addClass('hide');
                 form.find('#employee-list').html('');
                  if(key != ''){
                    $.ajax({
                      headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                       url: "{{ url('leader/employee/level-challenge/getregion') }}/"+key,
                      type:'POST',
                      dataType:'json',
                      success: function(result){
                        if(result.status){

                          form.find('#employee_industry').removeClass('hide');
                          form.find('#employee_industry').html(result.html);
                                                    
                          //in region case 
                          $('#region').change(function(e){
                                e.preventDefault();
                 		var form = $(this).closest('form');
                 		var region = $(this).val();
                 		$('#emp_industry').val(region);
                 		
                 		if(region != ''){
                 		      $.ajax({
		                      headers: {
		                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		                      },
		                       url: "{{ url('leader/employee/level-challenge/getaccesslevel') }}",
		                      type:'POST',
		                      dataType:'json',
		                      success: function(result){
		                        if(result.status){		                         
		                          form.find('#accesslevel').removeClass('hide');
		                          form.find('#accesslevel').html(result.html);
		                          
		                          $('#employee_accesslevel').change(function(e){
		                                e.preventDefault();
                 				var form = $(this).closest('form');
                 				var level= $(this).val();
                 				$('#emp_accesslevel').val(level);                				                				
		                          });		                          
		                 	}
		                 	else{
		                 	  swal("Oops!", result.message, "error");
		                 	}
		                      }
		                    });
		                }
                 	  });
                 	  
                 	  
                 	  
                 	  
                 	  //in employee case 
                          $('#employee_region').change(function(e){
                                e.preventDefault();
                 		var form = $(this).closest('form');
                 		var region = $(this).val();
                 			$('#emp_industry').val(region);
                 		if(region != ''){
                 		      $.ajax({
		                      headers: {
		                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		                      },
		                       url: "{{ url('leader/employee/level-challenge/getaccesslevel') }}",
		                      type:'POST',
		                      dataType:'json',
		                      success: function(result){
		                        if(result.status){		                         
		                          form.find('#accesslevel').removeClass('hide');
		                          form.find('#accesslevel').html(result.html);
		                          
		                          $('#employee_accesslevel').change(function(e){
		                                e.preventDefault();
                 				var form = $(this).closest('form');
                 				var level= $(this).val();
                 					$('#emp_accesslevel').val(level);
                 				 count  =0;
                 				id_arr = []; 
                 				
                 				if(level!= ''){
		                 		      $.ajax({
				                      headers: {
				                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				                      },
				                       url: "{{ url('leader/employee/level-challenge/getemployee') }}/"+region+"/"+level,
				                      type:'POST',
				                      dataType:'json',
				                      success: function(result){
				                        if(result.status){	
				                                              
				                          form.find('#employee-list').removeClass('hide');
				                          form.find('#employee-list').html(result.html);
				                          
				                           $(".dropdown dt").on('click', function() {
					                                $(".dropdown dd ul").slideToggle('fast');
					                    });
					
					                    $(".dropdown dd ul li a").on('click', function() {
					                                $(".dropdown dd ul").hide();
					                    });
					
					                    $(document).bind('click', function(e) {
					                              
					                         var $clicked = $(e.target);
					                          if (!$clicked.parents().hasClass("dropdown")) $(".dropdown dd ul").hide();
					                      });
					                     
					                    $('#all').click(function(){
					                       $('.mutliSelect ul li input').click();					                       					                        
					                    });
					                     
					                     $('.mutliSelect input[type="checkbox"]').on('click', function() {
					                                
					                                var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),title = $(this).val() + ",";
					                                var id = $(this).attr('id');
					                              
					                           if(id != 'all'){
					                                if ($(this).is(':checked')) {
					                                  
					                                  id_arr.push(id);
					                                  count ++;
					                                  var html = '<span>' + count +' Employee Selected' + '</span>';
					                                  $('.multiSel').html(html);
					                                 
					                                } else {
					                                    count --;
					                                    var index = id_arr.indexOf(id);
					                                    id_arr = removeElement(id_arr,index);
					                                    var html = '<span>' + count +' Employee Selected' + '</span>';
					                                    $('.multiSel').html(html);
					                                 
					                                  if(count == 0)
					                                  {
					                                     var html1 = '<span>Select Employee</span>';
					                                     $('.multiSel').html(html1);
					                                  }
					                                }
					                            
					                                var str = id_arr.toString();
					                                $("#empcount").val(str);
					                           }
					                              
					                      });
				                        }  
				                        else{
				                          swal("Oops!", result.message, "error");
				                        }
				                       }				                  
				                     });
				                }
                 				
		                          });		                          
		                 	}
		                 	else{
		                 	  swal("Oops!", result.message, "error");
		                 	}
		                      }
		                    });
		                }		                                     		
                          });
                        
                        }
                        else{
                           swal("Oops!", result.message, "error");
                        }
                      }
                    });
                }
      });



    	
        $( "#category" ).change(function() {
            var _token = $("input[name='_token']").val();
            var cat_id = $('#category').val();

            $.ajax({
                url: "{{ url('leader/employee/level-preset-challenge/getsubcategory') }}/"+cat_id,
                type: "post",
                data: {_token:_token},
                success: function(d) {
                    if(d.sub_cat_html){
                        $(".sub_cat").removeClass("hide");
                        $('#subcategory').html(d.sub_cat_html);
                    }else{
                        $('.sub_cat').addClass("hide");
                        swal("Oops!","No Subcategory", "error");
                    }
                }
            });
        });
        
     
    });
</script>
@endsection