

@extends('master/layouts.app')

@section('content')
<head>
  <link href="{{ asset('css/view/employeeview.css') }}" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="{{ asset('css/owl.carousel.css') }}" rel="stylesheet">
<link href="{{ asset('css/owl.theme.css') }}" rel="stylesheet">
<link href="{{ asset('css/portfolio.css') }}" rel="stylesheet">
<link href="{{ asset('css/view/style.css') }}" rel="stylesheet">
 <!--<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet"> -->
  
  </head>
  
 
  
<div class="continer">
 
<div class="main">
    
<div class = "overlay ">
    <button class="purchase_btn"><h3>Buy Now</h3></button>
</div>
 
  <!-- top banner  --> 
    <div class="row main-top-banner">
        <img src="<?php echo \Storage::disk('s3')->url('images/employeeview/topbanner.png');?>" alt="top banner image" class = "banner-image">
    </div>
    <input type="text" id="emp_id" value="{{$user_data['id']}}" hidden> 
    <input type = "text" id = "categorynames" value="{{$user_data['categorynames']}}" hidden>
    <input type = "text" id = "count_data" value = "{{$user_data['count_data']}}" hidden>
    
  <!-- end of top banner -->
<!-- report text and line  -->
    <div class="row report-layer">
      <div class="row">
        <div class="col-md-4 col-xs-4 my-col-4"><hr class="line"></div>
        <div class="col-md-4 col-xs-4 my-col-4">
          <div class="report-title">Up Time Report</div>
        </div>
        <div class="col-md-4 col-xs-4 my-col-4"><hr class="line"></div>
      </div>
    </div>
<!-- end of reort text and line  -->
<!-- main-introduce layer  -->

    <div class="row main-introduce-layer">
      <div class="row intro">
          
          <div class="col-md-6">
            <div class="row"> 
                  <div class="col-md-1"></div>
                  <div class="col-md-11">
                    <div class="row">
                          <div class="col-md-4 my-col-4">
                              <div class="profile-pic-layer">
                                        @if($user_data['image'] != '')
                                        
                            <img class="img-circle" src="<?php echo \Storage::disk('s3')->url('images/employee/'.$user_data['image']);?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                      @else
                      <img class="img-circle" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                      @endif
                              </div>
                          </div>
                          <div class="col-md-8 my-col-8">
                               <div class="introduce-text-layer">
                                        
                                        <div class="name title text-padding">{{$user_data['name']}}</div>
                                        <div class="info  text-padding">Personal Info</div>
                                        <div class="detail text-padding">{{$user_data['phone_number']}}</div>
                                        <div class="detail text-padding">{{$user_data['email']}}</div>
                                        <div class="detail text-padding">{{$user_data['company']}}</div>
                                         <div class="detail text-padding">{{$user_data['level']}}</div>
                                        <div class="detail text-padding">{{$user_data['times']}}</div>
                                        <input type="text" value="{{$user_data['id']}}" class="employee_id" hidden />
                                </div>
                          </div>
                    </div>
                  </div>
            </div>           
          </div>
          <div class="col-md-6">
            <div class="row">
               
              <div class="col-md-12">
                 <div class="chart-layer">
                        <h5>Company Initiatives</h5>
                        <canvas id="employeeChart"></canvas>  
                 </div>
              </div>
            </div>
          </div>          
      </div>

      <div class="row my-objective">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-1"></div>
              <div class="col-md-10">
                 <div class="my-objective">My Objective</div>
              </div>
              <div class="col-md-1"></div>
            </div>
          </div>
          <div class="col-md-6"></div>
        </div>
         <div class="col-md-6">

            <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                       
                         <div class="objective-text">{{$user_data['myobjective']}}</div> 
                    </div>  
                    <div class="col-md-1"></div>
            </div>
         </div>
         <div class="col-md-6">
  			        <div class = "row print-layer">
                <div class="col-md-3 my-col-3">
                    <div class="form-group">
                         <input type="text" class="startmonth monthpicker form-control" data-language='en' data-min-view="months" data-view="months"
       data-date-format="yyyy-MM"/>   
                </div>
                </div>
                <div class="col-md-1"></div>  
              <div class="col-md-3 my-col-3">
              <div class="form-group">
                         <input type="text" class="endmonth monthpicker form-control" data-language='en' data-min-view="months" data-view="months"
       data-date-format="yyyy-MM"/>   
                </div>

              </div>
              <div class="col-md-1"></div>
              <div class="col-md-3 my-col-3">
                   <div class="form-control print-button">
                        <div class="print_text">Print</div>
                        </div>
              </div>
             
                  <div class="col-md-2 my-col-2"></div>          
            </div>
      			 
         </div>
      </div>
    </div>
