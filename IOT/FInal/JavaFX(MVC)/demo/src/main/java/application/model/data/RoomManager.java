package application.model.data;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.ObjectMapper;

import application.view.AffDonneesViewController;
import javafx.application.Platform;

public class RoomManager {
    private List<Room> roomsList = new ArrayList<>();
    private static final String JSON_FILE = "IOT/FInal/datas/AM07_filtre_data.json"; // Remplacez par le chemin réel
    AffDonneesViewController viewDonnees = new AffDonneesViewController(); 
    
    // Ajouter une salle si elle n'existe pas encore
    private Room findRoomById(String roomId) {
        for (Room room : roomsList) {
            if (room.getRoomId().equals(roomId)) {
                return room;
            }
        }
        return null; // Si la salle n'existe pas, retourner null
    }

    // Ajouter les données d'une salle
    public void addRoomData(String roomId, Double temperature, Double humidity, Double co2, Double illumination) {
        Room room = findRoomById(roomId);
        if (room == null) {
            room = new Room(roomId);
            roomsList.add(room);
        }
        room.addData(temperature, humidity, co2, illumination);
        System.out.println("Données ajoutées : Temp=" + temperature + ", Hum=" + humidity + ", CO2=" + co2 + ", Lum=" + illumination);
    }
    public static String getJsonFilePath() {
        return JSON_FILE;
    }
    

    public void loadDataFromJson() {
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            List<Map<String, Object>> dataList = objectMapper.readValue(new File(JSON_FILE), List.class);
    
            // Itérer sur chaque entrée du JSON (chaque salle)
            for (Map<String, Object> data : dataList) {
                String roomId = (String) data.get("room");
                Map<String, Object> sensorData = (Map<String, Object>) data.get("data");
    
                // Utiliser Number pour gérer à la fois Integer et Double
                Double temperature = toDouble(sensorData.get("temperature"));
                Double humidity = toDouble(sensorData.get("humidity"));
                Double co2 = toDouble(sensorData.get("co2"));
                Double illumination = toDouble(sensorData.get("illumination"));
    
                addRoomData(roomId, temperature, humidity, co2, illumination);
                System.out.println("Données ajoutées pour la salle " + roomId + " : Temp=" + temperature + ", Hum=" + humidity + ", CO2=" + co2 + ", Lum=" + illumination);
            }
    
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    
    // Méthode utilitaire pour convertir à la fois Integer et Double en Double
    private Double toDouble(Object value) {
        if (value instanceof Number) {
            return ((Number) value).doubleValue();
        }
        return 0.0; // Valeur par défaut si ce n'est pas un nombre
    }
    

    public void addNewRoomData(String roomId, Double temperature, Double humidity, Double co2, Double illumination) {
        Room room = findRoomById(roomId);
        if (room != null) {
            room.addData(temperature, humidity, co2, illumination);
            System.out.println("Données ajoutées : Temp=" + temperature + ", Hum=" + humidity + ", CO2=" + co2 + ", Lum=" + illumination);

            // Mise à jour de l'interface graphique
            Platform.runLater(() -> {
                viewDonnees.loadRooms();
            });
        }
    }
    
    public List<Room> getRoomsList() {
        return roomsList;
    }
}
