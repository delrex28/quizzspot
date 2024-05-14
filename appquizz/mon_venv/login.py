import sys
from PySide6.QtWidgets import QApplication, QWidget, QVBoxLayout, QLabel, QLineEdit, QPushButton, QMessageBox
from PySide6.QtCore import Signal
import pymysql

class LoginPage(QWidget):
    # Déclarer un signal pour indiquer une connexion réussie
    connexion_reussie = Signal()

    def __init__(self):
        super().__init__()
        self.setWindowTitle("Connexion")
        self.setup_ui()

    def setup_ui(self):
        layout = QVBoxLayout()

        # Labels et champs de saisie pour le nom d'utilisateur et le mot de passe
        self.username_label = QLabel("Nom d'utilisateur:")
        self.username_input = QLineEdit()
        self.password_label = QLabel("Mot de passe:")
        self.password_input = QLineEdit()
        self.password_input.setEchoMode(QLineEdit.Password)

        # Bouton de connexion
        self.login_button = QPushButton("Se connecter")
        self.login_button.clicked.connect(self.login)

        # Ajouter les widgets au layout
        layout.addWidget(self.username_label)
        layout.addWidget(self.username_input)
        layout.addWidget(self.password_label)
        layout.addWidget(self.password_input)
        layout.addWidget(self.login_button)

        self.setLayout(layout)

    def login(self):
        # Récupérer le nom d'utilisateur et le mot de passe
        username = self.username_input.text()
        password = self.password_input.text()

        # Connexion à la base de données pour vérifier les informations d'identification
        try:
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',  # Remplacez par le nom d'utilisateur de la base de données
                password='louis',  # Remplacez par le mot de passe de la base de données
                database='quizzspot'
            )

            cursor = conn.cursor()

            # Exécutez une requête pour vérifier les informations d'identification
            query = "SELECT nom_user, mdp_user FROM utilisateurs WHERE nom_user = %s"
            cursor.execute(query, (username,))
            result = cursor.fetchone()

            if result is not None:
                db_username, db_password = result

                if password == db_password:
                    print("Connexion réussie !")
                    self.close()
                    self.connexion_reussie.emit()
                else:
                    self.show_error_message("Mot de passe incorrect")
            else:
                self.show_error_message("Nom d'utilisateur incorrect")

            cursor.close()
            conn.close()

        except pymysql.Error as e:
            self.show_error_message(f"Erreur lors de la connexion à la base de données : {e}")

    def show_error_message(self, message):
        # Afficher un message d'erreur dans une boîte de dialogue
        error_popup = QMessageBox()
        error_popup.setWindowTitle("Erreur de connexion")
        error_popup.setText(message)
        error_popup.setIcon(QMessageBox.Warning)
        error_popup.exec()


if __name__ == "__main__":
    app = QApplication(sys.argv)
    login_page = LoginPage()
    login_page.show()
    sys.exit(app.exec())
