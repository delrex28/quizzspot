import sys
from PySide6.QtWidgets import QApplication, QMainWindow, QPushButton, QLabel, QVBoxLayout, QWidget, QComboBox
import pymysql
from login import LoginPage


class ConnexionPage(QMainWindow):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Page de Connexion")
        self.setGeometry(100, 100, 400, 200)

        # Créer une instance de la page de connexion
        self.login_page = LoginPage()
        self.login_page.connexion_reussie.connect(self.afficher_page_selection_session)
        self.login_page.show()

        # Initialiser la page de sélection de session
        self.session_selection_page = None

    def afficher_page_selection_session(self):
        # Fermer la page de connexion
        self.login_page.close()

        # Créer une instance de la page de sélection de session si elle n'existe pas déjà
        if not self.session_selection_page:
            self.session_selection_page = SessionSelectionPage()
            self.session_selection_page.show()


class SessionSelectionPage(QWidget):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Sélection de Session")
        self.setGeometry(200, 200, 400, 200)

        layout = QVBoxLayout()

        self.label = QLabel("Sélectionnez une session :")
        layout.addWidget(self.label)

        self.sessions_combobox = QComboBox()  # Liste déroulante pour les sessions
        layout.addWidget(self.sessions_combobox)

        self.button = QPushButton("Se connecter")
        self.button.clicked.connect(self.se_connecter)
        layout.addWidget(self.button)

        self.setLayout(layout)

        self.remplir_sessions_combobox()  # Remplir la liste déroulante avec les sessions disponibles

    def remplir_sessions_combobox(self):
        try:
            # Connexion à la base de données
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )

            # Création d'un curseur
            cursor = conn.cursor()

            # Exemple : Exécuter une requête SQL pour sélectionner toutes les sessions
            cursor.execute("SELECT nom_session FROM sessions")

            # Récupérer les noms de session
            sessions = cursor.fetchall()

            # Remplir la liste déroulante avec les noms de session
            for session in sessions:
                self.sessions_combobox.addItem(session[0])

            # Fermer le curseur et la connexion
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la connexion à la base de données : {e}")

    def se_connecter(self):
        # Code pour se connecter à la session sélectionnée
        session_selectionnee = self.sessions_combobox.currentText()
        print(f"Connexion à la session : {session_selectionnee}")


if __name__ == "__main__":
    app = QApplication(sys.argv)
    
    login_page = LoginPage()
    connexion_page = ConnexionPage()  # Créez une instance de la page de sélection de session
    
    # Connectez le signal de la page de connexion à la fonction de la page de sélection de session
    login_page.connexion_reussie.connect(connexion_page.afficher_page_selection_session)
    
    login_page.show()

    sys.exit(app.exec())
