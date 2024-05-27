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

        # Initialiser les pages
        self.session_selection_page = None
        self.QR_page = None
        self.apprenants_page = None

    def afficher_page_selection_session(self):
        self.login_page.close()

        if not self.session_selection_page:
            self.session_selection_page = SessionSelectionPage()
            self.session_selection_page.signal_session.connect(self.afficher_page_QR)
            self.session_selection_page.show()

    def afficher_page_QR(self, id_quizz_selectionne, id_session):
        if self.session_selection_page:
            self.session_selection_page.close()

        if not self.QR_page:
            self.QR_page = AffichageQR(id_quizz_selectionne, id_session, self)
        else:
            self.QR_page.id_quizz_selectionne = id_quizz_selectionne
            self.QR_page.id_session = id_session

        self.QR_page.show()

    def afficher_page_apprenants(self, id_quizz_selectionne, id_session):
        if self.QR_page:
            self.QR_page.close()

        if not self.apprenants_page:
            self.apprenants_page = ApprenantsPage(id_quizz_selectionne, id_session, self)
        else:
            self.apprenants_page.id_quizz_selectionne = id_quizz_selectionne
            self.apprenants_page.id_session = id_session

        self.apprenants_page.show()


class SessionSelectionPage(QWidget):
    signal_session = Signal(object, object)

    def __init__(self):
        super().__init__()

        self.setWindowTitle("Sélection de Session")
        self.setGeometry(200, 200, 400, 200)

        layout = QVBoxLayout()

        self.label = QLabel("Sélectionnez une session :")
        layout.addWidget(self.label)

        self.sessions_combobox = QComboBox()
        layout.addWidget(self.sessions_combobox)

        self.button = QPushButton("Se connecter")
        self.button.clicked.connect(self.se_connecter)
        layout.addWidget(self.button)

        self.setLayout(layout)

        self.remplir_sessions_combobox()

    def remplir_sessions_combobox(self):
        try:
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("SELECT nom_session, date_session, id_quizz, id_session FROM sessions")
            sessions = cursor.fetchall()

            for session in sessions:
                nom_session, date_session, id_quizz, id_session = session
                self.sessions_combobox.addItem(f"{nom_session} - {date_session}", (id_quizz, id_session))

            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la connexion à la base de données : {e}")

    def se_connecter(self):
        index_selectionne = self.sessions_combobox.currentIndex()
        id_quizz_selectionne, id_session_selectionne = self.sessions_combobox.itemData(index_selectionne)
        print(f"ID du quizz sélectionné: {id_quizz_selectionne}, ID de la session sélectionnée: {id_session_selectionne}")

        try:
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("UPDATE quizzs SET bool_quizz = 1")
            conn.commit()
            cursor.execute("UPDATE quizzs SET bool_quizz = 2 WHERE id_quizz = %s", (id_quizz_selectionne,))
            conn.commit()
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la mise à jour de la base de données : {e}")

        self.signal_session.emit(id_quizz_selectionne, id_session_selectionne)


