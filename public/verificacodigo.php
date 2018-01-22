<?php
#Verifica se tem um email para pesquisa
if(isset($_GET['codigo'])){ 

    #Recebe o Email Postado
    $codigo = $_GET['codigo'];

    #Conecta banco de dados 
    $con = mysqli_connect("localhost", "root", "", "samu_aph");
    $sql = mysqli_query($con, "SELECT * FROM fichaaph WHERE codigo = '{$codigo}'") or print mysql_error();


    #Se o retorno for maior do que zero, diz que já existe um.
    if(mysqli_num_rows($sql)>0) {
        echo json_encode(array('codigo' => 'Este código já está cadastrado!')); 
        
       
    }else {
        echo json_encode(array('codigo' => 'Este código ainda não está cadastrado!' )); 
       
    }
}
?>