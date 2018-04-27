//--------------------------Pagination------------------------------------ 

function load(id_user , count_par_page , page ) {
  var   myObj, nbr=0, flag=false;
//alert("ahmed");
          $.ajax({
            url: "../php/FichierPhp.php?method=2", //the page containing php script
            type: "post", //request type,
            dataType: 'text',
           data: {id_user: id_user , page : page ,count_par_page:count_par_page },
            success:function(result){
              alert(result);
              myObj = JSON.parse(result);
              $.each(myObj, function(index , value){
                if(index==0){
                    nbr = value.nbr;
                    charger_page(id_user , count_par_page , page , nbr,myObj);
                }
              });
             //console.log(result.abc);
           }
         });

        
}








function charger_page(id_user , count_par_page , page , numberOfItems , myObj)
{
   
  var limitPerPage = count_par_page;
  var totalPages = Math.ceil(numberOfItems / limitPerPage);
   $('.pagination').html("  <li> <a id='Previous-page' href='javaScript:void(0)' aria-label='Previous'> <span aria-hidden='true'>&laquo;</span> </a> </li>");
   $('.pagination').append("<li class='current-page'><a href='javaScript:void(0)'>" + 1 +" </a></li>");
  for(var i =2; i<=totalPages;i++)
  {
    $(".pagination").append("<li class='current-page'><a href='javaScript:void(0)'>" + i +" </a></li>");
  }
  $('.pagination').append("<li id='next-page'> <a href='javaScript:void(0)' aria-label='Next'> <span aria-hidden='true'>&raquo;</span> </a> </li>");

$('.pagination .active').removeClass('active');
 $(".pagination li.current-page").each(function(index){
  if(index+1==page)
    $(this).addClass('active');
    affichage( myObj);
 });


 $(".pagination li.current-page").on("click" , function(){
      load(id_user , count_par_page ,$(this).index());
 });




 $('#next-page').on('click' ,  function(){

    var index=$('.pagination li.active').index();
 
      if(index==totalPages) return false;

      else load(id_user , count_par_page ,index+1);

       
 });


  $('#Previous-page').on('click' ,  function(){

    var index=$('.pagination li.active').index();
 
      if(index==1) return false;
       
      else load(id_user , count_par_page ,index-1);
          
      
       
 });
 }
//----------------------------------------------Récupiration Json--------------------------------------------------

function affichage( myObj) {
    var html="" ,flag=false;

                    $.each(myObj, function(index, value) {
                      if(!flag)
                      {
                        flag=true;
                        return true;
                      }
                        
                     
                    html+="<tr style='    height: 51px;' class='list_group'>";

                   // alert(value);
                    $.each(this, function(k, value) {

                     if(k!="id" )
                     {
                     
                          if(k=="source")
                          {
                               html+='<td style="width: 14%;">'+value+'</td>';
                          }else
                          {
                                html+='<td style=" width: 29%;" contenteditable="true">'+value+'</td>';
                          }      
                     }
                    });      html+="</tr>";
                    $('#row').html(html); 
                    
                
                        
                    });            
           
         
}




















