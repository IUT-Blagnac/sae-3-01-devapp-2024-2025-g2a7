import paho.mqtt.client as mqtt
import json
import configparser
from datetime import datetime

config = configparser.ConfigParser()
config.read('config.ini')

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
        with open("data_log.txt", "a") as f:
            log_entry = f"[{timestamp}] Topic: {topic} | Data: {data}\n"
            f.write(log_entry)
        print("Données enregistrées dans le fichier.")
    except Exception as e:
        print(f"Erreur lors de l'écriture dans le fichier: {e}")

def process_triphaso_data(payload):
    power_data = payload[0]
    device_info = payload[1]
    
    result = []
    if 'positive_active_power_W' in power_data:
        result.append(f"Puissance active positive: {power_data['positive_active_power_W']} W")
    if 'negative_reactive_power_VAR' in power_data:
        result.append(f"Puissance réactive négative: {power_data['negative_reactive_power_VAR']} VAR")
    if 'sum_positive_active_energy_Wh' in power_data:
        result.append(f"Énergie active positive: {power_data['sum_positive_active_energy_Wh']} Wh")
    if 'sum_negative_reactive_energy_VARh' in power_data:
        result.append(f"Énergie réactive négative: {power_data['sum_negative_reactive_energy_VARh']} VARh")
    
    if 'deviceName' in device_info:
        result.append(f"Nom du dispositif: {device_info['deviceName']}")
    if 'devEUI' in device_info:
        result.append(f"Identifiant du dispositif (devEUI): {device_info['devEUI']}")
    if 'room' in device_info:
        result.append(f"Pièce: {device_info['room']}")
    if 'floor' in device_info:
        result.append(f"Étage: {device_info['floor']}")
    if 'Building' in device_info:
        result.append(f"Bâtiment: {device_info['Building']}")

    return " | ".join(result)

def process_am107_data(payload):
    print("Traitement AM107\n")
    sensor_data, device_info = payload

    result = []
    if isinstance(sensor_data, dict) and isinstance(device_info, dict):
        if 'temperature' in sensor_data:
            result.append(f"Température: {sensor_data['temperature']} °C")
        if 'humidity' in sensor_data:
            result.append(f"Humidité: {sensor_data['humidity']} %")
        if 'co2' in sensor_data:
            result.append(f"CO2: {sensor_data['co2']} ppm")
        if 'tvoc' in sensor_data:
            result.append(f"TVOC: {sensor_data['tvoc']} ppb")
        if 'illumination' in sensor_data:
            result.append(f"Illumination: {sensor_data['illumination']} lux")
        if 'pressure' in sensor_data:
            result.append(f"Pression: {sensor_data['pressure']} hPa")
        if 'activity' in sensor_data:
            result.append(f"Activité: {sensor_data['activity']}")
        if 'infrared' in sensor_data:
            result.append(f"Infrarouge: {sensor_data['infrared']}")
        if 'infrared_and_visible' in sensor_data:
            result.append(f"Infrarouge + Visible: {sensor_data['infrared_and_visible']}")

        if 'deviceName' in device_info:
            result.append(f"Nom du dispositif: {device_info['deviceName']}")
        if 'devEUI' in device_info:
            result.append(f"Identifiant du dispositif (devEUI): {device_info['devEUI']}")
        if 'room' in device_info:
            result.append(f"Pièce: {device_info['room']}")
        if 'floor' in device_info:
            result.append(f"Étage: {device_info['floor']}")
        if 'Building' in device_info:
            result.append(f"Bâtiment: {device_info['Building']}")

    else:
        result.append("Erreur : Le format du payload AM107 est invalide.")
    
    return " | ".join(result)

def process_solaredge_data(payload):
    print("Traitement Solaredge\n")
    result = []
    if 'lastUpdateTime' in payload: # Vérifier si le payload contient les données attendues
        result.append(f"Dernière mise à jour: {payload['lastUpdateTime']}")
        result.append(f"Énergie totale: {payload['lifeTimeData']} Wh")
        result.append(f"Énergie l'année dernière: {payload['lastYearData']} Wh")
        result.append(f"Énergie le mois dernier: {payload['lastMonthData']} Wh")
        result.append(f"Énergie du dernier jour: {payload['lastDayData']} Wh")
        result.append(f"Puissance actuelle: {payload['currentPower']} W")
        result.append(f"Mesuré par: {payload['measuredBy']}")
    return " | ".join(result)

def alertes_data(data):
    alerts = []
    if 'temperature' in data and data['temperature'] > temperature_max:
        alerts.append(f"Température élevée: {data['temperature']}°C (max {temperature_max}°C)")
    if 'humidity' in data and data['humidity'] > humidite_max:
        alerts.append(f"Humidité élevée: {data['humidity']}% (max {humidite_max}%)")
    if 'pressure' in data and data['pressure'] > pression_max:
        alerts.append(f"Pression élevée: {data['pressure']} hPa (max {pression_max} hPa)")

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
