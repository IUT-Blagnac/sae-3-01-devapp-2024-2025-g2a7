# -*- coding: utf-8 -*- 

import paho.mqtt.client as mqtt
import json
import configparser
from datetime import datetime
import os

# Charger la configuration
config = configparser.ConfigParser()
configpath = os.path.normpath(os.path.join(os.path.dirname(__file__), 'config.ini'))
config.read(configpath)

solaredge_id = config['mqtt'].get('solaredge_id', '')
room = config['mqtt'].get('room', '')

topic_triphaso = config['mqtt']['topic_triphaso'].format(room=room)
topic_am107 = config['mqtt']['topic_am107'].format(room=room)
topic_solaredge = config['mqtt']['topic_solaredge'].format(solaredge_id=solaredge_id)
broker = config['mqtt']['broker']

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
        
        if isinstance(payload, list) and len(payload) == 2:
            if 'temperature' in payload[0]:
                processed_data = process_am107_data(payload)
            else:
                processed_data = process_triphaso_data(payload)
        else:
            processed_data = process_solaredge_data(payload)
        
        timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        log_data(timestamp, msg.topic, processed_data)

    except Exception as e:
        print(f"Erreur lors du traitement du message: {e}")

def log_data(timestamp, topic, data):
    try:
        log_path = os.path.normpath(os.path.join(os.path.dirname(__file__), 'IOT', 'Final', 'datas', 'data_log.txt'))
        
        # Créer le répertoire si nécessaire
        if not os.path.exists(os.path.dirname(log_path)):
            os.makedirs(os.path.dirname(log_path))
        
        with open(log_path, "a", encoding='utf-8') as f:
            log_entry = f"[{timestamp}] Topic: {topic} | Data: {data}\n"
            f.write(log_entry)
        print("Données enregistrées dans le fichier.")
    except Exception as e:
        print(f"Erreur lors de l'enregistrement des données: {e}")

def donnee_filtree(data_list, output_file):
    try:
        # Chemin absolu pour éviter les doublons
        output_path = os.path.normpath(os.path.join(os.path.dirname(__file__), output_file.lstrip('/').lstrip('\\')))
        
        # Créer le répertoire si nécessaire
        if not os.path.exists(os.path.dirname(output_path)):
            os.makedirs(os.path.dirname(output_path))

        with open(output_path, 'w', encoding='utf-8') as json_file:
            json.dump(data_list, json_file, ensure_ascii=False, indent=4)
        
        print(f"Données filtrées sauvegardées dans {output_path}")
    except Exception as e:
        print(f"Erreur lors de la sauvegarde des données filtrées: {e}")

def process_triphaso_data(payload):
    donnees_puissance = payload[0]
    infos_dispositif = payload[1]
    
    result = []

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
    
    for cle, modele in cles_puissance.items():
        if cle in donnees_puissance:
            result.append(modele.format(donnees_puissance[cle]))
    
    for cle, modele in cles_dispositif.items():
        if cle in infos_dispositif:
            result.append(modele.format(infos_dispositif[cle]))

    donnee_filtree(result, 'datas/Triphaso_filtre_data.json')
    return " | ".join(result)

def process_am107_data(payload):
    sensor_data, device_info = payload
    result = []

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

    for key, template in sensor_keys.items():
        if key in sensor_data:
            result.append(template.format(sensor_data[key]))

    for key, template in device_keys.items():
        if key in device_info:
            result.append(template.format(device_info[key]))

    donnee_filtree(result, 'datas/AM07_filtre_data.json')

    return " | ".join(result)

def process_solaredge_data(payload):
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
    for cle, modele in modeles_cles.items():
        if cle in payload:
            resultat.append(modele.format(payload[cle]))

    donnee_filtree(resultat, 'datas/Solaredge_filtre_data.json')

    return " | ".join(resultat)

client = mqtt.Client()

client.on_connect = on_connect
client.on_message = on_message

client.connect(broker, port=1883, keepalive=60)

client.loop_forever()
