import sys
import random
import string
from PySide6.QtWidgets import QApplication, QMainWindow, QWidget, QVBoxLayout, QHBoxLayout, QComboBox, QPushButton, QLabel
import pymysql
from login import LoginPage
from PySide6.QtCore import Signal, Qt
from PySide6.QtGui import QPixmap, QFont


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

    def afficher_page_QR(self, id_quizz_selectionne):
        if self.session_selection_page:
            self.session_selection_page.close()

        if not self.QR_page:
            self.QR_page = AffichageQR(id_quizz_selectionne)
        else:
            self.QR_page.id_quizz_selectionne = id_quizz_selectionne
        
        self.QR_page.show()


class SessionSelectionPage(QWidget):
    signal_session = Signal(object)

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
        self.signal_session.emit(id_quizz_selectionne)


class AffichageQR(QWidget):
    def __init__(self, id_quizz_selectionne):
        super().__init__()
        self.id_quizz_selectionne = id_quizz_selectionne
        self.setWindowTitle("QR Code")

        layout = QVBoxLayout()

        # Ajouter un QHBoxLayout pour centrer le QR code horizontalement
        self.qr_label = QLabel()
        h_layout = QHBoxLayout()
        h_layout.addStretch()
        h_layout.addWidget(self.qr_label)
        h_layout.addStretch()

        layout.addLayout(h_layout)

        self.setLayout(layout)
        
        # Charger l'image du QR code
        self.load_qr_code()
        self.showFullScreen()

    def load_qr_code(self):
        path = "F:/QuizzSpot/Appliquizz/mon_venv/qrcode.png"
        pixmap = QPixmap(path)

        # Vérifier si l'image a été chargée correctement
        if pixmap.isNull():
            print("Erreur: Le fichier qrcode.png n'a pas pu être chargé.")
            # Afficher un message d'erreur à la place
            self.qr_label.setText("Erreur: QR code non disponible.")
        else:
            self.qr_label.setPixmap(pixmap)
            self.adjust_qr_size()
            self.generer_code_et_stocke()

    def adjust_qr_size(self):
        # Ajuster la taille du QR code pour s'adapter à l'écran
        if self.qr_label.pixmap():
            self.qr_label.setPixmap(self.qr_label.pixmap().scaled(self.size(), Qt.KeepAspectRatio, Qt.SmoothTransformation))

    def generer_code_et_stocke(self):
        # Générer un code aléatoire
        code = ''.join(random.choices(string.ascii_uppercase + string.digits, k=6))

        try:
            # Connexion à la base de données
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )


            # Insérer le code généré dans la base de données
            cursor = conn.cursor()
            cursor.execute("UPDATE quizzs SET code_quizz = %s WHERE id_quizz = %s", (code, self.id_quizz_selectionne))
            conn.commit()

            # Fermer le curseur et la connexion
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de l'insertion dans la base de données : {e}")

        # Afficher le code généré
        code_label = QLabel(f"Utilisez le QR code pour rejoindre la page du quizz, et le code suivant pour vous y connecter : {code}")
        code_label.setFont(QFont('Arial', 20))  # Définir la taille de la police à 20
        code_label.setAlignment(Qt.AlignCenter)
        self.layout().addWidget(code_label)



if __name__ == "__main__":
    app = QApplication(sys.argv)

    connexion_page = ConnexionPage()
    connexion_page.login_page.show()

    sys.exit(app.exec())
