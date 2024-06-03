import sys
import random
import string
from PySide6.QtWidgets import QApplication, QMainWindow, QWidget, QVBoxLayout, QHBoxLayout, QComboBox, QPushButton, QLabel
import pymysql
from login import LoginPage
from PySide6.QtCore import Signal, Qt, QTimer
from PySide6.QtGui import QPixmap, QFont


class ConnexionPage(QMainWindow):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Page de Connexion")

        # Créer une instance de la page de connexion
        self.login_page = LoginPage()
        self.login_page.connexion_reussie.connect(self.afficher_page_selection_session)

        # Initialiser les pages
        self.session_selection_page = None
        self.QR_page = None
        self.apprenants_page = None
        self.questions_page = None
        self.setWindowState(Qt.WindowFullScreen)

    def afficher_page_selection_session(self):
        self.login_page.close()

        if not self.session_selection_page:
            self.session_selection_page = SessionSelectionPage()
            self.session_selection_page.signal_session.connect(self.afficher_page_QR)
            self.session_selection_page.showFullScreen()

    def afficher_page_QR(self, id_quizz_selectionne, id_session):
        if self.session_selection_page:
            self.session_selection_page.close()

        if not self.QR_page:
            self.QR_page = AffichageQR(id_quizz_selectionne, id_session, self)
        else:
            self.QR_page.id_quizz_selectionne = id_quizz_selectionne
            self.QR_page.id_session = id_session

        self.QR_page.showFullScreen()

    def afficher_page_apprenants(self, id_quizz_selectionne, id_session):
        if self.QR_page:
            self.QR_page.close()

        if not self.apprenants_page:
            self.apprenants_page = ApprenantsPage(id_quizz_selectionne, id_session, self)
        else:
            self.apprenants_page.id_quizz_selectionne = id_quizz_selectionne
            self.apprenants_page.id_session = id_session

        self.apprenants_page.showFullScreen()

    def afficher_page_questions(self, id_quizz_selectionne, id_session):
        if self.apprenants_page:
            self.apprenants_page.close()

        if not self.questions_page:
            self.questions_page = QuestionsPage(id_quizz_selectionne, id_session, self)
        else:
            self.questions_page.id_quizz_selectionne = id_quizz_selectionne

        self.questions_page.showFullScreen()


class SessionSelectionPage(QWidget):
    signal_session = Signal(object, object)

    def __init__(self):
        super().__init__()

        self.setWindowTitle("Sélection de Session")
        self.setGeometry(200, 200, 400, 200)

        layout = QVBoxLayout()
        layout.setAlignment(Qt.AlignCenter)

        # self.bvn_label = QLabel("Bienvenue sur Quizzspot")
        # self.bvn_label.setFont(QFont('Arial', 25))
        # layout.addWidget(self.bvn_label, alignment=Qt.AlignCenter)

        self.label = QLabel("Sélectionnez une session :")
        self.label.setFont(QFont('Arial', 20))
        layout.addWidget(self.label, alignment=Qt.AlignCenter)

        self.sessions_combobox = QComboBox()
        layout.addWidget(self.sessions_combobox, alignment=Qt.AlignCenter)

        self.button = QPushButton("Se connecter")
        self.button.clicked.connect(self.se_connecter)
        layout.addWidget(self.button, alignment=Qt.AlignCenter)

        self.setLayout(layout)

        self.remplir_sessions_combobox()

    def remplir_sessions_combobox(self):
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
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
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
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
        layout.setAlignment(Qt.AlignCenter)

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

        # Bouton pour basculer vers la page des apprenants
        self.switch_button = QPushButton("Voir les apprenants connectés")
        self.switch_button.clicked.connect(self.switch_to_apprenants_page)
        layout.addWidget(self.switch_button, alignment=Qt.AlignCenter)

    def load_qr_code(self):
        path = "G:\QuizzSpot\Appliquizz\mon_venv\qrcode.png"
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
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("UPDATE sessions SET code_session = %s WHERE id_session = %s", (code, self.id_session))
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
        layout.setAlignment(Qt.AlignCenter)

        self.apprenants_label = QLabel("Liste des apprenants connectés :")
        self.apprenants_label.setFont(QFont('Arial', 22))
        layout.addWidget(self.apprenants_label, alignment=Qt.AlignCenter)

        self.apprenants_list = QLabel()
        self.apprenants_list.setFont(QFont('Arial', 18))
        layout.addWidget(self.apprenants_list, alignment=Qt.AlignCenter)

        self.setLayout(layout)

        self.load_apprenants()

        # Bouton pour actualiser la liste des apprenants
        self.refresh_button = QPushButton("Actualiser")
        self.refresh_button.clicked.connect(self.load_apprenants)
        layout.addWidget(self.refresh_button, alignment=Qt.AlignCenter)

        # Bouton pour lancer le quiz
        self.start_quiz_button = QPushButton("Lancer le quiz")
        self.start_quiz_button.clicked.connect(self.start_quiz)
        layout.addWidget(self.start_quiz_button, alignment=Qt.AlignCenter)

        # Bouton pour retourner à la page du QR code
        self.switch_button = QPushButton("Retour au QR code")
        self.switch_button.clicked.connect(self.switch_to_QR_page)
        layout.addWidget(self.switch_button, alignment=Qt.AlignCenter)

    def load_apprenants(self):
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
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
                cursor.execute("SELECT nom_user, prenom_user, bool_connexion FROM utilisateurs WHERE id_user = %s", (utilisateur_id,))
                utilisateur = cursor.fetchone()
                if utilisateur:
                    nom, prenom, statut_connexion = utilisateur
                    apprenants.append((nom, prenom, statut_connexion))

            conn.close()

            apprenants_text = "\n".join([f"{nom} {prenom} - {'Connecté' if statut_connexion == 1 else 'Non connecté'}" for nom, prenom, statut_connexion in apprenants])
            self.apprenants_list.setText(apprenants_text)

        except pymysql.Error as e:
            print(f"Erreur lors de la récupération des apprenants : {e}")

    def start_quiz(self):
        self.parent.afficher_page_questions(self.id_quizz_selectionne, self.id_session)

    def switch_to_QR_page(self):
        self.parent.afficher_page_QR(self.id_quizz_selectionne, self.id_session)


