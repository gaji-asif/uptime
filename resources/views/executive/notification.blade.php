@extends('executive/layouts.app')


@section('content')
<div class="content-wrapper">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
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
        <p>{{ $message }}</p>
    </div>
    @endif

    <div class="card col-md-8 form-group">
        <div class="card-body">
            <h4 class="card-title theme-color form-group">Push Notification</h4>   
    
            <form action="{{ url('executive/sendnotification') }}" autocomplete="nope" method="POST" class="forms-sample" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <strong><label for="notification">Notification Text *</label> </strong>
                    <textarea rows="4" cols="50" name="notification-text" id="notification" class="form-control" required></textarea>
                </div>
                 
                <div class="form-group">
                    <strong><label for="role">Notification For *</label></strong>
                    <select class="form-control change-notification" name="notification-for" id="role" required>
                        <option value="" multiple = "multiple">Select</option>
                        <option value="1">Entire Company</option>
                        <option value="2">Store</option>
                        <option value="3">Access Level</option>
                        <option value="4">Employee</option>

                    </select>
                </div>

                <input type="text" class="form-control" name="empcount" id="empcount" value="" hidden>
 
                <input type="text" class="form-control" name="regionlist" id="regionlist" hidden value="">
                 <input type="text" class="form-control" name="levellist" id="levellist" hidden value="">
                 <input type="text" class="form-control" name="companyinfo" id="companyinfo" hidden value="">
							       
                 <div id="notification-role-list" class="form-group hide">
                 
                 </div>

                 <div id="company-list" class="form-group hide">
                 
                 </div>
                 
                 <div id="region-list" class="form-group hide">
                 
                 </div>

                 <div id="level-list" class="form-group hide">
                 
                 </div>

                 <div id="employee-list" class="form-group hide">
                    
                 </div>
                
                <button type="submit" class="btn btn-theme mr-2">Send</button>
            </form>
        </div>
    </div>
</div>
 <style>
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


$(document).ready(function(){
 

   $('.change-notification').change(function(e) {
    e.preventDefault();
    var form = $(this).closest('form');
    var key = $(this).val(); 
    form.find('#notification-role-list').removeClass('hide').addClass('hide');
    form.find('#notification-role-list').html('');
    form.find('#company-list').removeClass('hide').addClass('hide');
    form.find('#company-list').html('');
    form.find('#region-list').removeClass('hide').addClass('hide');
    form.find('#region-list').html('');
    form.find('#level-list').removeClass('hide').addClass('hide');
    form.find('#level-list').html('');
    form.find('#employee-list').removeClass('hide').addClass('hide');
    form.find('#employee-list').html('');
    var count = 0;
    var id_arr = []; 
  

    if(key != ''){
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url+"/executive/users/get-notification-role/"+key,
        type:'POST',
        dataType:'json',
        success: function(result){
          if(result.status){
              
            form.find('#notification-role-list').removeClass('hide');
            form.find('#notification-role-list').html(result.html);
//company case 
            $('.company-level').change(function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var company= $(this).val(); 
              
                 $("#companyinfo").val(company);
            });
            
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
//region case                               
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
                              $("#regionlist").val(str);
                         }
                                        
                });
//access level case 
             $('#level-all').click(function(){
                       $('.level-mutliSelect ul li input').click();
                    });
             $('.level-mutliSelect input[type="checkbox"]').on('click', function() {
                                          
                          var title = $(this).closest('.mutliSelect').find('input[type="checkbox"]').val(),title = $(this).val() + ",";
                          var id = $(this).attr('id');
                                        
                           if(id != 'level-all'){
                            if ($(this).is(':checked')) {
                                            
                               id_arr.push(id);
                                count ++;
                                var html = '<span>' + count +' Accesslevel Selected' + '</span>';
                                 $('.multiSel').html(html);
                                           
                             } else {
                                 count --;
                                 var index = id_arr.indexOf(id);
                                 id_arr = removeElement(id_arr,index);
                                 var html = '<span>' + count +' Accesslevel Selected' + '</span>';
                                  $('.multiSel').html(html);
                                           
                                   if(count == 0)
                                  {
                                        var html1 = '<span> Select Accesslevel </span>';
                                        $('.multiSel').html(html1);
                                  }
                              }
                                      
                              var str = id_arr.toString();
                              $("#levellist").val(str);
                         }
                                        
                });
                     //end of mulit select 

 //employee case            
            $('.employee-region').change(function(e) {
                
                e.preventDefault();
                var form = $(this).closest('form');
                var region = $(this).val(); 
                 
                form.find('#region-list').removeClass('hide').addClass('hide');
                form.find('#region-list').html('');
               
                if(region != ''){
               
                  $.ajax({
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: base_url+"/executive/users/get-access-level-users",
                    type:'POST',
                    dataType:'json',
                    success: function(result){
                   
                    
                      if(result.status){
                          
                        form.find('#level-list').removeClass('hide');
                        form.find('#level-list').html(result.html);
                         
                        $('.employee-level').change(function(e) {

                                e.preventDefault();
                                var form = $(this).closest('form');
                                var level = $(this).val(); 
                             
                                form.find('#employee-list').removeClass('hide').addClass('hide');
                                form.find('#employee-list').html('');
                                if(level != ''){
                                  
                                  $.ajax({
                                    headers: {
                                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    url: base_url+"/executive/users/get-notification-employee/"+region+"/"+level,
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
                          else {
                                        swal("Oops!", result.message, "error");
                            }
                    }
                  });
                           }     

                            });
                  //end of employee list 
             }
            //region role 

           else {
            swal("Oops!", result.message, "error");
          }
        }
      });
}
});
}
            //region role 

           else {
            swal("Oops!", result.message, "error");
          }
        }
      });
}
});
});
</script>
@endsection