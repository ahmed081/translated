function exporter(value) {
	var flag ;
	if(value == 'EXCEL')
		flag = 4;
	if(value == 'SQL')
		flag = 3;
	open("../php/FichierPhp.php?method="+flag,"_blank")

           
        
}