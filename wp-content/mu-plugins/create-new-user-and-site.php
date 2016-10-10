<?php
/*
    MU plugin que capta as informacoes enviadas via formulario (Gravity Form)
    Cria um site na rede para o usuario
    Insere o usuario como editor do site
    Remove esse usuario do site principal
*/

//Capta os dados enviados no formulario
add_action( 'gform_after_submission_1', '__check_insert_site_user', 10, 2 );

function __check_insert_site_user( $entry, $form ) {

    $nomecompleto   = $entry[1];
    $usuario        = $entry[5];
    $email          = $entry[2];
    $senha          = $entry[3];
    $slugsite       = $entry[4];

//Tenta verificar se o usuario ja existe, caso nao, cria o novo usuario
    $user_id = username_exists( $usuario );

    if ( $user_id and email_exists($user_email)) {
        return "Usuário já existente!";
    } else {
	    $user_id = wp_create_user( $usuario, $senha, $email );
        if(! $user_id){
            return;
        }

//Prepara para criar o novo site
        $rootsitedetails = get_blog_details(1);

        $domain = $rootsitedetails->domain;
        $path = "/imoveldono"."/".$slugsite;
        $title = "Novo Site Criado";
        //Insere o novo site na rede
        $site_id = wpmu_create_blog( $domain, $path, $title, 1 );

        //Adiciona o novo usuario como editor do site criado
        add_user_to_blog( $site_id, $user_id, 'editor' );
        //Remove o novo usuario do site raiz
        remove_user_from_blog($user_id, 1);
    }
}
