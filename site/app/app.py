from flask import Flask, request, jsonify
import mysql.connector

app = Flask(__name__)

# Configuration de la base de données
db = mysql.connector.connect(
    host="localhost",
    user="web",
    passwd="Uslof504",
    database="quizzspot"
)
cursor = db.cursor(dictionary=True)


@app.route('/validate_code', methods=['GET'])
def validate_code():
    code = request.args.get('code')
    if not code:
        return jsonify({"error": "Code is required"}), 400

    query = """
    SELECT utilisateurs.id_user, utilisateurs.nom_user, utilisateurs.prenom_user
    FROM sessions
    JOIN rel_utilisateurs_groupes ON sessions.id_groupe = rel_utilisateurs_groupes.id_groupe
    JOIN utilisateurs ON rel_utilisateurs_groupes.id_user = utilisateurs.id_user
    WHERE sessions.code_session = %s
    """
    cursor.execute(query, (code,))
    participants = cursor.fetchall()

    if participants:
        response = {
            'test': {'code': True},
            'nombre_participants': len(participants),
            'participants': participants
        }
    else:
        response = {'test': {'code': False}}

    return jsonify(response)


@app.route('/mark_connected', methods=['GET'])
def mark_connected():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Nom and Prenom are required"}), 400

    query = "UPDATE utilisateurs SET bool_connexion = 1 WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    db.commit()

    return jsonify({"status": "success", "message": "Participant marked as connected"})


@app.route('/unmark_connected', methods=['GET'])
def unmark_connected():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Nom and Prenom are required"}), 400

    query = "UPDATE utilisateurs SET bool_connexion = 0 WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    db.commit()

    return jsonify({"status": "success", "message": "Participant marked as disconnected"})


@app.route('/is_participant_available', methods=['GET'])
def is_participant_available():
    nom = request.args.get('nom')
    prenom = request.args.get('prenom')
    if not nom or not prenom:
        return jsonify({"error": "Nom and Prenom are required"}), 400

    query = "SELECT bool_connexion FROM utilisateurs WHERE nom_user = %s AND prenom_user = %s"
    cursor.execute(query, (nom, prenom))
    participant = cursor.fetchone()

    if participant and participant['bool_connexion'] == 1:
        return jsonify({'Nom_dispo': 'false'})
    else:
        return jsonify({'Nom_dispo': 'true'})


@app.route('/current_question', methods=['GET'])
def current_question():
    token = request.args.get('token')
    if not token:
        return jsonify({"error": "Token is required"}), 400

    query = "SELECT id_question FROM reponses_apprenant WHERE id_user = %s ORDER BY id_reponse_apprenant DESC LIMIT 1"
    cursor.execute(query, (token,))
    last_answered_question = cursor.fetchone()

    if last_answered_question:
        current_question = last_answered_question['id_question'] + 1
    else:
        current_question = 1

    return jsonify({'current_question': current_question})


@app.route('/submit_answer', methods=['POST'])
def submit_answer():
    data = request.json
    token = data.get('token')
    answer = data.get('answer')

    if not token or not answer:
        return jsonify({"error": "Token and Answer are required"}), 400

    # Placeholder values, replace with actual logic to fetch ids
    id_user = 1
    id_session = 1
    id_question = 1
    id_quizz = 1

    query = """
    INSERT INTO reponses_apprenant (id_user, id_session, id_question, id_quizz, id_reponse) 
    VALUES (%s, %s, %s, %s, %s)
    """
    cursor.execute(query, (id_user, id_session, id_question, id_quizz, answer))
    db.commit()

    return jsonify({'status': 'success', 'message': 'Answer submitted successfully'})


@app.route('/insert_token', methods=['POST'])
def insert_token():
    data = request.json
    token = data.get('token')
    nom_complet = data.get('nom_complet')

    if not token or not nom_complet:
        return jsonify({"error": "Token and Nom_complet are required"}), 400

    query = "UPDATE utilisateurs SET token = %s WHERE CONCAT(nom_user, ' ', prenom_user) = %s"
    cursor.execute(query, (token, nom_complet))
    db.commit()

    return jsonify({'status': 'success', 'message': 'Token inserted successfully'})


if __name__ == '__main__':
    app.run(debug=True)
