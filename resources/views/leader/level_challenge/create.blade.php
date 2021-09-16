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
            <h4 class="card-title theme-color">New Timed Challenge</h4>
            <form action="{{route('leader.level-challenge.store')}}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf              

                <div class="form-group">
                    <strong><label for="challenge_text">Challenge Name *</label> </strong>
                    <input type="text" name="challenge_text" id="challenge_text" class="form-control" placeholder="Challenge Title" required>
                </div>

                <div class="form-group {{($challenge_data['categories'] ? '' : 'hide')}}">
                    <strong><label for="category">Main Category *</label></strong>
                    <select class="form-control" name="category" id="category" required>
                        <option value="">select Category</option>
                        @foreach ($challenge_data['categories'] as $cat)
                            <option value="{{$cat['id']}}">{{$cat['category_name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group sub_cat hide">
                    <strong><label for="category">SubCategory</label></strong>
                    <select class="form-control" name="subcategory" id="subcategory">
                        <option value="">Select Subcategory</option>   
                    </select>
                </div>
            
                <div class="form-group">
                    <strong><label for="point">Points</label></strong>
                    <input type="number" class="form-control" name="point" id="point" min="0">
                </div>

               <div class="form-group hide">
                   <input type="text" class="form-control" name="empcount" id="empcount" hidden>
               </div>   

                <div class="form-group">
                    <strong><label>Challenge Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="Upload Image">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                </div>
       
                <div class="form-group">
                    <strong><label for="sent_to">Sent to</label></strong>
                    <select class="form-control select_target" name="sent_to" id="sent_to">
                         <option value="">select Person</option>  
                        <option value="1">Employee</option>
                        <option value="2">Region</option>
                        <option value="3">All </option>
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
                
                 
                <div class="form-group">
                    <strong><label for="end_date">Expiry date</label></strong>
                    <input type="text" value="" class="form-control" name="end_date" id="datetimepicker" placeholder="Select date">
                </div>
              
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
                
            </form>
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

    $('#datetimepicker').datetimepicker({
       format : 'Y/m/d h:m A',
        step:5
    });

    $("#company_id").change(function(){
        $('.sub_cat').addClass("hide");
    });


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
		                       url: "{{ url('leader/employee/level-challenge/getaccesslevelbyregion') }}",
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
                        swal("Oops!","No SubCategory", "error");
                    }
                }
            });

    });  
</script>



@endsection