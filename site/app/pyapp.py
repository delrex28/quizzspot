from flask import Flask, request, jsonify
from flask_restful import Resource, Api
from flask_jwt_extended import JWTManager, jwt_required, create_access_token
import pymysql

# Initialize Flask app
app = Flask(__name__)
api = Api(app)

# Setup JWT
app.config['JWT_SECRET_KEY'] = 'your-secret-key'
jwt = JWTManager(app)

# Setup MySQL connection
db = pymysql.connect(host='localhost',
                     user='web',
                     password='Uslof504',
                     database='quizzspot',
                     cursorclass=pymysql.cursors.DictCursor)

# API endpoints
class ValidateCode(Resource):
    @jwt_required()
    def get(self):
        code = request.args.get('Code')
        # Assume you have logic to validate the code against the database
        # and retrieve participants if code is valid
        participants = [{'id': '?', 'nom': '?', 'prenom': '?'}]
        response = {'test': {'code': True}, 'nombre_participants': len(participants), 'participants': participants}
        return jsonify(response)

class MarkConnected(Resource):
    @jwt_required()
    def get(self):
        nom = request.args.get('Nom')
        prenom = request.args.get('Prenom')
        # Assume you have logic to mark participant as connected
        response = {'status': 'success', 'message': 'Participant marked as connected'}
        return jsonify(response)

class UnmarkConnected(Resource):
    @jwt_required()
    def get(self):
        nom = request.args.get('Nom')
        prenom = request.args.get('Prenom')
        # Assume you have logic to unmark participant as connected
        response = {'status': 'success', 'message': 'Participant unmarked as connected'}
        return jsonify(response)

class IsQuizzStarted(Resource):
    @jwt_required()
    def get(self):
        code_quizz = request.args.get('Code_quizz')
        # Assume you have logic to check if the quiz has started
        quizz_debut = True  # Example value, replace with actual logic
        return jsonify({'quizz_debut': quizz_debut})

class SubmitAnswer(Resource):
    @jwt_required()
    def post(self):
        data = request.json
        token = data.get('Token')
        answer = data.get('Answer')
        # Assume you have logic to store the answer in the database
        response = {'success': True}
        return jsonify(response)

class GetScore(Resource):
    @jwt_required()
    def get(self):
        token = request.args.get('Token')
        # Assume you have logic to retrieve the score from the database
        score = 85  # Example value, replace with actual logic
        return jsonify({'score': score})

class InsertToken(Resource):
    @jwt_required()
    def post(self):
        data = request.json
        token = data.get('Token')
        nom_complet = data.get('Nom_complet')
        # Assume you have logic to insert the token into the database
        response = {'success': True}
        return jsonify(response)

class HasResponded(Resource):
    @jwt_required()
    def get(self):
        token = request.args.get('Token')
        # Assume you have logic to check if the participant has responded
        has_responded = True  # Example value, replace with actual logic
        return jsonify({'has_responded': has_responded})

class CurrentQuestion(Resource):
    @jwt_required()
    def get(self):
        # Assume you have logic to determine the current question
        current_question = {'num_question': 1, 'temps_alloue': 60}
        return jsonify(current_question)

class IsParticipantAvailable(Resource):
    @jwt_required()
    def get(self):
        nom = request.args.get('Nom')
        prenom = request.args.get('Prenom')
        # Assume you have logic to check if participant is available
        participant_available = True  # Example value, replace with actual logic
        return jsonify({'nom_dispo': participant_available})

class LastAnsweredQuestion(Resource):
    @jwt_required()
    def get(self):
        token = request.args.get('Token')
        # Assume you have logic to retrieve the last answered question
        last_answered_question = 5  # Example value, replace with actual logic
        return jsonify({'last_answered_question': last_answered_question})

# Add resources to API
api.add_resource(ValidateCode, '/validate_code')
api.add_resource(MarkConnected, '/mark_connected')
api.add_resource(UnmarkConnected, '/unmark_connected')
api.add_resource(IsQuizzStarted, '/is_quizz_started')
api.add_resource(SubmitAnswer, '/submit_answer')
api.add_resource(GetScore, '/get_score')
api.add_resource(InsertToken, '/insert_token')
api.add_resource(HasResponded, '/has_responded')
api.add_resource(CurrentQuestion, '/current_question')
api.add_resource(IsParticipantAvailable, '/is_participant_available')
api.add_resource(LastAnsweredQuestion, '/last_answered_question')

# Run the app
if __name__ == '__main__':
    app.run(debug=True)
