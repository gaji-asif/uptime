  
  $('.file-upload-default').bind('change', function() {
                if(Math.round(this.files[0].size/1000) >= 2000){
                     alert('The image may not be greater than 2048 kilobytes');
                     $('.file-upload-default').val("");
                }
                    
  });