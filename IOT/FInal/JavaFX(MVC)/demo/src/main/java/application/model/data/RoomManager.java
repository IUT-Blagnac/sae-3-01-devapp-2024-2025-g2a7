package application.model.data;


import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.ObjectMapper;

public class RoomManager {
    private List<Room> roomsList = new ArrayList<>();
    private static final String JSON_FILE = "IOT/FInal/datas/AM07_filtre_data.json"; // Remplacez par le chemin réel

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
    }

    // Lire les données du fichier JSON et ajouter les données dans les salles
    public void loadDataFromJson() {
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            Map<String, Object> data = objectMapper.readValue(new File(JSON_FILE), Map.class);

            String roomId = (String) data.get("room");
            Double temperature = (Double) data.get("temperature");
            Double humidity = (Double) data.get("humidity");
            Double co2 = (Double) data.get("co2");
            Double illumination = (Double) data.get("illumination");

            addRoomData(roomId, temperature, humidity, co2, illumination);

        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    public List<Room> getRoomsList() {
        return roomsList;
    }
}
