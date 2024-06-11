from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})  # Allow CORS for all routes and all origins

# Configuration de la base de données
db_config = {
    'host': "localhost",
    'user': "web",
    'passwd': "Uslof504",
    'database': "quizzspot"
}

def get_db_connection():
    return mysql.connector.connect(**db_config)

@app.route('/validate_code', methods=['GET'])
def validate_code():
    code = request.args.get('code')
    if not code:
        return jsonify({"error": "Un code est requis"}), 400

    db = get_db_connection()
    cursor = db.cursor(dictionary=True)

    query = """
    SELECT utilisateurs.id_user, utilisateurs.nom_user, utilisateurs.prenom_user
    FROM sessions
    JOIN rel_utilisateurs_groupes ON sessions.id_groupe = rel_utilisateurs_groupes.id_groupe
    JOIN utilisateurs ON rel_utilisateurs_groupes.id_user = utilisateurs.id_user
    WHERE sessions.code_session = %s
    """
    cursor.execute(query, (code,))
    participants = cursor.fetchall()
    cursor.close()
    db.close()

    if participants:
        response = {
            'test':{'code': True},
            'nombre_participants': len(participants),
            'participants': participants
        }
    else:
        response = {'test':{'code': False}}

    return jsonify(response)


@app.route('/mark_connected', methods=['GET'])
def mark_connected():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Un nom et prenom sont requis"}), 400

    db = get_db_connection()
    cursor = db.cursor()

    query = "UPDATE utilisateurs SET bool_connexion = 1 WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    db.commit()
    cursor.close()
    db.close()

    return jsonify({"status": "success", "message": "Participant marquer comme connecter"})


@app.route('/unmark_connected', methods=['GET'])
def unmark_connected():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Le nom et prenom sont requis"}), 400

    db = get_db_connection()
    cursor = db.cursor()

    query = "UPDATE utilisateurs SET bool_connexion = 0 WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    db.commit()
    cursor.close()
    db.close()

    return jsonify({"status": "success", "message": "Participant marquer comme déconnecter"})


@app.route('/is_participant_available', methods=['GET'])
def is_participant_available():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Le nom et le prenom sont requis"}), 400

    db = get_db_connection()
    cursor = db.cursor(dictionary=True)

    query = "SELECT bool_connexion FROM utilisateurs WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    participant = cursor.fetchone()
    cursor.close()
    db.close()

    if participant and participant['bool_connexion'] == 1:
        return jsonify({'Nom_dispo': 'false'})
    else:
        return jsonify({'Nom_dispo': 'true'})

@app.route('/current_question', methods=['GET'])
def current_question():
    db = get_db_connection()
    cursor = db.cursor(dictionary=True)

    # Requête pour récupérer la session de quiz active
    query = """
    SELECT s.id_session, q.id_question, q.intitule_question, mq.valeur_moda_quizz as temps_alloue
    FROM sessions s
    JOIN questions q ON s.id_quizz = q.id_quizz
    JOIN modalites_quizz mq ON s.id_quizz = mq.id_quizz
    WHERE s.bool_session = 2 AND q.bool_question = 2 AND mq.nom_moda_quizz = 'temps'
    ORDER BY q.id_question ASC
    LIMIT 1
    """
    cursor.execute(query)
    current_question = cursor.fetchone()
    cursor.close()
    db.close()

    if current_question:
        response = {
            'num_question': current_question['id_question'],
            'question': current_question['intitule_question'],
            'temps_alloue': current_question['temps_alloue']
        }
    else:
        response = {'error': 'Aucune question en cours'}

    return jsonify(response)


@app.route('/has_responded', methods=['GET'])
def has_responded():
    token = request.args.get('token')
    if not token:
        return jsonify({"error": "Un token et requis"}), 400

    db = get_db_connection()
    cursor = db.cursor()

    try:
        # Requête pour vérifier si l'apprenant a déjà répondu à la question en cours
        query = """
        SELECT COUNT(*) FROM reponses_apprenant 
        WHERE id_user = %s AND id_question = (
            SELECT id_question FROM reponses_apprenant 
            ORDER BY id_reponse_apprenant DESC LIMIT 1
        )
        """
        cursor.execute(query, (token,))
        has_responded = cursor.fetchone()[0] > 0
    except mysql.connector.Error as err:
        print("Erreur MySQL:", err)
        return jsonify({"error": "Erreur lors de la vérification de la réponse de l'apprenant"}), 500
    finally:
        cursor.close()
        db.close()

    return jsonify({"has_responded": has_responded})

