// ajout de la classe JS à HTML
document.querySelector("html").classList.add('js');
 
// initialisation des variables
var fileInput  = document.querySelector( ".input-file" ),  
    button     = document.querySelector( ".input-file-trigger" ),
    the_return = document.querySelector(".file-return");
 
// action lorsque la "barre d'espace" ou "Entrée" est pressée
button.addEventListener( "keydown", function( event ) {
    if ( event.keyCode == 13 || event.keyCode == 32 ) {
        fileInput.focus();
    }
});
 
// action lorsque le label est cliqué
button.addEventListener( "click", function( event ) {
   fileInput.focus();
   return false;
});
 
// affiche un retour visuel dès que input:file change
fileInput.addEventListener( "change", function( event ) {  
    the_return.innerHTML = this.value;  
});

function myFunction() {
    var x = document.getElementById("mySelect").value;
    //document.getElementById("demo").innerHTML = "You selected: " + x;
   // alert(x);
    var a = x. lastIndexOf("\\");
    var filename=x.substr(a+1);
    filename=filename.toLowerCase();
    var extention = filename.split('.');
    //alert(extention[0]+extention[1]+extention[2]);

    var langue =0;
    var length= parseInt(extention.length);
    
    switch(length) {
      case 3:
          {
            
              if(extention[0] == "messages" && extention[2] == "xlf")
              {
                      switch (extention[1]) {
                            case 'fr':
                              
                              langue=1;
                              break;
                            case 'ar':
                              langue=2;
                              break;
                            case 'es':
                              langue=3;
                              break;
                          }
              
              }
          }
          break;
      case 2:
            {
              var div = extention[0].split('_');
              if(div[0] === "strings" && extention[1] === "xml")
              {

                      switch (div[1]) {
                            case 'fr':
                              langue=1;
                              break;
                            case 'ar':
                              langue=2;
                              break;
                            case 'es':
                              langue=3;
                              break;

                          }
              }else if (div[0] === "str" && extention[1] === "js") {

                      switch (div[1]) {
                            case 'fr':

                              langue=1;
                              break;
                            case 'ar':

                              langue=2;

                              break;
                            case 'es':
                              langue=3;
                              break;

                          }
              }
            }
          break;


}

if(langue==0)
{
  alert("erreur | fichier imconvonable");
  document.getElementById("mySelect").value='';
  
}else if (langue>0) {
  $(function() {
    var file_data = $('#mySelect').prop('files')[0];   
    var form_data = new FormData();                  
    form_data.append('file', file_data);                             
    $.ajax({
        url: '../php/FichierPhp.php?method=5&id_user=1&type='+extention[length-1]+'&langue='+langue, // point to server-side PHP script 
        dataType: 'text',  // what to expect back from the PHP script, if anything
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,                         
        type: 'post',
        success: function(php_script_response){
          //  alert(php_script_response); // display response from the PHP script, if any
        }
     });
      alert("votre fichier et bien importer");
  document.getElementById("mySelect").value='';
});


}
}