<!-- end of main introduce layer  -->


<!-- start of sales layer  -->
  <div class="row sales-layer">
            
          
          <div class="row">
                <div class="col-md-6 sales-second-layer">
                    <div class="row">
                           
                           <div class="col-md-12  sales-image-layer">     
                            @if($firstcategory['appr_build_cnt']!=0)                    
                            <div class="title cat_name_title">{{$firstcategory['category_name']}}</div>
                              <div class = "row">                           
                                 <div class="owl-carousel circleslider" id= "sales-image-slider">
                                      @foreach($firstcategory['subcateogry'] as $subcat)
                                        @if($subcat['buildcount'] != 0)
                                         <div class =  "slider-item">
                                          <div class="circle {{$firstcategory['circle_color']}}" id="{{$subcat['id']}}">
                                              <img style ="width:30%" src="<?php echo \Storage::disk('s3')->url('images/employeeview/'.$subcat['image']);?>" alt="Coaching image" class="sales-icon">
                                              <div class="sales-number sales-padding">{{$subcat['buildcount']}}</div>
                                              <div class="circle-text">{!! $subcat['subcategory_name'] !!}</div>
                                          </div> 
                                          </div>
                                        @endif
                                     @endforeach                                                                                                                                   
                                 </div>
                             </div>
                             @endif
                           </div>
                            
                                                           
                   </div>
                   <!-- end of sales image layer  -->
                    <div class= "row">
                           
                          <div class="col-md-12">
                           <!--  <div class="subcat-description">Tablets sold within October-November 2018</div> -->
                             <div class="row">
                                  <div class="owl-carousel owl-theme buildslider hide" id="{{$firstcategory['id']}}buildslider">
                                  </div>
                             </div>
                          </div>
                   
                    </div>        

                </div>

                <div class="col-md-6">
                    <div class="row rank-text-layer">
                
                      <div class="col-md-4 my-col-4 rank">                      
                            
                            <div class="rank-title">Regional Rank</div>
                            <div class="rank-order">{{$user_data['region_rank']}}</div>
                          
                      </div>
                      <div class="col-md-4 my-col-4 duel-image-layer">
                       @if($user_data['image'] != '')
                                        
                              <img class="duels-image" src="<?php echo \Storage::disk('s3')->url('images/employee/'.$user_data['image']);?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                      @else
                      <img class="duels-image" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                      @endif
                               
                      </div>
                     <div class="col-md-4 rank my-col-4 ">
                            
                            <div class="rank-title">Company Rank</div>
                            <div class="rank-order">{{$user_data['company_rank']}}</div>
                            
                    </div>
                              
                   </div>
                   
                   <!-- end of rank title -->
                   <!-- start of regiona rank builds -->
                    <div class="row">
                           <div class="companychal_text chal_text hide">Company Challenge</div>
                           <div class="regionchal_text chal_text hide">Timed Challenge</div>
                           <div class="duel_text chal_text hide">Duel Challenge</div>
                           
                           <div class="owl-carousel owl-theme rankchallengeslider hide">
                          </div>
                    </div>
                   <!-- end of reginoal rank builds  -->
                   

                   <!-- start of company rank builds -->

                   <!-- end of company rank builds -->
                   <div class="row challegne-button-layer"> 
                     <div class="col-md-4 my-col-4">
                           <div class="challenge-button"  id = "1">
                               <img src="<?php echo \Storage::disk('s3')->url('images/employeeview/duel-1.png');?>" alt="Region Challenge image" class="bg-img">    
                               <div class="sub_title">
                                 <div class="sub1_title">Timed</div>
                                
                                 <div class="sub2_title">Challenge</div>  
                               </div> 
                               <div class="sub_number">{{$user_data['regionchalcount']}}</div>                                  
                           </div> 
                                                
                     </div>
                     <div class="col-md-4 my-col-4">
                        
                        <div class="challenge-button"  id = "2">
                               <img src="<?php echo \Storage::disk('s3')->url('images/employeeview/duel-2.png');?>" alt="Region Challenge image" class="bg-img"> 
                               <div class="sub_title">
                                 
                                 <div class="duel_title">Duel</div>
                               
                               </div>   
                               
                               <div class="sub_number">{{$user_data['duelcount']}}</div>                                  
                        </div> 
                                                          
                     </div>
                     <div class="col-md-4 my-col-4">
                           <div class="challenge-button"  id = "3">
                               <img src="<?php echo \Storage::disk('s3')->url('images/employeeview/duel-3.png');?>" alt="Region Challenge image" class="bg-img">    
                               <div class="sub_title">
                                 <div class="sub1_title">Company</div>
                                 
                                 <div class="sub2_title">Challenge</div>  
                               </div> 
                               <div class="sub_number">{{$user_data['companychalcount']}}</div>                                  
                           </div>                               
                     </div>
                  </div>
              </div>
          </div>
          
  </div>
  <!-- end of sales layer  -->
  <div class="row second-section">
      <div class="col-md-6 second-layer">
         
         @foreach($maincategory as $cat)
          <div class="row training-slider-layer">
          @if($cat['appr_build_cnt']!=0)     
           <div class="col-md-12">  
                      
              <div class="title cat_name_title">{{$cat['category_name']}}</div>
              <div class="row">
                <div id="training-slider" class="owl-carousel circleslider">
                   @foreach($cat['subcateogry'] as $subcat)
                      @if($subcat['buildcount'] != 0)
	                       <div class =  "slider-item">
	                           <div class="circle {{$cat['circle_color']}}" id="{{$subcat['id']}}">
	                              <img style ="width:30%" src="<?php echo \Storage::disk('s3')->url('images/employeeview/'.$subcat['image']);?>" alt="Coaching image" class="sales-icon">
	                              <div class="sales-number sales-padding">{{$subcat['buildcount']}}</div>
	                              <div class="circle-text ">{!! $subcat['subcategory_name'] !!}</div>
	                           </div>
	                       </div>
                      @endif
                   @endforeach 
                </div>
               </div>
              
             </div>
              
            </div>
            <div class="row">
                  
                 <div class="col-md-12">
                       <div class="owl-carousel buildslider" id="{{$cat['id']}}buildslider">
                
                       </div> 
                 </div>
                @endif  
            </div>
             @endforeach
           
      </div>
      <div class="col-md-6">
        
        <div class="my-plan">
           <div class="myplan-layer text-layer">
              <div class="text-layer-rect">
                <h4>Summary of Qualifications</h4>
                 <p>{{$user_data['myplan']}}</p>
             </div>
           </div>
      </div>
     <!-- Work Experience  -->
         <div class="past-jobs">
           <div class="pastjob-layer text-layer">
            <h4>Work Experience</h4>
             <p>{{$user_data['past_jobs']}}</p>
              
           </div>
         </div>
      <!-- References  -->
         <div class="references">
           <div class="references-layer text-layer">
            <h4>References</h4>
             <p>{{$user_data['references']}}</p>
              
           </div>
         </div>

   </div>
  </div>
  <div class="row bottom-banner-logo">
      <div class="col-md-1 my-col-1"></div>
      <div class="col-md-5 my-col-5">
        <div class=" row company-logo">
           <div class="col-md-2 my-col-4">
              <img  class="company-logo-img" src="<?php echo \Storage::disk('s3')->url('images/employeeview/company-logo.png');?>" alt="challenge_image">
           </div>
           <div class="col-md-10 my-col-8">
             <div class="logo-text">where your future and now meet</div>
           </div>
           
        </div>
      </div>

      <div class="col-md-5 my-col-5">
        <div class="connect-number">
           {{$user_data['ut_code']}} 
        </div>
      </div>
   
      <div class="col-md-1 my-col-1"></div>
   
    </div>



