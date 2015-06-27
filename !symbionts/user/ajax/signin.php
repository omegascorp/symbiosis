<?
if(isset($kernel)&&isset($_POST['username'])&&isset($_POST['password'])){
    $username=Data::word($_POST['username']);
    $password=Data::word($_POST['password']);
    $user->username=$username;
    $user->password=$password;
    $labels->import('symbionts/user/labels/');
    $user->md5=true;
    if($user->signIn()){
        print '{"accessLevel":"'.$user->accessLevel.'"}';
    }
    else{
        print '{"error":"'.$labels->get('symbionts.user.invalid').'"}';
    }
}
?>