import sys
from PySide6.QtWidgets import QApplication, QMainWindow, QVBoxLayout, QLabel, QComboBox, QPushButton, QWidget
from PySide6.QtGui import QPixmap
import pymysql
from PySide6.QtCore import Qt


class AffichageQR(QMainWindow):
    def __init__(self):
        super().__init__()

        self.setWindowTitle("Sélectionnez une session")
        self.setGeometry(100, 100, 400, 300)

        layout = QVBoxLayout()

        self.label = QLabel("Sélectionnez une session :")
        layout.addWidget(self.label)

        self.sessions_combobox = QComboBox()  # Liste déroulante pour les sessions
        layout.addWidget(self.sessions_combobox)

        self.button = QPushButton("Afficher QR Code")
        self.button.clicked.connect(self.display_qr_code)
        layout.addWidget(self.button)

        self.qr_label = QLabel()
        layout.addWidget(self.qr_label)

        self.central_widget = QWidget()
        self.central_widget.setLayout(layout)
        self.setCentralWidget(self.central_widget)

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

            # Exemple : Exécuter une requête SQL pour sélectionner tous les noms de session avec leur date
            cursor.execute("SELECT nom_session, date_session FROM sessions")

            # Récupérer les noms de session avec leur date
            sessions = cursor.fetchall()

            # Remplir la liste déroulante avec les noms de session et leur date
            for session in sessions:
                nom_session, date_session = session
                self.sessions_combobox.addItem(f"{nom_session} - {date_session}")

            # Fermer le curseur et la connexion
            cursor.close()
            conn.close()
        except pymysql.Error as e:
            print(f"Erreur lors de la connexion à la base de données : {e}")

    def display_qr_code(self):
        session_selectionnee = self.sessions_combobox.currentText()
        if not session_selectionnee:
            return

        # Charger le QR code pré-généré et sauvegardé sous forme d'image
        pixmap = QPixmap("qrcode.png")
        self.qr_label.setPixmap(pixmap.scaled(200, 200, Qt.KeepAspectRatio))


if __name__ == "__main__":
    app = QApplication(sys.argv)
    main_window = AffichageQR()
    main_window.show()
    sys.exit(app.exec())