<!-- Trigger the Modal -->

<!-- The Modal -->
 <div id="myModal" class="modal">
  <!-- The Close Button -->
   <div class="my-modal-content row">
       <div class="my-col-7">
         <div class="left-content">
            
            <div class="style-img-layer">
             <img  class="style-img hide" src="<?php echo url('images/gray_challenge.png');?>">
            </div>

            <div class="carousel-layer">
                   <div id="owl-demo" class="owl-carousel owl-theme">                   
                   </div>  
            </div>

            <div class="pre">
              
              <img src="<?php echo url('images/pre-arrow.png');?>" alt="Region Challenge image" class="pre-img"> 
            </div>
            <div class="next">
            
              <img src="<?php echo url('images/next-arrow.png');?>" alt="Region Challenge image" class="next-img"> 
            </div>
         </div>
       </div>
       <div class="my-col-5">
         <div class="right-content">
            <div class="description-section">
              <div class="row">
                <div class="description-header">                     
                       @if($user_data['image'] != '')
                            <img class="modal-desc-img" src="<?php echo \Storage::disk('s3')->url('images/employee/'.$user_data['image']);?>" alt="profile image" onerror="this.src='<?php echo url('images/avatar.png'); ?>'">
                       @else
                      <img class="modal-desc-img" src="{{ asset('images/avatar.png') }}" alt="Profile image">
                       @endif
                       <div class="desc-intro">
                              <div class="desc-emp-name desc-text">{{$user_data['name']}}</div>
                              <div class="desc-emp-date desc-text"></div>
                       </div>   
                        <div class="close-button">
                          <span class="close">&times;</span>  
                        </div>
                 
                </div>
                  <!--end of description header  -->
                 <div class="description-content">
                   <div class="description">Just sold 2 Purchase Speaker</div>
                 </div>

              </div>
              
            </div>

            <div class="subcategory-layer">
              
                <div class="category row">
                </div>
              
                 
            </div>
            
         </div>
       </div>

   </div>
         
        
