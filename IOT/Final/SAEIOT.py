import paho.mqtt.client as mqtt
import json
import configparser
from datetime import datetime

config = configparser.ConfigParser()
config.read('config.ini')

topic_triphaso = config['mqtt']['topic_triphaso']
topic_am107 = config['mqtt']['topic_am107']
topic_solaredge = config['mqtt']['topic_solaredge']
broker = config['mqtt']['broker']

temperature_max = float(config['seuils']['temperature_max'])
humidite_max = float(config['seuils']['humidite_max'])
pression_max = float(config['seuils']['pression_max'])

def on_connect(client, userdata, flags, rc):
    print(f"Connecté avec le code de résultat {rc}")
    client.subscribe(topic_triphaso)
    client.subscribe(topic_am107)
    client.subscribe(topic_solaredge)
    print(f"Abonné aux topics:\n{topic_triphaso}\n{topic_am107}\n{topic_solaredge}")

def on_message(client, userdata, msg):
    try:
        print(f"Données reçues sur le topic: {msg.topic}")
        payload = json.loads(msg.payload.decode())
        processed_data = ""
        
        if "Triphaso" in msg.topic:
            if isinstance(payload, list) and len(payload) == 2:
                processed_data = process_triphaso_data(payload)
            else:
                print("Format de données Triphaso invalide")
                return
                
        elif "AM107" in msg.topic:
            if isinstance(payload, list) and len(payload) == 2 and 'temperature' in payload[0]:
                processed_data = process_am107_data(payload)
            else:
                print("Format de données AM107 invalide")
                return
                
        elif "solaredge" in msg.topic:
            processed_data = process_solaredge_data(payload)
        
        if processed_data:
            timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            log_data(timestamp, msg.topic, processed_data)

    except json.JSONDecodeError as e:
        print(f"Erreur de décodage JSON: {e}")
    except Exception as e:
        print(f"Erreur lors du traitement du message: {e}")

def log_data(timestamp, topic, data):
    try:
        with open("data_log.txt", "a", encoding='utf-8') as f:
            log_entry = f"[{timestamp}] Topic: {topic} | Data: {data}\n"
            f.write(log_entry)
        print("Données enregistrées dans le fichier.")
    except Exception as e:
        print(f"Erreur lors de l'écriture dans le fichier: {e}")

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

    return " | ".join(resultat) if resultat else "Aucune donnée SolarEdge valide"

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

try:
    client.connect(broker, port=1883, keepalive=60)
    print(f"Connexion établie avec le broker: {broker}")
    client.loop_forever()
except Exception as e:
    print(f"Erreur de connexion au broker: {e}")