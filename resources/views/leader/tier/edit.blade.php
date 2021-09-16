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
    
    <!-- <div class="col-md-12">
        <div class="col-md-8"> -->
    <div class="card col-md-8">
        <div class="card-body">
        <h4 class="card-title theme-color">Edit Tier</h4>
        
            <form action="{{ url('/leader/tier/update',$tier->id) }}" method="POST" class="forms-sample"  enctype="multipart/form-data">
                @csrf
                
               <input type="text" name="listid" id="listid" class="form-control" value="{{$tier->id}}" placeholder="Number of Uploads" hidden>

     <div class="form-group">
                    <strong><label>Access Level</label></strong>
                    <select class="form-control" name="access_level" id="access_level" required>
                        <option value="">Select Access Level</option>
                            @foreach ($tier_data['access_level'] as $access_level)
                            <option value="{{$access_level['id']}}" {{$tier->access_level == $access_level['id'] ? 'selected' : ''}}>{{$access_level['access_level_name']}}</option>
                             @endforeach                        
                    </select>

                </div>
                
                <div class="form-group">
                    <strong><label>Select Tier*</label></strong>
                    <select class="form-control" name="tier" id="tier" required>                        
                        <option value="">Select Tier</option>
                        @foreach ($tier_data['tier'] as $item)
                            <option value="{{$item['id']}}" {{$tier->tier == $item['id'] ? 'selected' : ''}}>{{$item['tier_name']}}</option>
                        @endforeach                       
                    </select>
                </div>         

               

             <div id="subcategory" class="form-group hide">
             </div>


              <div  class="form-group">
                <div class="form-group">
                    <h6>Set Minimum Tier Requirements </h6>
                 </div>
               
                <div class="row subcategory-layer">
                    <div class="subcat">
                 
                         <strong><label class ="sub-title "for="uploads">Submissions *</label> </strong>
                         <div class ="input-box-layer" >
                            <input type="number" name="uploads" id="uploads" min = "0" class="input-box" value="0" placeholder="Number of Uploads" required>
                         </div>
                    </div>

                     <div class="subcat">
                   
                         <strong><label class="sub-title" for="challenges">Challenges *</label> </strong>
                         <div class ="input-box-layer" >
                            <input type="number" name="challenges" id="challenges"  min = "0" class="input-box" value="0" placeholder="Number of Challenges" required>
                         </div>
                     </div>
                 </div>
               </div>
               
                  <div class="form-group">
                    <strong><label for="points">Points Prize *</label> </strong>
                    <input type="number" name="points" id="points" min = "0"  class="form-control" value="{{$tier->points}}" placeholder="Number of Points Prize" required>
                 </div>

                <button type="submit" class="btn btn-theme mr-2">Submit</button>
                
            </form>
        </div>
    </div>
        <!-- </div>
    </div> -->
</div>
<style>
    .subcategory-layer{
        display: inline-flex;
    }
     
    .subcat{
        padding: 10px;
        margin: 10px;
       
        text-align: center;
    }
    .input-box-layer{
        width: 120px;
        height: 60px;
    }
    .sub-title{
        margin-top: 10px;

    }
    .input-box{
      width: 60px;
      margin:auto;
      margin-top: 20px;     
    }
</style>
<script>
    
      
    $(document).ready(function(){
        
        getsubcategory();

         $('#access_level').change(function(e){
                e.preventDefault();
                var form = $(this).closest('form');
                var level = $(this).val(); 
                
                form.find('#subcategory').removeClass('hide').addClass('hide');
                form.find('#subcategory').html('');
                if(level != ''){
                        $.ajax({
                        headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: base_url+"/leader/tier/getsubcategory/"+level,
                        type:'POST',
                        dataType:'json',
                        success: function(result){
                          if(result.status){
                            
                            form.find('#subcategory').removeClass('hide');
                            form.find('#subcategory').html(result.html);
                          }
                          else{
                            
                                swal("Oops!", result.message, "error");
                          }
                      }
                  });
                }               
         });

    });
    function getsubcategory(){
        var id = $("#listid").val();
        
         $('#subcategory').removeClass('hide').addClass('hide');
         $('#subcategory').html('');
         $.ajax({
                        headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: base_url+"/leader/tier/getsubcategoryfromid/"+id,
                        type:'POST',
                        dataType:'json',
                        success: function(result){
                          if(result.status){
                            $('#subcategory').removeClass('hide');
                            $('#subcategory').html(result.html);
                          }
                          else{
                            
                                swal("Oops!", result.message, "error");
                          }
                      }
          });
    }
</script>
@endsection