</div>

</div>
<!-- end of main  -->
</div>
<!-- end of container  -->
 
 <!-- <script src="{{ asset('js/mdb.min.js') }}"></script>-->
 <script src="{{ asset('js/bootstrap.js') }}"></script>  
 <script src="{{ asset('js/employeeview.js') }}"></script>

 
<script type="text/javascript">
  
     
$(document).ready(function() {

  
     $('#datetimepicker').datetimepicker({
       format : 'Y-m',
       timepicker:false,
       
       //date time change event
       
        onChangeDateTime:function(dp,$input){
            var id = $('.employee_id').val();
            var date = $input.val();
 
//sub category section 
        $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{url('master/check')}}",
              type:'get',
              dataType:'json',
              success: function(result){
                  if(result.status){  
                        //alert(result.message);
                       location.href= "http://uptime.shoecrate.co/master/test/"+id+"/"+date;
                  }                                                                                                                           
                  else{
                        
                  }
              }
        });
       
       
        }
    
    });

 //print button click 
 $('.print-button').click(function(){
     var id = $('.employee_id').val();
     location.href = "http://uptime.shoecrate.co/master/pdf/"+id;
 });
 //buynow button click 
  $('.purchase_btn').click(function(){
      $('.overlay').removeClass('showblock');
      $('.overlay').addClass('hideblock');
      
  });
  
  //date range select action 
  

  var start =new Date(); 
  
  
