@extends('executive/layouts.app')

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

    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
            <h4 class="card-title theme-color">Add New Upload</h4>
            <form action="{{ url('executive/upload/store') }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <strong><label for="url_link">Add Hyper Link</label> </strong>
                    <input type="text" name="url_link" id="url_link" class="form-control" placeholder="Hyper Link">
                </div>

                 <div class="form-group">
                    <strong><label for="description">Description *</label> </strong>
                    <input type="text" name="description" id="description" class="form-control" placeholder="Description"  maxlength="22" required>
                </div>

                <div class="form-group">
                        <strong><label>Access Level</label></strong>
                        <select class="form-control" name="send_level" id="send_level">                        
                            <option value="">Select Access Level</option>
                            @foreach ($upload_data['access_level'] as $access_level)
                                <option value="{{$access_level['id']}}">{{$access_level['access_level_name']}}</option>
                            @endforeach                       
                        </select>
                </div>     
          
                <div class="form-group">
                
                    <strong><label for='company'>Store </label></strong>
                    
                    <dl class='dropdown form-control'>        
                 
                       <dt><a href='#'><span class='multiSel'>Select Store</span></a></dt>     
                        <dd>
                            <div class='region-mutliSelect'>
                               <ul>
                                <span>
                                    <input type="checkbox" id ="region-all">&nbsp;&nbsp;&nbsp;Select All
                                </span>
                                    @foreach ($upload_data['region'] as $item) 

                                      <li><input type="checkbox" id ="{{$item['id']}} ">&nbsp;&nbsp;&nbsp;{{$item['industry_name']}}</li>

                                    @endforeach 
                                </ul>
                           </div>
                        <dd>
                    </dl>
                </div>
        
                <div class="form-group hide">
                   <input type="text" class="form-control" name="send_region" id="send_region" hidden>
                </div>   
                

                <div class="form-group">
                    <strong><label>Upload Image</label></strong>
                    <input type="file" name="image" class="file-upload-default">
                    <div class="input-group col-xs-12">
                    <input type="text" class="form-control file-upload-info" disabled="" placeholder="">
                    <span class="input-group-append">
                        <button class="file-upload-browse btn btn-light" type="button">Upload</button>
                    </span>
                    </div>
                </div>
                
               
                <button type="submit" class="btn btn-theme mr-2">Submit</button>
               
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>
<style type="text/css">
       dt {
    font-weight: 300;
}

</style>
<script>
  
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
 $('#region-all').click(function(){
                       $('.region-mutliSelect ul li input').click();
                    });
 
                   $('.region-mutliSelect input[type="checkbox"]').on('click', function() {
                                          
                          var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),title = $(this).val() + ",";
                          var id = $(this).attr('id');
                                        
                           if(id != 'region-all'){
                            if ($(this).is(':checked')) {
                                            
                               id_arr.push(id);
                                count ++;
                                var html = '<span>' + count +' Store Selected' + '</span>';
                                 $('.multiSel').html(html);
                                           
                             } else {
                                 count --;
                                 var index = id_arr.indexOf(id);
                                 id_arr = removeElement(id_arr,index);
                                 var html = '<span>' + count +' Store Selected' + '</span>';
                                  $('.multiSel').html(html);
                                           
                                   if(count == 0)
                                  {
                                        var html1 = '<span>Select Store</span>';
                                        $('.multiSel').html(html1);
                                  }
                              }
                                      
                              var str = id_arr.toString();
                              $("#send_region").val(str);
                         }
                                        
                });

</script>
@endsection