class AffichageQR(QWidget):
    def __init__(self, id_quizz_selectionne, id_session, parent=None):
        super().__init__()
        self.id_quizz_selectionne = id_quizz_selectionne
        self.id_session = id_session
        self.parent = parent
        self.setWindowTitle("QR Code")

        layout = QVBoxLayout()

        self.qr_label = QLabel()
        h_layout = QHBoxLayout()
        h_layout.addStretch()
        h_layout.addWidget(self.qr_label)
        h_layout.addStretch()

        layout.addLayout(h_layout)

        self.code_label = QLabel()
        self.code_label.setFont(QFont('Arial', 20))
        self.code_label.setAlignment(Qt.AlignCenter)
        layout.addWidget(self.code_label)

        self.setLayout(layout)

        self.load_qr_code()
        self.showFullScreen()

        # Bouton pour basculer vers la page des apprenants
        self.switch_button = QPushButton("Voir les apprenants connectés")
        self.switch_button.clicked.connect(self.switch_to_apprenants_page)
        layout.addWidget(self.switch_button)

    def load_qr_code(self):
        path = "G:/QuizzSpot/Appliquizz/mon_venv/qrcode.png"
        pixmap = QPixmap(path)

        if pixmap.isNull():
            print("Erreur: Le fichier qrcode.png n'a pas pu être chargé.")
            self.qr_label.setText("Erreur: QR code non disponible.")
        else:
            self.qr_label.setPixmap(pixmap)
            self.adjust_qr_size()
            self.generer_code_et_stocke()

    def adjust_qr_size(self):
        if self.qr_label.pixmap():
            self.qr_label.setPixmap(self.qr_label.pixmap().scaled(self.size(), Qt.KeepAspectRatio, Qt.SmoothTransformation))

    def generer_code_et_stocke(self):
        code = ''.join(random.choices(string.ascii_uppercase + string.digits, k=6))

        try:
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("UPDATE quizzs SET code_quizz = %s WHERE id_quizz = %s", (code, self.id_quizz_selectionne))
            conn.commit()
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de l'insertion dans la base de données : {e}")

        self.code_label.setText(f"Utilisez le QR code pour rejoindre la page du quizz, et le code suivant pour vous y connecter : {code}")


    def switch_to_apprenants_page(self):
        self.parent.afficher_page_apprenants(self.id_quizz_selectionne, self.id_session)


class ApprenantsPage(QWidget):
    def __init__(self, id_quizz_selectionne, id_session, parent=None):
        super().__init__()
        self.id_quizz_selectionne = id_quizz_selectionne
        self.id_session = id_session
        self.parent = parent
        self.setWindowTitle("Apprenants Connectés")

        layout = QVBoxLayout()
        self.apprenants_label = QLabel("Liste des apprenants connectés :")
        layout.addWidget(self.apprenants_label)

        self.apprenants_list = QLabel()
        layout.addWidget(self.apprenants_list)

        self.setLayout(layout)

        self.load_apprenants()

        # Bouton pour actualiser la liste des apprenants
        self.refresh_button = QPushButton("Actualiser")
        self.refresh_button.clicked.connect(self.load_apprenants)
        layout.addWidget(self.refresh_button)

        # Bouton pour retourner à la page du QR code
        self.switch_button = QPushButton("Retour au QR code")
        self.switch_button.clicked.connect(self.switch_to_QR_page)
        layout.addWidget(self.switch_button)

    def load_apprenants(self):
        try:
            conn = pymysql.connect(
                host='10.40.1.58',
                user='louis',
                password='louis',
                database='quizzspot'
            )
            cursor = conn.cursor()

            # Récupérer l'ID du groupe lié à la session sélectionnée
            cursor.execute("SELECT id_groupe FROM sessions WHERE id_session = %s", (self.id_session,))
            groupe_id = cursor.fetchone()[0]

            # Récupérer les IDs des utilisateurs du groupe
            cursor.execute("SELECT id_user FROM rel_utilisateurs_groupes WHERE id_groupe = %s", (groupe_id,))
            utilisateurs_ids = cursor.fetchall()

            # Récupérer les noms, prénoms et statut de connexion des utilisateurs
            apprenants = []
            for utilisateur_id in utilisateurs_ids:
                cursor.execute("SELECT nom_user, prenom_user, statut_connexion FROM utilisateurs WHERE id_user = %s", (utilisateur_id,))
                utilisateur = cursor.fetchone()
                if utilisateur:
                    nom, prenom, statut_connexion = utilisateur
                    apprenants.append((nom, prenom, statut_connexion))

            conn.close()

            apprenants_text = "\n".join([f"{nom} {prenom} - {'Connecté' if statut_connexion==1 else 'Non connecté'}" for nom, prenom, statut_connexion in apprenants])
            self.apprenants_list.setText(apprenants_text)

        except pymysql.Error as e:
            print(f"Erreur lors de la récupération des apprenants : {e}")

    def switch_to_QR_page(self):
        self.parent.afficher_page_QR(self.id_quizz_selectionne, self.id_session)


if __name__ == "__main__":
    app = QApplication(sys.argv)

    connexion_page = ConnexionPage()
    connexion_page.login_page.show()

    sys.exit(app.exec())