var sday = $('.startmonth').datepicker().data('datepicker');
sday.selectDate(new Date(start));


var end = new Date();

var eday =  $('.endmonth').datepicker().data('datepicker');
eday.selectDate(new Date(end));


 var startmonth = '';
 var startmonthinst = $('.startmonth').datepicker({

      onSelect:function(datetext,inst){
            startmonth = datetext;
      }
 });
 
  var endmonth='';
  var endmonthinst = $('.endmonth').datepicker({
      minDate:startmonth,
      onSelect:function(datetext,inst){
            endmonth = datetext;

             if(startmonth != '' && startmonth <= endmonth){
                     var id = $('.employee_id').val();
                     location.href= "http://uptime.shoecrate.co/master/test/"+id+"/"+startmonth+"/"+endmonth;
             }
      }
 }); 
  

//global variable 
  var modal = document.getElementById('myModal');
  var modalImg = document.getElementById("modal-box");
  var span = document.getElementsByClassName("close")[0];
  var descriptionText = document.getElementById("description");
  var categoryblockText = document.getElementById("categoryblock");
 
   $("#owl-demo").owlCarousel({
     items : 1,
     dots:true
     
  });
  
  var demo_owl = $("#owl-demo");
  $('.pre-img').click(function(){
      demo_owl.trigger('prev.owl.carousel'); 
      
  });
   
  $('.next-img').click(function(){
       demo_owl.trigger('next.owl.carousel'); 

  });
 
 $(".buildslider").on("click", ".owl-item", function(e){

    e.preventDefault();
    var id = $(this).find('.slider-item .slider-image').attr('id');
   
     $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ url('master/test/getbuildinfofromid') }}/"+id,
              type:'POST',
              dataType:'json',
              success: function(result){
                  if(result.status){                            
                        var res =  result.html.split("*");
                       
                        //res[0]-image res[1]-type,res[2]-categoryname res[3]-text res[4]->created_at
                       $('#owl-demo').trigger('replace.owl.carousel', res[0]).trigger('refresh.owl.carousel');   
                    
                        var topic_image = document.getElementsByClassName("style-img");
                        
                        if(res[1] == 1){
                              $('.style-img').removeClass('hide');
                            
                            }

                        var categoryname = res[2];
                          
                        $('.category').html(res[2]);
                        $('.description').text(res[3]);
                        $('.desc-emp-date').text(res[4]);
                         modal.style.display = "block"; 
                       $('#owl-demo').trigger('refresh.owl.carousel');
                  }                                                                       
                  else{
                      //  swal("Oops!", result.message, "error");
                  }
              }
            });   
  
 });
 
 
 
 
  $(".rankchallengeslider").on("click", ".owl-item", function(e){
      e.preventDefault();
      var id = $(this).find('.slider-item .slider-image').attr('id');
     
      $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ url('master/test/getchallengeinfofromid') }}/"+id,
              type:'POST',
              dataType:'json',
              success: function(result){
                  if(result.status){ 
                     
                        var res =  result.html.split("*");
                       
                        //res[0]-image,res[2]-categoryname res[3]-text res[4]->created_at
                        
                       $('#owl-demo').trigger('replace.owl.carousel', res[0]).trigger('refresh.owl.carousel');   
                    
                        var topic_image = document.getElementsByClassName("style-img");
                         
                        $('.category').html(res[1]);
                        $('.description').text(res[2]);
                       // alert(res[3]);
                        $('.desc-emp-date').text(res[3]);
                         modal.style.display = "block"; 
                       $('#owl-demo').trigger('refresh.owl.carousel');
                  }                                                                       
                  else{
                      //  swal("Oops!", result.message, "error");
                  }
              }
            });   
  
    
  });
  
   
 
  span.onclick = function() { 
     modal.style.display = "none";
     //$('.style-img').addClass('hide');
  }


