<?php
/*
Plugin Name: JoeCaptchaWP
Plugin URI: http://startmotifmedia.com
Description: Agrega un captcha al formulario de inicio de sesión, a los archivos de medios adjuntos, y a todos los comentarios de wordpress de WordPress
Version: 1.0
Author: José Oliva
Author URI: http://startmotifmedia.com
License: GPL2
*/
defined( 'ABSPATH' ) || exit;

// Función para generar el HTML del captcha
function wp_captcha_generate_html() {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $answer = $num1 + $num2;
    setcookie('captcha', $answer, 0, '/');
    $html = '<p><label for="captcha">' . __('Por favor, responde a la operación matemática para verificar que eres humano.', 'wp-captcha') . '</label></p>';
    $html .= '<p><strong>' . $num1 . ' + ' . $num2 . ' = </strong><input type="text" name="captcha" id="captcha" size="6" /></p>';
    echo $html;
}
// Función para validar el captcha
function wp_captcha_validate_captcha() {
    if ( isset( $_POST['comment'] ) ) {
        $captcha_answer = isset( $_COOKIE['captcha'] ) ? $_COOKIE['captcha'] : '';
        $user_answer = isset( $_POST['captcha'] ) ? $_POST['captcha'] : '';
        if ( $captcha_answer != $user_answer ) {
            $error = new WP_Error();
            $error->add( 'captcha_error', __('Error: la respuesta al captcha es incorrecta.', 'wp-captcha') );
            return $error;
        }
    }
    return null;
}
// Función para agregar el captcha al formulario de comentarios principal
function wp_captcha_add_captcha_to_comment_form() {
    wp_captcha_generate_html();
}
add_action( 'comment_form', 'wp_captcha_add_captcha_to_comment_form' );
// Agrega el captcha en el formulario de inicio de sesión de administración
function wp_captcha_add_captcha_admin() {
    // Genera un número aleatorio para la operación matemática
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $answer = $num1 + $num2;
    
    // Guarda la respuesta en una cookie
    setcookie('captcha_admin', $answer, 0, '/');
    
    // Crea el HTML del captcha
    $html = '<p><label for="captcha_admin">' . __('Por favor, responde a la operación matemática para verificar que eres humano.', 'wp-captcha') . '</label></p>';
    $html .= '<p><strong>' . $num1 . ' + ' . $num2 . ' = </strong><input type="text" name="captcha_admin" id="captcha_admin" size="6" /></p>';
    
    // Muestra el captcha en el formulario de inicio de sesión de administración
    echo $html;
}
add_action( 'login_form', 'wp_captcha_add_captcha_admin' );
// Parte 4: Validación y envío de datos

// Verifica si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Valida el campo "nombre"
  if (empty($_POST['nombre'])) {
    $errores[] = 'Por favor, ingrese su nombre.';
  } else {
    $nombre = validar_campo($_POST['nombre']);
  }

  // Valida el campo "email"
  if (empty($_POST['email'])) {
    $errores[] = 'Por favor, ingrese su correo electrónico.';
  } else {
    $email = validar_campo($_POST['email']);
    // Verifica si el correo electrónico es válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errores[] = 'El correo electrónico ingresado no es válido.';
    }
  }

  // Valida el campo "mensaje"
  if (empty($_POST['mensaje'])) {
    $errores[] = 'Por favor, ingrese su mensaje.';
  } else {
    $mensaje = validar_campo($_POST['mensaje']);
  }

  // Verifica si se han producido errores
  if (empty($errores)) {
    // Si no hay errores, envía el correo electrónico
    $destino = 'contacto@midominio.com';
    $asunto = 'Mensaje enviado desde el sitio web';
    $mensaje_enviar = "De: $nombre\n";
    $mensaje_enviar .= "Correo electrónico: $email\n";
    $mensaje_enviar .= "Mensaje:\n\n$mensaje";
    $cabeceras = 'From: contacto@midominio.com' . "\r\n" .
      'Reply-To: ' . $email . "\r\n" .
      'X-Mailer: PHP/' . phpversion();
    mail($destino, $asunto, $mensaje_enviar, $cabeceras);
    // Redirige al usuario a una página de confirmación
    header('Location: confirmacion.html');
    exit;
  }
}

// Función para validar los campos del formulario
function validar_campo($campo) {
  $campo = trim($campo);
  $campo = stripslashes($campo);
  $campo = htmlspecialchars($campo);
  return $campo;
}
/*En esta parte del código, se verifica si el formulario ha sido enviado usando $_SERVER['REQUEST_METHOD'] === 'POST'. Si se ha enviado, se procede a validar cada campo del formulario.

Para cada campo, se verifica si está vacío. Si está vacío, se agrega un mensaje de error a la matriz $errores. Si no está vacío, se llama a la función validar_campo() para limpiar el campo de cualquier posible código malicioso. En el caso del campo "email", también se verifica si es un correo electrónico válido usando filter_var().

Después de validar todos los campos, se verifica si hay algún error. Si no hay errores, se crea el contenido del correo electrónico y se envía usando la función mail(). Luego, se redirige al usuario a una página de confirmación usando header('Location: confirmacion.html').

La función validar_campo() se utiliza para eliminar cualquier posible código malicioso del campo del formulario antes de enviarlo. La función trim() se utiliza para eliminar cualquier espacio en blanco al principio y al final del campo. La función stripslashes() se utiliza para eliminar cualquier barra invertida que se haya agregado para escapar de*/