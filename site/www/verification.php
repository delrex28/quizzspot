<?php
session_start();
if(isset($_POST['username']) && isset($_POST['password']))
{
 // connexion à la base de données
 $db_host = 'localhost';
 $db_username = 'web';
 $db_password = 'Uslof504';
 $db_name = 'quizzspot';
 $db = mysqli_connect($db_host, $db_username, $db_password,$db_name)
 or die('could not connect to database');
 
 // on applique les deux fonctions mysqli_real_escape_string et htmlspecialchars
 // pour éliminer toute attaque de type injection SQL et XSS
 $username = mysqli_real_escape_string($db,htmlspecialchars($_POST['username'])); 
 $password = mysqli_real_escape_string($db,htmlspecialchars($_POST['password']));
 
 if($username !== "" && $password !== "")
 {
 $requete = "SELECT count(*) FROM utilisateurs where 
 email_user = '".$username."' and mdp_user = '".sha1($password)."' ";
 $exec_requete = mysqli_query($db,$requete);
 $reponse = mysqli_fetch_array($exec_requete);
 $count = $reponse['count(*)'];
 if($count!=0) // nom d'utilisateur et mot de passe correctes
 {
 $_SESSION['username'] = $username;
 header('Location: //bilan.quizzspot.fr/dashboard');
 }
 else
 {
 header('Location: index.php?erreur=1'); // utilisateur ou mot de passe incorrect
 }
 }
 else
 {
 header('Location: index.php?erreur=2'); // utilisateur ou mot de passe vide
 }
}
else
{
 header('Location: index.php');
}
mysqli_close($db); // fermer la connexion
?>