class QuestionsPage(QWidget):
    def __init__(self, id_quizz_selectionne, id_session, parent=None):
        super().__init__()
        self.id_quizz_selectionne = id_quizz_selectionne
        self.id_session = id_session
        self.parent = parent
        self.setWindowTitle("Questions du Quiz")
        self.showFullScreen()

        self.layout = QVBoxLayout()
        self.layout.setAlignment(Qt.AlignCenter)
        
        self.question_label = QLabel()
        self.layout.addWidget(self.question_label, alignment=Qt.AlignCenter)

        self.setLayout(self.layout)

        self.questions = []
        self.current_question_index = 0

        self.timer = QTimer(self)
        self.timer.timeout.connect(self.next_question)

        self.load_questions()
        self.start_quiz()

    def load_questions(self):
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
                database='quizzspot'
            )
            cursor = conn.cursor()

            cursor.execute("SELECT id_question, intitule_question FROM questions WHERE id_quizz = %s", (self.id_quizz_selectionne,))
            self.questions = cursor.fetchall()

            conn.close()

        except pymysql.Error as e:
            print(f"Erreur lors de la récupération des questions : {e}")

    def load_responses(self, question_id):
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("SELECT nom_reponse, contenu_reponse, bool_bonne_reponse FROM reponses WHERE id_question = %s", (question_id,))
            responses = cursor.fetchall()
            conn.close()
            return responses
        except pymysql.Error as e:
            print(f"Erreur lors de la récupération des réponses : {e}")
            return []

    def clear_responses(self):
        # Enlever les anciennes réponses
        for i in reversed(range(self.layout.count())): 
            widget = self.layout.itemAt(i).widget()
            if widget != self.question_label:
                self.layout.removeWidget(widget)
                widget.deleteLater()

    def start_quiz(self):
        self.current_question_index = 0
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("SELECT valeur_moda_quizz FROM modalites_quizz WHERE id_quizz = %s", (self.id_quizz_selectionne,))
            moda_quizz_value = cursor.fetchone()
            conn.close()

            if moda_quizz_value and moda_quizz_value[0] is not None:
                self.timer_duration = int(moda_quizz_value[0]) * 1000  # Convert seconds to milliseconds

            self.timer.start(self.timer_duration)
            self.show_question()

        except pymysql.Error as e:
            print(f"Erreur lors de la récupération des modalités : {e}")

    def show_question(self):
        if self.current_question_index < len(self.questions):
            question_id, question_text = self.questions[self.current_question_index]
            self.question_label.setText(question_text)
            self.question_label.setFont(QFont('Arial', 35))

            self.clear_responses()  # Clear les réponses avant d'afficher la nouvelle question
                    
            responses = self.load_responses(question_id)
            for response in responses:
                response_text = f"{response[0]}: {response[1]}"
                response_label = QLabel(response_text)
                response_label.setFont(QFont('Arial', 25))
                self.layout.addWidget(response_label, alignment=Qt.AlignCenter)

    def next_question(self):
        self.current_question_index += 1
        if self.current_question_index < len(self.questions):
            self.show_question()
        else:
            self.timer.stop()
            self.question_label.setText("Le quiz est terminé.")
            self.clear_responses()  # Clear les réponses à la fin du quiz
            self.clear_quiz_code()  # Efface le code du quiz
            QTimer.singleShot(10000, self.close)  # Ferme l'application après 10 secondes

    def clear_quiz_code(self):
        try:
            conn = pymysql.connect(
                host='quizzspot.fr',
                user='web',
                password='Uslof504',
                database='quizzspot'
            )
            cursor = conn.cursor()
            cursor.execute("UPDATE sessions SET code_session = NULL WHERE id_session = %s", (self.id_session,))
            conn.commit()
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la mise à jour de la base de données pour effacer le code du quiz : {e}")





if __name__ == "__main__":
    app = QApplication(sys.argv)

    connexion_page = ConnexionPage()
    connexion_page.login_page.show()

    sys.exit(app.exec())
