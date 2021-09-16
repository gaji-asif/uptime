var datecheck_flag = 1;
$('.info-circle').click(function(){
  datecheck_flag = datecheck_flag == 1 ? 2: 1;
   
   if(datecheck_flag == 2){
	   $('.desc-emp-date').hide();
   }
	else $('.desc-emp-date').show();
});

//get the rejected or approved build 