//  $('[data-toggle="responsive"]').hurkanSwitch({responsive:true,width:false});
 
 var emp_id = $('#emp_id').val();
 
 
 $(".circleslider").owlCarousel({
  nav : false,
  dots:false,
  items : 3
   
 });
 
   $(".buildslider").owlCarousel({
  nav : true,
  dots:false,
  items : 4
   
 });
 
 $(".rankchallengeslider").owlCarousel({
  nav : false,
  dots:false,
  items : 4
   
 });
 

 $('.challenge-button').click(function(e){

  
    e.preventDefault();
    var id = this.id;
    
    //in the case this element has already clicked 
    if($(this).hasClass("clicked")){
        
         $(this).removeClass("clicked");
         $(this).removeClass("color-button");
         $('.rank-text-layer').removeClass('hide');
         $('.rankchallengeslider').addClass('hide');
          

    }
    //in the case this element clicked newly
    else{  

      $(this).parent().parent().children().children().removeClass("clicked");
      $(this).parent().parent().children().children().removeClass("color-button");
      $(this).addClass("clicked");
      $(this).addClass("color-button");
      $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{url('master/test/getChallengeImage')}}/"+emp_id+"/"+id,
              type:'POST',
              dataType:'json',
              success: function(result){
                  if(result.status){ 
                      
                    
                    $('.rank-text-layer').addClass('hide');
                    $('.rankchallengeslider').removeClass('hide');
                    $('.rankchallengeslider').trigger('replace.owl.carousel', result.html).trigger('refresh.owl.carousel');                                   
                  }                                                                                                                                                     
                  else{
                     //   swal("Oops!", result.message, "error");
                        $('.rank-text-layer').removeClass('hide');
                        $('.rankchallengeslider').addClass('hide');
                  }
              }
       });
    }  

 });
 

$('.circle').click(function(e){
      e.preventDefault();
      var id  =  this.id;
      //in the case this element already clicked 
      if($(this).hasClass("clicked")){         
          $(this).removeClass("clicked");
          $(this).removeClass("circle-border");

              $.ajax({
              headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ url('master/test/getbuildsfromsubcatid') }}/"+emp_id +"/"+id,
              type:'POST',
              dataType:'json',
              success: function(result){
                  if(result.status){                            
                          var res =  result.html.split(",");
                          var buildslider_id = '#'+res[0]+'buildslider';                 
                          $(buildslider_id).addClass('hide');                          
                                                
                  }                                                                       
                  else{
                        swal("Oops!", result.message, "error");
                  }
              }
            });
      }
      //
      else{
        //alert("New Click");
        $(this).parent().parent().parent().children().children().children().removeClass("clicked");
        $(this).parent().parent().parent().children().children().children().removeClass("circle-border");
        
        $(this).addClass("clicked");
        $(this).addClass("circle-border");
              
          $.ajax({
          headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "{{ url('master/test/getbuildsfromsubcatid') }}/"+emp_id +"/"+id,
          type:'POST',
          dataType:'json',
          success: function(result){
              if(result.status){  
                      
                      var res =  result.html.split(",");
                      var buildslider_id = '#'+res[0]+'buildslider';                      
                      $(buildslider_id).removeClass('hide');
                      $(buildslider_id).trigger('replace.owl.carousel', res[1]).trigger('refresh.owl.carousel');
                                            
              }                                                                                    
              else{
                    swal("Oops!", result.message, "error");
              }
          }
        });
      } 
 });
   
});
</script>
@endsection