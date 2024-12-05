import paho.mqtt.client as mqtt
import json
import configparser
from datetime import datetime
import os 

config = configparser.ConfigParser()
configpath = os.path.join(os.path.dirname(__file__), 'config.ini')
config.read(configpath)

solaredge_id = config['mqtt']['solaredge_id']
room = config['mqtt']['room']


topic_triphaso = config['mqtt']['topic_triphaso'].format(room=room)
topic_am107 = config['mqtt']['topic_am107'].format(room=room)
topic_solaredge = config['mqtt']['topic_solaredge'].format(solaredge_id=solaredge_id)
broker = config['mqtt']['broker']

# Charger les seuils depuis le fichier de configuration
temperature_max = float(config['seuils']['temperature_max'])
humidite_max = float(config['seuils']['humidite_max'])
pression_max = float(config['seuils']['pression_max'])

def on_connect(client, userdata, flags, rc):
    print(f"Connecté avec le code de résultat {rc}")
    client.subscribe(topic_triphaso)
    client.subscribe(topic_am107)
    client.subscribe(topic_solaredge)
    print("Abonné aux topics pour la salle et le panneau solaire spécifiés.")

def on_message(client, userdata, msg):
    try:
        print(f"Message reçu sur le topic: {msg.topic}")
        payload = json.loads(msg.payload.decode())
        
        # Vérifie le type de données et appelle la fonction de traitement appropriée
        if isinstance(payload, list) and len(payload) == 2:
            if 'temperature' in payload[0]:
                processed_data = process_am107_data(payload)
            else:
                processed_data = process_triphaso_data(payload)
        else:
            processed_data = process_solaredge_data(payload)
        
        # Génère un timestamp et log les données traitées
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        log_data(timestamp, msg.topic, processed_data)

    except Exception as e:
        print(f"Erreur lors du traitement du message: {e}")

def log_data(timestamp, topic, data):
    try:
        with open("data_log.txt", "a") as f:
            log_entry = f"[{timestamp}] Topic: {topic} | Data: {data}\n"
            f.write(log_entry)
        print("Données enregistrées dans le fichier.")
    except Exception as e:
        print(f"Erreur lors de l'écriture dans le fichier: {e}")

def process_triphaso_data(payload):
    donnees_puissance = payload[0]
    infos_dispositif = payload[1]
    
    results = []
    
    # Dictionnaires des clés et modèles pour les données de puissance et les infos du dispositif
    cles_puissance = {
        'positive_active_power_W': "Puissance active positive: {} W",
        'negative_reactive_power_VAR': "Puissance réactive négative: {} VAR",
        'sum_positive_active_energy_Wh': "Énergie active positive: {} Wh",
        'sum_negative_reactive_energy_VARh': "Énergie réactive négative: {} VARh"
    }
    
    cles_dispositif = {
        'deviceName': "Nom du dispositif: {}",
        'devEUI': "Identifiant du dispositif (devEUI): {}",
        'room': "Pièce: {}",
        'floor': "Étage: {}",
        'Building': "Bâtiment: {}"
    }
    
    # Traitement des données de puissance
    for cle, modele in cles_puissance.items():
        if cle in donnees_puissance:
            results.append(modele.format(donnees_puissance[cle]))
    
    # Traitement des infos du dispositif
    for cle, modele in cles_dispositif.items():
        if cle in infos_dispositif:
            results.append(modele.format(infos_dispositif[cle]))
    
    return " | ".join(results)

def process_am107_data(payload):
    print("Traitement AM107\n")
    sensor_data, device_info = payload

    result = []
    if isinstance(sensor_data, dict) and isinstance(device_info, dict):
        # Dictionnaires des clés et modèles pour les données des capteurs et les infos du dispositif
        sensor_keys = {
            'temperature': "Température: {} °C",
            'humidity': "Humidité: {} %",
            'co2': "CO2: {} ppm",
            'tvoc': "TVOC: {} ppb",
            'illumination': "Illumination: {} lux",
            'pressure': "Pression: {} hPa",
            'activity': "Activité: {}",
            'infrared': "Infrarouge: {}",
            'infrared_and_visible': "Infrarouge + Visible: {}"
        }

        device_keys = {
            'deviceName': "Nom du dispositif: {}",
            'devEUI': "Identifiant du dispositif (devEUI): {}",
            'room': "Pièce: {}",
            'floor': "Étage: {}",
            'Building': "Bâtiment: {}"
        }

        # Traitement des données des capteurs
        for key, template in sensor_keys.items():
            if key in sensor_data:
                result.append(template.format(sensor_data[key]))

        # Traitement des infos du dispositif
        for key, template in device_keys.items():
            if key in device_info:
                result.append(template.format(device_info[key]))

    else:
        result.append("Erreur : Le format du payload AM107 est invalide.")
    
    return " | ".join(result)

def process_solaredge_data(payload):
    print("Traitement Solaredge\n")
    
    # Dictionnaire des clés et modèles pour les données Solaredge
    modeles_cles = {
        'lastUpdateTime': "Dernière mise à jour: {}",
        'lifeTimeData': "Énergie totale: {} Wh",
        'lastYearData': "Énergie l'année dernière: {} Wh",
        'lastMonthData': "Énergie le mois dernier: {} Wh",
        'lastDayData': "Énergie du dernier jour: {} Wh",
        'currentPower': "Puissance actuelle: {} W",
        'measuredBy': "Mesuré par: {}"
    }

    resultat = []
    # Traitement des données Solaredge
    for cle, modele in modeles_cles.items():
        if cle in payload:
            resultat.append(modele.format(payload[cle]))
    
    return " | ".join(resultat)


def alertes_data(data):
    alerts = []
    
    # Vérification des seuils et ajout des alertes correspondantes
    if 'temperature' in data and data['temperature'] > temperature_max:
        alerts.append(f"Température élevée: {data['temperature']}°C (max {temperature_max}°C)")
    if 'humidity' in data and data['humidity'] > humidite_max:
        alerts.append(f"Humidité élevée: {data['humidity']}% (max {humidite_max}%)")
    if 'pressure' in data and data['pressure'] > pression_max:
        alerts.append(f"Pression élevée: {data['pressure']} hPa (max {pression_max} hPa)")

    # Enregistrement des alertes dans un fichier externe
    if alerts:
        with open('alertes.txt', 'a') as f:
            for alert in alerts:
                f.write(f"{datetime.now()} - {alert}\n")
        print("Alerte enregistrée dans le fichier externe.")

client = mqtt.Client()

client.on_connect = on_connect
client.on_message = on_message

client.connect(broker, port = 1883, keepalive=60)

client.loop_forever()
