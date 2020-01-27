<?php

include_once('BUILD.php');
include_once('./storage/session.php');

$message = '';


if(isset($_POST['submit'])) {
    $nickname = $_POST['nickname'];
    if (isset($nickname)) {
        if (strlen($nickname) > 2) {
            include_once('./databases/players/DB.php');
            include_once('./databases/access/connection.php');
            include_once('./databases/tools/commands.php');

            $players = get_players (get_connection(), $nickname);

            if (count($players) == 0) {
                 $new_player = array();
                 $new_player['id'] = uniqid();
                 $new_player['removed'] = 0;
                 $new_player['nickname'] = $nickname;
                 
                 if (get_connection()->query(get_insert_sql_command ('players', $new_player)) === TRUE) {
                     $sessionData = array();
                     $sessionData['player'] = $new_player;
                     setSessionData($sessionData);
                 } else {
                      $message = 'Nastavení se nepodařilo uložit';
                 }
            }
            else {
                  $sessionData = array();
                  $sessionData['player'] = $players[0];
                  setSessionData($sessionData);
            }

            include_once('./helpers/redirectHelper.php');
            redirect('board');
        }
        else {
            $message = 'Pole přezdívka musí mít alespoň 3 znaky';
        }
    }
    else {
        $message = 'Pole přezdívka je povinné';
    }
}


if (strlen($message) > 0) {
   $message = '<div class="alert alert-danger">
    '.$message.'
</div>';
}



build_page("Nastavení", basename($_SERVER["SCRIPT_FILENAME"], '.php' ), '
<div class="row">
    <div class="col-lg-7">
        '.$message.'
        <form method="POST" action="/set">
            <div class="form-group">
                <label for="nickname">Přezdívka</label>
                <input name="nickname" type="text" class="form-control" id="nickname" placeholder="Zvolte přezdívku">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Přejít na panel <i class="fa fa-arrow-right"></i></button>
        </form>
    </div>
</div>


');
?>