import sys
import os

# Ajoutez le chemin du répertoire contenant votre application à la variable d'environnement PYTHONPATH
sys.path.insert(0, '/var/www/app')

from app import app as application