@app.route('/insert_token', methods=['POST'])
def insert_token():
    data = request.json
    token = data.get('token')
    nom_complet = data.get('nom_complet')

    if not token or not nom_complet:
        return jsonify({"error": "Le Token et le nom_complet sont requis"}), 400

    db = get_db_connection()
    cursor = db.cursor()

    query = "UPDATE utilisateurs SET token = %s WHERE CONCAT(prenom_user, ' ', nom_user ) = %s"
    cursor.execute(query, (token, nom_complet))
    db.commit()
    cursor.close()
    db.close()

    return jsonify({'status': 'success', 'message': 'Token inserer avec succes'})

@app.route('/submit_answer', methods=['POST'])
def submit_answer():
    data = request.get_json()
    
    token = data.get('token')
    nom_reponse = data.get('nom_reponse')
    
    if not token or not nom_reponse:
        return jsonify({"error": "Token ou nom_reponse manquant"}), 400
    
    try:
        db = get_db_connection()
        cursor = db.cursor(dictionary=True)
        
        # Vérifier si le token est valide et récupérer l'id_user correspondant
        cursor.execute("SELECT id_user FROM utilisateurs WHERE token = %s", (token,))
        user = cursor.fetchone()
        
        if not user:
            return jsonify({"error": "Token invalide"}), 400
        
        id_user = user['id_user']
        
        # Récupérer la session active pour l'utilisateur et le quiz
        cursor.execute("""
            SELECT id_session, id_quizz
            FROM sessions WHERE bool_session = 2
        """,)
        session = cursor.fetchone()
        
        if not session:
            return jsonify({"error": "Aucune session active trouvée pour cet utilisateur"}), 400
        
        id_session = session['id_session']
        id_quizz = session['id_quizz']
        
        # Récupérer la question active pour la session
        cursor.execute("""
            SELECT id_question
            FROM questions
            WHERE id_quizz = %s AND bool_question = 2
            ORDER BY id_question ASC
            LIMIT 1
        """, (id_quizz,))
        question = cursor.fetchone()
        
        if not question:
            return jsonify({"error": "Aucune question active trouvée"}), 400
        
        id_question = question['id_question']
        
        # Récupérer l'id_reponse basé sur nom_reponse et id_question
        cursor.execute("""
            SELECT id_reponse
            FROM reponses
            WHERE nom_reponse = %s AND id_question = %s
        """, (nom_reponse, id_question))
        reponse_info = cursor.fetchone()
        
        if not reponse_info:
            return jsonify({"error": "nom_reponse invalide pour la question active"}), 400
        
        id_reponse = reponse_info['id_reponse']
        
        # Insérer la réponse de l'apprenant dans la base de données
        cursor.execute("""
            INSERT INTO reponses_apprenant (id_user, id_session, id_question, id_reponse) 
            VALUES (%s, %s, %s, %s)
        """, (id_user, id_session, id_question, id_reponse))
        db.commit()
        
        return jsonify({"status": "success", "message": "Réponse enregistrée avec succès"}), 200
        
    except mysql.connector.Error as err:
        return jsonify({"error": str(err)}), 500
    finally:
        cursor.close()
        db.close()

@app.route('/is_quizz_started', methods=['GET'])
def is_quizz_started():
    #try:
    db = get_db_connection()
    cursor = db.cursor()

    query = "SELECT COUNT(id_quizz) FROM quizzs WHERE bool_quizz = '2' "
    cursor.execute(query)
    results = cursor.fetchall()
    cursor.close()
    db.close()

    reponse = False
    if results[0][0] == 1 :
        reponse = True

    return jsonify({"msg": reponse})
    
    
    #return jsonify({"msg" : resultats})
    # if session and session.get('bool_quizz') == '2':
    #     return jsonify({"quizz_debut": True})
    # else:
    #     return jsonify({"quizz_debut": False})

    # except mysql.connector.Error as err:
    #     print("Erreur MySQL:", err)
    #     return jsonify({"error": f"{err}"}), 500


# Endpoint par défaut pour les routes non définies
@app.route('/')
@app.route('/<path:path>')
def catch_all(path=None):
    return jsonify({"error": "Endpoint non fourni"}), 404


if __name__ == '__main__':
    app.run(debug=True)