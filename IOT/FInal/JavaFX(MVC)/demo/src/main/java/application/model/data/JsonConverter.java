package application.model.data;

import java.io.File;
import java.io.IOException;
import com.fasterxml.jackson.databind.ObjectMapper;

public class JsonConverter {

    public EnergieData loadEnergieDataFromJson() {
        try {
            ObjectMapper objectMapper = new ObjectMapper();

            return objectMapper.readValue(new File("IOT/FInal/datas/Solaredge_filtre_data.json"), EnergieData.class);

        } catch (IOException e) {
            System.err.println("Erreur lors de la lecture du fichier JSON: " + e.getMessage());
            return null;
        }
    }
}
