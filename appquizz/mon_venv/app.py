import sys
from PySide6.QtWidgets import QApplication, QMainWindow, QWidget, QVBoxLayout, QComboBox, QPushButton, QLabel
import pymysql
from login import LoginPage
from PySide6.QtCore import Signal
from PySide6.QtGui import QPixmap


class ConnexionPage(QMainWindow):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Page de Connexion")
        self.setGeometry(100, 100, 400, 200)

        # Créer une instance de la page de connexion
        self.login_page = LoginPage()
        self.login_page.connexion_reussie.connect(self.afficher_page_selection_session)

        # Initialiser la page de sélection de session et la page QR
        self.session_selection_page = None
        self.QR_page = None

    def afficher_page_selection_session(self):
        # Fermer la page de connexion
        self.login_page.close()

        # Créer une instance de la page de sélection de session si elle n'existe pas déjà
        if not self.session_selection_page:
            self.session_selection_page = SessionSelectionPage()
            self.session_selection_page.signal_session.connect(self.afficher_page_QR)
            self.session_selection_page.show()

    def afficher_page_QR(self):
        if self.session_selection_page:
            self.session_selection_page.close()

        if not self.QR_page:
            self.QR_page = AffichageQR()
        self.QR_page.show()


class SessionSelectionPage(QWidget):
    signal_session = Signal()

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

            # Exécuter une requête SQL pour sélectionner toutes les sessions
            cursor.execute("SELECT nom_session, date_session, id_quizz FROM sessions")

            # Récupérer les noms de session
            sessions = cursor.fetchall()

            # Remplir la liste déroulante avec les noms de session
            for session in sessions:
                nom_session, date_session, id_quizz = session
                self.sessions_combobox.addItem(f"{nom_session} - {date_session}", id_quizz)

            # Fermer le curseur et la connexion
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la connexion à la base de données : {e}")

    def se_connecter(self):
        # Récupérer l'ID de la session sélectionnée
        index_selectionne = self.sessions_combobox.currentIndex()
        id_quizz_selectionne = self.sessions_combobox.itemData(index_selectionne)

        # Code pour se connecter à la session sélectionnée
        print(f"ID du quizz sélectionné: {id_quizz_selectionne}")
        
        # Mettre à jour le booléen lié à la session dans la base de données
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

            # Remettre tous les booléens de quizzs à 1
            cursor.execute("UPDATE quizzs SET bool_quizz = 1")
            conn.commit()

            # Mettre à jour le booléen du quizz sélectionné à 2
            cursor.execute("UPDATE quizzs SET bool_quizz = 2 WHERE id_quizz = %s", (id_quizz_selectionne,))
            conn.commit()

            # Fermer le curseur et la connexion
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la mise à jour de la base de données : {e}")

        # Émettre le signal pour afficher la page du QR code
        self.signal_session.emit()


class AffichageQR(QWidget):
    def __init__(self):
        super().__init__()
        self.setWindowTitle("QR Code")
        self.setGeometry(300, 300, 300, 300)

        layout = QVBoxLayout()

        self.qr_label = QLabel()
        layout.addWidget(self.qr_label)

        # Charger l'image du QR code
        path = "F:/QuizzSpot/Appliquizz/mon_venv/qrcode.png"
        pixmap = QPixmap(path)

        # Vérifier si l'image a été chargée correctement
        if pixmap.isNull():
            print("Erreur: Le fichier qrcode.png n'a pas pu être chargé.")
            # Afficher un message d'erreur à la place
            self.qr_label.setText("Erreur: QR code non disponible.")
        else:
            self.qr_label.setPixmap(pixmap)

        self.setLayout(layout)


if __name__ == "__main__":
    app = QApplication(sys.argv)

    connexion_page = ConnexionPage()
    connexion_page.login_page.show()

    sys.exit(app.exec())
