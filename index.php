<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    $messages[] = 'Спасибо! Результаты отправлены в базу. Ня:3';
  }
  if (!empty($_COOKIE['notsave'])) {
    setcookie('notsave', '', 100000);
    $messages[] = 'Ошибка!!!';
  }

  $errors = array();
  $errors['name'] = empty($_COOKIE['name_error']) ? '' : $_COOKIE['name_error'];
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['powers'] = !empty($_COOKIE['powers_error']);
  $errors['bio'] = !empty($_COOKIE['bio_error']);
  $errors['check'] = !empty($_COOKIE['check_error']);

  if ($errors['name'] == 'null') {
    setcookie('name_error', '', 100000);
    $messages[] = '<div>Заполни имя.</div>';
  }
  else if ($errors['name'] == 'incorrect') {
      setcookie('name_error', '', 100000);
      $messages[] = '<div>Бака! Недопустимые символы.</div>';
  }

  if ($errors['email']) {
    setcookie('email_error', '', 100000);
    $messages[] = '<div>Заполни почту.</div>';
  }
  
  if ($errors['powers']) {
    setcookie('powers_error', '', 100000);
    $messages[] = '<div>Выбери хотя бы одну сверхспособность.</div>';
  }

  if ($errors['bio']) {
    setcookie('bio_error', '', 100000);
    $messages[] = '<div>Хочу что-нибудь узнать о тебе, братик!</div>';
  }

  if ($errors['check']) {
    setcookie('check_error', '', 100000);
    $messages[] = '<div>Ты не можешь отправить форму не согласившись с контрактом.</div>';
  }

  $values = array();
  $powers = array();
  $powers['levit'] = "Левитация";
  $powers['tp'] = "Похищение способностей";
  $powers['walk'] = "Хождение сквозь стены";
  $powers['vision'] = "Соколиное зрение";
  $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
  $values['sex'] = empty($_COOKIE['sex_value']) ? 'male' : $_COOKIE['sex_value'];
  $values['limbs'] = empty($_COOKIE['limbs_value']) ? '4' : $_COOKIE['limbs_value'];
  $values['bio'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  if (!empty($_COOKIE['powers_value'])) {
      $powers_value = json_decode($_COOKIE['powers_value']);
  }
  $values['powers'] = [];
  if (isset($powers_value) && is_array($powers_value)) {
      foreach ($powers_value as $power) {
          if (!empty($powers[$power])) {
              $values['powers'][$power] = $power;
          }
      }
  }
  include('form.php');
}
else {
  $errors = FALSE;
  if (empty($_POST['name'])) {
    setcookie('name_error', 'null', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else if (!preg_match("#^[aA-zZ0-9-]+$#", $_POST["name"])) {
      setcookie('name_error', 'incorrect', time() + 24 * 60 * 60);
      $errors = TRUE;
  }
  else {setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);}

  if (empty($_POST['email'])) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);}

  $powers = array();
  foreach ($_POST['powers'] as $key => $value) {
      $powers[$key] = $value;
  }
  if (!sizeof($powers)) {
    setcookie('powers_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {setcookie('powers_value', json_encode($powers), time() + 30 * 24 * 60 * 60);}

  if (empty($_POST['bio'])) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  else {setcookie('bio_value', $_POST['bio'], time() + 30 * 24 * 60 * 60);  }

  if (empty($_POST['check'])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
  setcookie('sex_value', $_POST['sex'], time() + 30 * 24 * 60 * 60);
  setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60);

// *************
// TODO: тут необходимо проверить правильность заполнения всех остальных полей.
// Сохранить в Cookie признаки ошибок и значения полей.
// *************

  if ($errors) {
    header('Location: index.php');
    exit();
  }
  else {
    setcookie('name_error', '', 100000);
    setcookie('email_error', '', 100000);
    setcookie('powers_error', '', 100000);
    setcookie('bio_error', '', 100000);
    setcookie('check_error', '', 100000);
    // TODO: тут необходимо удалить остальные Cookies.
  }

    $db_user = "u16355";
    $db_password = "2629125";
    $db_table = "bd"; // Имя Таблицы БД

    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['year'];
    $sex = $_POST['sex'];
    $limbs = $_POST['limbs'];
    $bio = $_POST['bio'];
    $check = $_POST['check'];
    $powers_bd = array();
    foreach ($_POST['powers'] as $key => $value) {
        $powers_bd[$key] = $value;
    }
    $powers_string = implode(', ', $powers_bd);

    try {
        $db = new PDO('mysql:host=localhost;dbname=u1635', $db_user, $db_password, array(PDO::ATTR_PERSISTENT => true));
        $statement = $db->prepare("INSERT INTO ".$db_table." (name, email, age, sex, limbs, powers, bio) VALUES ('$name','$email',$age,'$sex',$limbs,'$powers_string','$bio')");
        $statement = $db->prepare('INSERT INTO '.$db_table.' (name, email, age, sex, limbs, powers, bio) VALUES (:name, :email, :age, :sex, :limbs, :powers, :bio)');
        $statement->execute([
            'name' => $name,
            'email' => $email,
            'age' => $age,
            'sex' => $sex,
            'limbs' => $limbs,
            'bio' => $bio,
            'powers' => $powers_string
        ]);
        setcookie('save', '1');
    } catch (PDOException $e) {
        setcookie('notsave', '1');
    }
  header('Location: index.php');
}
