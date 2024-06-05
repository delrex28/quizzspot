<!doctype html>
<html>

<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>QuizzSpot Bilan - connexion</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css' rel='stylesheet'>
    <link href="css/login.css" rel="stylesheet">
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/vue@2.6.12/dist/vue.js'></script>
</head>

<body className='snippet-body'>
    <div class="container mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card px-5 py-5" id="form1">
                    <?php
                    session_start();
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = $_POST['email'];
                        $password = $_POST['password'];

                        // Connexion à la base de données
                        $servername = "quizzspot.fr";
                        $username = "web";
                        $db_password = "Uslof504";
                        $dbname = "quizzspot"; 

                        $conn = new mysqli($servername, $username, $db_password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        // Éviter les injections SQL
                        $email = $conn->real_escape_string($email);
                        $password = $conn->real_escape_string($password);

                        // Vérification des identifiants
                        $sql = "SELECT id_user FROM utilisateurs WHERE email_user='$email' AND mdp_user=SHA1('$password')";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $_SESSION['loggedin'] = true;
                            $_SESSION['userid'] = $row['id_user'];
                            $_SESSION['email'] = $email;
                            header("Location: index.php");
                            exit();
                        } else {
                            echo "<div class='alert alert-danger'>Email ou mot de passe incorrect</div>";
                        }

                        $conn->close();
                    }
                    ?>
                    <div class="form-data" v-if="!submitted">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="forms-inputs mb-4">
                                <span>Email</span>
                                <input autocomplete="off" type="text" name="email" v-model="email" v-bind:class="{'form-control':true, 'is-invalid' : !validEmail(email) && emailBlured}" v-on:blur="emailBlured = true">
                                <div class="invalid-feedback">Un email valide est requis.</div>
                            </div>
                            <div class="forms-inputs mb-4">
                                <span>Mot de passe</span>
                                <input autocomplete="off" type="password" name="password" v-model="password" v-bind:class="{'form-control':true, 'is-invalid' : !validPassword(password) && passwordBlured}" v-on:blur="passwordBlured = true">
                                <div class="invalid-feedback">Le mot de passe doit faire au moins 4 caractères.</div>
                            </div>
                            <div class="mb-3">
                                <button type="submit" class="btn btn-dark w-100">Se connecter</button>
                            </div>
                        </form>
                    </div>
                    <div class="success-data" v-else>
                        <div class="text-center d-flex flex-column"> <i class='bx bxs-badge-check'></i> <span class="text-center fs-1">Connexion<br> Réussie</span> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>
    <script type='text/javascript'>
        var app = new Vue({
            el: '#form1',
            data: function() {
                return {
                    email: "",
                    emailBlured: false,
                    valid: false,
                    submitted: false,
                    password: "",
                    passwordBlured: false
                }
            },

            methods: {

                validate: function() {
                    this.emailBlured = true;
                    this.passwordBlured = true;
                    if (this.validEmail(this.email) && this.validPassword(this.password)) {
                        this.valid = true;
                    }
                },

                validEmail: function(email) {
                    var re = /(.+)@(.+){2,}\.(.+){2,}/;
                    if (re.test(email.toLowerCase())) {
                        return true;
                    }
                },

                validPassword: function(password) {
                    if (password.length > 3) {
                        return true;
                    }
                },

                submit: function() {
                    this.validate();
                    if (this.valid) {
                        this.submitted = true;
                    }
                }
            }
        });
    </script>
</body>

</html>
